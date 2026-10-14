<?php  
session_start();  
require "koneksi.php";  
 
$error = "";  
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {  
    $username = mysqli_real_escape_string($koneksi, $_POST["username"]);  
    $password = mysqli_real_escape_string($koneksi, $_POST["password"]);  
 
    $query  = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";  
    $result = mysqli_query($koneksi, $query);  
 
    if ($result && mysqli_num_rows($result) === 1) {  
        $user = mysqli_fetch_assoc($result);  
 
        $_SESSION["id_user"]  = $user["id_user"];  
        $_SESSION["nama"]     = $user["nama"];  
        $_SESSION["username"] = $user["username"];  
        $_SESSION["role"]     = $user["role"];  
 
        header("Location: index.php");  
        exit();  
    } else {  
        $error = "Username atau password salah!";  
    }  
}  

// Ambil daftar user dari database untuk selector
$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id_user ASC");
?>  

<!DOCTYPE html>  
<html lang="id">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"> 
    <title>Login - Bank Sampah riski</title>  

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
            --plastik:    #4E8FE0;
            --organik:    #2563A8;
            --kertas:     #1D7A8C;
            --tarik:      #3B4C9E;
        }
        *{ box-sizing:border-box; margin:0; padding:0; }

        body{
            font-family:'IBM Plex Sans', sans-serif;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:var(--paper);
            color:var(--ink);
            padding:24px 16px;
        }
        h1{ font-family:'Bitter', serif; }

        .card{
            width:100%;
            max-width:460px;
            background:var(--card);
            border:1px solid var(--line);
            border-radius:10px;
            padding:30px 30px 26px;
        }

        /* header row: brand left, step indicator right */
        .card-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:18px;
        }
        .brand-tag{
            display:flex;
            align-items:center;
            gap:7px;
            font-size:12.5px;
            font-weight:600;
            color:var(--ink-soft);
        }
        .brand-tag .sq{ width:9px; height:9px; border-radius:2px; background:var(--plastik); }
        .step-indicator{ display:flex; gap:5px; }
        .step-indicator span{ width:18px; height:4px; border-radius:2px; background:var(--line); }
        .step-indicator span.on{ background:var(--ink); }

        h1{ font-size:22px; margin-bottom:6px; }
        .sub{ font-size:13px; color:var(--ink-soft); line-height:1.5; margin-bottom:22px; }

        .error{
            padding:11px 14px;
            margin-bottom:18px;
            border-radius:6px;
            font-size:13px;
            font-weight:600;
            color:var(--tarik);
            background:#F2DFD8;
            border:1px solid var(--tarik);
        }

        .steps-wrapper{ position:relative; min-height:270px; }
        .step-view{ transition: opacity .25s ease, transform .3s cubic-bezier(0.16,1,0.3,1), visibility .25s; width:100%; }
        .step-view.active{ opacity:1; visibility:visible; transform:translateY(0); position:relative; pointer-events:auto; }
        .step-view.hidden-left{ opacity:0; visibility:hidden; transform:translateY(10px); position:absolute; top:0; left:0; pointer-events:none; }
        .step-view.hidden-right{ opacity:0; visibility:hidden; transform:translateY(-10px); position:absolute; top:0; left:0; pointer-events:none; }

        /* STEP 1 — user picker as button grid (top of card) */
        .btn-manual-top{
            display:block;
            width:100%;
            text-align:right;
            background:none;
            border:none;
            color:var(--ink-soft);
            font-size:12px;
            text-decoration:underline;
            cursor:pointer;
            margin-bottom:10px;
            font-family:inherit;
        }
        .btn-manual-top:hover{ color:var(--ink); }

        .user-grid{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:10px;
            max-height:280px;
            overflow-y:auto;
            padding:2px;
        }
        .user-grid::-webkit-scrollbar{ width:4px; }
        .user-grid::-webkit-scrollbar-thumb{ background:var(--line); border-radius:4px; }

        .user-tile{
            display:flex;
            flex-direction:column;
            align-items:center;
            text-align:center;
            gap:7px;
            padding:14px 8px;
            background:#fff;
            border:1px solid var(--line);
            border-radius:8px;
            cursor:pointer;
            transition:border-color .15s ease, background .15s ease, transform .15s ease;
        }
        .user-tile:hover{ border-color:var(--ink); transform:translateY(-2px); background:#EEF2F8; }

        .avatar{
            width:40px; height:40px;
            border-radius:50%;
            background:var(--ink);
            color:#E7ECF5;
            display:flex; align-items:center; justify-content:center;
            font-family:'Bitter', serif;
            font-weight:700;
            font-size:15px;
            flex-shrink:0;
        }
        .user-tile h4{ font-size:12.5px; font-weight:600; line-height:1.25; }
        .user-tile p{ font-size:10.5px; color:var(--ink-soft); }

        .role-badge{
            display:inline-block;
            padding:1px 7px;
            border-radius:100px;
            font-size:9.5px;
            font-weight:700;
            text-transform:capitalize;
            border:1px solid;
            margin-top:2px;
        }
        .role-admin    { color:var(--tarik);   border-color:var(--tarik);   background:#DDE1F2; }
        .role-nasabah  { color:var(--organik); border-color:var(--organik); background:#DCE9F7; }
        .role-siswa    { color:var(--kertas);  border-color:var(--kertas);  background:#D8EEF1; }
        .role-guru     { color:#5B4FAE;        border-color:#5B4FAE;        background:#E1DEF5; }
        .role-pengepul { color:var(--plastik); border-color:var(--plastik); background:#DCEAF9; }

        /* STEP 2 — password entry, submit button inline with field */
        .back-row{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:20px;
            padding-bottom:16px;
            border-bottom:1px dashed var(--line);
        }
        .btn-back{
            width:32px; height:32px;
            border-radius:50%;
            border:1px solid var(--line);
            background:#fff;
            cursor:pointer;
            font-size:14px;
            color:var(--ink);
            flex-shrink:0;
        }
        .btn-back:hover{ border-color:var(--ink); }
        .who-row{ display:flex; align-items:center; gap:10px; }
        .who-row h4{ font-size:14px; font-weight:600; }
        .who-row p{ font-size:11.5px; color:var(--ink-soft); margin-top:1px; }

        .form-group{ margin-bottom:16px; }
        .form-group label{ display:block; margin-bottom:7px; color:var(--ink-soft); font-size:12.5px; font-weight:600; }

        .field-row{
            display:flex;
            gap:8px;
            align-items:stretch;
        }
        .field-row input{
            flex:1;
            padding:12px 14px;
            border-radius:6px;
            border:1px solid var(--line);
            outline:none;
            background:#fff;
            color:var(--ink);
            font-size:14px;
            font-family:inherit;
        }
        .field-row input:focus{ border-color:var(--ink); }

        .login-btn{
            padding:0 20px;
            border:none;
            border-radius:6px;
            cursor:pointer;
            color:#E7ECF5;
            font-weight:700;
            font-size:13px;
            background:var(--ink);
            font-family:inherit;
            white-space:nowrap;
        }
        .login-btn:hover{ background:#233A5C; }

        .manual-field{ margin-top:14px; }

        .login-footer{
            text-align:center;
            margin-top:22px;
            padding-top:14px;
            border-top:1px dashed var(--line);
            color:var(--ink-soft);
            font-size:11.5px;
        }
        .login-footer span{ color:var(--organik); font-weight:600; }

        @media (max-width: 420px){
            .user-grid{ grid-template-columns:repeat(2, 1fr); }
        }
    </style>
</head>  

<body>  

    <div class="card">

        <div class="card-top">
            <div class="brand-tag"><span class="sq"></span> Bank Sampah riski</div>
            <div class="step-indicator" id="step-indicator">
                <span class="on" id="ind-1"></span><span id="ind-2"></span>
            </div>
        </div>

        <h1>Selamat datang kembali</h1>
        <p class="sub">Pilih akun Anda untuk masuk dan mulai mengelola tabungan lingkungan.</p>

        <?php if ($error): ?>  
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>  
        <?php endif; ?>  

        <div class="steps-wrapper">

            <!-- STEP 1: PILIH USER (button grid, manual-entry link moved to top-right) -->
            <div id="step-users" class="step-view active">
                <button type="button" class="btn-manual-top" onclick="enableManualInput()">Ketik username manual &rarr;</button>

                <div class="user-grid">
                    <?php 
                    if ($users && mysqli_num_rows($users) > 0):
                        while ($u = mysqli_fetch_assoc($users)): 
                            $initial = strtoupper(substr($u["nama"], 0, 1));
                    ?>
                        <div class="user-tile" onclick="selectUser('<?= htmlspecialchars($u['username']) ?>', '<?= htmlspecialchars($u['nama']) ?>', '<?= htmlspecialchars($u['role']) ?>')">
                            <div class="avatar"><?= $initial ?></div>
                            <h4><?= htmlspecialchars($u["nama"]) ?></h4>
                            <p>@<?= htmlspecialchars($u["username"]) ?></p>
                            <span class="role-badge role-<?= strtolower($u["role"]) ?>"><?= ucfirst($u["role"]) ?></span>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <!-- Fallback default jika DB belum ada data -->
                        <div class="user-tile" onclick="selectUser('riski', 'riski Admin', 'admin')">
                            <div class="avatar">P</div>
                            <h4>riski Admin</h4>
                            <p>@riski</p>
                            <span class="role-badge role-admin">Admin</span>
                        </div>
                        <div class="user-tile" onclick="selectUser('budi', 'Budi Nasabah', 'nasabah')">
                            <div class="avatar">B</div>
                            <h4>Budi Nasabah</h4>
                            <p>@budi</p>
                            <span class="role-badge role-nasabah">Nasabah</span>
                        </div>
                        <div class="user-tile" onclick="selectUser('siti', 'Siti Siswa', 'siswa')">
                            <div class="avatar">S</div>
                            <h4>Siti Siswa</h4>
                            <p>@siti</p>
                            <span class="role-badge role-siswa">Siswa</span>
                        </div>
                        <div class="user-tile" onclick="selectUser('bambang', 'Bambang Guru', 'guru')">
                            <div class="avatar">B</div>
                            <h4>Bambang Guru</h4>
                            <p>@bambang</p>
                            <span class="role-badge role-guru">Guru</span>
                        </div>
                        <div class="user-tile" onclick="selectUser('agus', 'Agus Pengepul', 'pengepul')">
                            <div class="avatar">A</div>
                            <h4>Agus Pengepul</h4>
                            <p>@agus</p>
                            <span class="role-badge role-pengepul">Pengepul</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- STEP 2: MASUKKAN PASSWORD (back button as circular icon, submit inline with field) -->
            <div id="step-password" class="step-view hidden-right">

                <div class="back-row">
                    <button type="button" class="btn-back" onclick="backToUserSelect()">&larr;</button>
                    <div class="who-row">
                        <div class="avatar" id="disp-avatar">U</div>
                        <div>
                            <h4 id="disp-nama">Nama User</h4>
                            <p id="disp-role">Role</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="login.php">  

                    <input type="hidden" id="username" name="username" required>

                    <div class="form-group manual-field" id="manual-username-group" style="display: none;">
                        <label for="manual_username">Username</label>
                        <input type="text" id="manual_username" placeholder="Masukkan username..." autocomplete="off">
                    </div>

                    <div class="form-group">  
                        <label for="password">Password</label>  
                        <div class="field-row">
                            <input   
                                type="password"   
                                id="password"   
                                name="password"   
                                placeholder="Masukkan password kamu..."
                                required
                                autofocus
                            >  
                            <button type="submit" class="login-btn">Masuk</button>
                        </div>
                    </div>  

                </form> 
            </div>

        </div>

        <div class="login-footer">
            Kelola Sampah, Jaga Bumi <span>#EcoSystem</span>
        </div>

    </div>  

    <script>
        const stepUsers = document.getElementById('step-users');
        const stepPassword = document.getElementById('step-password');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const dispAvatar = document.getElementById('disp-avatar');
        const dispNama = document.getElementById('disp-nama');
        const dispRole = document.getElementById('disp-role');
        const manualGroup = document.getElementById('manual-username-group');
        const ind1 = document.getElementById('ind-1');
        const ind2 = document.getElementById('ind-2');

        function goStep2() {
            stepUsers.classList.remove('active');
            stepUsers.classList.add('hidden-left');

            stepPassword.classList.remove('hidden-right');
            stepPassword.classList.add('active');

            ind1.classList.remove('on');
            ind2.classList.add('on');
        }

        function selectUser(username, nama, role) {
            usernameInput.value = username;
            dispNama.textContent = nama;
            const roleLabel = role.charAt(0).toUpperCase() + role.slice(1);
            dispRole.innerHTML = '@' + username + ' • <span class="role-badge role-' + role.toLowerCase() + '">' + roleLabel + '</span>';
            dispAvatar.textContent = nama.charAt(0).toUpperCase();

            manualGroup.style.display = 'none';

            goStep2();

            setTimeout(() => {
                passwordInput.focus();
            }, 300);
        }

        function enableManualInput() {
            usernameInput.value = '';
            dispNama.textContent = 'Ketik Username';
            dispRole.textContent = 'Akses manual';
            dispAvatar.textContent = '?';

            manualGroup.style.display = 'block';
            const manualInput = document.getElementById('manual_username');
            
            manualInput.oninput = function() {
                usernameInput.value = this.value;
            };

            goStep2();

            setTimeout(() => {
                manualInput.focus();
            }, 300);
        }

        function backToUserSelect() {
            stepPassword.classList.remove('active');
            stepPassword.classList.add('hidden-right');

            stepUsers.classList.remove('hidden-left');
            stepUsers.classList.add('active');

            ind2.classList.remove('on');
            ind1.classList.add('on');

            passwordInput.value = '';
        }

        <?php if ($error && isset($_POST['username'])): ?>
            selectUser(
                '<?= htmlspecialchars($_POST['username']) ?>', 
                '<?= htmlspecialchars($_POST['username']) ?>', 
                'User'
            );
        <?php endif; ?>
    </script>

</body>  
</html>
