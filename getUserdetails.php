<?php 

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
    }

    echo json_encode($person);
}

?>
