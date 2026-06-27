<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require "./connect.php";
$name = $_POST["username"];

$query = "SELECT * FROM `guests` WHERE `guest_name` = '$name'";

$res = mysqli_query($conn, $query);

if ($res) {
    $person = [];

    while ($row = mysqli_fetch_array($res)) {
        $person = [
            "name" => $row["guest_name"],
            "role" => $row["role"]
        ];
        $_SESSION['logged_in'] = true;
        $_SESSION['role'] = $row["role"];
        $_SESSION['username'] = $row["guest_name"];
    }

    echo json_encode($person);
}

?>
