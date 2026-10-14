<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET["aksi"], $_GET["id"])) {
    $id   = mysqli_real_escape_string($koneksi, $_GET["id"]);
    $aksi = $_GET["aksi"];

    $qTarik = mysqli_query($koneksi, "SELECT * FROM tarik_saldo WHERE id_tarik = '$id'");
    $tarik  = mysqli_fetch_assoc($qTarik);

    if ($tarik && $tarik["status"] === "pending") {
        if ($aksi === "setuju") {
            mysqli_query($koneksi, "UPDATE users SET saldo = saldo - '{$tarik['nominal']}' WHERE id_user = '{$tarik['id_user']}'");
            mysqli_query($koneksi, "UPDATE tarik_saldo SET status = 'disetujui' WHERE id_tarik = '$id'");
        } elseif ($aksi === "tolak") {
            mysqli_query($koneksi, "UPDATE tarik_saldo SET status = 'ditolak' WHERE id_tarik = '$id'");
        }
    }
}

header("Location: index.php");
exit();
