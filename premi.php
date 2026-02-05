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

    $descrizione = post_param("descrizione");
    $manifestazione = post_param("manifestazione");

    $query = "INSERT INTO premi (descrizione, manifestazione) VALUES (" .

        sql_value($connection, $descrizione) . ", " .
        sql_value($connection, $manifestazione) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Premio inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento del premio.";
        $tipo = "errore";
    }
}

if ($azione === "update") {

    $descrizione = post_param("descrizione");
    $manifestazione = post_param("manifestazione");

    $query = "UPDATE premi SET " .
        "descrizione = " . sql_value($connection, $descrizione) . ", " .
        "manifestazione = " . sql_value($connection, $manifestazione) . " " .
        "WHERE id_premio = " . sql_value($connection, $id_premio, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Premio aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento del premio.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_premio = get_param("id_premio");
    $query = "DELETE FROM premi WHERE id_premio = " . sql_value($connection, $id_premio, true);
    mysqli_query($connection, $query);
    redirect_to("premi.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Premio eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$premio_edit = null;
if ($azione === "edit") {
    $id_premio = get_param("id_premio");
    $result = mysqli_query($connection, "SELECT * FROM premi WHERE id_premio = " . sql_value($connection, $id_premio, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $premio_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Premio non trovato.";
        $tipo = "errore";
    }
}

$result = mysqli_query($connection, "SELECT * FROM premi ORDER BY id_premio");
$premi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $premi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Premi - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Premi</h1>
            <p>Inserisci, modifica o elimina i premi.</p>
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
            <h2>Premi</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica premio" : "Nuovo premio"; ?></h3>
            <form method="post" action="premi.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <?php if ($edit) { ?>
                        <label for="id_premio">ID Premio</label>
                        <input type="number" id="id_premio" name="id_premio" value="<?php echo h($premio_edit["id_premio"] ?? ""); ?>" readonly>
                    <?php } ?>

                    <label for="descrizione">Descrizione</label>
                    <input type="text" id="descrizione" name="descrizione" value="<?php echo h($premio_edit["descrizione"] ?? ""); ?>">

                    <label for="manifestazione">Manifestazione</label>
                    <input type="text" id="manifestazione" name="manifestazione" value="<?php echo h($premio_edit["manifestazione"] ?? ""); ?>">
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="premi.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco premi</h3>
            <p class="conteggio">Totale: <?php echo count($premi); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrizione</th>
                            <th>Manifestazione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($premi as $row) { ?>
                            <tr>
                                <td><?php echo h($row["id_premio"]); ?></td>
                                <td><?php echo h($row["descrizione"]); ?></td>
                                <td><?php echo h($row["manifestazione"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="premi.php?azione=edit&id_premio=<?php echo h($row["id_premio"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="premi.php?azione=delete&id_premio=<?php echo h($row["id_premio"]); ?>" onclick="return confirm('Eliminare il premio?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($premi) === 0) { ?>
                            <tr><td colspan="4">Nessun premio presente.</td></tr>
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



