<?php
try {
    $dbh = new PDO("mysql:host=127.0.0.1", "root", "");
    $dbs = $dbh->query('SHOW DATABASES');
    while (($db = $dbs->fetchColumn(0)) !== false) {
        echo $db . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
