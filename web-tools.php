<?php
require_once 'db.php';

$is_admin = function_exists('isAdmin') ? isAdmin() : false;
$is_logged_in = function_exists('isLoggedIn') ? isLoggedIn() : false;

$web_tools = [
    [
        'id' => 1,
        'title' => 'Live Digital Clock',
        'description' => 'Beautiful animated digital clock with multiple themes',
        'category' => 'clock',
        'image_url' => 'https://images.unsplash.com/photo-1563864712996-644a16f81b9f?w=400',
        'download_link' => '#'
    ],
    [
        'id' => 2,
        'title' => 'Modern Login Page',
        'description' => 'Beautiful login page template with animations',
        'category' => 'login',
        'image_url' => 'https://images.unsplash.com/photo-1616469829941-c7200edec809?w=400',
        'download_link' => '#'
    ],
    [
        'id' => 3,
        'title' => 'Responsive Navbar',
        'description' => 'Mobile-responsive navigation bar with hamburger menu',
        'category' => 'navbar',
        'image_url' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=400',
        'download_link' => '#'
    ],
    [
        'id' => 4,
        'title' => 'Animated Buttons',
        'description' => 'Collection of CSS buttons with hover effects',
        'category' => 'buttons',
        'image_url' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400',
        'download_link' => '#'
    ],
    [
        'id' => 5,
        'title' => 'Card Designs',
        'description' => 'Modern card UI designs for various purposes',
        'category' => 'cards',
        'image_url' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400',
        'download_link' => '#'
    ],
    [
        'id' => 6,
        'title' => 'Form Templates',
        'description' => 'Professional form designs with validation',
        'category' => 'forms',
        'image_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400',
        'download_link' => '#'
    ]
];

