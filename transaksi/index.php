<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

$data = mysqli_query($koneksi, "
    SELECT t.*, u.nama, s.Jenis_sampah
    FROM transaksi t
    JOIN users u ON t.id_user = u.id_user
    JOIN sampah s ON t.id_sampah = s.Id_sampah
    ORDER BY t.id_transaksi DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi - Bank Sampah Panji</title>

    <!-- Font Modern Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background-color: #0e050d;
            color: #f9f1f5;
            position: relative;
            overflow-x: hidden;
            padding-bottom: 50px;
        }

        /* 🌟 GLOWING BACKGROUND ORBS */
        .bg-glow-1 {
            position: fixed;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(5, 150, 105, 0.05) 60%, transparent 80%);
            top: -100px;
            left: -100px;
            filter: blur(80px);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        .bg-glow-2 {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(211, 52, 171, 0.2) 0%, rgba(16, 185, 129, 0.03) 60%, transparent 80%);
            bottom: -150px;
            right: -100px;
            filter: blur(90px);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(15, 30, 24, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(211, 52, 198, 0.2);
            padding: 16px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .navbar .brand {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.3px;
        }

        .navbar .brand span {
            color: #d334b8;
        }

        .navbar .menu {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar .menu a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
            border: 1px solid transparent;
        }

        .navbar .menu a:hover {
            color: #d3349e;
            background: rgba(185, 16, 185, 0.12);
            border-color: rgba(211, 52, 171, 0.25);
        }

        .navbar .menu a.active {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(16, 185, 129, 0.15));
            color: #d334c6;
            border-color: rgba(211, 52, 139, 0.4);
            box-shadow: 0 0 15px rgba(52, 211, 153, 0.15);
        }

        .navbar .menu a.logout-btn {
            color: #fca5a5;
        }

        .navbar .menu a.logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(248, 113, 113, 0.3);
            color: #f87171;
        }

        /* ================= CONTAINER ================= */
        .container {
            max-width: 1100px;
            margin: 35px auto 0;
            padding: 0 25px;
            position: relative;
            z-index: 10;
        }

        .header-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 22px;
            background: #c634d3;
            border-radius: 4px;
            box-shadow: 0 0 10px #d334be;
        }

        .btn-tambah {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #b910a2 0%, #96058f 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
        }

        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.45);
            filter: brightness(1.1);
        }

        /* ================= TABLE CARD ================= */
        .table-card {
            background: rgba(15, 30, 24, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(52, 211, 153, 0.2);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 16px 20px;
        }

        th {
            background: rgba(16, 185, 129, 0.1);
            color: #d334ab;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid rgba(211, 52, 211, 0.2);
        }

        td {
            color: #e2e8f0;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(52, 211, 153, 0.05);
        }

        /* TIPE BADGES */
        .badge-tipe {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .tipe-setor {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(211, 52, 163, 0.35);
            color: #d334d3;
        }

        .tipe-tarik {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fbbf24;
        }

        /* BUTTON ACTIONS */
        .btn-hapus {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #f87171;
            text-decoration: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-hapus:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(248, 113, 113, 0.5);
            color: #ffffff;
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.3);
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 14px;
                padding: 16px;
                text-align: center;
            }

            .navbar .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .container {
                padding: 0 16px;
            }

            .table-card {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <!-- Background Orbs -->
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="navbar">
        <div class="brand">
            <span>♻️</span> Bank Sampah riski
        </div>
        <div class="menu">
            <a href="../index.php">Dashboard</a>
            <a href="../sampah/index.php">Sampah</a>
            <a href="../transaksi/index.php" class="active">Transaksi</a>
            <a href="../tarik_saldo/index.php">Tarik Saldo</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="header-action">
            <h2>Data Transaksi</h2>
            <a class="btn-tambah" href="tambah.php">+ Tambah Transaksi</a>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nasabah</th>
                        <th>Jenis Sampah</th>
                        <th>Tipe</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td>#<?= $row["id_transaksi"] ?></td>
                        <td><strong><?= htmlspecialchars($row["nama"]) ?></strong></td>
                        <td><?= htmlspecialchars($row["Jenis_sampah"]) ?></td>
                        <td>
                            <span class="badge-tipe tipe-<?= strtolower($row["tipe"]) ?>">
                                <?= ucfirst($row["tipe"]) ?>
                            </span>
                        </td>
                        <td><?= $row["berat_kg"] ?> Kg</td>
                        <td style="color: #34d399; font-weight: 700;">
                            Rp <?= number_format($row["total_rp"], 0, ",", ".") ?>
                        </td>
                        <td><?= $row["tanggal"] ?></td>
                        <td>
                            <a class="btn-hapus" href="hapus.php?id=<?= $row["id_transaksi"] ?>"
                               onclick="return confirm('Hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>