<?php

try {
    $conn = new PDO("mysql:host=localhost;port=3306;dbname=space", "root", "");
    // установка режима вывода ошибок
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


}
catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

