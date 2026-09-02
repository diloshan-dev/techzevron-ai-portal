<?php
require_once 'db.php';

$ai_resources = getAllAIResources();
$is_admin = isAdmin();
$is_logged_in = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechZevron - AI Resources</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Fixed Sidebar Position & Smooth Transition */
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

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Platform Features Section (Middle Content) */
        .platform-features {
            margin-top: 60px;
            margin-bottom: 40px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .feature-box {
            background: var(--glass, rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.1));
            border-radius: var(--radius, 16px);
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-8px);
            border-color: var(--primary, #3b82f6);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            font-size: 36px;
            color: var(--primary, #3b82f6);
            margin-bottom: 15px;
        }

        .feature-box h3 {
            font-size: 18px;
            color: var(--text-primary, #ffffff);
            margin-bottom: 10px;
        }

        .feature-box p {
            font-size: 13px;
            color: var(--text-secondary, #a0aec0);
            margin: 0;
        }

        /* Owner Section Styles */
        .about-owner {
            background: var(--glass, rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.1));
            border-radius: var(--radius, 16px);
            padding: 40px;
            text-align: center;
            margin-top: 80px; /* Pushed further down */
            position: relative;
            overflow: hidden;
        }
        
        .about-owner::before {
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
        
        #typewriter-h1::after, #typewriter-p::after {
            content: "|";
            animation: blink 0.7s infinite;
            margin-left: 5px;
            color: var(--primary, #3b82f6);
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .finished-typing::after {
            display: none;
        }
        
        .owner-photo-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        
        .owner-rainbow-border {
            position: absolute;
            top: -10px;
            left: -10px;
            width: calc(100% + 20px);
            height: calc(100% + 20px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #0000ff, #ff0000);
            background-size: 300% 300%;
            animation: rainbowMove 3s linear infinite;
            z-index: 0;
        }
        
        @keyframes rainbowMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .owner-breathing-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            width: calc(100% + 10px);
            height: calc(100% + 10px);
            border-radius: 50%;
            border: 3px solid var(--primary, #3b82f6);
            animation: breathing 3s ease-in-out infinite;
            z-index: 1;
        }
        
        @keyframes breathing {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
        
        .owner-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 2;
            border: 3px solid var(--bg-dark, #0f172a);
        }
        
        .about-owner h2 {
            font-size: 24px;
            color: var(--text-primary, #ffffff);
            margin-bottom: 8px;
        }
        
        .about-owner .title {
            color: var(--primary, #3b82f6);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .owner-bio {
            color: var(--text-secondary, #a0aec0);
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .owner-social {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .page-header h1 {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .tool-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border-color: var(--primary, #3b82f6);
            transition: all 0.3s ease;
        }
        
        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm, 8px);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition, 0.3s ease);
        }
        
        .social-btn.youtube-btn {
            background: linear-gradient(45deg, #ff0000, #ff4444);
            color: white;
        }
        
        .social-btn.github-btn {
            background: #333;
            color: white;
        }
        
        .social-btn.whatsapp-btn {
            background: linear-gradient(45deg, #25d366, #128c7e);
            color: white;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        
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
            
            <!-- Hamburger Menu Button -->
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="cursor: pointer;">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-auth">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="margin-right: 15px; color: var(--primary); font-weight: 600;">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
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
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper">
                <div class="sidebar-glow"></div>
                <div class="sidebar-rainbow"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" class="sidebar-logo-img">
            </div>
            <a href="index.php" class="logo" style="font-size: 24px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" id="sidebarClose" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer; color: var(--text-secondary);">&times;</span>
        </div>

        <ul class="sidebar-menu" style="padding: 15px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="ai-prompts.php"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php"><i class="fas fa-tools"></i> Web Tools</a></li>
            <li><a href="#" onclick="openThemeModal(); return false;"><i class="fas fa-palette"></i> Themes</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>

            <?php if ($is_logged_in): ?>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a></li>
            <?php endif; ?>

            <?php if ($is_admin): ?>
                <li class="menu-divider" style="border-top: 1px solid var(--glass-border); margin: 10px 0;"></li>
                <li><a href="admin.php" style="color: var(--accent);"><i class="fas fa-user-shield"></i> Admin Panel</a></li>
            <?php endif; ?>

            <li class="menu-divider" style="border-top: 1px solid var(--glass-border); margin: 10px 0;"></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>

        <div class="sidebar-footer" style="padding: 20px; border-top: 1px solid var(--glass-border);">
            <?php if ($is_logged_in): ?>
                <a href="logout.php" class="btn-logout" style="display: block; width: 100%; text-align: center; padding: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="login.php" style="display: block; width: 100%; text-align: center; padding: 12px; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" style="display: block; width: 100%; text-align: center; padding: 12px; background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </div>
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
        <div class="container">
            <!-- Hero Section -->
            <div class="page-header reveal">
                <h1 id="typewriter-h1"></h1>
                <p id="typewriter-p" class="typing-text"></p>
            </div>

            <!-- Category Filter -->
            <div class="category-filter reveal">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="ai">AI</button>
                <button class="filter-btn" data-category="coding">Coding</button>
                <button class="filter-btn" data-category="design">Design</button>
            </div>

            <!-- Admin Add Button -->
            <?php if ($is_admin): ?>
            <div class="reveal" style="text-align: center; margin-bottom: 30px;">
                <button class="btn-submit" style="width: auto; padding: 14px 30px;" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add New Prompt
                </button>
            </div>
            <?php endif; ?>

            <!-- AI Resources Grid -->
            <div class="card-grid" id="resourceGrid">
                <?php foreach($ai_resources as $resource): 
                    $like_count = getLikeCount($resource['id']);
                    $comment_count = getCommentCount($resource['id']);
                    $report_count = getReportCount($resource['id']);
                    $user_liked = $is_logged_in ? userHasLiked($resource['id'], $_SESSION['user_id'] ?? 0) : false;
                ?>
                <div class="tool-card" data-category="<?php echo htmlspecialchars($resource['category']); ?>" id="resource-<?php echo $resource['id']; ?>">
                    <?php if ($is_admin): ?>
                    <div class="admin-actions" style="position: absolute; top: 10px; right: 10px; display: flex; gap: 5px; z-index: 10;">
                        <button class="edit-btn" onclick="editResource(<?php echo $resource['id']; ?>, '<?php echo addslashes($resource['title']); ?>', '<?php echo addslashes($resource['description']); ?>', '<?php echo addslashes($resource['prompt_text']); ?>', '<?php echo $resource['category']; ?>', '<?php echo addslashes($resource['image_url']); ?>')" style="background: var(--primary); color: white; border: none; padding: 5px 10px; border-radius: 5px;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteResource(<?php echo $resource['id']; ?>)" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($resource['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($resource['image_url']); ?>" alt="<?php echo htmlspecialchars($resource['title']); ?>" class="resource-image">
                    <?php else: ?>
                    <span class="tool-icon"><?php 
                        if($resource['category'] == 'ai') echo '🤖';
                        elseif($resource['category'] == 'design') echo '🎨';
                        else echo '💻';
                    ?></span>
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                    <p><?php echo htmlspecialchars($resource['description']); ?></p>
                    
                    <a href="#" class="btn-download" onclick="alert('Download coming soon!')">
                        <i class="fas fa-download"></i> Download Now
                    </a>
                    
                    <div class="engagement-section">
                        <button class="engagement-btn like-btn <?php echo $user_liked ? 'liked' : ''; ?>" onclick="toggleLike(<?php echo $resource['id']; ?>)">
                            <i class="<?php echo $user_liked ? 'fas' : 'far'; ?> fa-heart"></i>
                            <span class="like-count"><?php echo $like_count; ?></span>
                        </button>
                        <button class="engagement-btn comment-btn" onclick="toggleComment(<?php echo $resource['id']; ?>)">
                            <i class="far fa-comment"></i>
                            <span class="comment-count"><?php echo $comment_count; ?></span>
                        </button>
                        <?php if ($is_admin): ?>
                        <button class="engagement-btn likes-view-btn" onclick="viewLikes(<?php echo $resource['id']; ?>)">
                            <i class="fas fa-users"></i> View Likes
                        </button>
                        <?php endif; ?>
                        <?php if ($is_logged_in): ?>
                        <button class="engagement-btn report-btn" onclick="toggleReport(<?php echo $resource['id']; ?>)">
                            <i class="far fa-flag"></i>
                            <span class="report-count"><?php echo $report_count > 0 ? $report_count : ''; ?></span>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($is_admin): ?>
                    <div class="admin-only-tools" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--glass-border);">
                        <p style="font-size: 11px; color: var(--accent);"><i class="fas fa-user-secret"></i> Admin Mode</p>
                        <button class="btn-sm" onclick="toggleComment(<?php echo $resource['id']; ?>)" style="background: rgba(139, 92, 246, 0.2); color: #a855f7; border: 1px solid #a855f7; font-size: 10px; padding: 5px 10px; border-radius: 4px;">
                            <i class="fas fa-magic"></i> Add Fake Comment
                        </button>
                        <button class="btn-sm" onclick="addFakeLikes(<?php echo $resource['id']; ?>)" style="background: rgba(236, 72, 153, 0.2); color: #ec4899; border: 1px solid #ec4899; font-size: 10px; padding: 5px 10px; border-radius: 4px; margin-left: 5px;">
                            <i class="fas fa-heart"></i> Add Fake Likes
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Comment Section -->
                    <div class="comment-section" id="comment-section-<?php echo $resource['id']; ?>" style="display: none;">
                        <div class="comment-list" id="comment-list-<?php echo $resource['id']; ?>"></div>
                        <?php if ($is_logged_in): ?>
                        <div class="comment-form">
                            <textarea id="comment-text-<?php echo $resource['id']; ?>" placeholder="Write a comment..." rows="2"></textarea>
                            <button class="btn-submit" style="padding: 8px 16px; font-size: 12px; width: auto;" onclick="submitComment(<?php echo $resource['id']; ?>)">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                        <?php else: ?>
                        <p class="login-prompt"><a href="login.php">Login</a> to comment</p>
                        <?php endif; ?>
                        
                        <!-- Admin: Fake Comment Form -->
                        <?php if ($is_admin): ?>
                        <div class="fake-comment-form">
                            <h5><i class="fas fa-user-secret"></i> Add Fake Comment</h5>
                            <input type="text" id="fake-name-<?php echo $resource['id']; ?>" placeholder="Name" class="form-control">
                            <input type="email" id="fake-email-<?php echo $resource['id']; ?>" placeholder="Email" class="form-control">
                            <textarea id="fake-comment-<?php echo $resource['id']; ?>" placeholder="Fake comment..." rows="2" class="form-control"></textarea>
                            <button class="btn-submit" style="padding: 8px 16px; font-size: 12px; width: auto; background: linear-gradient(135deg, #8b5cf6, #a855f7);" onclick="addFakeComment(<?php echo $resource['id']; ?>)">
                                <i class="fas fa-magic"></i> Add Fake
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- YouTube Dashboard (Admin Only) -->
            <?php if ($is_admin): ?>
            <div id="youtube-dashboard" class="youtube-dashboard reveal">
                <?php 
                $yt_stats = getYouTubeChannelStats();
                $most_liked = getMostLikedVideo();
                ?>
                <?php if ($yt_stats): ?>
                <div class="youtube-header">
                    <div class="channel-logo-container">
                        <?php if (!empty($yt_stats['thumbnail'])): ?>
                        <img src="<?php echo htmlspecialchars($yt_stats['thumbnail']); ?>" alt="Channel Logo" class="channel-logo">
                        <?php else: ?>
                        <img src="https://www.youtube.com/s/channel/UC2Xvqo5QjpeR1xp9CKsPMWA" alt="YouTube" class="channel-logo">
                        <?php endif; ?>
                        <div class="logo-ring"></div>
                    </div>
                    <div class="channel-info">
                        <h2><?php echo htmlspecialchars($yt_stats['title']); ?></h2>
                        <p><?php echo htmlspecialchars($yt_stats['description'] ?? ''); ?></p>
                    </div>
                </div>
                
                <div class="youtube-stats">
                    <div class="yt-stat-card">
                        <i class="fab fa-youtube"></i>
                        <div class="stat-number"><?php echo $yt_stats['subscribers']; ?></div>
                        <div class="stat-label">Subscribers</div>
                    </div>
                    <div class="yt-stat-card">
                        <i class="fas fa-eye"></i>
                        <div class="stat-number"><?php echo $yt_stats['views']; ?></div>
                        <div class="stat-label">Total Views</div>
                    </div>
                    <div class="yt-stat-card">
                        <i class="fas fa-video"></i>
                        <div class="stat-number"><?php echo $yt_stats['videos']; ?></div>
                        <div class="stat-label">Total Videos</div>
                    </div>
                </div>
                
                <?php if ($most_liked): ?>
                <div class="video-highlight">
                    <h3><i class="fas fa-fire"></i> Most Popular Video</h3>
                    <div class="popular-video">
                        <div class="video-thumbnail">
                            <?php if (!empty($most_liked['thumbnail'])): ?>
                            <img src="<?php echo htmlspecialchars($most_liked['thumbnail']); ?>" alt="<?php echo htmlspecialchars($most_liked['title']); ?>">
                            <?php endif; ?>
                            <span class="most-popular-badge">🔥 Most Popular</span>
                        </div>
                        <div class="video-details">
                            <h4><?php echo htmlspecialchars($most_liked['title']); ?></h4>
                            <div class="video-stats">
                                <span><i class="fas fa-heart"></i> <?php echo $most_liked['likes']; ?> likes</span>
                                <span><i class="fas fa-eye"></i> <?php echo $most_liked['views']; ?> views</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="alert alert-error">Failed to load YouTube data. Check API key.</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Admin Stats -->
            <?php if ($is_admin): ?>
            <div id="admin-panel" class="dashboard-grid reveal">
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
                $stmt->execute();
                $totalUsers = $stmt->fetch()['total'];
                $all_reports = getReports();
                ?>
                <div class="stat-card">
                    <div class="number"><?php echo $totalUsers; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo count($ai_resources); ?></div>
                    <div class="label">AI Resources</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo count($all_reports); ?></div>
                    <div class="label">Reports</div>
                </div>
                <div class="stat-card">
                    <div class="number">🟢</div>
                    <div class="label">Online</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Middle Feature Content (Inserted between content and owner info) -->
            <div class="platform-features reveal">
                <div style="text-align: center;">
                    <h2 style="font-size: 26px; font-weight: 700;">Why Choose <span style="color: var(--primary);">TechZevron</span></h2>
                    <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Explore powerful tools, curated prompts, and modern templates designed for creators and developers.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <h3>Lightning Fast</h3>
                        <p>Optimized resources designed for high efficiency and smooth workflow integration.</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Verified Prompts</h3>
                        <p>Pre-tested AI prompts engineered for ChatGPT, Midjourney, and Claude.</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon"><i class="fas fa-code"></i></div>
                        <h3>Modern Code Snippets</h3>
                        <p>Clean, responsive CSS & Web components ready for production use.</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon"><i class="fas fa-sync-alt"></i></div>
                        <h3>Regular Updates</h3>
                        <p>Fresh resources, innovative tools, and prompts added consistently.</p>
                    </div>
                </div>
            </div>

            <!-- Owner Info with Animations (Pushed Further Down) -->
            <div class="about-owner reveal">
                <div class="owner-photo-wrapper">
                    <div class="owner-rainbow-border"></div>
                    <div class="owner-breathing-ring"></div>
                    <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="P. Diloshan" class="owner-photo">
                </div>
                <h2>Developed by P. Diloshan</h2>
                <p class="title">Full Stack Web Developer</p>
                <p class="owner-bio">Passionate developer creating amazing web experiences</p>
                <div class="owner-social">
                    <a href="https://www.youtube.com/channel/UC2Xvqo5QjpeR1xp9CKsPMWA?sub_confirmation=1" target="_blank" class="social-btn youtube-btn">
                        <i class="fab fa-youtube"></i> Subscribe
                    </a>
                    <a href="https://github.com/techzevron" target="_blank" class="social-btn github-btn">
                        <i class="fab fa-github"></i> GitHub
                    </a>
                    <a href="https://wa.me/94721617577" target="_blank" class="social-btn whatsapp-btn">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Modal -->
    <?php if ($is_admin): ?>
    <div class="modal" id="resourceModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Prompt</h2>
            <form id="resourceForm">
                <input type="hidden" id="resourceId">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter description" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="prompt_text">Prompt Text</label>
                    <textarea id="prompt_text" name="prompt_text" placeholder="Enter prompt text" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="ai">AI</option>
                        <option value="coding">Coding</option>
                        <option value="design">Design</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" id="image_url" name="image_url" placeholder="https://...">
                </div>
                <button type="submit" class="btn-submit" id="modalSubmitBtn">
                    <i class="fas fa-plus"></i> Add Resource
                </button>
            </form>
        </div>
    </div>

    <!-- Likes Modal -->
    <div class="modal" id="likesModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeLikesModal()">&times;</span>
            <h2><i class="fas fa-heart"></i> Who Liked This</h2>
            <div class="likes-list" id="likesList"></div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal" id="reportModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeReportModal()">&times;</span>
            <h2><i class="fas fa-flag"></i> Report Content</h2>
            <input type="hidden" id="reportResourceId">
            <div class="form-group">
                <label for="reportReason">Reason</label>
                <select id="reportReason" class="form-control">
                    <option value="Inappropriate content">Inappropriate content</option>
                    <option value="Spam">Spam</option>
                    <option value="Misleading information">Misleading information</option>
                    <option value="Copyright violation">Copyright violation</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button class="btn-submit" style="background: #ef4444;" onclick="submitReport()">
                <i class="fas fa-paper-plane"></i> Submit Report
            </button>
        </div>
    </div>
    <?php endif; ?>

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
        
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        });

        // Toggle Sidebar Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                if (menuToggle) menuToggle.classList.toggle('active');
                
                if (sidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
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
        
        // Category Filter
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
                        card.style.animation = 'fadeInUp 0.5s ease-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        function openAddModal() {
            document.getElementById('resourceModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Add New Prompt';
            document.getElementById('resourceId').value = '';
            document.getElementById('resourceForm').reset();
            document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Resource';
        }
        
        function closeModal() {
            document.getElementById('resourceModal').style.display = 'none';
        }
        
        function editResource(id, title, description, prompt_text, category, image_url) {
            document.getElementById('resourceModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Edit Prompt';
            document.getElementById('resourceId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            document.getElementById('prompt_text').value = prompt_text;
            document.getElementById('category').value = category;
            document.getElementById('image_url').value = image_url;
            document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Resource';
        }
        
        document.getElementById('resourceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('resourceId').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('action', 'update_resource');
                formData.append('id', id);
            } else {
                formData.append('action', 'add_resource');
            }
            
            formData.append('title', document.getElementById('title').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('prompt_text', document.getElementById('prompt_text').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('image_url', document.getElementById('image_url').value);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeModal();
                    location.reload();
                }
            });
        });
        
        function deleteResource(id) {
            if (confirm('Are you sure you want to delete this resource?')) {
                const formData = new FormData();
                formData.append('action', 'delete_resource');
                formData.append('id', id);
                
                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
        
        function toggleLike(resourceId) {
            const formData = new FormData();
            formData.append('action', 'toggle_like');
            formData.append('resource_id', resourceId);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeBtn = document.querySelector(`[onclick="toggleLike(${resourceId})"]`);
                    const likeCount = likeBtn.querySelector('.like-count');
                    const heartIcon = likeBtn.querySelector('i');
                    
                    likeCount.textContent = data.like_count;
                    if (data.liked) {
                        likeBtn.classList.add('liked');
                        heartIcon.classList.remove('far');
                        heartIcon.classList.add('fas');
                    } else {
                        likeBtn.classList.remove('liked');
                        heartIcon.classList.remove('fas');
                        heartIcon.classList.add('far');
                    }
                } else {
                    alert(data.message);
                }
            });
        }
        
        function toggleComment(resourceId) {
            const commentSection = document.getElementById('comment-section-' + resourceId);
            if (commentSection.style.display === 'none') {
                commentSection.style.display = 'block';
                loadComments(resourceId);
            } else {
                commentSection.style.display = 'none';
            }
        }

        function loadComments(resourceId) {
            const formData = new FormData();
            formData.append('action', 'get_comments');
            formData.append('resource_id', resourceId);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentList = document.getElementById('comment-list-' + resourceId);
                    if (data.comments.length === 0) {
                        commentList.innerHTML = '<p class="no-comments">No comments yet. Be the first!</p>';
                    } else {
                        commentList.innerHTML = data.comments.map(comment => `
                            <div class="comment-item">
                                <div class="comment-avatar">
                                    ${comment.profile_image ? 
                                        `<img src="${comment.profile_image}" alt="${comment.user_name || comment.user_name}">` :
                                        '<i class="fas fa-user"></i>'
                                    }
                                </div>
                                <div class="comment-content">
                                    <div class="comment-header">
                                        <strong>${comment.user_name || 'User'} ${comment.is_fake ? '<span class="fake-badge"><i class="fas fa-user-secret"></i></span>' : ''}</strong>
                                        <span class="comment-time">${new Date(comment.created_at).toLocaleDateString()}</span>
                                    </div>
                                    <p>${comment.comment_text}</p>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            });
        }
        
        function submitComment(resourceId) {
            const commentText = document.getElementById('comment-text-' + resourceId).value.trim();
            
            if (!commentText) {
                alert('Please write a comment');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'add_comment');
            formData.append('resource_id', resourceId);
            formData.append('comment_text', commentText);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('comment-text-' + resourceId).value = '';
                    const commentBtn = document.querySelector(`[onclick="toggleComment(${resourceId})"]`);
                    const commentCount = commentBtn.querySelector('.comment-count');
                    commentCount.textContent = data.comment_count;
                    loadComments(resourceId);
                } else {
                    alert(data.message);
                }
            });
        }
        
        function addFakeComment(resourceId) {
            const name = document.getElementById('fake-name-' + resourceId).value.trim();
            const email = document.getElementById('fake-email-' + resourceId).value.trim();
            const comment = document.getElementById('fake-comment-' + resourceId).value.trim();
            
            if (!name || !email || !comment) {
                alert('Please fill all fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'add_fake_comment');
            formData.append('resource_id', resourceId);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('comment_text', comment);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    document.getElementById('fake-name-' + resourceId).value = '';
                    document.getElementById('fake-email-' + resourceId).value = '';
                    document.getElementById('fake-comment-' + resourceId).value = '';
                    const commentBtn = document.querySelector(`[onclick="toggleComment(${resourceId})"]`);
                    const commentCount = commentBtn.querySelector('.comment-count');
                    commentCount.textContent = data.comment_count;
                    loadComments(resourceId);
                }
            });
        }
        
        function viewLikes(resourceId) {
            document.getElementById('likesModal').style.display = 'flex';
            
            const formData = new FormData();
            formData.append('action', 'get_likes');
            formData.append('resource_id', resourceId);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const likesList = document.getElementById('likesList');
                if (data.success && data.likes.length > 0) {
                    likesList.innerHTML = data.likes.map(like => `
                        <div class="like-item">
                            <div class="like-avatar">
                                ${like.profile_image ? 
                                    `<img src="${like.profile_image}" alt="${like.user_name}">` :
                                    '<i class="fas fa-user"></i>'
                                }
                            </div>
                            <div class="like-info">
                                <strong>${like.user_name || 'Unknown'}</strong>
                                <span>${like.user_email || ''}</span>
                            </div>
                            <span class="like-date">${new Date(like.created_at).toLocaleDateString()}</span>
                        </div>
                    `).join('');
                } else {
                    likesList.innerHTML = '<p class="no-likes">No likes yet</p>';
                }
            });
        }
        
        function closeLikesModal() {
            document.getElementById('likesModal').style.display = 'none';
        }
        
        function toggleReport(resourceId) {
            document.getElementById('reportModal').style.display = 'flex';
            document.getElementById('reportResourceId').value = resourceId;
        }
        
        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
        }
        
        function submitReport() {
            const resourceId = document.getElementById('reportResourceId').value;
            const reason = document.getElementById('reportReason').value;
            
            const formData = new FormData();
            formData.append('action', 'add_report');
            formData.append('resource_id', resourceId);
            formData.append('reason', reason);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeReportModal();
                }
            });
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        const h1Text = "AI Resource Dashboard";
        const pText = "Unlock premium prompts & tools";
        let h1Index = 0;
        let pIndex = 0;

        function typeWriter() {
            if (h1Index < h1Text.length) {
                document.getElementById("typewriter-h1").innerHTML += h1Text.charAt(h1Index);
                h1Index++;
                setTimeout(typeWriter, 70);
            } 
            else if (pIndex < pText.length) {
                document.getElementById("typewriter-p").innerHTML += pText.charAt(pIndex);
                pIndex++;
                setTimeout(typeWriter, 50);
            }
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
            typeWriter();
            loadTheme();
            reveal();
        });
    </script>
</body>
</html>