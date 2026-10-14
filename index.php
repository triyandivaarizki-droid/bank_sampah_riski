<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION["id_user"];

// Data User
$qUser = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($qUser);

// Data Statistik
$qSampahCount = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM sampah");
$sampahCount = mysqli_fetch_assoc($qSampahCount)['total'] ?? 0;

$qStokTotal = mysqli_query($koneksi, "SELECT SUM(stok_kg) as total FROM sampah");
$stokTotal = mysqli_fetch_assoc($qStokTotal)['total'] ?? 0;

$qTxCount = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi");
$txCount = mysqli_fetch_assoc($qTxCount)['total'] ?? 0;

// Data 5 Transaksi Terakhir
$qRecent = mysqli_query($koneksi, "
    SELECT t.*, u.nama, s.Jenis_sampah 
    FROM transaksi t 
    JOIN users u ON t.id_user = u.id_user 
    JOIN sampah s ON t.id_sampah = s.Id_sampah 
    ORDER BY t.id_transaksi DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard - Bank Sampah riski</title>

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

        /* ============= TOP NAVBAR (buttons moved here) ============= */
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
        .main{ max-width:1180px; margin:0 auto; padding:30px 34px 0; }

        /* WELCOME BANNER — quick-action buttons now live here */
        .banner{
            background:var(--card);
            border:1px solid var(--line);
            border-radius:8px;
            padding:22px 26px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            flex-wrap:wrap;
            margin-bottom:22px;
        }
        .banner .who{ display:flex; align-items:center; gap:14px; }
        .banner .seal{
            width:52px; height:52px;
            border:2px solid var(--ink);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-family:'Bitter', serif;
            font-weight:700;
            font-size:18px;
            flex-shrink:0;
        }
        .banner h1{ font-size:19px; }
        .banner p{ color:var(--ink-soft); font-size:12.5px; margin-top:3px; }
        .banner .role{
            display:inline-block;
            font-size:10.5px;
            font-weight:600;
            padding:3px 9px;
            border:1px solid var(--ink);
            border-radius:3px;
            margin-top:5px;
        }

        .banner .actions{ display:flex; gap:10px; flex-wrap:wrap; }
        .btn-action{
            display:flex;
            align-items:center;
            gap:8px;
            text-decoration:none;
            color:var(--ink);
            background:#fff;
            border:1px solid var(--line);
            padding:10px 16px;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            transition:border-color .15s ease, transform .15s ease, background .15s ease;
        }
        .btn-action .ic{ font-size:15px; }
        .btn-action:hover{ transform:translateY(-2px); border-color:var(--ink); }
        .btn-action.a1:hover{ background:var(--organik-bg); }
        .btn-action.a2:hover{ background:var(--kertas-bg); }
        .btn-action.a3:hover{ background:var(--tarik-bg); }

        /* STATS — horizontal strip instead of grid */
        .section-label{
            font-size:12px;
            font-weight:600;
            color:var(--ink-soft);
            margin:0 0 12px;
        }
        .stats-strip{
            display:flex;
            gap:14px;
            overflow-x:auto;
            padding-bottom:4px;
            margin-bottom:28px;
        }
        .stat-pill{
            flex:1 1 220px;
            min-width:200px;
            background:var(--card);
            border:1px solid var(--line);
            border-left:4px solid var(--tone,var(--ink));
            border-radius:6px;
            padding:14px 16px;
        }
        .stat-pill.c1{ --tone:var(--plastik); }
        .stat-pill.c2{ --tone:var(--organik); }
        .stat-pill.c3{ --tone:var(--kertas); }
        .stat-pill.c4{ --tone:var(--tarik); }
        .stat-pill .lbl{ font-size:11.5px; color:var(--ink-soft); font-weight:500; }
        .stat-pill .val{ font-size:20px; font-weight:600; margin-top:5px; }
        .stat-pill.c1 .val{ color:var(--plastik); }
        .stat-pill.c2 .val{ color:var(--organik); }
        .stat-pill.c3 .val{ color:var(--kertas); }
        .stat-pill.c4 .val{ color:var(--tarik); }

        /* ============= LEDGER TABLE ============= */
        .table-head-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:12px;
        }
        .ledger{
            background:var(--card);
            border:1px solid var(--line);
            border-radius:8px;
            overflow:hidden;
        }
        table{ width:100%; border-collapse:collapse; }
        th, td{ padding:11px 16px; text-align:left; font-size:13.5px; }
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

        .tag{ display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:100px; border:1px solid transparent; }
        .tag-setor{ background:var(--organik-bg); color:var(--organik); border-color:var(--organik); }
        .tag-tarik{ background:var(--tarik-bg); color:var(--tarik); border-color:var(--tarik); }
        .empty-row{ text-align:center; color:var(--ink-soft); padding:30px !important; font-style:italic; }

        @media (max-width: 760px){
            .navbar{ flex-direction:column; align-items:stretch; gap:10px; }
            .nav-pills{ justify-content:center; }
            .banner{ flex-direction:column; align-items:flex-start; }
            .banner .actions{ width:100%; }
            .btn-action{ flex:1; justify-content:center; }
            .main{ padding:22px 18px 0; }
            .ledger{ overflow-x:auto; }
            table{ min-width:640px; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand"><span class="mark"></span> Bank Sampah riski</div>
        <div class="nav-pills">
            <a href="index.php" class="active">Dashboard</a>
            <a href="sampah/index.php">Sampah</a>
            <a href="transaksi/index.php">Transaksi</a>
            <a href="tarik_saldo/index.php">Tarik Saldo</a>
        </div>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>

    <div class="main">

        <div class="banner">
            <div class="who">
                <div class="seal"><?= htmlspecialchars(strtoupper(substr($user['nama'] ?? 'P', 0, 1))) ?></div>
                <div>
                    <h1>Selamat datang, <?= htmlspecialchars($user['nama'] ?? 'Panji') ?></h1>
                    <p>@<?= htmlspecialchars($user['username'] ?? 'panji') ?></p>
                    <span class="role"><?= strtoupper($user['role'] ?? 'ADMIN') ?></span>
                </div>
            </div>
            <div class="actions">
                <a href="sampah/index.php" class="btn-action a1"><span class="ic">&#9851;</span> Sampah</a>
                <a href="transaksi/index.php" class="btn-action a2"><span class="ic">&#8644;</span> Transaksi</a>
                <a href="tarik_saldo/index.php" class="btn-action a3"><span class="ic">&#9878;</span> Tarik Saldo</a>
            </div>
        </div>

        <div class="section-label">ringkasan sistem</div>
        <div class="stats-strip">
            <div class="stat-pill c1">
                <div class="lbl">Saldo Anda</div>
                <div class="val num">Rp <?= number_format($user['saldo'] ?? 0, 0, ',', '.') ?></div>
            </div>
            <div class="stat-pill c2">
                <div class="lbl">Jenis Sampah</div>
                <div class="val num"><?= $sampahCount ?></div>
            </div>
            <div class="stat-pill c3">
                <div class="lbl">Total Stok Sampah</div>
                <div class="val num"><?= number_format($stokTotal, 1, ',', '.') ?> Kg</div>
            </div>
            <div class="stat-pill c4">
                <div class="lbl">Total Transaksi</div>
                <div class="val num"><?= $txCount ?></div>
            </div>
        </div>

        <div class="table-head-row">
            <div class="section-label" style="margin:0;">transaksi terbaru</div>
        </div>
        <div class="ledger">
            <table>
                <thead>
                    <tr>
                        <th class="num">ID</th>
                        <th>Nasabah</th>
                        <th>Jenis Sampah</th>
                        <th>Tipe</th>
                        <th class="num">Berat</th>
                        <th class="num">Total</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($qRecent) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($qRecent)): ?>
                        <tr>
                            <td class="num">#<?= $row["id_transaksi"] ?></td>
                            <td><strong><?= htmlspecialchars($row["nama"]) ?></strong></td>
                            <td><?= htmlspecialchars($row["Jenis_sampah"]) ?></td>
                            <td>
                                <span class="tag tag-<?= strtolower($row["tipe"]) ?>">
                                    <?= ucfirst($row["tipe"]) ?>
                                </span>
                            </td>
                            <td class="num"><?= $row["berat_kg"] ?> Kg</td>
                            <td class="num"><strong>Rp <?= number_format($row["total_rp"], 0, ",", ".") ?></strong></td>
                            <td><?= $row["tanggal"] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-row">Belum ada transaksi tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
