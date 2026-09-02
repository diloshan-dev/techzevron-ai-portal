<?php
require_once 'db.php';

$is_admin = isAdmin();
$is_logged_in = isLoggedIn();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body data-theme="midnight" oncontextmenu="return false" onselectstart="return false" ondragstart="return false">
<script>
document.oncontextmenu = e => e.preventDefault();
document.onkeydown = e => {
    if(e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.key === 'u') || (e.ctrlKey && e.key === 's')) {
        e.preventDefault();
    }
};
document.ondragstart = e => e.preventDefault();
document.oncopy = e => e.preventDefault();
</script>
    
    <!-- Animated Mesh Background -->
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>
    
    <!-- Full Moon (Dark Mode) -->
    <div class="full-moon"></div>
    
    <!-- Twinkling Stars -->
    <div class="star" style="top: 10%; left: 20%; animation-delay: 0s;"></div>
    <div class="star" style="top: 20%; left: 80%; animation-delay: 0.5s;"></div>
    <div class="star" style="top: 30%; left: 40%; animation-delay: 1s;"></div>
    <div class="star" style="top: 50%; left: 70%; animation-delay: 1.5s;"></div>
    <div class="star" style="top: 70%; left: 30%; animation-delay: 0.3s;"></div>
    <div class="star" style="top: 80%; left: 60%; animation-delay: 0.8s;"></div>
    <div class="star" style="top: 15%; left: 55%; animation-delay: 1.2s;"></div>
    <div class="star" style="top: 45%; left: 15%; animation-delay: 0.7s;"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-auth">
                <?php if ($is_logged_in): ?>
                    <span class="user-greeting"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Slide-out Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()">&times;</span>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Home
            </a></li>
            <li><a href="ai-prompts.php">
                <i class="fas fa-robot"></i> AI Prompts
            </a></li>
            <li><a href="web-tools.php">
                <i class="fas fa-tools"></i> Web Tools
            </a></li>
            <li><a href="#" onclick="openThemeModal(); return false;">
                <i class="fas fa-palette"></i> Themes
            </a></li>
            
            <?php if ($is_logged_in): ?>
                <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> My Profile
                </a></li>
            <?php endif; ?>
            
            <?php if ($is_admin): ?>
                <li class="admin-menu-item">
                    <a href="#" class="<?php echo $current_page == 'admin' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Admin Panel
                    </a>
                    <ul class="submenu">
                        <li><a href="admin.php"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            
            <li><a href="about.php" class="<?php echo $current_page == 'about' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i> About
            </a></li>
            <li><a href="contact.php" class="<?php echo $current_page == 'contact' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Contact
            </a></li>
        </ul>
        
        <div class="sidebar-footer">
            <?php if ($is_logged_in): ?>
                <a href="logout.php" class="btn-logout-sidebar"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login-sidebar"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn-register-sidebar"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
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
                <div class="theme-option" data-theme="classic" onclick="applyTheme('classic')">
                    <div class="theme-preview" style="background: linear-gradient(135deg, #f5f5f5, #3b82f6);"></div>
                    <span>Classic White</span>
                </div>
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
        <div class="container mt-4">
            <!-- Dynamic page content goes here -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> TechZevron. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap & Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    function openThemeModal() {
        document.getElementById('themeModal').style.display = 'block';
    }

    function closeThemeModal() {
        document.getElementById('themeModal').style.display = 'none';
    }

    function applyTheme(theme) {
        document.body.setAttribute('data-theme', theme);
        localStorage.setItem('selectedTheme', theme);
        closeThemeModal();
    }

    // Load saved theme
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('selectedTheme') || 'midnight';
        document.body.setAttribute('data-theme', savedTheme);
    });
    </script>
</body>
</html>