foreach ($web_tools as &$tool) {
    $tool['likes'] = rand(10, 100);
    $tool['comments'] = rand(0, 20);
    $tool['shares'] = rand(0, 10);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Tools - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-hero {
            text-align: center;
            padding: 60px 20px;
            background: var(--glass, rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(20px);
            border-radius: var(--radius, 16px);
            border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.1));
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .page-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary, #3b82f6), var(--accent, #8b5cf6), var(--primary, #3b82f6));
            background-size: 200% 100%;
            animation: gradientMove 3s linear infinite;
        }
        
        /* Web Tools title bounce animation */
        .bounce-title {
            display: inline-block;
            font-size: 42px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--text-primary, #ffffff), var(--primary, #3b82f6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: titleBounce 2s ease-in-out infinite;
        }
        
        .bounce-title i {
            display: inline-block;
            animation: iconBounce 1.5s ease-in-out infinite alternate;
        }

        @keyframes titleBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes iconBounce {
            0% { transform: rotate(0deg) scale(1); }
            100% { transform: rotate(15deg) scale(1.15); }
        }

        .page-hero p {
            color: var(--text-secondary, #a0aec0);
            font-size: 18px;
        }
        
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes rainbowMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: linear-gradient(135deg, var(--primary, #3b82f6), var(--accent, #8b5cf6));
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .tool-stats {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .tool-stat {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            color: var(--text-secondary, #a0aec0);
        }
        
        .tool-stat i {
            color: var(--primary, #3b82f6);
        }

        /* Sidebar Bar Fixed Position & Layout */
        .sidebar {
            position: fixed;
            top: 0;
            right: -360px;
            width: 320px;
            height: 100vh;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(25px);
            z-index: 1050;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
        }

        .sidebar.active {
            right: 0 !important;
        }

        .sidebar-logo-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
        }
        
        .sidebar-glow {
            position: absolute;
            top: -8px;
            left: -8px;
            width: calc(100% + 16px);
            height: calc(100% + 16px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff);
            background-size: 300% 300%;
            animation: rainbowMove 2s linear infinite;
            z-index: 0;
            filter: blur(8px);
            opacity: 0.8;
        }
        
        .sidebar-rainbow {
            position: absolute;
            top: -4px;
            left: -4px;
            width: calc(100% + 8px);
            height: calc(100% + 8px);
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
            border: 2px solid #fff;
        }
    </style>
</head>
<body data-theme="classic" oncontextmenu="return false;">
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="cursor: pointer;">
                <span></span><span></span><span></span>
            </div>
            
            <div class="nav-auth">
                <?php if ($is_logged_in): ?>
                    <span style="margin-right: 8px; color: var(--primary); font-size: 12px;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                    <a href="logout.php" class="btn-register">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Slide-out Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header" style="padding: 25px 20px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper">
                <div class="sidebar-glow"></div>
                <div class="sidebar-rainbow"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" class="sidebar-logo-img">
            </div>
            <a href="index.php" class="logo" style="font-size: 22px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: var(--text-secondary);">&times;</span>
        </div>
        <ul class="sidebar-menu" style="padding: 15px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="ai-prompts.php"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php" class="active"><i class="fas fa-tools"></i> Web Tools</a></li>
            <li><a href="#" onclick="openThemeModal(); return false;"><i class="fas fa-palette"></i> Themes</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="dashboard.php"><i class="fas fa-user"></i> My Profile</a></li>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <li><a href="index.php#admin-panel"><i class="fas fa-cog"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>
        <div class="sidebar-footer" style="padding: 20px; border-top: 1px solid var(--glass-border);">
            <?php if ($is_logged_in): ?>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
                <div class="theme-option" data-theme="pure-dark" onclick="applyTheme('pure-dark')">
                    <div class="theme-preview" style="background: linear-gradient(135deg, #0d0d0d, #1a1a1a);"></div>
                    <span>Pure Dark</span>
                </div>
                <div class="theme-option" data-theme="clean-white" onclick="applyTheme('clean-white')">
                    <div class="theme-preview" style="background: linear-gradient(135deg, #ffffff, #f0f0f0);"></div>
                    <span>Clean White</span>
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
            <!-- Page Hero with Animated Bounce Title -->
            <div class="page-hero reveal">
                <h1 class="bounce-title"><i class="fas fa-tools"></i> Web Tools</h1>
                <p>Ready-to-use web components, templates & snippets</p>
            </div>

            <!-- Category Filter -->
            <div class="category-filter reveal">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="clock">Clocks</button>
                <button class="filter-btn" data-category="login">Login Pages</button>
                <button class="filter-btn" data-category="navbar">Navigation</button>
                <button class="filter-btn" data-category="buttons">Buttons</button>
                <button class="filter-btn" data-category="cards">Cards</button>
                <button class="filter-btn" data-category="forms">Forms</button>
            </div>

            <?php if ($is_admin): ?>
            <div class="reveal" style="text-align: center; margin-bottom: 30px;">
                <button class="btn-submit" style="width: auto; padding: 14px 30px;" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add New Tool
                </button>
            </div>
            <?php endif; ?>

            <!-- Web Tools Grid -->
            <div class="card-grid" id="resourceGrid">
                <?php foreach($web_tools as $tool): ?>
                <div class="tool-card" data-category="<?php echo htmlspecialchars($tool['category']); ?>" id="tool-<?php echo $tool['id']; ?>">
                    <?php if ($is_admin): ?>
                    <div class="admin-actions">
                        <button class="edit-btn" onclick="editTool(<?php echo $tool['id']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteTool(<?php echo $tool['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($tool['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($tool['image_url']); ?>" alt="<?php echo htmlspecialchars($tool['title']); ?>" class="resource-image">
                    <?php else: ?>
                    <span class="tool-icon">🛠️</span>
                    <?php endif; ?>
                    
                    <span class="category-badge"><?php echo htmlspecialchars($tool['category']); ?></span>
                    <h3><?php echo htmlspecialchars($tool['title']); ?></h3>
                    <p><?php echo htmlspecialchars($tool['description']); ?></p>
                    
                    <?php if (!empty($tool['download_link'])): ?>
                    <a href="<?php echo htmlspecialchars($tool['download_link']); ?>" class="btn-download" target="_blank">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <?php else: ?>
                    <a href="#" class="btn-download" onclick="alert('Download coming soon!')">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <?php endif; ?>
                    
                    <div class="tool-stats">
                        <span class="tool-stat"><i class="fas fa-heart"></i> <?php echo $tool['likes']; ?></span>
                        <span class="tool-stat"><i class="fas fa-share-alt"></i> <?php echo $tool['shares']; ?></span>
                        <span class="tool-stat"><i class="fas fa-comment"></i> <?php echo $tool['comments']; ?></span>
                    </div>
                    
                    <div class="engagement-section">
                        <button class="engagement-btn like-btn" onclick="toggleLike(<?php echo $tool['id']; ?>)">
                            <i class="far fa-heart"></i>
                            <span class="like-count"><?php echo $tool['likes']; ?></span>
                        </button>
                        <button class="engagement-btn share-btn" onclick="toggleShare(<?php echo $tool['id']; ?>)">
                            <i class="far fa-share-square"></i>
                            <span class="share-count"><?php echo $tool['shares']; ?></span>
                        </button>
                        <button class="engagement-btn comment-btn" onclick="toggleComment(<?php echo $tool['id']; ?>)">
                            <i class="far fa-comment"></i>
                            <span class="comment-count"><?php echo $tool['comments']; ?></span>
                        </button>
                    </div>
                    
                    <div class="comment-section" id="comment-section-<?php echo $tool['id']; ?>" style="display: none;">
                        <div class="comment-list" id="comment-list-<?php echo $tool['id']; ?>">
                            <p class="no-comments">No comments yet.</p>
                        </div>
                        <?php if ($is_logged_in): ?>
                        <div class="comment-form">
                            <textarea id="comment-text-<?php echo $tool['id']; ?>" placeholder="Write a comment..." rows="2"></textarea>
                            <button class="btn-submit" style="padding: 8px 16px; font-size: 12px; width: auto;" onclick="submitComment(<?php echo $tool['id']; ?>)">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                        <?php else: ?>
                        <p class="login-prompt"><a href="login.php">Login</a> to comment</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="developed-by">
                <p>Developed by <a href="#">P. Diloshan</a></p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ⚡ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.shiftKey && e.key === 'J') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 's') ||
                (e.ctrlKey && e.key === 'p')) {
                e.preventDefault();
                return false;
            }
        });

        // Fixed Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                if (menuToggle) menuToggle.classList.toggle('active');
            }
        }
        
        function applyTheme(themeName) {
            document.body.setAttribute('data-theme', themeName);
            localStorage.setItem('techzevron_theme', themeName);
            closeThemeModal();
        }
        
        function loadTheme() {
            const savedTheme = localStorage.getItem('techzevron_theme') || 'classic';
            document.body.setAttribute('data-theme', savedTheme);
        }
        
        function openThemeModal() {
            document.getElementById('themeModal').style.display = 'flex';
        }
        
        function closeThemeModal() {
            document.getElementById('themeModal').style.display = 'none';
        }

        const filterBtns = document.querySelectorAll('.filter-btn');
        const toolCards = document.querySelectorAll('.tool-card');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const category = btn.dataset.category;
                
                toolCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        function toggleLike(toolId) {
            const likeBtn = document.querySelector(`#tool-${toolId} .like-btn`);
            const likeCount = likeBtn.querySelector('.like-count');
            const heartIcon = likeBtn.querySelector('i');
            
            if (likeBtn.classList.contains('liked')) {
                likeBtn.classList.remove('liked');
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            } else {
                likeBtn.classList.add('liked');
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            }
        }
        
        function toggleShare(toolId) {
            const shareBtn = document.querySelector(`#tool-${toolId} .share-btn`);
            const shareCount = shareBtn.querySelector('.share-count');
            shareCount.textContent = parseInt(shareCount.textContent) + 1;
            alert('Shared successfully!');
        }
        
        function toggleComment(toolId) {
            const commentSection = document.getElementById('comment-section-' + toolId);
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        }
        
        function submitComment(toolId) {
            const commentText = document.getElementById('comment-text-' + toolId).value.trim();
            if (!commentText) {
                alert('Please write a comment');
                return;
            }
            alert('Comment submitted!');
            document.getElementById('comment-text-' + toolId).value = '';
        }
        
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
        
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
            reveal();
        });
    </script>
</body>
</html>