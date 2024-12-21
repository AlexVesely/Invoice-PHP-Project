<?php
// This file should normally be put in a folder with certain security access right
// Remember to change the username and password below if necessary
$conn = mysqli_connect("localhost", "root", "");
if(!$conn) {
    die ("Error connecting to MySQL: " . mysqli_error($conn));
}

// Change "invoiceDatabase" to the name of your created database
$db_select_success =  mysqli_select_db($conn, "invoiceDatabase");

if(!$db_select_success) {
    die ("Error selecting database: ".mysqli_error($conn));
}
?>
