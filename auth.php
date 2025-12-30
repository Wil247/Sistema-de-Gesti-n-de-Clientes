<?php
$usersFile = "users.txt";

if (!file_exists($usersFile)) {
    file_put_contents($usersFile, "admin|1234");
}

$user = $_POST['u'] ?? '';
$pass = $_POST['p'] ?? '';

$lines = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    [$u, $p] = explode("|", $line);
    if ($u === $user && $p === $pass) {
        echo "OK";
        exit;
    }
}

echo "FAIL";
