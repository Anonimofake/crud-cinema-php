<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>DBCinema - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/sito.css">
</head>
<body>
    <div id="pagina">
        <div id="intestazione">
            <h1>DBCinema</h1>
            <p>CRUD completo per il database cinema.</p>
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
            <h2>Benvenuto</h2>
            <p>Da qui puoi gestire tutte le tabelle del database <span class="mono">cinema</span>.</p>
            <div class="azioni">
                <a class="card" href="film.php">Gestisci Film</a>
                <a class="card" href="genere.php">Gestisci Generi</a>
                <a class="card" href="attori.php">Gestisci Attori</a>
                <a class="card" href="musicisti.php">Gestisci Musicisti</a>
                <a class="card" href="premi.php">Gestisci Premi</a>
                <a class="card" href="colonne_sonore.php">Gestisci Colonne Sonore</a>
                <a class="card" href="recita_in.php">Gestisci Recite</a>
                <a class="card" href="ha_vinto.php">Gestisci Premi Vinti</a>
            </div>
            <h3>Nota</h3>
            <p>Se il DB non si collega, controlla i parametri in <span class="mono">inc/db.php</span>.</p>
        </div>
        <div id="pie_pagina">
            &copy; 2026 DBCinema - Pannello di controllo
        </div>
    </div>
</body>
</html>
