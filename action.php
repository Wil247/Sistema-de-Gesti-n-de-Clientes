<?php
$file = "clients.txt";
date_default_timezone_set("America/Santo_Domingo");

if(!file_exists($file)){
    file_put_contents($file,"");
}

/* AGREGAR CLIENTE */
if(isset($_POST['add'])){
    $name = trim($_POST['client']);

    $price      = floatval($_POST['price'] ?? 0);
    $commission = floatval($_POST['commission'] ?? 0);
    $shipping   = floatval($_POST['shipping'] ?? 0);

    $total = $price + $commission + $shipping;
    $date  = date("Y-m-d H:i:s");

    file_put_contents($file, "$name|$total|$date".PHP_EOL, FILE_APPEND);
}

/* ABONAR */
if(isset($_POST['pay'])){
    $name = trim($_POST['client']);
    $pay  = abs(floatval($_POST['payment'] ?? 0)) * -1;
    $date = date("Y-m-d H:i:s");

    file_put_contents($file, "$name|$pay|$date".PHP_EOL, FILE_APPEND);
}

/* ELIMINAR CLIENTE COMPLETO */
if(isset($_POST['delete'])){
    $name = trim($_POST['client']);
    $rows = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new  = [];

    foreach($rows as $r){
        if(strpos($r, $name."|") !== 0){
            $new[] = $r;
        }
    }
    file_put_contents($file, implode(PHP_EOL, $new));
}

header("Location: index.php");
exit;
