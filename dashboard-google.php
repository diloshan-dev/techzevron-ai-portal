<?php
require_once 'db.php';

// Check for Google login session first
$googleUser = null;
$isGoogleLogin = false;

// Check for Google user data from cookie (set by JavaScript after Google login)
if (isset($_COOKIE['google_user_data'])) {
    $googleUser = json_decode(stripslashes($_COOKIE['google_user_data']), true);
    $_SESSION['google_user'] = $googleUser;
    $isGoogleLogin = true;
} elseif (isset($_SESSION['google_user'])) {
    $googleUser = $_SESSION['google_user'];
    $isGoogleLogin = true;
} else {
    // Require login if not Google user
    requireLogin();
    $user = getCurrentUser();
}

$totalUsers = 0;
$totalMessages = 0;
if (isAdmin()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM messages");
    $stmt->execute();
    $totalMessages = $stmt->fetch()['total'];
}

$userMessages = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM messages WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$userMessages = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Moving Stars Animation */
        .moving-star {
            position: fixed;
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
            z-index: -1;
            animation: moveStar 15s linear infinite;
            box-shadow: 0 0 10px white, 0 0 20px white;
        }
        
        @keyframes moveStar {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
            25% { transform: translate(100px, -150px) rotate(90deg); opacity: 0.8; }
            50% { transform: translate(200px, -50px) rotate(180deg); opacity: 1; }
            75% { transform: translate(100px, -200px) rotate(270deg); opacity: 0.6; }
            100% { transform: translate(0, 0) rotate(360deg); opacity: 1; }
        }
        
        /* Enhanced Profile Image with More Effects */
        .profile-wrapper {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto 25px;
        }
        
        .profile-glow {
            position: absolute;
            top: -15px;
            left: -15px;
            width: calc(100% + 30px);
            height: calc(100% + 30px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000);
            background-size: 400% 400%;
            animation: rainbowMove 2s linear infinite, breathe 3s ease-in-out infinite;
            z-index: 0;
            filter: blur(8px);
            opacity: 0.7;
        }
        
        .profile-rainbow-ring {
            position: absolute;
            top: -10px;
            left: -10px;
            width: calc(100% + 20px);
            height: calc(100% + 20px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ff8800, #ffff00, #00ff00, #00ffff, #0088ff, #0000ff, #8800ff, #ff00ff, #ff0088);
            background-size: 300% 300%;
            animation: rainbowMove 3s linear infinite;
            z-index: 1;
        }
        
        .profile-inner-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            width: calc(100% + 10px);
            height: calc(100% + 10px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff);
            background-size: 200% 200%;
            animation: rainbowMove 1.5s linear infinite;
            z-index: 2;
        }
        
        .profile-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            z-index: 3;
            border: 4px solid #fff;
            animation: breathe 3s ease-in-out infinite;
        }
        
        @keyframes rainbowMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes breathe {
            0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(14, 165, 233, 0.5); }
            50% { transform: scale(1.05); box-shadow: 0 0 50px rgba(14, 165, 233, 0.8), 0 0 80px rgba(139, 92, 246, 0.4); }
        }
        
        /* Google Badge */
        .google-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            margin-top: 10px;
        }
        
        /* Sidebar Logo Enhanced */
        .sidebar-logo-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }
        
        .sidebar-glow {
            position: absolute;
            top: -12px;
            left: -12px;
            width: calc(100% + 24px);
            height: calc(100% + 24px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff);
            background-size: 400% 400%;
            animation: rainbowMove 2s linear infinite;
            z-index: 0;
            filter: blur(10px);
            opacity: 0.8;
        }
        
        .sidebar-rainbow {
            position: absolute;
            top: -6px;
            left: -6px;
            width: calc(100% + 12px);
            height: calc(100% + 12px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff, #ff0000);
            background-size: 300% 300%;
            animation: rainbowMove 1.5s linear infinite;
            z-index: 1;
        }
        
        .sidebar-logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 2;
            border: 3px solid #fff;
        }
        
        .touch-area {
            position: fixed;
            top: 0;
            left: 0;
            width: 50px;
            height: 100%;
            z-index: 999;
            touch-action: none;
        }
        
        .touch-area-right {
            right: 0;
            left: auto;
        }
    </style>
