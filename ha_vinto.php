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

function carica_premi($connection)
{
    $lista = [];
    $result = mysqli_query($connection, "SELECT id_premio, descrizione FROM premi ORDER BY descrizione");
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
    $id_premio = post_param("id_premio");
    $id_film = post_param("id_film");
    $anno = post_param("anno");

    $query = "INSERT INTO ha_vinto (id_premio, id_film, anno) VALUES (" .
        sql_value($connection, $id_premio, true) . ", " .
        sql_value($connection, $id_film, true) . ", " .
        sql_value($connection, $anno, true) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Premio vinto inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento del premio vinto.";
        $tipo = "errore";
    }
}

if ($azione === "update") {
    $id_premio = post_param("id_premio");
    $id_film = post_param("id_film");
    $anno = post_param("anno");
    $anno_originale = post_param("anno_originale");

    $query = "UPDATE ha_vinto SET anno = " . sql_value($connection, $anno, true) .
        " WHERE id_premio = " . sql_value($connection, $id_premio, true) .
        " AND id_film = " . sql_value($connection, $id_film, true) .
        " AND anno = " . sql_value($connection, $anno_originale, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Premio vinto aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento del premio vinto.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_premio = get_param("id_premio");
    $id_film = get_param("id_film");
    $anno = get_param("anno");
    $query = "DELETE FROM ha_vinto WHERE id_premio = " . sql_value($connection, $id_premio, true) .
        " AND id_film = " . sql_value($connection, $id_film, true) .
        " AND anno = " . sql_value($connection, $anno, true);
    mysqli_query($connection, $query);
    redirect_to("ha_vinto.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Premio vinto eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$ha_vinto_edit = null;
if ($azione === "edit") {
    $id_premio = get_param("id_premio");
    $id_film = get_param("id_film");
    $anno = get_param("anno");
    $result = mysqli_query($connection, "SELECT * FROM ha_vinto WHERE id_premio = " . sql_value($connection, $id_premio, true) .
        " AND id_film = " . sql_value($connection, $id_film, true) .
        " AND anno = " . sql_value($connection, $anno, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $ha_vinto_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Record non trovato.";
        $tipo = "errore";
    }
}

$film_list = carica_film($connection);
$premi_list = carica_premi($connection);

$result = mysqli_query($connection, "SELECT hv.*, f.titolo AS titolo_film, p.descrizione AS premio FROM ha_vinto hv LEFT JOIN film f ON hv.id_film = f.id_film LEFT JOIN premi p ON hv.id_premio = p.id_premio ORDER BY hv.anno DESC");
$ha_vinto = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ha_vinto[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Ha Vinto - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Premi Vinti</h1>
            <p>Inserisci, modifica o elimina i premi vinti.</p>
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
            <h2>Ha Vinto</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica premio vinto" : "Nuovo premio vinto"; ?></h3>
            <form method="post" action="ha_vinto.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <input type="hidden" name="anno_originale" value="<?php echo h($ha_vinto_edit["anno"] ?? ""); ?>">
                <div class="form-grid">
                    <label for="id_premio">Premio</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_premio" name="id_premio" value="<?php echo h($ha_vinto_edit["id_premio"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_premio" name="id_premio" required>
                            <option value="">--</option>
                            <?php foreach ($premi_list as $premio) { ?>
                                <option value="<?php echo h($premio["id_premio"]); ?>" <?php echo (isset($ha_vinto_edit["id_premio"]) && (string) $ha_vinto_edit["id_premio"] === (string) $premio["id_premio"]) ? "selected" : ""; ?>>
                                    <?php echo h($premio["descrizione"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="id_film">Film</label>
                    <?php if ($edit) { ?>
                        <input type="number" id="id_film" name="id_film" value="<?php echo h($ha_vinto_edit["id_film"] ?? ""); ?>" readonly>
                    <?php } else { ?>
                        <select id="id_film" name="id_film" required>
                            <option value="">--</option>
                            <?php foreach ($film_list as $film) { ?>
                                <option value="<?php echo h($film["id_film"]); ?>" <?php echo (isset($ha_vinto_edit["id_film"]) && (string) $ha_vinto_edit["id_film"] === (string) $film["id_film"]) ? "selected" : ""; ?>>
                                    <?php echo h($film["titolo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>

                    <label for="anno">Anno</label>
                    <input type="number" id="anno" name="anno" value="<?php echo h($ha_vinto_edit["anno"] ?? ""); ?>" required>
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="ha_vinto.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco premi vinti</h3>
            <p class="conteggio">Totale: <?php echo count($ha_vinto); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>Premio</th>
                            <th>Film</th>
                            <th>Anno</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ha_vinto as $row) { ?>
                            <tr>
                                <td><?php echo h($row["premio"] ?? $row["id_premio"]); ?></td>
                                <td><?php echo h($row["titolo_film"] ?? $row["id_film"]); ?></td>
                                <td><?php echo h($row["anno"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="ha_vinto.php?azione=edit&id_premio=<?php echo h($row["id_premio"]); ?>&id_film=<?php echo h($row["id_film"]); ?>&anno=<?php echo h($row["anno"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="ha_vinto.php?azione=delete&id_premio=<?php echo h($row["id_premio"]); ?>&id_film=<?php echo h($row["id_film"]); ?>&anno=<?php echo h($row["anno"]); ?>" onclick="return confirm('Eliminare il premio vinto?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($ha_vinto) === 0) { ?>
                            <tr><td colspan="4">Nessun premio vinto presente.</td></tr>
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
