<?php
function db_connect()
{
    $config = null;
    $local_path = __DIR__ . "/db.local.php";
    if (is_file($local_path)) {
        $config = require $local_path;
    }

    if (!is_array($config)) {
        $config = [
            "host" => getenv("DB_HOST") ?: "",
            "user" => getenv("DB_USER") ?: "",
            "pass" => getenv("DB_PASS") ?: "",
            "name" => getenv("DB_NAME") ?: "",
        ];
    }

    $missing = [];
    if ($config["host"] === "") { $missing[] = "DB_HOST"; }
    if ($config["user"] === "") { $missing[] = "DB_USER"; }
    if ($config["pass"] === "") { $missing[] = "DB_PASS"; }
    if ($config["name"] === "") { $missing[] = "DB_NAME"; }
    if (!empty($missing)) {
        die("Config DB mancante: imposta inc/db.local.php o variabili d'ambiente (" . implode(", ", $missing) . ").");
    }

    $connection = mysqli_connect($config["host"], $config["user"], $config["pass"], $config["name"]);
    if (!$connection) {
        die("Impossibile stabilire una connessione con il DB server");
    }
    mysqli_set_charset($connection, "utf8");
    return $connection;
}
?>
