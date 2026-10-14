<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

$data = mysqli_query($koneksi, "SELECT * FROM sampah ORDER BY Id_sampah DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Data Sampah - Bank Sampah riski</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root{
            --paper:      #E9EEF5;
            --card:       #F8FAFD;
            --ink:        #16263D;
            --ink-soft:   #5C6E82;
            --line:       #CBD8E6;
            --organik:    #2563A8;
            --organik-bg: #DCE9F7;
            --plastik:    #1D7A8C;
            --plastik-bg: #D8EEF1;
            --kertas:     #3B4C9E;
            --kertas-bg:  #DFE2F5;
            --tarik:      #2E4C74;
            --tarik-bg:   #DDE6F0;
        }
        *{ box-sizing:border-box; margin:0; padding:0; }

        body{
            font-family:'IBM Plex Sans', sans-serif;
            background:var(--paper);
            color:var(--ink);
            min-height:100vh;
            padding-bottom:50px;
        }
        h1,h2,h3,h4{ font-family:'Bitter', serif; font-weight:700; }
        .num{ font-family:'IBM Plex Mono', monospace; }

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
        .main{ max-width:1100px; margin:0 auto; padding:30px 34px 0; }

        .header-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:20px;
        }
        .header-row h1{ font-size:21px; }

        .btn-add{
            display:inline-flex;
            align-items:center;
            gap:7px;
            background:var(--ink);
            color:#E7ECF5;
            text-decoration:none;
            padding:11px 20px;
            border-radius:8px;
            font-size:13.5px;
            font-weight:600;
            transition:background .15s ease, transform .15s ease;
        }
        .btn-add:hover{ background:#233A5C; transform:translateY(-2px); }

        /* ============= TABLE ============= */
        .ledger{
            background:var(--card);
            border:1px solid var(--line);
            border-radius:8px;
            overflow:hidden;
        }
        table{ width:100%; border-collapse:collapse; }
        th, td{ padding:13px 18px; text-align:left; font-size:13.5px; }
        thead th{
            font-size:11px;
            font-weight:600;
            color:var(--ink-soft);
            border-bottom:2px solid var(--ink);
            padding-bottom:9px;
        }
        tbody tr:nth-child(even){ background:#EEF2F8; }
        tbody tr:hover{ background:var(--plastik-bg); }
        td{ border-bottom:1px solid var(--line); }
        tr:last-child td{ border-bottom:none; }
        td.num, th.num{ font-family:'IBM Plex Mono', monospace; }

        .btn-hapus{
            display:inline-block;
            padding:7px 15px;
            background:var(--tarik-bg);
            border:1px solid var(--tarik);
            color:var(--tarik);
            text-decoration:none;
            border-radius:100px;
            font-size:12.5px;
            font-weight:600;
            transition:background .15s ease, color .15s ease;
        }
        .btn-hapus:hover{ background:var(--tarik); color:#fff; }

        .empty-row{ text-align:center; color:var(--ink-soft); padding:30px !important; font-style:italic; }

        @media (max-width: 760px){
            .navbar{ flex-direction:column; align-items:stretch; gap:10px; }
            .nav-pills{ justify-content:center; }
            .header-row{ justify-content:flex-start; }
            .main{ padding:22px 18px 0; }
            .ledger{ overflow-x:auto; }
            table{ min-width:560px; }
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

        <div class="header-row">
            <h1>Data Sampah</h1>
            <a class="btn-add" href="tambah.php">+ Tambah Sampah</a>
        </div>

        <div class="ledger">
            <table>
                <thead>
                    <tr>
                        <th class="num">ID</th>
                        <th>Jenis Sampah</th>
                        <th class="num">Harga/Kg</th>
                        <th class="num">Stok (Kg)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="num">#<?= $row["Id_sampah"] ?></td>
                            <td><strong><?= htmlspecialchars($row["Jenis_sampah"]) ?></strong></td>
                            <td class="num">Rp <?= number_format($row["Harga_per_kg"], 0, ",", ".") ?></td>
                            <td class="num"><?= number_format($row["stok_kg"], 1) ?> kg</td>
                            <td>
                                <a class="btn-hapus" href="hapus.php?id=<?= $row["Id_sampah"] ?>"
                                   onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-row">Belum ada data sampah.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
