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

    $query = "INSERT INTO genere (descrizione) VALUES (" .

        sql_value($connection, $descrizione) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Genere inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento del genere.";
        $tipo = "errore";
    }
}

if ($azione === "update") {

    $descrizione = post_param("descrizione");

    $query = "UPDATE genere SET descrizione = " . sql_value($connection, $descrizione) .
        " WHERE id_genere = " . sql_value($connection, $id_genere, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Genere aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento del genere.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_genere = get_param("id_genere");
    $query = "DELETE FROM genere WHERE id_genere = " . sql_value($connection, $id_genere, true);
    mysqli_query($connection, $query);
    redirect_to("genere.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Genere eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$genere_edit = null;
if ($azione === "edit") {
    $id_genere = get_param("id_genere");
    $result = mysqli_query($connection, "SELECT * FROM genere WHERE id_genere = " . sql_value($connection, $id_genere, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $genere_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Genere non trovato.";
        $tipo = "errore";
    }
}

$result = mysqli_query($connection, "SELECT * FROM genere ORDER BY id_genere");
$generi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $generi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Generi - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Generi</h1>
            <p>Inserisci, modifica o elimina i generi.</p>
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
            <h2>Generi</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica genere" : "Nuovo genere"; ?></h3>
            <form method="post" action="genere.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <?php if ($edit) { ?>
                        <label for="id_genere">ID Genere</label>
                        <input type="number" id="id_genere" name="id_genere" value="<?php echo h($genere_edit["id_genere"] ?? ""); ?>" readonly>
                    <?php } ?>

                    <label for="descrizione">Descrizione</label>
                    <input type="text" id="descrizione" name="descrizione" value="<?php echo h($genere_edit["descrizione"] ?? ""); ?>">
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="genere.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco generi</h3>
            <p class="conteggio">Totale: <?php echo count($generi); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrizione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($generi as $row) { ?>
                            <tr>
                                <td><?php echo h($row["id_genere"]); ?></td>
                                <td><?php echo h($row["descrizione"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="genere.php?azione=edit&id_genere=<?php echo h($row["id_genere"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="genere.php?azione=delete&id_genere=<?php echo h($row["id_genere"]); ?>" onclick="return confirm('Eliminare il genere?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($generi) === 0) { ?>
                            <tr><td colspan="3">Nessun genere presente.</td></tr>
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