</head>
<body data-theme="midnight">
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>

    <div class="full-moon"></div>
    
    <div class="moving-star" style="top: 5%; left: 10%; animation-delay: 0s; animation-duration: 12s;"></div>
    <div class="moving-star" style="top: 15%; left: 80%; animation-delay: 1s; animation-duration: 18s;"></div>
    <div class="moving-star" style="top: 25%; left: 30%; animation-delay: 2s; animation-duration: 15s;"></div>
    <div class="moving-star" style="top: 40%; left: 70%; animation-delay: 3s; animation-duration: 20s;"></div>
    <div class="moving-star" style="top: 60%; left: 20%; animation-delay: 4s; animation-duration: 14s;"></div>
    <div class="moving-star" style="top: 75%; left: 60%; animation-delay: 5s; animation-duration: 16s;"></div>
    <div class="moving-star" style="top: 85%; left: 40%; animation-delay: 6s; animation-duration: 19s;"></div>
    <div class="moving-star" style="top: 35%; left: 50%; animation-delay: 7s; animation-duration: 13s;"></div>
    <div class="moving-star" style="top: 55%; left: 85%; animation-delay: 8s; animation-duration: 17s;"></div>
    <div class="moving-star" style="top: 90%; left: 15%; animation-delay: 9s; animation-duration: 11s;"></div>

    <div class="touch-area" id="touchAreaLeft"></div>
    <div class="touch-area touch-area-right" id="touchAreaRight"></div>

    <nav class="navbar" id="navbar">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 30px;">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="display: flex !important; cursor: pointer;">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-auth">
                <?php if ($isGoogleLogin && $googleUser): ?>
                    <span class="user-greeting"><?php echo htmlspecialchars($googleUser['name']); ?></span>
                <?php elseif (isset($_SESSION['user_name'])): ?>
                    <span class="user-greeting"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar" style="width: 80%; max-width: 400px; right: -80%; transform: translateX(0);">
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper">
                <div class="sidebar-glow"></div>
                <div class="sidebar-rainbow"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" class="sidebar-logo-img">
            </div>
            <a href="index.php" class="logo" style="font-size: 24px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer; color: var(--text-secondary);">&times;</span>
        </div>
        
        <ul class="sidebar-menu" style="padding: 20px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="dashboard.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="profile.php"><i class="fas fa-id-card"></i> Edit Profile</a></li>
            <li><a href="#" onclick="openThemeModal()"><i class="fas fa-palette"></i> Themes</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            <?php if (isAdmin()): ?>
                <li class="admin-menu-item">
                    <a href="#"><i class="fas fa-cog"></i> Admin Panel</a>
                    <ul class="submenu">
                        <li><a href="index.php#admin-panel"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                        <li><a href="index.php#youtube-dashboard"><i class="fab fa-youtube"></i> YouTube Stats</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                <div class="theme-option" data-theme="midnight" onclick="applyTheme('midnight')">
                    <div class="theme-preview midnight-preview"></div>
                    <span>Midnight</span>
                </div>
                <div class="theme-option" data-theme="forest" onclick="applyTheme('forest')">
                    <div class="theme-preview forest-preview"></div>
                    <span>Forest</span>
                </div>
                <div class="theme-option" data-theme="matrix" onclick="applyTheme('matrix')">
                    <div class="theme-preview matrix-preview"></div>
                    <span>Matrix</span>
                </div>
                <div class="theme-option" data-theme="royal" onclick="applyTheme('royal')">
                    <div class="theme-preview royal-preview"></div>
                    <span>Royal Gold</span>
                </div>
                <div class="theme-option" data-theme="deepsea" onclick="applyTheme('deepsea')">
                    <div class="theme-preview deepsea-preview"></div>
                    <span>Deep Sea</span>
                </div>
                <div class="theme-option" data-theme="bloodmoon" onclick="applyTheme('bloodmoon')">
                    <div class="theme-preview bloodmoon-preview"></div>
                    <span>Blood Moon</span>
                </div>
                <div class="theme-option" data-theme="lavender" onclick="applyTheme('lavender')">
                    <div class="theme-preview lavender-preview"></div>
                    <span>Lavender</span>
                </div>
                <div class="theme-option" data-theme="arctic" onclick="applyTheme('arctic')">
                    <div class="theme-preview arctic-preview"></div>
                    <span>Arctic</span>
                </div>
                <div class="theme-option" data-theme="space" onclick="applyTheme('space')">
                    <div class="theme-preview space-preview"></div>
                    <span>Space</span>
                </div>
                <div class="theme-option" data-theme="sunset" onclick="applyTheme('sunset')">
                    <div class="theme-preview sunset-preview"></div>
                    <span>Sunset</span>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header reveal">
                <h1>Dashboard</h1>
                <p>Welcome to your personal dashboard</p>
            </div>

            <div class="user-welcome reveal" style="text-align: center; padding: 40px; background: var(--glass); backdrop-filter: blur(20px); border-radius: var(--radius); border: 1px solid var(--glass-border); margin-bottom: 40px;">
                <div class="profile-wrapper">
                    <div class="profile-glow"></div>
                    <div class="profile-rainbow-ring"></div>
                    <div class="profile-inner-ring"></div>
                    <?php if ($isGoogleLogin && $googleUser): ?>
                        <img src="<?php echo htmlspecialchars($googleUser['picture']); ?>" alt="Profile" class="profile-img">
                    <?php elseif (!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-img">
                    <?php else: ?>
                        <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="Profile" class="profile-img">
                    <?php endif; ?>
                </div>
                
                <?php if ($isGoogleLogin && $googleUser): ?>
                    <h2>Welcome back, <?php echo htmlspecialchars($googleUser['name']); ?>!</h2>
                    <p style="color: var(--text-secondary);">Signed in with Google</p>
                    <div class="google-badge">
                        <i class="fab fa-google"></i> Google Account
                    </div>
                <?php else: ?>
                    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                    <p style="color: var(--text-secondary);">Here's what's happening with your account</p>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
            <div class="dashboard-grid reveal">
                <div class="stat-card">
                    <div class="number"><?php echo $totalUsers; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $totalMessages; ?></div>
                    <div class="label">Messages</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo !empty($user['skills']) ? count(explode(',', $user['skills'])) : 0; ?></div>
                    <div class="label">Skills</div>
                </div>
                <div class="stat-card">
                    <div class="number">🟢</div>
                    <div class="label">Online</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card-grid reveal">
                <div class="card">
                    <span class="tool-icon">👤</span>
                    <h3>My Profile</h3>
                    <p>View and edit your profile information.</p>
                    <a href="profile.php" class="btn-download">View Profile</a>
                </div>
                
                <div class="card">
                    <span class="tool-icon">⚙️</span>
                    <h3>Settings</h3>
                    <p>Manage your account settings.</p>
                    <a href="settings.php" class="btn-download">Settings</a>
                </div>
                
                <div class="card">
                    <span class="tool-icon">📧</span>
                    <h3>Contact Admin</h3>
                    <p>Send messages to the administrator.</p>
                    <a href="contact.php" class="btn-download">Contact</a>
                </div>
            </div>

            <div class="card reveal" style="max-width: 700px; margin: 0 auto; padding: 35px;">
                <h3 style="margin-bottom: 30px; text-align: center; font-size: 22px; color: var(--text-primary);">
                    <i class="fas fa-user-circle" style="margin-right: 10px; color: var(--primary);"></i> Account Summary
                </h3>
                
                <?php if ($isGoogleLogin && $googleUser): ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Name</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($googleUser['name']); ?>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Email</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($googleUser['email']); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Name</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($user['name'] ?? 'Not set'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Email</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Phone</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">Country</label>
                            <div style="padding: 14px 16px; background: var(--bg-darker); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 15px; font-weight: 500;">
                                <?php echo htmlspecialchars($user['country'] ?? 'Sri Lanka'); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="developed-by">
                <p>Developed by <a href="#">P. Diloshan</a></p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ❤️ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        let touchStartX = 0;
        let touchEndX = 0;
        
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
            
            if (menuToggle) {
                menuToggle.classList.toggle('active');
            }
        }
        
        function applyTheme(themeName) {
            document.body.setAttribute('data-theme', themeName);
            localStorage.setItem('techzevron_theme', themeName);
            closeThemeModal();
            showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
        }
        
        function loadTheme() {
            const savedTheme = localStorage.getItem('techzevron_theme') || 'midnight';
            document.body.setAttribute('data-theme', savedTheme);
        }
        
        function openThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) modal.style.display = 'flex';
        }
        
        function closeThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) modal.style.display = 'none';
        }
        
        function showToast(message) {
            const existing = document.querySelector('.toast-notification');
            if (existing) existing.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
            if (event.target.classList.contains('sidebar-overlay')) {
                toggleSidebar();
            }
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeThemeModal();
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
        
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add('active');
                }
            }
        }
        window.addEventListener('scroll', reveal);
        reveal();
    </script>
</body>
</html>
