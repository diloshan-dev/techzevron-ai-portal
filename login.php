<?php
require_once 'db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $message = 'Please fill in all fields';
        $message_type = 'error';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $message = 'Invalid email or password';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }
        input, textarea {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }
        
        /* Neon Rainbow Glow Keyframes */
        @keyframes rainbowMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .sidebar-glow {
            position: absolute;
            top: -8px;
            left: -8px;
            width: calc(100% + 16px);
            height: calc(100% + 16px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff, #ff0000, #ffff00, #0000ff);
            background-size: 300% 300%;
            animation: rainbowMove 2s linear infinite;
            z-index: 0;
            filter: blur(4px);
            opacity: 0.8;
        }

        /* Modern Glassmorphism Styled Google Button Wrapper */
        .google-btn-container { 
            margin: 25px 0; 
            display: flex; 
            justify-content: center;
            width: 100%;
        }
        
        .custom-google-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            padding: 6px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .custom-google-wrapper:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 20px var(--glow-color);
        }

        .divider { display: flex; align-items: center; margin: 25px 0; color: var(--text-secondary); font-size: 14px; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span { padding: 0 15px; }
        .user-profile-display { display: none; text-align: center; padding: 40px; background: var(--glass); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius); animation: fadeInUp 0.5s ease-out; }
        .user-profile-display.active { display: block; }
        .user-profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); box-shadow: 0 8px 30px rgba(0,0,0,0.25); margin-bottom: 20px; }
        .user-profile-name { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
        .user-profile-email { font-size: 14px; color: var(--text-secondary); margin-bottom: 25px; }
        .user-profile-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600; }
        .user-profile-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px var(--glow-color); }
        .form-container.hidden { display: none; }
        .welcome-alert { padding: 16px 20px; border-radius: var(--radius); margin-bottom: 25px; font-weight: 500; animation: fadeInUp 0.5s ease-out; display: flex; align-items: center; gap: 12px; }
        .welcome-alert.new-user { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; }
        .welcome-alert.returning-user { background: rgba(59, 130, 246, 0.15); border: 1px solid #3b82f6; color: #3b82f6; }
    </style>
</head>
<body data-theme="midnight" oncontextmenu="return false;">
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>

    <nav class="navbar" id="navbar">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 30px;">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="display: flex !important; cursor: pointer;">
                <span></span><span></span><span></span>
            </div>
            <div class="nav-auth">
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar" style="width: 80%; max-width: 400px; right: -80%;">
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border);">
            <div class="sidebar-logo-wrapper" style="width: 100px; height: 100px; margin: 0 auto 15px; position: relative;">
                <!-- Re-added Rainbow Neon Glow Animation -->
                <div class="sidebar-glow"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; border: 3px solid #fff;">
            </div>
            <a href="index.php" class="logo" style="font-size: 22px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer;">&times;</span>
        </div>
        <ul class="sidebar-menu" style="padding: 20px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="ai-prompts.php"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php"><i class="fas fa-tools"></i> Web Tools</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="#" onclick="openThemeModal()"><i class="fas fa-palette"></i> Themes</a></li>
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        </ul>
        <div class="sidebar-footer" style="padding: 25px; border-top: 1px solid var(--glass-border); text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px; margin: 0;">Developed by P. Diloshan</p>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="modal" id="themeModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeThemeModal()">&times;</span>
            <h2><i class="fas fa-palette"></i> Choose Theme</h2>
            <div class="theme-grid">
                <div class="theme-option" data-theme="classic" onclick="applyTheme('classic')"><div class="theme-preview" style="background: linear-gradient(135deg, #f5f5f5, #3b82f6);"></div><span>Classic White</span></div>
                <div class="theme-option" data-theme="midnight" onclick="applyTheme('midnight')"><div class="theme-preview midnight-preview"></div><span>Midnight</span></div>
                <div class="theme-option" data-theme="darki" onclick="applyTheme('darki')"><div class="theme-preview darki-preview"></div><span>Darki (YouTube)</span></div>
                <div class="theme-option" data-theme="darky" onclick="applyTheme('darky')"><div class="theme-preview darky-preview"></div><span>Darky</span></div>
                <div class="theme-option" data-theme="forest" onclick="applyTheme('forest')"><div class="theme-preview forest-preview"></div><span>Forest</span></div>
                <div class="theme-option" data-theme="matrix" onclick="applyTheme('matrix')"><div class="theme-preview matrix-preview"></div><span>Matrix</span></div>
                <div class="theme-option" data-theme="royal" onclick="applyTheme('royal')"><div class="theme-preview royal-preview"></div><span>Royal Gold</span></div>
                <div class="theme-option" data-theme="deepsea" onclick="applyTheme('deepsea')"><div class="theme-preview deepsea-preview"></div><span>Deep Sea</span></div>
                <div class="theme-option" data-theme="bloodmoon" onclick="applyTheme('bloodmoon')"><div class="theme-preview bloodmoon-preview"></div><span>Blood Moon</span></div>
                <div class="theme-option" data-theme="lavender" onclick="applyTheme('lavender')"><div class="theme-preview lavender-preview"></div><span>Lavender</span></div>
                <div class="theme-option" data-theme="arctic" onclick="applyTheme('arctic')"><div class="theme-preview arctic-preview"></div><span>Arctic</span></div>
                <div class="theme-option" data-theme="space" onclick="applyTheme('space')"><div class="theme-preview space-preview"></div><span>Space</span></div>
                <div class="theme-option" data-theme="sunset" onclick="applyTheme('sunset')"><div class="theme-preview sunset-preview"></div><span>Sunset</span></div>
                <div class="theme-option" data-theme="ultimate-dark" onclick="applyTheme('ultimate-dark')"><div class="theme-preview ultimate-dark-preview"></div><span>Ultimate Dark</span></div>
                <div class="theme-option" data-theme="clean-white" onclick="applyTheme('clean-white')"><div class="theme-preview clean-white-preview"></div><span>Clean White</span></div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header reveal">
                <h1>Welcome Back</h1>
                <p>Login to your TechZevron account</p>
            </div>

            <div class="user-profile-display" id="userProfileDisplay">
                <img src="" alt="Profile" class="user-profile-img" id="userProfileImg">
                <h2 class="user-profile-name" id="userProfileName"></h2>
                <p class="user-profile-email" id="userProfileEmail"></p>
                <div id="welcomeAlert"></div>
                <a href="dashboard-google.php" class="user-profile-btn"><i class="fas fa-rocket"></i> Go to Dashboard</a>
                <br><br>
                <a href="login.php?logout=1" style="color: var(--text-secondary); font-size: 14px;"><i class="fas fa-sign-out-alt"></i> Sign out</a>
            </div>

            <div class="form-container reveal" id="loginFormContainer">
                <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form id="loginForm" method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required style="padding-right: 50px;">
                            <button type="button" onclick="togglePassword()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0;">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
                
                <div class="divider"><span>or</span></div>
                
                <div class="google-btn-container">
                    <div class="custom-google-wrapper">
                        <div id="g_id_onload"
                             data-client_id="636322235683-bhvnig4ubvn62795jn5gfolvurisg1iv.apps.googleusercontent.com"
                             data-context="signin"
                             data-ux_mode="popup"
                             data-callback="handleGoogleLogin"
                             data-auto_prompt="false">
                        </div>
                        <div class="g_id_signin"
                             data-type="standard"
                             data-shape="pill"
                             data-theme="filled_blue"
                             data-text="continue_with"
                             data-size="large"
                             data-logo_alignment="left"
                             data-width="320">
                        </div>
                    </div>
                </div>
                
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ❤️ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Handle logout
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout') === '1') {
            sessionStorage.removeItem('google_user');
            document.cookie = 'google_user_data=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;';
        }
        
        // Google Login Callback
        function handleGoogleLogin(response) {
            console.log("Google Login Response:", response);
            
            const payload = parseJwt(response.credential);
            console.log("User Payload:", payload);
            
            const userEmail = payload.email;
            const userName = payload.name;
            const userPicture = payload.picture;
            
            const formData = new FormData();
            formData.append('action', 'google_login');
            formData.append('name', userName);
            formData.append('email', userEmail);
            formData.append('picture', userPicture);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                console.log('API Response:', data);
                if (data.success) {
                    const userData = {
                        name: userName,
                        email: userEmail,
                        picture: userPicture,
                        loggedIn: true
                    };
                    
                    sessionStorage.setItem('google_user', JSON.stringify(userData));
                    
                    const expires = new Date();
                    expires.setHours(expires.getHours() + 1);
                    document.cookie = 'google_user_data=' + encodeURIComponent(JSON.stringify(userData)) + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
                    
                    if (data.is_new) {
                        showToast('Welcome! Account created successfully!');
                    } else {
                        showToast('Welcome back! Login successful!');
                    }
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard-google.php';
                    }, 1000);
                } else {
                    alert('Login failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const userData = {
                    name: userName,
                    email: userEmail,
                    picture: userPicture,
                    loggedIn: true
                };
                sessionStorage.setItem('google_user', JSON.stringify(userData));
                window.location.href = 'dashboard-google.php';
            });
        }
        
        function parseJwt(token) {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        }
        
        // Check if already logged in with Google
        document.addEventListener('DOMContentLoaded', function() {
            const googleUser = sessionStorage.getItem('google_user');
            if (googleUser) {
                const user = JSON.parse(googleUser);
                if (user.loggedIn) {
                    document.getElementById('loginFormContainer').classList.add('hidden');
                    document.getElementById('userProfileDisplay').classList.add('active');
                    document.getElementById('userProfileImg').src = user.picture;
                    document.getElementById('userProfileName').textContent = user.name;
                    document.getElementById('userProfileEmail').textContent = user.email;
                    document.getElementById('welcomeAlert').innerHTML = '<div class="welcome-alert returning-user"><i class="fas fa-hand-sparkles"></i> Good to see you again!</div>';
                }
            }
            
            loadTheme();
        });
        
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.shiftKey && e.key === 'J') || (e.ctrlKey && e.key === 'u') || (e.ctrlKey && e.key === 's') || (e.ctrlKey && e.key === 'p')) {
                e.preventDefault();
                return false;
            }
        });
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            
            if (sidebar && overlay) {
                const isOpen = sidebar.classList.contains('active');
                if (isOpen) {
                    sidebar.classList.remove('active');
                    sidebar.style.right = '-80%';
                    overlay.classList.remove('active');
                } else {
                    sidebar.classList.add('active');
                    sidebar.style.right = '0';
                    overlay.classList.add('active');
                }
            }
            if (menuToggle) menuToggle.classList.toggle('active');
        }
        
        function applyTheme(themeName) {
            document.body.setAttribute('data-theme', themeName);
            localStorage.setItem('techzevron_theme', themeName);
            closeThemeModal();
            showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
        }
        
        function loadTheme() {
            const savedTheme = localStorage.getItem('techzevron_theme') || 'classic';
            document.body.setAttribute('data-theme', savedTheme);
        }
        
        function openThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) modal.style.display = 'flex';
            closeSidebar();
        }
        
        function closeThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) modal.style.display = 'none';
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            if (sidebar) { sidebar.classList.remove('active'); sidebar.style.right = '-80%'; }
            if (overlay) overlay.classList.remove('active');
            if (menuToggle) menuToggle.classList.remove('active');
        }
        
        function showToast(message) {
            const existing = document.querySelector('.toast-notification');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) event.target.style.display = 'none';
            if (event.target.classList.contains('sidebar-overlay')) toggleSidebar();
        }
        
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) reveals[i].classList.add('active');
            }
        }
        window.addEventListener('scroll', reveal);
        reveal();
    </script>
</body>
</html>