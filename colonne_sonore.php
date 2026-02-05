<?php
require_once __DIR__ . "/inc/db.php";
require_once __DIR__ . "/inc/util.php";

$connection = db_connect();
$messaggio = "";
$tipo = "info";

function carica_film($connection)
{
    $lista = [];
    $result = mysqli_query($connection, "SELECT id_film, titolo FROM film ORDER BY titolo");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = $row;
        }
    }
    return $lista;
}

function carica_musicisti($connection)
{
    $lista = [];
    $result = mysqli_query($connection, "SELECT id_musicista, nominativo FROM musicisti ORDER BY nominativo");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = $row;
        }
    }
    return $lista;
}

$azione = post_param("azione");
if ($azione === "") {
    $azione = get_param("azione");
}

if ($azione === "create") {
    $id_musicista = post_param("id_musicista");
    $id_film = post_param("id_film");
    $brano = post_param("brano");
    $valutazione = post_param("valutazione");

    $query = "INSERT INTO colonne_sonore (id_musicista, id_film, brano, valutazione) VALUES (" .
        sql_value($connection, $id_musicista, true) . ", " .
        sql_value($connection, $id_film, true) . ", " .
        sql_value($connection, $brano) . ", " .
        sql_value($connection, $valutazione, true) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Colonna sonora inserita correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento della colonna sonora.";
        $tipo = "errore";
    }
}

if ($azione === "update") {
    $id_musicista = post_param("id_musicista");
    $id_film = post_param("id_film");
    $brano = post_param("brano");
    $valutazione = post_param("valutazione");

    $query = "UPDATE colonne_sonore SET " .
        "brano = " . sql_value($connection, $brano) . ", " .
        "valutazione = " . sql_value($connection, $valutazione, true) . " " .
        "WHERE id_musicista = " . sql_value($connection, $id_musicista, true) .
        " AND id_film = " . sql_value($connection, $id_film, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Colonna sonora aggiornata correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento della colonna sonora.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_musicista = get_param("id_musicista");
    $id_film = get_param("id_film");
    $query = "DELETE FROM colonne_sonore WHERE id_musicista = " . sql_value($connection, $id_musicista, true) .
        " AND id_film = " . sql_value($connection, $id_film, true);
    mysqli_query($connection, $query);
    redirect_to("colonne_sonore.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Colonna sonora eliminata correttamente.";
    $tipo = "successo";
}

$edit = false;
$colonna_edit = null;
if ($azione === "edit") {
    $id_musicista = get_param("id_musicista");
    $id_film = get_param("id_film");
    $result = mysqli_query($connection, "SELECT * FROM colonne_sonore WHERE id_musicista = " . sql_value($connection, $id_musicista, true) .
        " AND id_film = " . sql_value($connection, $id_film, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $colonna_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Colonna sonora non trovata.";
        $tipo = "errore";
    }
}

$film_list = carica_film($connection);
$musicisti_list = carica_musicisti($connection);

$result = mysqli_query($connection, "SELECT cs.*, f.titolo AS titolo_film, m.nominativo AS nome_musicista FROM colonne_sonore cs LEFT JOIN film f ON cs.id_film = f.id_film LEFT JOIN musicisti m ON cs.id_musicista = m.id_musicista ORDER BY cs.id_film");
$colonne = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $colonne[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Colonne Sonore - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Colonne Sonore</h1>
            <p>Inserisci, modifica o elimina le colonne sonore.</p>
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
            <h2>Colonne Sonore</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica colonna sonora" : "Nuova colonna sonora"; ?></h3>
            <form method="post" action="colonne_sonore.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <label for="id_musicista">Musicista</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_musicista" name="id_musicista" value="<?php echo h($colonna_edit["id_musicista"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_musicista" name="id_musicista" required>
                            <option value="">--</option>
                            <?php foreach ($musicisti_list as $musicista) { ?>
                                <option value="<?php echo h($musicista["id_musicista"]); ?>" <?php echo (isset($colonna_edit["id_musicista"]) && (string) $colonna_edit["id_musicista"] === (string) $musicista["id_musicista"]) ? "selected" : ""; ?>>
                                    <?php echo h($musicista["nominativo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="id_film">Film</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_film" name="id_film" value="<?php echo h($colonna_edit["id_film"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_film" name="id_film" required>
                            <option value="">--</option>
                            <?php foreach ($film_list as $film) { ?>
                                <option value="<?php echo h($film["id_film"]); ?>" <?php echo (isset($colonna_edit["id_film"]) && (string) $colonna_edit["id_film"] === (string) $film["id_film"]) ? "selected" : ""; ?>>
                                    <?php echo h($film["titolo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="brano">Brano</label>
                    <input type="text" id="brano" name="brano" value="<?php echo h($colonna_edit["brano"] ?? ""); ?>" required>

                    <label for="valutazione">Valutazione</label>
                    <input type="number" id="valutazione" name="valutazione" value="<?php echo h($colonna_edit["valutazione"] ?? ""); ?>">
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="colonne_sonore.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco colonne sonore</h3>
            <p class="conteggio">Totale: <?php echo count($colonne); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>Musicista</th>
                            <th>Film</th>
                            <th>Brano</th>
                            <th>Valutazione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($colonne as $row) { ?>
                            <tr>
                                <td><?php echo h($row["nome_musicista"] ?? $row["id_musicista"]); ?></td>
                                <td><?php echo h($row["titolo_film"] ?? $row["id_film"]); ?></td>
                                <td><?php echo h($row["brano"]); ?></td>
                                <td><?php echo h($row["valutazione"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="colonne_sonore.php?azione=edit&id_musicista=<?php echo h($row["id_musicista"]); ?>&id_film=<?php echo h($row["id_film"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="colonne_sonore.php?azione=delete&id_musicista=<?php echo h($row["id_musicista"]); ?>&id_film=<?php echo h($row["id_film"]); ?>" onclick="return confirm('Eliminare la colonna sonora?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($colonne) === 0) { ?>
                            <tr><td colspan="5">Nessuna colonna sonora presente.</td></tr>
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
