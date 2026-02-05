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

    $query = "INSERT INTO attori (nominativo, nazionalita, data_nascita, sesso, note) VALUES (" .

        sql_value($connection, $nominativo) . ", " .
        sql_value($connection, $nazionalita) . ", " .
        sql_value($connection, $data_nascita) . ", " .
        sql_value($connection, $sesso) . ", " .
        sql_value($connection, $note) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Attore inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento dell'attore.";
        $tipo = "errore";
    }
}

if ($azione === "update") {
    $id_attore = post_param("id_attore");
    $nominativo = post_param("nominativo");
    $nazionalita = post_param("nazionalita");
    $data_nascita = post_param("data_nascita");
    $sesso = post_param("sesso");
    $note = post_param("note");

    $query = "UPDATE attori SET " .
        "nominativo = " . sql_value($connection, $nominativo) . ", " .
        "nazionalita = " . sql_value($connection, $nazionalita) . ", " .
        "data_nascita = " . sql_value($connection, $data_nascita) . ", " .
        "sesso = " . sql_value($connection, $sesso) . ", " .
        "note = " . sql_value($connection, $note) . " " .
        "WHERE id_attore = " . sql_value($connection, $id_attore, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Attore aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento dell'attore.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_attore = get_param("id_attore");
    $query = "DELETE FROM attori WHERE id_attore = " . sql_value($connection, $id_attore, true);
    mysqli_query($connection, $query);
    redirect_to("attori.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Attore eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$attore_edit = null;
if ($azione === "edit") {
    $id_attore = get_param("id_attore");
    $result = mysqli_query($connection, "SELECT * FROM attori WHERE id_attore = " . sql_value($connection, $id_attore, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $attore_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Attore non trovato.";
        $tipo = "errore";
    }
}

$result = mysqli_query($connection, "SELECT * FROM attori ORDER BY id_attore");
$attori = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $attori[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Attori - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Attori</h1>
            <p>Inserisci, modifica o elimina gli attori.</p>
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
            <h2>Attori</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica attore" : "Nuovo attore"; ?></h3>
            <form method="post" action="attori.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <?php if ($edit) { ?>
                        <label for="id_attore">ID Attore</label>
                        <input type="number" id="id_attore" name="id_attore" value="<?php echo h($attore_edit["id_attore"] ?? ""); ?>" readonly>
                    <?php } ?>

                    <label for="nominativo">Nominativo</label>
                    <input type="text" id="nominativo" name="nominativo" value="<?php echo h($attore_edit["nominativo"] ?? ""); ?>" required>

                    <label for="nazionalita">Nazionalita</label>
                    <input type="text" id="nazionalita" name="nazionalita" value="<?php echo h($attore_edit["nazionalita"] ?? ""); ?>">

                    <label for="data_nascita">Data di nascita</label>
                    <input type="date" id="data_nascita" name="data_nascita" value="<?php echo h($attore_edit["data_nascita"] ?? ""); ?>" required>

                    <label for="sesso">Sesso</label>
                    <select id="sesso" name="sesso" required>
                        <option value="">--</option>
                        <option value="M" <?php echo (isset($attore_edit["sesso"]) && $attore_edit["sesso"] === "M") ? "selected" : ""; ?>>M</option>
                        <option value="F" <?php echo (isset($attore_edit["sesso"]) && $attore_edit["sesso"] === "F") ? "selected" : ""; ?>>F</option>
                    </select>

                    <label for="note">Note</label>
                    <textarea id="note" name="note"><?php echo h($attore_edit["note"] ?? ""); ?></textarea>
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="attori.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco attori</h3>
            <p class="conteggio">Totale: <?php echo count($attori); ?></p>
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
                        <?php foreach ($attori as $row) { ?>
                            <tr>
                                <td><?php echo h($row["id_attore"]); ?></td>
                                <td><?php echo h($row["nominativo"]); ?></td>
                                <td><?php echo h($row["nazionalita"]); ?></td>
                                <td><?php echo h($row["data_nascita"]); ?></td>
                                <td><?php echo h($row["sesso"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="attori.php?azione=edit&id_attore=<?php echo h($row["id_attore"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="attori.php?azione=delete&id_attore=<?php echo h($row["id_attore"]); ?>" onclick="return confirm('Eliminare l\'attore?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($attori) === 0) { ?>
                            <tr><td colspan="6">Nessun attore presente.</td></tr>
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



