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

function carica_attori($connection)
{
    $lista = [];
    $result = mysqli_query($connection, "SELECT id_attore, nominativo FROM attori ORDER BY nominativo");
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
    $id_attore = post_param("id_attore");
    $id_film = post_param("id_film");
    $personaggio = post_param("personaggio");
    $valutazione = post_param("valutazione");

    $query = "INSERT INTO recita_in (id_attore, id_film, personaggio, valutazione) VALUES (" .
        sql_value($connection, $id_attore, true) . ", " .
        sql_value($connection, $id_film, true) . ", " .
        sql_value($connection, $personaggio) . ", " .
        sql_value($connection, $valutazione, true) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Recita inserita correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento della recita.";
        $tipo = "errore";
    }
}

if ($azione === "update") {
    $id_attore = post_param("id_attore");
    $id_film = post_param("id_film");
    $personaggio = post_param("personaggio");
    $valutazione = post_param("valutazione");

    $query = "UPDATE recita_in SET " .
        "personaggio = " . sql_value($connection, $personaggio) . ", " .
        "valutazione = " . sql_value($connection, $valutazione, true) . " " .
        "WHERE id_attore = " . sql_value($connection, $id_attore, true) .
        " AND id_film = " . sql_value($connection, $id_film, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Recita aggiornata correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento della recita.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_attore = get_param("id_attore");
    $id_film = get_param("id_film");
    $query = "DELETE FROM recita_in WHERE id_attore = " . sql_value($connection, $id_attore, true) .
        " AND id_film = " . sql_value($connection, $id_film, true);
    mysqli_query($connection, $query);
    redirect_to("recita_in.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Recita eliminata correttamente.";
    $tipo = "successo";
}

$edit = false;
$recita_edit = null;
if ($azione === "edit") {
    $id_attore = get_param("id_attore");
    $id_film = get_param("id_film");
    $result = mysqli_query($connection, "SELECT * FROM recita_in WHERE id_attore = " . sql_value($connection, $id_attore, true) .
        " AND id_film = " . sql_value($connection, $id_film, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $recita_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Recita non trovata.";
        $tipo = "errore";
    }
}

$film_list = carica_film($connection);
$attori_list = carica_attori($connection);

$result = mysqli_query($connection, "SELECT r.*, f.titolo AS titolo_film, a.nominativo AS nome_attore FROM recita_in r LEFT JOIN film f ON r.id_film = f.id_film LEFT JOIN attori a ON r.id_attore = a.id_attore ORDER BY r.id_film");
$recite = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recite[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Recita In - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Recite</h1>
            <p>Inserisci, modifica o elimina le recite nei film.</p>
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
            <h2>Recita In</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica recita" : "Nuova recita"; ?></h3>
            <form method="post" action="recita_in.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <label for="id_attore">Attore</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_attore" name="id_attore" value="<?php echo h($recita_edit["id_attore"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_attore" name="id_attore" required>
                            <option value="">--</option>
                            <?php foreach ($attori_list as $attore) { ?>
                                <option value="<?php echo h($attore["id_attore"]); ?>" <?php echo (isset($recita_edit["id_attore"]) && (string) $recita_edit["id_attore"] === (string) $attore["id_attore"]) ? "selected" : ""; ?>>
                                    <?php echo h($attore["nominativo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="id_film">Film</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_film" name="id_film" value="<?php echo h($recita_edit["id_film"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_film" name="id_film" required>
                            <option value="">--</option>
                            <?php foreach ($film_list as $film) { ?>
                                <option value="<?php echo h($film["id_film"]); ?>" <?php echo (isset($recita_edit["id_film"]) && (string) $recita_edit["id_film"] === (string) $film["id_film"]) ? "selected" : ""; ?>>
                                    <?php echo h($film["titolo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="personaggio">Personaggio</label>
                    <input type="text" id="personaggio" name="personaggio" value="<?php echo h($recita_edit["personaggio"] ?? ""); ?>" required>

                    <label for="valutazione">Valutazione</label>
                    <input type="number" id="valutazione" name="valutazione" value="<?php echo h($recita_edit["valutazione"] ?? ""); ?>">
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="recita_in.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco recite</h3>
            <p class="conteggio">Totale: <?php echo count($recite); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>Attore</th>
                            <th>Film</th>
                            <th>Personaggio</th>
                            <th>Valutazione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recite as $row) { ?>
                            <tr>
                                <td><?php echo h($row["nome_attore"] ?? $row["id_attore"]); ?></td>
                                <td><?php echo h($row["titolo_film"] ?? $row["id_film"]); ?></td>
                                <td><?php echo h($row["personaggio"]); ?></td>
                                <td><?php echo h($row["valutazione"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="recita_in.php?azione=edit&id_attore=<?php echo h($row["id_attore"]); ?>&id_film=<?php echo h($row["id_film"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="recita_in.php?azione=delete&id_attore=<?php echo h($row["id_attore"]); ?>&id_film=<?php echo h($row["id_film"]); ?>" onclick="return confirm('Eliminare la recita?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($recite) === 0) { ?>
                            <tr><td colspan="5">Nessuna recita presente.</td></tr>
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
