<?php
$db_server = 'localhost';
$db_andmebaas = 'kmustkivi';
$db_kasutaja = 'kmustkivi';
$db_salasona = 'w85TgLln80cx3LwF';

$yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

if (!$yhendus) {
    die("Probleem andmebaasiga: " . mysqli_connect_error());
}
?>
