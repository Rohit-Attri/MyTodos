<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'todos';

// connection code and by mysqli procedural method
try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
} catch (Exception $e) {
    echo "There is an error occured to setup a connection" . $e->getMessage();
}

// // connection by PDO method
// $dbname = "" ;
?>