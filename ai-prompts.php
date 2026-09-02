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
    <title>AI Prompts - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Page specific styles */
        .page-hero {
            text-align: center;
            padding: 60px 20px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
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
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s linear infinite;
        }
        
        .page-hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--text-primary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .page-hero p {
            color: var(--text-secondary);
            font-size: 18px;
        }
        
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
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
                <?php if ($is_logged_in): ?>
                    <span style="margin-right: 8px; color: var(--primary); font-size: 12px;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
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
            <!-- Tech Zevron Channel Logo with Enhanced Moving Border -->
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
            <li><a href="ai-prompts.php" class="active"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php"><i class="fas fa-tools"></i> Web Tools</a></li>
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
        <div class="sidebar-footer">
            <?php if ($is_logged_in): ?>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login-sidebar"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn-register-sidebar"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
            <!-- Page Hero -->
            <div class="page-hero reveal">
                <h1><i class="fas fa-robot"></i> AI Prompts</h1>
                <p>Premium AI prompts for ChatGPT, Midjourney & more</p>
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

            <!-- AI Prompts Grid -->
            <div class="card-grid" id="resourceGrid">
                <?php foreach($ai_resources as $resource): 
                    $like_count = getLikeCount($resource['id']);
                    $comment_count = getCommentCount($resource['id']);
                    $share_count = getShareCount($resource['id']);
                    $report_count = getReportCount($resource['id']);
                    $user_liked = $is_logged_in ? userHasLiked($resource['id'], $_SESSION['user_id'] ?? 0) : false;
                ?>
                <div class="tool-card" data-category="<?php echo htmlspecialchars($resource['category']); ?>" id="resource-<?php echo $resource['id']; ?>">
                    <?php if ($is_admin): ?>
                    <div class="admin-actions">
                        <button class="edit-btn" onclick="editResource(<?php echo $resource['id']; ?>, '<?php echo addslashes($resource['title']); ?>', '<?php echo addslashes($resource['description']); ?>', '<?php echo addslashes($resource['prompt_text']); ?>', '<?php echo $resource['category']; ?>', '<?php echo addslashes($resource['image_url']); ?>', '<?php echo addslashes($resource['download_link'] ?? ''); ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteResource(<?php echo $resource['id']; ?>)">
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
                    
                    <span class="category-badge"><?php echo htmlspecialchars($resource['category']); ?></span>
                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                    <p><?php echo htmlspecialchars($resource['description']); ?></p>
                    
                    <?php if (!empty($resource['download_link'])): ?>
                        <a href="<?php echo htmlspecialchars($resource['download_link']); ?>" class="btn-download" target="_blank">
                        <i class="fas fa-download"></i> Download Now
                    </a>
                    <?php else: ?>
                    <a href="#" class="btn-download" onclick="alert('Download coming soon!')">
                        <i class="fas fa-download"></i> Download Now
                    </a>
                    <?php endif; ?>
                    
                    <div class="engagement-section">
                        <button class="engagement-btn like-btn <?php echo $user_liked ? 'liked' : ''; ?>" onclick="toggleLike(<?php echo $resource['id']; ?>)">
                            <i class="<?php echo $user_liked ? 'fas' : 'far'; ?> fa-heart"></i>
                            <span class="like-count"><?php echo $like_count; ?></span>
                        </button>
                        <button class="engagement-btn share-btn" onclick="toggleShare(<?php echo $resource['id']; ?>)">
                            <i class="far fa-share-square"></i>
                            <span class="share-count"><?php echo $share_count; ?></span>
                        </button>
                        <button class="engagement-btn comment-btn" onclick="toggleComment(<?php echo $resource['id']; ?>)">
                            <i class="far fa-comment"></i>
                            <span class="comment-count"><?php echo $comment_count; ?></span>
                        </button>
                        <?php if ($is_admin): ?>
                        <button class="engagement-btn likes-view-btn" onclick="viewLikes(<?php echo $resource['id']; ?>)">
                            <i class="fas fa-users"></i>
                        </button>
                        <button class="engagement-btn shares-view-btn" onclick="viewShares(<?php echo $resource['id']; ?>)">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <?php endif; ?>
                        <?php if ($is_logged_in): ?>
                        <button class="engagement-btn report-btn" onclick="toggleReport(<?php echo $resource['id']; ?>)">
                            <i class="far fa-flag"></i>
                            <span class="report-count"><?php echo $report_count > 0 ? $report_count : ''; ?></span>
                        </button>
                        <?php endif; ?>
                    </div>
                    
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
                        
                        <!-- Admin: Fake Like Form -->
                        <div class="fake-like-form">
                            <h5><i class="fas fa-heart-broken"></i> Add Fake Like</h5>
                            <input type="text" id="fake-like-name-<?php echo $resource['id']; ?>" placeholder="Name" class="form-control">
                            <input type="email" id="fake-like-email-<?php echo $resource['id']; ?>" placeholder="Email" class="form-control">
                            <input type="text" id="fake-like-picture-<?php echo $resource['id']; ?>" placeholder="Profile Picture URL" class="form-control">
                            <input type="datetime-local" id="fake-like-timestamp-<?php echo $resource['id']; ?>" class="form-control">
                            <button class="btn-submit" style="padding: 8px 16px; font-size: 12px; width: auto; background: linear-gradient(135deg, #ec4899, #f43f5e);" onclick="addFakeLike(<?php echo $resource['id']; ?>)">
                                <i class="fas fa-magic"></i> Add Fake Like
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Empty State -->
            <?php if (empty($ai_resources)): ?>
            <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-robot" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-secondary);">No AI Prompts Yet</h3>
                <p style="color: var(--text-muted);">Check back soon for new prompts!</p>
            </div>
            <?php endif; ?>
            
            <!-- Developed By Section -->
            <div class="developed-by">
                <p>Developed by <a href="#">P. Diloshan</a></p>
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
                <div class="form-group">
                    <label for="download_link">Download Link</label>
                    <input type="text" id="download_link" name="download_link" placeholder="https://...">
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

    <!-- Shares Modal -->
    <div class="modal" id="sharesModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeSharesModal()">&times;</span>
            <h2><i class="fas fa-share"></i> Who Shared This</h2>
            <div class="shares-list" id="sharesList"></div>
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
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable keyboard dev tools
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
        
        // Theme System
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
        
        // Modal Functions
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
        
        function editResource(id, title, description, prompt_text, category, image_url, download_link) {
            document.getElementById('resourceModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Edit Prompt';
            document.getElementById('resourceId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            document.getElementById('prompt_text').value = prompt_text;
            document.getElementById('category').value = category;
            document.getElementById('image_url').value = image_url;
            document.getElementById('download_link').value = download_link || '';
            document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Resource';
        }
        
        // Form Submit
        document.getElementById('resourceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('resourceId').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('action', 'update_resource_with_download');
                formData.append('id', id);
            } else {
                formData.append('action', 'add_resource_with_download');
            }
            
            formData.append('title', document.getElementById('title').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('prompt_text', document.getElementById('prompt_text').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('image_url', document.getElementById('image_url').value);
            formData.append('download_link', document.getElementById('download_link').value);
            
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
        
        // Delete Resource
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
        
        // Like Function
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
        
        // Share Function
        function toggleShare(resourceId) {
            const formData = new FormData();
            formData.append('action', 'add_share');
            formData.append('resource_id', resourceId);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const shareBtn = document.querySelector(`[onclick="toggleShare(${resourceId})"]`);
                    const shareCount = shareBtn.querySelector('.share-count');
                    shareCount.textContent = data.share_count;
                    alert('Shared successfully!');
                } else {
                    alert(data.message);
                }
            });
        }
        
        // Comment Functions
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
                                        ${data.is_admin ? `<button class="btn-delete-comment" onclick="deleteComment(${comment.id}, ${resourceId})"><i class="fas fa-trash"></i></button>` : ''}
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
        
        function deleteComment(commentId, resourceId) {
            if (confirm('Delete this comment?')) {
                const formData = new FormData();
                formData.append('action', 'delete_comment');
                formData.append('comment_id', commentId);
                formData.append('resource_id', resourceId);
                
                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadComments(resourceId);
                    }
                });
            }
        }
        
        // Admin: Fake Comment
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
        
        // Admin: Fake Like
        function addFakeLike(resourceId) {
            const name = document.getElementById('fake-like-name-' + resourceId).value.trim();
            const email = document.getElementById('fake-like-email-' + resourceId).value.trim();
            const picture = document.getElementById('fake-like-picture-' + resourceId).value.trim();
            const timestamp = document.getElementById('fake-like-timestamp-' + resourceId).value;
            
            if (!name || !email) {
                alert('Please fill Name and Email fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'add_fake_like');
            formData.append('resource_id', resourceId);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('profile_picture', picture);
            formData.append('custom_timestamp', timestamp || new Date().toISOString());
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    document.getElementById('fake-like-name-' + resourceId).value = '';
                    document.getElementById('fake-like-email-' + resourceId).value = '';
                    document.getElementById('fake-like-picture-' + resourceId).value = '';
                    document.getElementById('fake-like-timestamp-' + resourceId).value = '';
                    const likeBtn = document.querySelector(`[onclick="toggleLike(${resourceId})"]`);
                    const likeCount = likeBtn.querySelector('.like-count');
                    likeCount.textContent = data.like_count;
                }
            });
        }
        
        // Admin: View Likes
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
                                <strong>${like.user_name || 'Unknown'} ${like.is_fake ? '<span class="fake-badge"><i class="fas fa-user-secret"></i></span>' : ''}</strong>
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
        
        // Admin: View Shares
        function viewShares(resourceId) {
            document.getElementById('sharesModal').style.display = 'flex';
            
            const formData = new FormData();
            formData.append('action', 'get_shares');
            formData.append('resource_id', resourceId);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const sharesList = document.getElementById('sharesList');
                if (data.success && data.shares.length > 0) {
                    sharesList.innerHTML = data.shares.map(share => `
                        <div class="like-item">
                            <div class="like-avatar">
                                ${share.profile_image ? 
                                    `<img src="${share.profile_image}" alt="${share.user_name}">` :
                                    '<i class="fas fa-user"></i>'
                                }
                            </div>
                            <div class="like-info">
                                <strong>${share.user_name || 'Unknown'} ${share.is_fake ? '<span class="fake-badge"><i class="fas fa-user-secret"></i></span>' : ''}</strong>
                                <span>${share.user_email || ''}</span>
                            </div>
                            <span class="like-date">${new Date(share.created_at).toLocaleDateString()}</span>
                        </div>
                    `).join('');
                } else {
                    sharesList.innerHTML = '<p class="no-likes">No shares yet</p>';
                }
            });
        }
        
        function closeSharesModal() {
            document.getElementById('sharesModal').style.display = 'none';
        }
        
        // Report Functions
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
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Scroll Reveal Animation
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
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
            reveal();
        });
    </script>
</body>
</html>
