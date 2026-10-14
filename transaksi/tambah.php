<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_user   = mysqli_real_escape_string($koneksi, $_POST["id_user"]);
    $id_sampah = mysqli_real_escape_string($koneksi, $_POST["id_sampah"]);
    $tipe      = mysqli_real_escape_string($koneksi, $_POST["tipe"]);
    $berat_kg  = (float) $_POST["berat_kg"];

    // Ambil harga per kg dari tabel sampah
    $qSampah   = mysqli_query($koneksi, "SELECT Harga_per_kg FROM sampah WHERE Id_sampah = '$id_sampah'");
    $rowSampah = mysqli_fetch_assoc($qSampah);
    $harga_per_kg = $rowSampah ? $rowSampah["Harga_per_kg"] : 0;

    $total_rp = $berat_kg * $harga_per_kg;

    $query = "INSERT INTO transaksi (id_user, id_sampah, tipe, berat_kg, total_rp)
              VALUES ('$id_user', '$id_sampah', '$tipe', '$berat_kg', '$total_rp')";
    mysqli_query($koneksi, $query);

    if ($tipe === "setor") {
        mysqli_query($koneksi, "UPDATE sampah SET stok_kg = stok_kg + '$berat_kg' WHERE Id_sampah = '$id_sampah'");
        mysqli_query($koneksi, "UPDATE users SET saldo = saldo + '$total_rp' WHERE id_user = '$id_user'");
    } else {
        mysqli_query($koneksi, "UPDATE sampah SET stok_kg = stok_kg - '$berat_kg' WHERE Id_sampah = '$id_sampah'");
    }

    header("Location: index.php");
    exit();
}

$users   = mysqli_query($koneksi, "SELECT id_user, nama FROM users");
$sampahs = mysqli_query($koneksi, "SELECT Id_sampah, Jenis_sampah FROM sampah");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Bank Sampah Panji</title>

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
            background-color: #050e0a;
            color: #f1f5f9;
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
            background: radial-gradient(circle, rgba(52, 211, 153, 0.2) 0%, rgba(16, 185, 129, 0.03) 60%, transparent 80%);
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
            border-bottom: 1px solid rgba(52, 211, 153, 0.2);
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
            color: #34d399;
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
            color: #34d399;
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(52, 211, 153, 0.25);
        }

        .navbar .menu a.active {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(16, 185, 129, 0.15));
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.4);
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
            max-width: 580px;
            margin: 40px auto 0;
            padding: 0 25px;
            position: relative;
            z-index: 10;
        }

        .form-card {
            background: rgba(15, 30, 24, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(52, 211, 153, 0.2);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        h2 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 22px;
            background: #34d399;
            border-radius: 4px;
            box-shadow: 0 0 10px #34d399;
        }

        /* ================= FORM STYLES ================= */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-control {
            width: 100%;
            background: rgba(5, 14, 10, 0.6);
            border: 1px solid rgba(52, 211, 153, 0.25);
            border-radius: 12px;
            padding: 12px 16px;
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: all 0.25s ease;
        }

        .input-control option {
            background-color: #0f1e18;
            color: #ffffff;
        }

        .input-control:focus {
            border-color: #34d399;
            box-shadow: 0 0 15px rgba(52, 211, 153, 0.25);
            background: rgba(5, 14, 10, 0.8);
        }

        .input-control::placeholder {
            color: #64748b;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.45);
            filter: brightness(1.1);
        }

        .btn-kembali {
            display: inline-block;
            margin-top: 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .btn-kembali:hover {
            color: #34d399;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 600px) {
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

            .form-card {
                padding: 24px;
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
            <span>♻️</span> Bank Sampah Panji
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
        <div class="form-card">
            <h2>Tambah Transaksi</h2>

            <form method="POST" action="tambah.php">
                <div class="form-group">
                    <label>Nasabah</label>
                    <select name="id_user" class="input-control" required>
                        <option value="">-- Pilih Nasabah --</option>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <option value="<?= $u["id_user"] ?>"><?= htmlspecialchars($u["nama"]) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Sampah</label>
                    <select name="id_sampah" class="input-control" required>
                        <option value="">-- Pilih Sampah --</option>
                        <?php while ($s = mysqli_fetch_assoc($sampahs)): ?>
                            <option value="<?= $s["Id_sampah"] ?>"><?= htmlspecialchars($s["Jenis_sampah"]) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipe Transaksi</label>
                    <select name="tipe" class="input-control" required>
                        <option value="setor">Setor</option>
                        <option value="tarik">Tarik</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Berat (Kg)</label>
                    <input type="number" step="0.01" name="berat_kg" class="input-control" placeholder="Contoh: 2.5" required>
                </div>

                <button type="submit" class="btn-submit">Simpan Transaksi</button>
            </form>

            <a class="btn-kembali" href="index.php">&larr; Kembali ke daftar transaksi</a>
        </div>
    </div>

</body>
</html>