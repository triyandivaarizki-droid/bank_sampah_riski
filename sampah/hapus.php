<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET["id"])) {
    $id = mysqli_real_escape_string($koneksi, $_GET["id"]);
    mysqli_query($koneksi, "DELETE FROM sampah WHERE Id_sampah = '$id'");
}

header("Location: index.php");
exit();
