<?php
require_once __DIR__ . "/inc/db.php";
require_once __DIR__ . "/inc/util.php";

$connection = db_connect();
$messaggio = "";
$tipo = "info";

function carica_generi($connection)
{
    $generi = [];
    $result = mysqli_query($connection, "SELECT id_genere, descrizione FROM genere ORDER BY descrizione");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $generi[] = $row;
        }
    }
    return $generi;
}

$azione = post_param("azione");
if ($azione === "") {
    $azione = get_param("azione");
}

if ($azione === "create") {
    $titolo = post_param("titolo");
    $anno = post_param("anno");
    $regista = post_param("regista");
    $nazionalita = post_param("nazionalita");
    $produzione = post_param("produzione");
    $distribuzione = post_param("distribuzione");
    $durata = post_param("durata");
    $colore = post_param("colore");
    $trama = post_param("trama");
    $valutazione = post_param("valutazione");
    $id_genere = post_param("id_genere");

    $query = "INSERT INTO film (titolo, anno, regista, nazionalita, produzione, distribuzione, durata, colore, trama, valutazione, id_genere) VALUES (" .
        sql_value($connection, $titolo) . ", " .
        sql_value($connection, $anno, true) . ", " .
        sql_value($connection, $regista) . ", " .
        sql_value($connection, $nazionalita) . ", " .
        sql_value($connection, $produzione) . ", " .
        sql_value($connection, $distribuzione) . ", " .
        sql_value($connection, $durata) . ", " .
        sql_value($connection, $colore, true) . ", " .
        sql_value($connection, $trama) . ", " .
        sql_value($connection, $valutazione, true) . ", " .
        sql_value($connection, $id_genere, true) . ")";

    if (mysqli_query($connection, $query)) {
        $messaggio = "Film inserito correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'inserimento del film.";
        $tipo = "errore";
    }
}

if ($azione === "update") {
    $titolo = post_param("titolo");
    $anno = post_param("anno");
    $regista = post_param("regista");
    $nazionalita = post_param("nazionalita");
    $produzione = post_param("produzione");
    $distribuzione = post_param("distribuzione");
    $durata = post_param("durata");
    $colore = post_param("colore");
    $trama = post_param("trama");
    $valutazione = post_param("valutazione");
    $id_genere = post_param("id_genere");

    $query = "UPDATE film SET " .
        "titolo = " . sql_value($connection, $titolo) . ", " .
        "anno = " . sql_value($connection, $anno, true) . ", " .
        "regista = " . sql_value($connection, $regista) . ", " .
        "nazionalita = " . sql_value($connection, $nazionalita) . ", " .
        "produzione = " . sql_value($connection, $produzione) . ", " .
        "distribuzione = " . sql_value($connection, $distribuzione) . ", " .
        "durata = " . sql_value($connection, $durata) . ", " .
        "colore = " . sql_value($connection, $colore, true) . ", " .
        "trama = " . sql_value($connection, $trama) . ", " .
        "valutazione = " . sql_value($connection, $valutazione, true) . ", " .
        "id_genere = " . sql_value($connection, $id_genere, true) . " " .
        "WHERE id_film = " . sql_value($connection, $id_film, true);

    if (mysqli_query($connection, $query)) {
        $messaggio = "Film aggiornato correttamente.";
        $tipo = "successo";
    } else {
        $messaggio = "Errore durante l'aggiornamento del film.";
        $tipo = "errore";
    }
}

if ($azione === "delete") {
    $id_film = get_param("id_film");
    $query = "DELETE FROM film WHERE id_film = " . sql_value($connection, $id_film, true);
    mysqli_query($connection, $query);
    redirect_to("film.php?msg=deleted");
}

if (get_param("msg") === "deleted") {
    $messaggio = "Film eliminato correttamente.";
    $tipo = "successo";
}

$edit = false;
$film_edit = null;
if ($azione === "edit") {
    $id_film = get_param("id_film");
    $result = mysqli_query($connection, "SELECT * FROM film WHERE id_film = " . sql_value($connection, $id_film, true));
    if ($result && mysqli_num_rows($result) === 1) {
        $film_edit = mysqli_fetch_assoc($result);
        $edit = true;
    } else {
        $messaggio = "Film non trovato.";
        $tipo = "errore";
    }
}

