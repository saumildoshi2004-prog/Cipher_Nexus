<?php
session_start();
include("db.php");

$error = "";

if (isset($_POST['signup'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role']; // "user" or "driver"

    // Basic server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address";
    } elseif (strlen($password) < 12) {
        $error = "Password must be at least 12 characters";
    } elseif (!in_array($role, ['user', 'driver'])) {
        $error = "Invalid role selected";
    } else {

        // Check if email already exists (prepared statement - no SQL injection)
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");

        if (!$check) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email already exists";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_prepare($conn, "INSERT INTO users (email, password, role) VALUES (?, ?, ?)");

            if (!$insert) {
                die("Prepare failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($insert, "sss", $email, $hashedPassword, $role);

            if (mysqli_stmt_execute($insert)) {
                echo "<script>
                        alert('Registration Successful');
                        window.location='login.php';
                      </script>";
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TransitOps - Sign Up</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}
body{min-height:100vh;display:flex;justify-content:center;align-items:center;background:linear-gradient(145deg,#f6f9fc,#e9f0f5);padding:20px;}
:root{--card-bg:rgba(255,255,255,.85);--border:#dbe5ef;--text:#0b1e2b;--input:#ffffff;--btn:#0a2b3c;--btn-hover:#1a3f54;--badge:#eef3f8;}
[data-theme="dark"]{background:#111827;--card-bg:rgba(24,32,44,.9);--border:#3b4d63;--text:#fff;--input:#1f2937;--btn:#35516d;--btn-hover:#456784;--badge:#223445;}
body{background:var(--bg,linear-gradient(145deg,#f6f9fc,#e9f0f5));}
.auth-card{width:100%;max-width:470px;background:var(--card-bg);border:1px solid var(--border);border-radius:35px;padding:35px;backdrop-filter:blur(10px);box-shadow:0 20px 45px rgba(0,0,0,.15);}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
.brand{display:flex;align-items:center;gap:10px;}
.brand i{font-size:28px;padding:12px;background:var(--badge);border-radius:15px;}
.brand h2{color:var(--text);}
.theme-toggle{border:none;background:#ddd;border-radius:25px;padding:8px 15px;cursor:pointer;}
.input-group{margin-bottom:20px;}
.input-group label{display:block;margin-bottom:8px;font-size:14px;font-weight:600;color:var(--text);}
.input-group input,.input-group select{width:100%;padding:15px;border-radius:15px;border:1px solid var(--border);background:var(--input);color:var(--text);outline:none;font-size:15px;}
.input-group input:focus,.input-group select:focus{border-color:#0066cc;}
.btn-primary{width:100%;border:none;background:var(--btn);color:white;padding:16px;border-radius:30px;font-size:16px;font-weight:600;cursor:pointer;transition:.3s;}
.btn-primary:hover{background:var(--btn-hover);}
.footer{margin-top:25px;text-align:center;font-size:13px;color:#666;}
.error-box{background:#ffe0e0;color:#b30000;padding:12px 15px;border-radius:10px;margin-bottom:20px;font-size:14px;text-align:center;}
.app-dialog{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:white;padding:15px 25px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.2);transition:.3s;opacity:0;}
.app-dialog.visible{opacity:1;transform:translateX(-50%) translateY(0);}
.divider{display:flex;align-items:center;gap:10px;margin:20px 0;}
.divider hr{flex:1;border:none;border-top:1px solid var(--divider);}
.divider span{color:var(--text-muted);font-size:14px;font-weight:600;}
.btn-login{width:100%;display:flex;justify-content:center;align-items:center;gap:8px;text-decoration:none;background:transparent;color:var(--btn-primary);border:2px solid var(--btn-primary);border-radius:40px;padding:15px;font-weight:700;transition:.3s;}
.btn-login:hover{background:var(--btn-primary);color:#fff;}
</style>
</head>
<body>

<div id="appDialog" class="app-dialog"></div>

<div class="auth-card">

<div class="top">
<div class="brand">
<i class="fas fa-truck-fast"></i>
<div>
<h2>TransitOps</h2>
<small>Sign Up</small>
</div>
</div>
<button class="theme-toggle" id="themeToggle">
<i class="fas fa-moon" id="themeIcon"></i>
</button>
</div>

<?php if (!empty($error)) : ?>
<div class="error-box"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form action="signup.php" method="POST" id="signupForm">

<div class="input-group">
<label>Email</label>
<input type="email" name="email" placeholder="operator@transitops.com" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" placeholder="Minimum 12 Characters" minlength="12" required>
</div>

<div class="input-group">
<label>Role</label>
<select name="role" required>
<option value="">Select Role</option>
<option value="user">User</option>
<option value="driver">Driver</option>
</select>
</div>

<button type="submit" name="signup" class="btn-primary">
    <i class="fas fa-user-plus"></i>
    Create Account
</button>

<div class="divider">
    <hr><span>OR</span><hr>
</div>

<a href="login.php" class="btn-login">
    <i class="fas fa-arrow-right-to-bracket"></i>
    Login
</a>
</form>

<div class="footer">
Fleet Management • Analytics • TransitOps
</div>

</div>

<script>
const themeToggle=document.getElementById("themeToggle");
const themeIcon=document.getElementById("themeIcon");
const dialog=document.getElementById("appDialog");

function showMessage(msg){
    dialog.textContent=msg;
    dialog.classList.add("visible");
    setTimeout(()=>{dialog.classList.remove("visible");},3000);
}

document.getElementById("signupForm").addEventListener("submit",function(){
    showMessage("Creating your account...");
});

function applyTheme(mode){
    document.documentElement.setAttribute("data-theme",mode);
    localStorage.setItem("theme",mode);
    themeIcon.className=(mode==="dark")?"fas fa-sun":"fas fa-moon";
}

themeToggle.onclick=function(){
    let current=document.documentElement.getAttribute("data-theme");
    applyTheme(current==="dark"?"light":"dark");
};

let saved=localStorage.getItem("theme");
applyTheme(saved?saved:"light");
</script>

</body>
</html>
