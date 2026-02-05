# DBCinema

CRUD completo per la gestione del database **cinema** in PHP + MySQL.

Demo: `cinemaCRUD.wuaze.com`

Autore: Dennys Teora

## Funzionalità
- Gestione Film
- Gestione Generi
- Gestione Attori
- Gestione Musicisti
- Gestione Premi
- Gestione Colonne Sonore
- Gestione Recita In
- Gestione Ha Vinto

## Requisiti
- PHP (con estensione `mysqli`)
- MySQL / MariaDB
- Server web (es. Apache)

## Installazione
1. Clona il repository.
2. Importa il database:
- `cinema.sql` (consigliato)
- oppure `cinema_compat_5_2_min.sql` per compatibilità minima.
3. Configura le credenziali DB creando `inc/db.local.php`:

```php
<?php
return [
    "host" => "localhost",
    "user" => "tuo_user",
    "pass" => "tua_password",
    "name" => "cinema",
];
```

In alternativa puoi usare variabili d’ambiente:
- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`

4. Avvia il server web e visita `index.php`.

## Struttura
- `index.php` dashboard principale
- `inc/db.php` connessione DB (senza credenziali)
- `inc/db.local.php` configurazione locale (ignorata da git)
- `*.php` CRUD delle tabelle

## Sicurezza
Non committare mai le credenziali reali. Usa `inc/db.local.php` o variabili d’ambiente.

## Licenza
Progetto didattico / uso libero.