$generi = carica_generi($connection);
$result = mysqli_query($connection, "SELECT f.*, g.descrizione AS genere FROM film f LEFT JOIN genere g ON f.id_genere = g.id_genere ORDER BY f.id_film");
$film = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $film[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Film - DBCinema</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>Gestione Film</h1>
            <p>Inserisci, modifica o elimina i film.</p>
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
            <h2>Film</h2>
            <?php if ($messaggio !== "") { ?>
                <p class="messaggio <?php echo h($tipo); ?>"><?php echo h($messaggio); ?></p>
            <?php } ?>

            <h3><?php echo $edit ? "Modifica film" : "Nuovo film"; ?></h3>
            <form method="post" action="film.php">
                <input type="hidden" name="azione" value="<?php echo $edit ? "update" : "create"; ?>">
                <div class="form-grid">
                    <?php if ($edit) { ?>
                        <label for="id_film">ID Film</label>
                        <input type="number" id="id_film" name="id_film" value="<?php echo h($film_edit["id_film"] ?? ""); ?>" readonly>
                    <?php } ?>

                    <label for="titolo">Titolo</label>
                    <input type="text" id="titolo" name="titolo" value="<?php echo h($film_edit["titolo"] ?? ""); ?>" required>

                    <label for="anno">Anno</label>
                    <input type="number" id="anno" name="anno" value="<?php echo h($film_edit["anno"] ?? ""); ?>" required>

                    <label for="regista">Regista</label>
                    <input type="text" id="regista" name="regista" value="<?php echo h($film_edit["regista"] ?? ""); ?>" required>

                    <label for="nazionalita">Nazionalita</label>
                    <input type="text" id="nazionalita" name="nazionalita" value="<?php echo h($film_edit["nazionalita"] ?? ""); ?>">

                    <label for="produzione">Produzione</label>
                    <input type="text" id="produzione" name="produzione" value="<?php echo h($film_edit["produzione"] ?? ""); ?>">

                    <label for="distribuzione">Distribuzione</label>
                    <input type="text" id="distribuzione" name="distribuzione" value="<?php echo h($film_edit["distribuzione"] ?? ""); ?>">

                    <label for="durata">Durata</label>
                    <input type="time" id="durata" name="durata" value="<?php echo h($film_edit["durata"] ?? ""); ?>" required>

                    <label for="colore">Colore</label>
                    <select id="colore" name="colore">
                        <option value="">--</option>
                        <option value="1" <?php echo (isset($film_edit["colore"]) && (string) $film_edit["colore"] === "1") ? "selected" : ""; ?>>Si</option>
                        <option value="0" <?php echo (isset($film_edit["colore"]) && (string) $film_edit["colore"] === "0") ? "selected" : ""; ?>>No</option>
                    </select>

                    <label for="trama">Trama</label>
                    <textarea id="trama" name="trama"><?php echo h($film_edit["trama"] ?? ""); ?></textarea>

                    <label for="valutazione">Valutazione</label>
                    <input type="number" id="valutazione" name="valutazione" value="<?php echo h($film_edit["valutazione"] ?? ""); ?>">

                    <label for="id_genere">Genere</label>
                    <select id="id_genere" name="id_genere">
                        <option value="">--</option>
                        <?php foreach ($generi as $genere) { ?>
                            <option value="<?php echo h($genere["id_genere"]); ?>" <?php echo (isset($film_edit["id_genere"]) && (string) $film_edit["id_genere"] === (string) $genere["id_genere"]) ? "selected" : ""; ?>>
                                <?php echo h($genere["descrizione"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="azioni-form">
                    <button class="btn-primario" type="submit"><?php echo $edit ? "Aggiorna" : "Inserisci"; ?></button>
                    <?php if ($edit) { ?>
                        <a class="btn-secondario" href="film.php">Annulla</a>
                    <?php } ?>
                </div>
            </form>

            <h3>Elenco film</h3>
            <p class="conteggio">Totale: <?php echo count($film); ?></p>
            <div class="tabella-wrap">
                <table class="tabella">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titolo</th>
                            <th>Anno</th>
                            <th>Regista</th>
                            <th>Genere</th>
                            <th>Durata</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($film as $row) { ?>
                            <tr>
                                <td><?php echo h($row["id_film"]); ?></td>
                                <td><?php echo h($row["titolo"]); ?></td>
                                <td><?php echo h($row["anno"]); ?></td>
                                <td><?php echo h($row["regista"]); ?></td>
                                <td><?php echo h($row["genere"]); ?></td>
                                <td><?php echo h($row["durata"]); ?></td>
                                <td>
                                    <a class="btn-secondario" href="film.php?azione=edit&id_film=<?php echo h($row["id_film"]); ?>">Modifica</a>
                                    <a class="btn-secondario" href="film.php?azione=delete&id_film=<?php echo h($row["id_film"]); ?>" onclick="return confirm('Eliminare il film?');">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (count($film) === 0) { ?>
                            <tr><td colspan="7">Nessun film presente.</td></tr>
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


