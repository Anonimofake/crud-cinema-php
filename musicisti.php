<?php
require_once __DIR__ . "/inc/db.php";
require_once __DIR__ . "/inc/util.php";

$connection = db_connect();
$messaggio = "";
$tipo = "info";

$azione = post_param("azione");
if ($azione === "") {
    $azione = get_param("azione");
}

if ($azione === "create") {

    $nominativo = post_param("nominativo");
    $nazionalita = post_param("nazionalita");
    $data_nascita = post_param("data_nascita");
    $sesso = post_param("sesso");
    $note = post_param("note");

    $query = "INSERT INTO musicisti (nominativo, nazionalita, data_nascita, sesso, note) VALUES (" .

        sql_value($connection, $nominativo) . ", " .
        sql_value($connection, $nazionalita) . ", " .
        sql_value($connection, $data_nascita) . ", " .
        sql_value($connection, $sesso) . ", " .
        sql_value($connection, $note) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Musicista inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento del musicista.";
        $tipo = "errore";
    }
}

if ($azione === "update") {

    $nominativo = post_param("nominativo");
    $nazionalita = post_param("nazionalita");
    $data_nascita = post_param("data_nascita");
    $sesso = post_param("sesso");
    $note = post_param("note");

    $query = "UPDATE musicisti SET " .
        "nominativo = " . sql_value($connection, $nominativo) . ", " .
        "nazionalita = " . sql_value($connection, $nazionalita) . ", " .
        "data_nascita = " . sql_value($connection, $data_nascita) . ", " .
        "sesso = " . sql_value($connection, $sesso) . ", " .
        "note = " . sql_value($connection, $note) . " " .
        "WHERE id_musicista = " . sql_value($connection, $id_musicista, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Musicista aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento del musicista.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_musicista = get_param("id_musicista");
    $query = "DELETE FROM musicisti WHERE id_musicista = " . sql_value($connection, $id_musicista, true);
    mysqli_query($connection, $query);
    redirect_to("musicisti.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Musicista eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$musicista_edit = null;
if ($azione === "edit") {
    $id_musicista = get_param("id_musicista");
    $result = mysqli_query($connection, "SELECT * FROM musicisti WHERE id_musicista = " . sql_value($connection, $id_musicista, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $musicista_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Musicista non trovato.";
        $tipo = "errore";
    }
}

$result = mysqli_query($connection, "SELECT * FROM musicisti ORDER BY id_musicista");
$musicisti = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $musicisti[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Musicisti - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Musicisti</h1>
            <p>Inserisci, modifica o elimina i musicisti.</p>
        </div>
        <div id="menu">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="film.php">Film</a></li>
                <li><a href="genere.php">Generi</a></li>
                <li><a href="attori.php">Attori</a></li>
                <li><a href="musicisti.php">Musicisti</a></li>
                <li><a href="premi.php">Premi</a></li>
                <li><a href="colonne_sonore.php">Colonne Sonore</a></li>
                <li><a href="recita_in.php">Recita In</a></li>
                <li><a href="ha_vinto.php">Ha Vinto</a></li>
            </ul>
        </div>
        <div id="contenuto">
            <h2>Musicisti</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica musicista" : "Nuovo musicista"; ?></h3>
            <form method="post" action="musicisti.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <?php if ($edit) { ?>
                        <label for="id_musicista">ID Musicista</label>
                        <input type="number" id="id_musicista" name="id_musicista" value="<?php echo h($musicista_edit["id_musicista"] ?? ""); ?>" readonly>
                    <?php } ?>

                    <label for="nominativo">Nominativo</label>
                    <input type="text" id="nominativo" name="nominativo" value="<?php echo h($musicista_edit["nominativo"] ?? ""); ?>" required>

                    <label for="nazionalita">Nazionalita</label>
                    <input type="text" id="nazionalita" name="nazionalita" value="<?php echo h($musicista_edit["nazionalita"] ?? ""); ?>">

                    <label for="data_nascita">Data di nascita</label>
                    <input type="date" id="data_nascita" name="data_nascita" value="<?php echo h($musicista_edit["data_nascita"] ?? ""); ?>" required>

                    <label for="sesso">Sesso</label>
                    <select id="sesso" name="sesso" required>
                        <option value="">--</option>
                        <option value="M" <?php echo (isset($musicista_edit["sesso"]) && $musicista_edit["sesso"] === "M") ? "selected" : ""; ?>>M</option>
                        <option value="F" <?php echo (isset($musicista_edit["sesso"]) && $musicista_edit["sesso"] === "F") ? "selected" : ""; ?>>F</option>
                    </select>

                    <label for="note">Note</label>
                    <textarea id="note" name="note"><?php echo h($musicista_edit["note"] ?? ""); ?></textarea>
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="musicisti.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco musicisti</h3>
            <p class="conteggio">Totale: <?php echo count($musicisti); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nominativo</th>
                            <th>Nazionalita</th>
                            <th>Data di nascita</th>
                            <th>Sesso</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($musicisti as $row) { ?>
                            <tr>
                                <td><?php echo h($row["id_musicista"]); ?></td>
                                <td><?php echo h($row["nominativo"]); ?></td>
                                <td><?php echo h($row["nazionalita"]); ?></td>
                                <td><?php echo h($row["data_nascita"]); ?></td>
                                <td><?php echo h($row["sesso"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="musicisti.php?azione=edit&id_musicista=<?php echo h($row["id_musicista"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="musicisti.php?azione=delete&id_musicista=<?php echo h($row["id_musicista"]); ?>" onclick="return confirm('Eliminare il musicista?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($musicisti) === 0) { ?>
                            <tr><td colspan="6">Nessun musicista presente.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="pie_pagina">
            &copy; 2026 DBCinema - Pannello di controllo
        </div>
    </div>
</body>
</html>



