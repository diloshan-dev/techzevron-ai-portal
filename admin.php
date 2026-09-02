<?php
require_once 'db.php';
requireLogin();

if (!isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

$user = getCurrentUser();
$stats = getAdminStats();
$users = getAllUsers();
$resources = getAllAIResources();
$youtube_stats = getYouTubeChannelStats();
$latest_videos = getLatestYouTubeVideos();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="script.js"></script>
    <style>
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: var(--secondary);
            border-right: 1px solid var(--glass-border);
            padding: 20px;
            overflow-y: auto;
            z-index: 100;
        }
        
        .admin-sidebar .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            text-decoration: none;
            display: block;
            margin-bottom: 30px;
        }
        
        .admin-sidebar .logo span {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--radius-sm);
            margin-bottom: 5px;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .admin-nav-item:hover, .admin-nav-item.active {
            background: rgba(14, 165, 233, 0.1);
            color: var(--primary);
        }
        
        .admin-nav-item i {
            width: 20px;
        }
        
        .admin-main {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .admin-section {
            display: none;
        }
        
        .admin-section.active {
            display: block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card-admin {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 25px;
            text-align: center;
        }
        
        .stat-card-admin i {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .stat-card-admin .number {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-primary);
        }
        
        .stat-card-admin .label {
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
        }
        
        .table-container {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .table-container h3 {
            color: var(--text-primary);
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
        }
        
        .data-table th {
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        
        .youtube-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .youtube-stat-card {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border-radius: var(--radius);
            padding: 25px;
            text-align: center;
            color: white;
        }
        
        .youtube-stat-card i {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .youtube-stat-card .number {
            font-size: 28px;
            font-weight: 700;
        }
        
        .youtube-stat-card .label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .video-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        
        .video-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .video-card .content {
            padding: 15px;
        }
        
        .video-card h4 {
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-card .date {
            color: var(--text-muted);
            font-size: 12px;
        }
        
        .settings-form {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 30px;
            max-width: 600px;
        }
        
        .settings-form .form-group {
            margin-bottom: 20px;
        }
        
        .settings-form label {
            display: block;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .settings-form input, .settings-form textarea {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
        }
        
        .settings-form input:focus, .settings-form textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
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
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: var(--primary);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .engagement-table td {
            vertical-align: middle;
        }
        
        .engagement-table .count-cell {
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                display: none;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body data-theme="midnight">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <a href="index.php" class="logo">Tech<span>Zevron</span></a>
        
        <nav>
            <div class="admin-nav-item active" onclick="showSection('dashboard', event)">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </div>
            <div class="admin-nav-item" onclick="showSection('users', event)">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </div>
            <div class="admin-nav-item" onclick="showSection('resources', event)">
                <i class="fas fa-file-alt"></i>
                <span>Resources</span>
            </div>
            <div class="admin-nav-item" onclick="showSection('engagement', event)">
                <i class="fas fa-heart"></i>
                <span>Engagement</span>
            </div>
            <div class="admin-nav-item" onclick="showSection('youtube', event)">
                <i class="fab fa-youtube"></i>
                <span>YouTube</span>
            </div>
            <div class="admin-nav-item" onclick="showSection('settings', event)">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
            <a href="dashboard.php" class="admin-nav-item">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Profile</span>
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></strong></span>
            </div>
        </div>
        
        <!-- Dashboard Section -->
        <div id="dashboard" class="admin-section active">
            <h2><i class="fas fa-tachometer-alt"></i> Overview</h2>
            <br>
            
            <div class="stats-grid">
                <div class="stat-card-admin">
                    <i class="fas fa-users"></i>
                    <div class="number"><?php echo $stats['total_users'] ?? 0; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-envelope"></i>
                    <div class="number"><?php echo $stats['total_messages'] ?? 0; ?></div>
                    <div class="label">Messages</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-file-alt"></i>
                    <div class="number"><?php echo $stats['total_resources'] ?? 0; ?></div>
                    <div class="label">Resources</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-heart"></i>
                    <div class="number"><?php echo $stats['total_likes'] ?? 0; ?></div>
                    <div class="label">Total Likes</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-comments"></i>
                    <div class="number"><?php echo $stats['total_comments'] ?? 0; ?></div>
                    <div class="label">Comments</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-share"></i>
                    <div class="number"><?php echo $stats['total_shares'] ?? 0; ?></div>
                    <div class="label">Shares</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-flag"></i>
                    <div class="number"><?php echo $stats['total_reports'] ?? 0; ?></div>
                    <div class="label">Reports</div>
                </div>
                <div class="stat-card-admin">
                    <i class="fas fa-user-plus"></i>
                    <div class="number"><?php echo $stats['new_users_month'] ?? 0; ?></div>
                    <div class="label">New Users (30d)</div>
                </div>
            </div>
        </div>
        
        <!-- Users Section -->
        <div id="users" class="admin-section">
            <h2><i class="fas fa-users"></i> User Management</h2>
            <br>
            
            <div class="table-container">
                <h3>All Users (<?php echo is_array($users) ? count($users) : 0; ?>)</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                            <?php if ($u['email'] === 'techzevron@gmail.com'): ?>
                                <span class="status-badge status-active">Admin</span>
                                <?php else: ?>
                                <span class="status-badge" style="background: rgba(100, 100, 100, 0.2); color: #888;">User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Resources Section -->
        <div id="resources" class="admin-section">
            <h2><i class="fas fa-file-alt"></i> Content Management</h2>
            <br>
            
            <div class="table-container">
                <h3>All Resources (<?php echo is_array($resources) ? count($resources) : 0; ?>)</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($resources)): foreach ($resources as $r): ?>
                        <tr>
                            <td><?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['title']); ?></td>
                            <td><span class="status-badge status-active"><?php echo htmlspecialchars($r['category']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editResource(<?php echo $r['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteResource(<?php echo $r['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Engagement Section -->
        <div id="engagement" class="admin-section">
            <h2><i class="fas fa-heart"></i> Engagement Analytics</h2>
            <br>
            
            <div class="table-container">
                <h3>Resource Engagement</h3>
                <table class="data-table engagement-table">
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th class="count-cell">Likes</th>
                            <th class="count-cell">Comments</th>
                            <th class="count-cell">Shares</th>
                            <th class="count-cell">Reports</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $engagement = function_exists('getAllEngagement') ? getAllEngagement() : [];
                        if(!empty($engagement)):
                        foreach ($engagement as $e): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['resource_title']); ?></td>
                            <td class="count-cell"><?php echo $e['likes']; ?></td>
                            <td class="count-cell"><?php echo $e['comments']; ?></td>
                            <td class="count-cell"><?php echo $e['shares']; ?></td>
                            <td class="count-cell"><?php echo $e['reports']; ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- YouTube Section -->
        <div id="youtube" class="admin-section">
            <h2><i class="fab fa-youtube"></i> YouTube Analytics</h2>
            <br>
            
            <?php if ($youtube_stats): ?>
            <div class="youtube-stats-grid">
                <div class="youtube-stat-card">
                    <i class="fab fa-youtube"></i>
                    <div class="number"><?php echo $youtube_stats['subscribers']; ?></div>
                    <div class="label">Subscribers</div>
                </div>
                <div class="youtube-stat-card">
                    <i class="fas fa-eye"></i>
                    <div class="number"><?php echo $youtube_stats['views']; ?></div>
                    <div class="label">Total Views</div>
                </div>
                <div class="youtube-stat-card">
                    <i class="fas fa-video"></i>
                    <div class="number"><?php echo $youtube_stats['videos']; ?></div>
                    <div class="label">Videos</div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="table-container">
                <h3>Latest Videos</h3>
                <div class="video-grid">
                    <?php if(!empty($latest_videos)): foreach ($latest_videos as $video): ?>
                    <div class="video-card">
                        <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                        <div class="content">
                            <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                            <div class="date"><?php echo date('M d, Y', strtotime($video['published'])); ?></div>
                            <a href="https://www.youtube.com/watch?v=<?php echo $video['video_id']; ?>" target="_blank" class="btn btn-sm btn-primary" style="margin-top: 10px;">
                                <i class="fab fa-youtube"></i> View
                            </a>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Settings Section -->
        <div id="settings" class="admin-section">
            <h2><i class="fas fa-cog"></i> Site Settings</h2>
            <br>
            
            <div class="settings-form">
                <form id="siteSettingsForm">
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" id="siteName" value="TechZevron" required>
                    </div>
                    <div class="form-group">
                        <label>Site Description</label>
                        <textarea id="siteDescription" rows="3">AI Resources & Web Tools Platform</textarea>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 15px;">
                        <label class="toggle-switch">
                            <input type="checkbox" id="maintenanceMode">
                            <span class="toggle-slider"></span>
                        </label>
                        <span>Maintenance Mode</span>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showSection(sectionId, event) {
            // Hide all sections
            document.querySelectorAll('.admin-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) targetSection.classList.add('active');
            
            // Update nav
            document.querySelectorAll('.admin-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
        }
        
        // Save settings
        const settingsForm = document.getElementById('siteSettingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('action', 'update_site_settings');
                formData.append('site_name', document.getElementById('siteName').value);
                formData.append('site_description', document.getElementById('siteDescription').value);
                formData.append('maintenance_mode', document.getElementById('maintenanceMode').checked ? '1' : '0');
                
                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Settings saved successfully!');
                    } else {
                        alert(data.message);
                    }
                });
            });
        }
        
        function editResource(id) {
            alert('Edit resource ID: ' + id);
        }
        
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
                    if (data.success) {
                        showToast('Resource deleted successfully!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
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
    </script>
</body>
</html>