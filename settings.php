<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
$is_admin = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Settings Page Specific Styles */
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .settings-header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .settings-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--text-primary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .settings-header p {
            color: var(--text-secondary);
            font-size: 16px;
        }
        
        .settings-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 30px;
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .settings-card:nth-child(2) { animation-delay: 0.1s; }
        .settings-card:nth-child(3) { animation-delay: 0.2s; }
        .settings-card:nth-child(4) { animation-delay: 0.3s; }
        
        .settings-card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .settings-card-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            font-size: 22px;
            color: white;
        }
        
        .settings-card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .settings-card-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 5px 0 0;
        }
        
        /* Owner Details */
        .owner-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(var(--primary-rgb), 0); }
        }
        
        .owner-info {
            text-align: center;
        }
        
        .owner-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
        }
        
        .owner-email {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .owner-badge {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        /* Privacy Options */
        .privacy-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-sm);
            margin-bottom: 12px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        
        .privacy-option:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border);
        }
        
        .privacy-option-info strong {
            display: block;
            color: var(--text-primary);
            font-size: 15px;
            margin-bottom: 3px;
        }
        
        .privacy-option-info span {
            color: var(--text-secondary);
            font-size: 13px;
        }
        
        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            width: 56px;
            height: 28px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--bg-darker);
            border: 2px solid var(--border);
            border-radius: 34px;
            transition: 0.4s;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 2px;
            bottom: 2px;
            background-color: var(--text-secondary);
            border-radius: 50%;
            transition: 0.4s;
        }
        
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-color: transparent;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(28px);
            background-color: white;
        }
        
        /* Theme Grid */
        .theme-grid-settings {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .theme-option-settings {
            position: relative;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .theme-option-settings:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .theme-option-settings.active {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }
        
        .theme-option-settings.active::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 8px;
            right: 8px;
            color: var(--primary);
            font-size: 14px;
        }
        
        .theme-preview-settings {
            width: 100%;
            height: 50px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .theme-name-settings {
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 600;
        }
        
        /* Form Groups */
        .form-group-settings {
            margin-bottom: 20px;
        }
        
        .form-group-settings label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group-settings input,
        .form-group-settings select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-group-settings input:focus,
        .form-group-settings select:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px var(--glow-color);
        }
        
        .btn-save-settings {
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-save-settings:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px var(--glow-color);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .settings-container {
                padding: 20px 15px;
            }
            
            .settings-card {
                padding: 20px;
            }
            
            .theme-grid-settings {
                grid-template-columns: repeat(2, 1fr);
            }
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
    </style>
</head>
<body data-theme="classic" oncontextmenu="return false;">
    <!-- Animated Mesh Background -->
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>
 
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle">
                <span></span><span></span><span></span>
            </div>
            
            <div class="nav-auth">
                <span style="margin-right: 8px; color: var(--primary); font-size: 12px;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="btn-register">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Slide-out Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper">
                <div class="sidebar-glow"></div>
                <div class="sidebar-rainbow"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" class="sidebar-logo-img">
            </div>
            <a href="index.php" class="logo" style="font-size: 24px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer; color: var(--text-secondary);">&times;</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="ai-prompts.php"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php"><i class="fas fa-tools"></i> Web Tools</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="dashboard.php"><i class="fas fa-user"></i> My Profile</a></li>
            <?php if ($is_admin): ?>
                <li><a href="index.php#admin-panel"><i class="fas fa-cog"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-footer">
            <p style="color: var(--text-muted); font-size: 12px; margin: 0;">Developed by P. Diloshan</p>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="settings-container">
            <!-- Header -->
            <div class="settings-header">
                <h1><i class="fas fa-cog"></i> Settings</h1>
                <p>Manage your account preferences and settings</p>
            </div>
            
            <!-- Owner Details -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Owner Details</h3>
                        <p class="settings-card-subtitle">Your profile information</p>
                    </div>
                </div>
                
                <div class="owner-info">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="owner-avatar">
                    <?php else: ?>
                        <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="Profile" class="owner-avatar">
                    <?php endif; ?>
                    
                    <div class="owner-name"><?php echo htmlspecialchars($user['name']); ?></div>
                    <div class="owner-email"><?php echo htmlspecialchars($user['email']); ?></div>
                    <?php if ($is_admin): ?>
                        <span class="owner-badge"><i class="fas fa-crown"></i> Admin</span>
                    <?php endif; ?>
                </div>
                
                <form id="profileForm" style="margin-top: 30px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group-settings">
                            <label>Name</label>
                            <input type="text" id="settingsName" value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        <div class="form-group-settings">
                            <label>Phone</label>
                            <input type="text" id="settingsPhone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group-settings">
                        <label>Address</label>
                        <input type="text" id="settingsAddress" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group-settings">
                            <label>Country</label>
                            <input type="text" id="settingsCountry" value="<?php echo htmlspecialchars($user['country'] ?? 'Sri Lanka'); ?>">
                        </div>
                        <div class="form-group-settings">
                            <label>District</label>
                            <input type="text" id="settingsDistrict" value="<?php echo htmlspecialchars($user['district'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn-save-settings">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
            
            <!-- Account Privacy -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Account Privacy</h3>
                        <p class="settings-card-subtitle">Control your privacy settings</p>
                    </div>
                </div>
                
                <div class="privacy-option">
                    <div class="privacy-option-info">
                        <strong>Profile Visibility</strong>
                        <span>Allow others to view your profile</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="privacyProfile" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="privacy-option">
                    <div class="privacy-option-info">
                        <strong>Show Email</strong>
                        <span>Display your email on your profile</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="privacyEmail">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="privacy-option">
                    <div class="privacy-option-info">
                        <strong>Activity Status</strong>
                        <span>Show when you're online</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="privacyActivity" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="privacy-option">
                    <div class="privacy-option-info">
                        <strong>Comment Notifications</strong>
                        <span>Receive notifications for comments</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="privacyNotifications" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            
            <!-- Theme Switcher -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Theme Switcher</h3>
                        <p class="settings-card-subtitle">Choose your preferred theme</p>
                    </div>
                </div>
                
                <div class="theme-grid-settings">
                    <div class="theme-option-settings" data-theme="classic" onclick="applyTheme('classic')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #f5f5f5, #3b82f6);"></div>
                        <div class="theme-name-settings">Classic White</div>
                    </div>
                    <div class="theme-option-settings" data-theme="midnight" onclick="applyTheme('midnight')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #1e1e2e, #2d2d44);"></div>
                        <div class="theme-name-settings">Midnight</div>
                    </div>
                    <div class="theme-option-settings" data-theme="ultimate-dark" onclick="applyTheme('ultimate-dark')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #000000, #00f2ff);"></div>
                        <div class="theme-name-settings">Ultimate Dark</div>
                    </div>
                    <div class="theme-option-settings" data-theme="clean-white" onclick="applyTheme('clean-white')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #ffffff, #f0f0f0);"></div>
                        <div class="theme-name-settings">Clean White</div>
                    </div>
                    <div class="theme-option-settings" data-theme="forest" onclick="applyTheme('forest')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #1a3a2a, #2d5a3d);"></div>
                        <div class="theme-name-settings">Forest</div>
                    </div>
                    <div class="theme-option-settings" data-theme="matrix" onclick="applyTheme('matrix')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #000000, #00ff00);"></div>
                        <div class="theme-name-settings">Matrix</div>
                    </div>
                    <div class="theme-option-settings" data-theme="royal" onclick="applyTheme('royal')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #1a1a2e, #d4af37);"></div>
                        <div class="theme-name-settings">Royal Gold</div>
                    </div>
                    <div class="theme-option-settings" data-theme="deepsea" onclick="applyTheme('deepsea')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #0a192f, #112d4e);"></div>
                        <div class="theme-name-settings">Deep Sea</div>
                    </div>
                    <div class="theme-option-settings" data-theme="bloodmoon" onclick="applyTheme('bloodmoon')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #1a0505, #991b1b);"></div>
                        <div class="theme-name-settings">Blood Moon</div>
                    </div>
                    <div class="theme-option-settings" data-theme="lavender" onclick="applyTheme('lavender')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #2d1b4e, #7c3aed);"></div>
                        <div class="theme-name-settings">Lavender</div>
                    </div>
                    <div class="theme-option-settings" data-theme="arctic" onclick="applyTheme('arctic')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #e0f2f1, #26a69a);"></div>
                        <div class="theme-name-settings">Arctic</div>
                    </div>
                    <div class="theme-option-settings" data-theme="space" onclick="applyTheme('space')">
                        <div class="theme-preview-settings" style="background: linear-gradient(135deg, #0c0c1e, #4a148c);"></div>
                        <div class="theme-name-settings">Space</div>
                    </div>
                </div>
            </div>
            
            <!-- Logout Section -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Sign Out</h3>
                        <p class="settings-card-subtitle">Sign out of your account</p>
                    </div>
                </div>
                
                <a href="logout.php" class="btn-save-settings" style="background: linear-gradient(135deg, #ef4444, #dc2626); text-align: center; text-decoration: none; display: block;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ⚡ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Theme System
        function applyTheme(themeName) {
            document.body.setAttribute('data-theme', themeName);
            localStorage.setItem('techzevron_theme', themeName);
            
            // Update active class
            document.querySelectorAll('.theme-option-settings').forEach(opt => {
                opt.classList.remove('active');
            });
            document.querySelector(`.theme-option-settings[data-theme="${themeName}"]`)?.classList.add('active');
            
            showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
        }
        
        function loadTheme() {
            const savedTheme = localStorage.getItem('techzevron_theme') || 'classic';
            document.body.setAttribute('data-theme', savedTheme);
            
            // Update active class
            document.querySelectorAll('.theme-option-settings').forEach(opt => {
                opt.classList.remove('active');
            });
            document.querySelector(`.theme-option-settings[data-theme="${savedTheme}"]`)?.classList.add('active');
        }
        
        // Sidebar Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle?.addEventListener('click', openSidebar);
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);
        
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
        
        // Profile Form Submit
        document.getElementById('profileForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_profile');
            formData.append('name', document.getElementById('settingsName').value);
            formData.append('phone', document.getElementById('settingsPhone').value);
            formData.append('address', document.getElementById('settingsAddress').value);
            formData.append('country', document.getElementById('settingsCountry').value);
            formData.append('district', document.getElementById('settingsDistrict').value);
            formData.append('gender', '<?php echo htmlspecialchars($user['gender'] ?? ''); ?>');
            formData.append('theme', localStorage.getItem('techzevron_theme') || 'classic');
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message);
            });
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
    </script>
</body>
</html>
