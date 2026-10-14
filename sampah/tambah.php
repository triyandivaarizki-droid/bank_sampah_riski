<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jenis = mysqli_real_escape_string($koneksi, $_POST["jenis_sampah"]);
    $harga = mysqli_real_escape_string($koneksi, $_POST["harga_per_kg"]);
    $stok  = mysqli_real_escape_string($koneksi, $_POST["stok_kg"]);

    $query = "INSERT INTO sampah (Jenis_sampah, Harga_per_kg, stok_kg) VALUES ('$jenis', '$harga', '$stok')";
    mysqli_query($koneksi, $query);

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Tambah Sampah - Bank Sampah riski</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">

    <style>
        :root{
            --paper:      #E9EEF5;
            --card:       #F8FAFD;
            --ink:        #16263D;
            --ink-soft:   #5C6E82;
            --line:       #CBD8E6;
            --organik:    #2563A8;
            --plastik:    #1D7A8C;
            --plastik-bg: #D8EEF1;
            --kertas:     #3B4C9E;
            --tarik:      #2E4C74;
        }
        *{ box-sizing:border-box; margin:0; padding:0; }

        body{
            font-family:'IBM Plex Sans', sans-serif;
            background:var(--paper);
            color:var(--ink);
            min-height:100vh;
            padding-bottom:50px;
        }
        h1,h2{ font-family:'Bitter', serif; font-weight:700; }

        /* ============= TOP NAVBAR ============= */
        .navbar{
            position:sticky;
            top:0;
            z-index:100;
            background:var(--ink);
            padding:14px 34px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:18px;
            flex-wrap:wrap;
        }
        .navbar .brand{
            font-family:'Bitter', serif;
            font-weight:700;
            font-size:16.5px;
            color:#E7ECF5;
            display:flex;
            align-items:center;
            gap:8px;
            flex-shrink:0;
        }
        .navbar .brand .mark{ width:9px; height:9px; border-radius:2px; background:var(--plastik-bg); transform:rotate(45deg); }

        .nav-pills{
            display:flex;
            gap:6px;
            background:rgba(255,255,255,0.06);
            padding:5px;
            border-radius:100px;
            flex-wrap:wrap;
        }
        .nav-pills a{
            color:#B7C4D6;
            text-decoration:none;
            font-size:13px;
            font-weight:600;
            padding:8px 16px;
            border-radius:100px;
            transition:background .15s ease, color .15s ease;
        }
        .nav-pills a:hover{ color:#fff; background:rgba(255,255,255,0.08); }
        .nav-pills a.active{ background:var(--plastik-bg); color:var(--ink); }

        .btn-logout{
            background:transparent;
            border:1px solid rgba(255,255,255,0.25);
            color:#D3E1F2;
            text-decoration:none;
            font-size:13px;
            font-weight:600;
            padding:9px 18px;
            border-radius:100px;
            flex-shrink:0;
            transition:background .15s ease, border-color .15s ease;
        }
        .btn-logout:hover{ background:rgba(255,255,255,0.1); border-color:rgba(255,255,255,0.4); }

        /* ============= MAIN ============= */
        .main{ max-width:520px; margin:0 auto; padding:34px 24px 0; }

        .form-card{
            background:var(--card);
            border:1px solid var(--line);
            border-radius:10px;
            padding:28px 26px;
        }
        .form-card h1{ font-size:19px; margin-bottom:20px; }

        .form-group{ margin-bottom:16px; }
        .form-group label{ display:block; margin-bottom:7px; color:var(--ink-soft); font-size:12.5px; font-weight:600; }
        .input-control{
            width:100%;
            padding:12px 14px;
            border-radius:6px;
            border:1px solid var(--line);
            outline:none;
            background:#fff;
            color:var(--ink);
            font-size:14px;
            font-family:inherit;
            transition:border-color .15s ease;
        }
        .input-control:focus{ border-color:var(--ink); }
        .input-control::placeholder{ color:#9AA9BC; }

        .form-actions{
            display:flex;
            align-items:center;
            gap:12px;
            margin-top:22px;
        }

        .btn-submit{
            padding:12px 24px;
            background:var(--ink);
            border:none;
            border-radius:6px;
            color:#E7ECF5;
            font-family:inherit;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:background .15s ease;
        }
        .btn-submit:hover{ background:#233A5C; }

        .btn-back{
            color:var(--ink-soft);
            text-decoration:none;
            font-size:13px;
            font-weight:600;
        }
        .btn-back:hover{ color:var(--ink); }

        @media (max-width: 600px){
            .navbar{ flex-direction:column; align-items:stretch; gap:10px; }
            .nav-pills{ justify-content:center; }
            .main{ padding:24px 16px 0; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand"><span class="mark"></span> Bank Sampah riski</div>
        <div class="nav-pills">
            <a href="../index.php">Dashboard</a>
            <a href="../sampah/index.php" class="active">Sampah</a>
            <a href="../transaksi/index.php">Transaksi</a>
            <a href="../tarik_saldo/index.php">Tarik Saldo</a>
        </div>
        <a href="../logout.php" class="btn-logout">Keluar</a>
    </div>

    <div class="main">
        <div class="form-card">
            <h1>Tambah Data Sampah</h1>

            <form method="POST" action="tambah.php">
                <div class="form-group">
                    <label>Jenis Sampah</label>
                    <input type="text" name="jenis_sampah" class="input-control" placeholder="Contoh: Plastik Pet, Kardus, Botol Kaca" required>
                </div>

                <div class="form-group">
                    <label>Harga per Kg (Rp)</label>
                    <input type="number" step="0.01" name="harga_per_kg" class="input-control" placeholder="Contoh: 3000" required>
                </div>

                <div class="form-group">
                    <label>Stok Awal (Kg)</label>
                    <input type="number" step="0.01" name="stok_kg" class="input-control" placeholder="Stok Awal" value="0" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Sampah</button>
                    <a class="btn-back" href="index.php">&larr; Kembali ke daftar sampah</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
