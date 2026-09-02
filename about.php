<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Disable text selection */
        * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }
        
        /* Allow selection for input fields */
        input, textarea, .allow-select {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }
        
        /* About Page Specific Styles */
        .about-hero {
            text-align: center;
            padding: 60px 20px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            margin-bottom: 50px;
        }
        
        .about-owner {
            text-align: center;
            padding: 40px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            margin-bottom: 50px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Owner Photo with Enhanced Effects */
        .owner-photo-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 25px;
        }
        
        .owner-glow {
            position: absolute;
            top: -15px;
            left: -15px;
            width: calc(100% + 30px);
            height: calc(100% + 30px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff);
            background-size: 400% 400%;
            animation: rainbowMove 2s linear infinite;
            z-index: 0;
            filter: blur(10px);
            opacity: 0.8;
        }
        
        .owner-rainbow {
            position: absolute;
            top: -8px;
            left: -8px;
            width: calc(100% + 16px);
            height: calc(100% + 16px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff, #ff0000);
            background-size: 300% 300%;
            animation: rainbowMove 1.5s linear infinite;
            z-index: 1;
        }
        
        .owner-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 2;
            border: 4px solid #fff;
            animation: breathe 3s ease-in-out infinite;
        }
        
        .owner-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .owner-title {
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .owner-contact {
            text-align: left;
            margin: 20px 0;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-sm);
            margin-bottom: 10px;
            color: var(--text-secondary);
        }
        
        .contact-item i {
            color: var(--primary);
            width: 20px;
        }
        
        .youtube-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: linear-gradient(45deg, #ff0000, #ff4444);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .youtube-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.4);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            font-size: 30px;
        }
        
        @keyframes rainbowMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes breathe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        
        /* Disable context menu */
        body {
            -webkit-touch-callout: none;
        }
        
        /* Fixed CSS property for drag disable */
        img {
            -webkit-user-drag: none;
            user-drag: none;
        }
    </style>
</head>
<body data-theme="midnight" oncontextmenu="return false;">
    <!-- Animated Mesh Background -->
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>

    <!-- Full Moon -->
    <div class="full-moon"></div>
    
    <!-- Moving Stars -->
    <div class="moving-star" style="top: 5%; left: 10%; animation-duration: 12s;"></div>
    <div class="moving-star" style="top: 15%; left: 80%; animation-duration: 18s;"></div>
    <div class="moving-star" style="top: 25%; left: 30%; animation-duration: 15s;"></div>
    <div class="moving-star" style="top: 40%; left: 70%; animation-duration: 20s;"></div>
    <div class="moving-star" style="top: 60%; left: 20%; animation-duration: 14s;"></div>
    <div class="moving-star" style="top: 75%; left: 60%; animation-duration: 16s;"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 30px;">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="display: flex !important; cursor: pointer;">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-auth">
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <span class="user-greeting"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                    <a href="dashboard.php" class="btn-login">Dashboard</a>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Slide-out Sidebar -->
    <div class="sidebar" id="sidebar" style="width: 80%; max-width: 400px; right: -80%;">
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper" style="width: 100px; height: 100px; margin: 0 auto 15px; position: relative;">
                <div class="sidebar-glow" style="position: absolute; top: -8px; left: -8px; width: calc(100% + 16px); height: calc(100% + 16px); border-radius: 50%; background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff); background-size: 300% 300%; animation: rainbowMove 2s linear infinite; z-index: 0; filter: blur(8px);"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; border: 3px solid #fff;">
            </div>
            <a href="index.php" class="logo" style="font-size: 22px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer;">&times;</span>
        </div>
        
        <ul class="sidebar-menu" style="padding: 20px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php" class="active"><i class="fas fa-info-circle"></i> About</a></li>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <li><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
            <?php endif; ?>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="#" onclick="openThemeModal()"><i class="fas fa-palette"></i> Themes</a></li>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="sidebar-footer" style="padding: 25px; border-top: 1px solid var(--glass-border); text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px; margin: 0;">Developed by P. Diloshan</p>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Theme Modal -->
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="about-hero reveal">
                <h1 style="font-size: 42px; margin-bottom: 15px;">About TechZevron</h1>
                <p style="color: var(--text-secondary); font-size: 18px;">Empowering digital experiences with innovative solutions</p>
            </div>
            
            <!-- Owner Information -->
            <div class="about-owner reveal">
                <div class="owner-photo-wrapper">
                    <div class="owner-glow"></div>
                    <div class="owner-rainbow"></div>
                    <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="P. Diloshan" class="owner-photo">
                </div>
                <h2 class="owner-name">P. Diloshan</h2>
                <p class="owner-title">Web Developer & Founder</p>
                
                <div class="owner-contact">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>New Meehitiya Mahawala Waththa, Ratnapura</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>techzevron@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+94 77 002 0184, +94 72 161 7577</span>
                    </div>
                </div>
                
                <a href="https://www.youtube.com/channel/UC2Xvqo5QjpeR1xp9CKsPMWA?sub_confirmation=1" target="_blank" class="youtube-btn">
                    <i class="fab fa-youtube"></i> Subscribe on YouTube
                </a>
            </div>
            
            <!-- Features Cards -->
            <div class="card-grid">
                <div class="card reveal">
                    <div class="feature-icon">🚀</div>
                    <h3>Our Mission</h3>
                    <p>We strive to provide seamless digital experiences with modern design and robust functionality.</p>
                </div>
                
                <div class="card reveal">
                    <div class="feature-icon">💡</div>
                    <h3>What We Offer</h3>
                    <p>Complete user management system with secure registration, login, profile management, and responsive design.</p>
                </div>
                
                <div class="card reveal">
                    <div class="feature-icon">🎯</div>
                    <h3>Key Features</h3>
                    <p>• Secure password hashing<br>• Profile management<br>• 10 themes<br>• Dark/Light mode<br>• Real-time messaging</p>
                </div>
                
                <div class="card reveal">
                    <div class="feature-icon">🔒</div>
                    <h3>Security</h3>
                    <p>Industry-standard security with bcrypt password hashing and prepared statements.</p>
                </div>
                
                <div class="card reveal">
                    <div class="feature-icon">🎨</div>
                    <h3>Design</h3>
                    <p>Modern UI with gradient backgrounds, smooth animations, glassmorphism effects.</p>
                </div>
                
                <div class="card reveal">
                    <div class="feature-icon">📞</div>
                    <h3>Support</h3>
                    <p>Have questions? Contact us through our contact page. Messages go directly to our email.</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="contact.php" class="btn-download" style="display: inline-block; text-decoration: none;">Contact Us</a>
            </div>
            
            <!-- Developed By Section -->
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
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable keyboard shortcuts for developer tools
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) ||
                (e.ctrlKey && ['u', 's', 'p'].includes(e.key.toLowerCase()))) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable drag
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable copy
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Sidebar Toggle
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
        
        // Theme System
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
            
            if (sidebar) {
                sidebar.classList.remove('active');
                sidebar.style.right = '-80%';
            }
            if (overlay) overlay.classList.remove('active');
            if (menuToggle) menuToggle.classList.remove('active');
        }
        
        // Toast Notification
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
        
        // Close on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
            if (event.target.classList.contains('sidebar-overlay')) {
                toggleSidebar();
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
        
        // Scroll reveal
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