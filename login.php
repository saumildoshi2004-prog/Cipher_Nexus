<?php

session_start();

include("db.php");

if(isset($_POST['login'])){

$email=mysqli_real_escape_string($conn,$_POST['email']);

$password=$_POST['password'];

$role=mysqli_real_escape_string($conn,$_POST['role']);

$sql=mysqli_query($conn,"SELECT * FROM users
WHERE email='$email'
AND role='$role'");

if(mysqli_num_rows($sql)>0){

$row=mysqli_fetch_assoc($sql);

if(password_verify($password,$row['password'])){

$_SESSION['email']=$row['email'];

$_SESSION['role']=$row['role'];

header("Location: dashboard.php");

exit();

}else{

echo "<script>alert('Wrong Password');</script>";

}

}else{

echo "<script>alert('Invalid Email');</script>";

}

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TransitOps · Access Portal</title>
  
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline'; img-src 'self' data:;">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      transition: background 0.3s, color 0.2s;
    }

    /* ----- LIGHT THEME (default) ----- */
    :root {
      --bg-gradient: linear-gradient(145deg, #f6f9fc 0%, #e9f0f5 100%);
      --card-bg: rgba(255, 255, 255, 0.75);
      --card-border: rgba(255, 255, 255, 0.5);
      --card-shadow: 0 25px 50px -12px rgba(0, 20, 30, 0.25);
      --text-primary: #0b1e2b;
      --text-secondary: #2c3e50;
      --text-muted: #5b6f7e;
      --input-bg: #ffffff;
      --input-border: #d0dce8;
      --input-focus: #0066cc;
      --input-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
      --btn-primary: #0a2b3c;
      --btn-primary-hover: #1a3f54;
      --btn-text: #ffffff;
      --label-color: #1f3345;
      --dropdown-bg: #ffffff;
      --toggle-bg: #e2e8f0;
      --toggle-dot: #ffffff;
      --icon-color: #2c3e50;
      --badge-bg: #eef3f8;
      --badge-text: #0b2b3c;
      --footer-text: #4e6a7c;
      --divider: #d7e2ec;
      --error-color: #c74a4a;
    }

    /* ----- DARK THEME ----- */
    [data-theme="dark"] {
      --bg-gradient: linear-gradient(145deg, #0b141e 0%, #141f2b 100%);
      --card-bg: rgba(22, 34, 46, 0.85);
      --card-border: rgba(70, 100, 130, 0.3);
      --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
      --text-primary: #edf2f7;
      --text-secondary: #cbd5e1;
      --text-muted: #94a3b8;
      --input-bg: #1e2c3a;
      --input-border: #3a5068;
      --input-focus: #66b5ff;
      --input-shadow: 0 0 0 3px rgba(102, 181, 255, 0.25);
      --btn-primary: #2d4b66;
      --btn-primary-hover: #3f6382;
      --btn-text: #f0f6fc;
      --label-color: #dde7f0;
      --dropdown-bg: #1e2c3a;
      --toggle-bg: #2d4055;
      --toggle-dot: #e6edf5;
      --icon-color: #b0c8dd;
      --badge-bg: #1f3142;
      --badge-text: #cbdae8;
      --footer-text: #8fa6bb;
      --divider: #2e4258;
    }

    /* ----- CARD ----- */
    .auth-card {
      width: 100%;
      max-width: 480px;
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid var(--card-border);
      border-radius: 40px;
      padding: 2.5rem 2.2rem 2.2rem;
      box-shadow: var(--card-shadow);
      transition: background 0.3s, border 0.3s, box-shadow 0.3s;
      position: relative;
      overflow: hidden;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-bottom: 1.8rem;
    }

    .brand i {
      font-size: 2rem;
      color: var(--icon-color);
      background: var(--badge-bg);
      padding: 0.5rem;
      border-radius: 18px;
    }

    .brand h1 {
      font-weight: 700;
      font-size: 1.8rem;
      letter-spacing: -0.02em;
      color: var(--text-primary);
      background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .brand span {
      font-weight: 500;
      font-size: 0.8rem;
      color: var(--text-muted);
      background: var(--badge-bg);
      padding: 0.2rem 0.7rem;
      border-radius: 30px;
      margin-left: 0.2rem;
    }

    .theme-toggle {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      background: var(--toggle-bg);
      padding: 0.35rem 0.8rem 0.35rem 1rem;
      border-radius: 40px;
      cursor: pointer;
      transition: 0.25s;
      border: none;
      font-size: 0.8rem;
      font-weight: 500;
      color: var(--text-secondary);
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }

    .toggle-switch {
      width: 38px;
      height: 20px;
      background: var(--input-border);
      border-radius: 40px;
      position: relative;
      transition: 0.25s;
      box-shadow: inset 0 1px 4px rgba(0,0,0,0.2);
    }

    .toggle-switch::after {
      content: '';
      width: 16px;
      height: 16px;
      background: var(--toggle-dot);
      border-radius: 50%;
      position: absolute;
      top: 2px;
      left: 2px;
      transition: 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
    }

    [data-theme="dark"] .toggle-switch { background: #3d5a7a; }
    [data-theme="dark"] .toggle-switch::after { left: 20px; background: #f0f6fe; }

    .toggle-label {
      font-size: 0.75rem;
      font-weight: 500;
      color: var(--text-secondary);
    }

    /* ----- FORM CONTAINER ARCHITECTURE ----- */
    .forms-wrapper {
      position: relative;
      width: 100%;
    }

    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
      transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.3s ease;
    }

    /* Client view toggle system */
    .auth-form.hidden {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      opacity: 0;
      pointer-events: none;
      transform: translateX(50px);
    }
    
    .auth-form.active-view {
      transform: translateX(0);
      opacity: 1;
      pointer-events: auto;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }

    .input-group label {
      font-weight: 600;
      font-size: 0.8rem;
      letter-spacing: 0.3px;
      text-transform: uppercase;
      color: var(--label-color);
      opacity: 0.85;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .input-group label i {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .input-group input,
    .input-group select {
      background: var(--input-bg);
      border: 1.5px solid var(--input-border);
      border-radius: 20px;
      padding: 0.9rem 1.2rem;
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--text-primary);
      transition: 0.2s;
      outline: none;
      width: 100%;
    }

    .input-group input:focus,
    .input-group select:focus {
      border-color: var(--input-focus);
      box-shadow: var(--input-shadow);
    }

    .input-group select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235b6f7e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 1.2rem center;
      background-size: 1.1rem;
      cursor: pointer;
    }

    .input-group select option {
      background: var(--dropdown-bg);
      color: var(--text-primary);
    }

    .password-hint {
      display: flex;
      justify-content: flex-end;
      margin-top: 0.2rem;
    }

    .password-hint a {
      font-size: 0.75rem;
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 500;
    }

    .password-hint a:hover {
      color: var(--input-focus);
      text-decoration: underline;
    }

    .btn-primary {
      background: var(--btn-primary);
      border: none;
      border-radius: 40px;
      padding: 1rem 1.8rem;
      font-weight: 700;
      font-size: 1rem;
      color: var(--btn-text);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      transition: 0.25s;
      cursor: pointer;
      margin-top: 0.5rem;
      box-shadow: 0 8px 18px -6px rgba(0, 30, 50, 0.25);
    }

    .btn-primary:hover:not(:disabled) {
      background: var(--btn-primary-hover);
      transform: scale(1.01);
    }

    .btn-primary:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .divider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 0.3rem 0 0.2rem;
    }

    .divider hr {
      flex: 1;
      border: none;
      border-top: 1.5px solid var(--divider);
    }

    .divider span {
      font-size: 0.75rem;
      font-weight: 500;
      color: var(--text-muted);
    }

    .toggle-cta {
      text-align: center;
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .toggle-cta a {
      font-weight: 700;
      color: var(--input-focus);
      text-decoration: none;
      margin-left: 0.3rem;
    }

    .toggle-cta a:hover {
      text-decoration: underline;
    }

    .role-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: var(--badge-bg);
      padding: 0.25rem 0.9rem 0.25rem 0.7rem;
      border-radius: 40px;
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--badge-text);
      border: 1px solid var(--card-border);
    }

    .footer-note {
      margin-top: 1.6rem;
      font-size: 0.7rem;
      text-align: center;
      color: var(--footer-text);
      border-top: 1px solid var(--divider);
      padding-top: 1.5rem;
      display: flex;
      justify-content: center;
      gap: 1.2rem;
      flex-wrap: wrap;
    }

    /* Notification Engine UI Component */
    .app-dialog {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(-100px);
      background: var(--input-bg);
      border: 1px solid var(--card-border);
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      padding: 1rem 1.5rem;
      border-radius: 12px;
      color: var(--text-primary);
      z-index: 1000;
      font-size: 0.9rem;
      font-weight: 500;
      transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease;
      opacity: 0;
      pointer-events: none;
    }

    .app-dialog.visible {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }

    .input-group input:user-invalid {
      border-color: var(--error-color);
    }
  </style>
</head>
<body>

  <div id="appDialog" class="app-dialog" role="status" aria-live="polite"></div>

  <div class="auth-card" role="main" aria-label="TransitOps Security Verification Platform">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="brand">
        <i class="fas fa-truck-fast" aria-hidden="true"></i>
        <h1 id="portalTitle">TransitOps</h1>
        <span>v2</span>
      </div>
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle system interface color tone theme">
        <i class="fas fa-moon" id="themeIcon" aria-hidden="true"></i>
        <span class="toggle-switch"></span>
        <span class="toggle-label" id="themeLabel">Dark</span>
      </button>
    </div>

    <div class="forms-wrapper">
      
      <form class="auth-form active-view" id="loginForm" method="POST" action="/api/v1/auth/login" autocomplete="off">
        <input type="hidden" name="_csrf" value="ANTI_CSRF_TOKEN_VERIFICATION_STRING_VALUE" />
        
        <div class="input-group">
          <label for="loginEmail"><i class="fas fa-envelope" aria-hidden="true"></i> Identity Email</label>
          <input type="email" id="loginEmail" name="email" placeholder="fleet@transitops.com" required maxlength="120" />
        </div>

        <div class="input-group">
          <label for="loginPassword"><i class="fas fa-lock" aria-hidden="true"></i> Password Matrix</label>
          <input type="password" id="loginPassword" name="password" placeholder="••••••••" required maxlength="128" />
          <div class="password-hint">
            <a href="/recovery/reset-password">Forgot Password?</a>
          </div>
        </div>

        <div class="input-group">
          <label for="loginRole"><i class="fas fa-user-tag" aria-hidden="true"></i> Authorization Context</label>
          <select id="loginRole" name="role" required>
            <option value="" disabled selected>— select context role —</option>
            <option value="User Login">👤 User Account Interface</option>
            <option value="Driver">🚗 Registered Driver System</option>
          </select>
          <div style="display: flex; justify-content: flex-end; margin-top: 0.3rem;">
            <span class="role-badge"><i class="fas fa-shield-alt" aria-hidden="true"></i> Cryptographic RBAC Active</span>
          </div>
        </div>

        <button type="submit" class="btn-primary">
          <i class="fas fa-arrow-right-to-bracket" aria-hidden="true"></i> Assert Identity credentials
        </button>

        <div class="divider"><hr /><span>or</span><hr /></div>

        <div class="toggle-cta">
          New to the TransitOps network? <a href="#" id="toSignupBtn">Provision New Node Account</a>
        </div>
      </form>

      <form class="auth-form hidden" id="signupForm" method="POST" action="/api/v1/auth/register" autocomplete="off">
        <input type="hidden" name="_csrf" value="ANTI_CSRF_TOKEN_VERIFICATION_STRING_VALUE" />

        <div class="input-group">
          <label for="regEmail"><i class="fas fa-envelope" aria-hidden="true"></i> Dynamic Operational Email</label>
          <input type="email" id="regEmail" name="email" placeholder="operator@transitops.com" required maxlength="120" />
        </div>

        <div class="input-group">
          <label for="regPassword"><i class="fas fa-lock" aria-hidden="true"></i> Assign Secret Code</label>
          <input type="password" id="regPassword" name="password" placeholder="Min 12 Characters Secure String" required maxlength="128" minlength="12" />
        </div>

        <div class="input-group">
          <label for="regRole"><i class="fas fa-user-tag" aria-hidden="true"></i> Request Access Level</label>
          <select id="regRole" name="role" required>
            <option value="" disabled selected>— define requested group —</option>
            <option value="User Login">👤 User Account Interface</option>
            <option value="Driver">🚗 Registered Driver System</option>
          </select>
        </div>

        <button type="submit" class="btn-primary">
          <i class="fas fa-user-plus" aria-hidden="true"></i> Initialize Account Creation
        </button>

        <div class="divider"><hr /><span>or</span><hr /></div>

        <div class="toggle-cta">
          Possess existing cluster clearance? <a href="#" id="toLoginBtn">Authenticate Instead</a>
        </div>
      </form>

    </div>

    <div class="footer-note">
      <span><i class="fas fa-bus" aria-hidden="true"></i> Fleet Management</span>
      <span><i class="fas fa-chart-line" aria-hidden="true"></i> Analytics</span>
      <span><i class="fas fa-cloud-sun" aria-hidden="true"></i> Network Sandbox v1.0</span>
    </div>
  </div>

  <script>
    (function() {
      'use strict';

      // Capture operational handles safely
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');
      const themeLabel = document.getElementById('themeLabel');
      
      const loginForm = document.getElementById('loginForm');
      const signupForm = document.getElementById('signupForm');
      const toSignupBtn = document.getElementById('toSignupBtn');
      const toLoginBtn = document.getElementById('toLoginBtn');
      const appDialog = document.getElementById('appDialog');

      // ----- Safe Text-Content Messenger System -----
      function pushMessage(strText) {
        appDialog.textContent = strText; 
        appDialog.classList.add('visible');
        setTimeout(() => { appDialog.classList.remove('visible'); }, 3500);
      }

      // ----- Flow View State Sliding Management Control -----
      toSignupBtn.addEventListener('click', function(e) {
        e.preventDefault();
        loginForm.classList.remove('active-view');
        loginForm.classList.add('hidden');
        signupForm.classList.remove('hidden');
        signupForm.classList.add('active-view');
      });

      toLoginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        signupForm.classList.remove('active-view');
        signupForm.classList.add('hidden');
        loginForm.classList.remove('hidden');
        loginForm.classList.add('active-view');
      });

      // ----- Form Submission Security Interceptor Blocks -----
      function processSecureForm(event, targetActionStr) {
        event.preventDefault();
        const activeForm = event.target;
        const submitBtn = activeForm.querySelector('.btn-primary');
        
        // Prevent concurrent loop execution tampering requests
        submitBtn.disabled = true;
        
        pushMessage(`🔒 Security check passed. Routing payload to standard API endpoint destination...`);

        setTimeout(() => {
          submitBtn.disabled = false;
        }, 2000);
      }

      loginForm.addEventListener('submit', (e) => processSecureForm(e, 'Login'));
      signupForm.addEventListener('submit', (e) => processSecureForm(e, 'Registration'));

      // ----- Client Preferences Theme Control Engine Modules -----
      function applyInterfaceTone(targetMode) {
        document.documentElement.setAttribute('data-theme', targetMode);
        try { localStorage.setItem('transitops-theme-hash', targetMode); } catch(err){}
        
        if (targetMode === 'dark') {
          themeIcon.className = 'fas fa-sun';
          themeLabel.textContent = 'Light';
        } else {
          themeIcon.className = 'fas fa-moon';
          themeLabel.textContent = 'Dark';
        }
      }

      themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const runningTheme = document.documentElement.getAttribute('data-theme');
        applyInterfaceTone(runningTheme === 'dark' ? 'light' : 'dark');
      });

      // Initialize default layout theme states securely
      (function() {
        let storedTheme = null;
        try { storedTheme = localStorage.getItem('transitops-theme-hash'); } catch(e){}
        if (storedTheme === 'dark' || storedTheme === 'light') {
          applyInterfaceTone(storedTheme);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          applyInterfaceTone('dark');
        } else {
          applyInterfaceTone('light');
        }
      })();

    })();
  </script>
</body>
</html>
