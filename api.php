<?php
require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    // ========== AI Resources (Admin Only) ==========
    case 'add_resource':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prompt_text = trim($_POST['prompt_text'] ?? '');
        $category = $_POST['category'] ?? 'ai';
        $image_url = trim($_POST['image_url'] ?? '');
        
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            exit;
        }
        
        if (addAIResource($title, $description, $prompt_text, $category, $image_url)) {
            echo json_encode(['success' => true, 'message' => 'Resource added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add resource']);
        }
        break;
    
    case 'update_resource':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prompt_text = trim($_POST['prompt_text'] ?? '');
        $category = $_POST['category'] ?? 'ai';
        $image_url = trim($_POST['image_url'] ?? '');
        
        if (empty($title) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Title and ID are required']);
            exit;
        }
        
        if (updateAIResource($id, $title, $description, $prompt_text, $category, $image_url)) {
            echo json_encode(['success' => true, 'message' => 'Resource updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update resource']);
        }
        break;
    
    case 'delete_resource':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        
        if (deleteAIResource($id)) {
            echo json_encode(['success' => true, 'message' => 'Resource deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete resource']);
        }
        break;
    
    // ========== Engagement ==========
    case 'toggle_like':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to like']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $user_id = $_SESSION['user_id'];
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        $result = toggleLike($resource_id, $user_id);
        $like_count = getLikeCount($resource_id);
        
        echo json_encode([
            'success' => true,
            'liked' => $result['liked'],
            'like_count' => $like_count
        ]);
        break;
    
    case 'add_comment':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to comment']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $comment_text = trim($_POST['comment_text'] ?? '');
        $user_id = $_SESSION['user_id'];
        
        if (empty($resource_id) || empty($comment_text)) {
            echo json_encode(['success' => false, 'message' => 'Resource ID and comment text are required']);
            exit;
        }
        
        if (addComment($resource_id, $user_id, $comment_text)) {
            $comment_count = getCommentCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Comment added', 'comment_count' => $comment_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
        break;
    
    // ========== Admin: Fake Comments ==========
    case 'add_fake_comment':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $comment_text = trim($_POST['comment_text'] ?? '');
        
        if (empty($resource_id) || empty($name) || empty($email) || empty($comment_text)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        if (addFakeComment($resource_id, $name, $email, $comment_text)) {
            $comment_count = getCommentCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Fake comment added', 'comment_count' => $comment_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add fake comment']);
        }
        break;
    
    // ========== Admin: Edit/Delete Comments ==========
    case 'update_comment':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $comment_id = $_POST['comment_id'] ?? 0;
        $comment_text = trim($_POST['comment_text'] ?? '');
        
        if (empty($comment_id) || empty($comment_text)) {
            echo json_encode(['success' => false, 'message' => 'Comment ID and text are required']);
            exit;
        }
        
        if (updateComment($comment_id, $comment_text)) {
            echo json_encode(['success' => true, 'message' => 'Comment updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update comment']);
        }
        break;
    
    case 'delete_comment':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $comment_id = $_POST['comment_id'] ?? 0;
        $resource_id = $_POST['resource_id'] ?? 0;
        
        if (empty($comment_id)) {
            echo json_encode(['success' => false, 'message' => 'Comment ID is required']);
            exit;
        }
        
        if (deleteComment($comment_id)) {
            $comment_count = getCommentCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Comment deleted', 'comment_count' => $comment_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
        break;
    
    // ========== View Likes (Public - Everyone can see) ==========
    case 'get_likes':
        $resource_id = $_POST['resource_id'] ?? $_GET['resource_id'] ?? 0;
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        $likes = getLikes($resource_id);
        echo json_encode(['success' => true, 'likes' => $likes]);
        break;
    
    // ========== Admin Dashboard: Get All Users ==========
    case 'get_all_users':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $users = getAllUsers();
        echo json_encode(['success' => true, 'users' => $users]);
        break;
    
    // ========== Admin Dashboard: Get All Resources ==========
    case 'get_all_resources':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resources = getAllAIResources();
        echo json_encode(['success' => true, 'resources' => $resources]);
        break;
    
    // ========== Admin Dashboard: Get All Engagement ==========
    case 'get_all_engagement':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $engagement = getAllEngagement();
        echo json_encode(['success' => true, 'engagement' => $engagement]);
        break;
    
    // ========== Admin Dashboard: Get Stats ==========
    case 'get_admin_stats':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $stats = getAdminStats();
        echo json_encode(['success' => true, 'stats' => $stats]);
        break;
    
    // ========== Admin Dashboard: Get YouTube Stats ==========
    case 'get_youtube_stats':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $youtube_stats = getYouTubeChannelStats();
        echo json_encode(['success' => true, 'youtube_stats' => $youtube_stats]);
        break;
    
    // ========== Admin Dashboard: Update Site Settings ==========
    case 'update_site_settings':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $site_name = trim($_POST['site_name'] ?? '');
        $site_description = trim($_POST['site_description'] ?? '');
        $maintenance_mode = isset($_POST['maintenance_mode']) ? (bool)$_POST['maintenance_mode'] : false;
        
        if (updateSiteSettings($site_name, $site_description, $maintenance_mode)) {
            echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
        }
        break;
    
    // ========== Admin Dashboard: Get Latest YouTube Videos ==========
    case 'get_latest_videos':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $videos = getLatestYouTubeVideos();
        echo json_encode(['success' => true, 'videos' => $videos]);
        break;
    
    // ========== Reports ==========
    case 'add_report':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to report']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $reason = trim($_POST['reason'] ?? '');
        $user_id = $_SESSION['user_id'];
        
        if (empty($resource_id) || empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Resource ID and reason are required']);
            exit;
        }
        
        if (addReport($resource_id, $user_id, $reason)) {
            echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit report']);
        }
        break;
    
    case 'get_reports':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? $_GET['resource_id'] ?? null;
        $reports = getReports($resource_id);
        echo json_encode(['success' => true, 'reports' => $reports]);
        break;
    
    // ========== Get Comments ==========
    case 'get_comments':
        $resource_id = $_POST['resource_id'] ?? $_GET['resource_id'] ?? 0;
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        $comments = getComments($resource_id);
        echo json_encode(['success' => true, 'comments' => $comments, 'is_admin' => isAdmin()]);
        break;
    
    case 'get_engagement':
        $resource_id = $_POST['resource_id'] ?? $_GET['resource_id'] ?? 0;
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        $like_count = getLikeCount($resource_id);
        $comment_count = getCommentCount($resource_id);
        $report_count = getReportCount($resource_id);
        $share_count = getShareCount($resource_id);
        $user_liked = isLoggedIn() ? userHasLiked($resource_id, $_SESSION['user_id'] ?? 0) : false;
        
        echo json_encode([
            'success' => true,
            'like_count' => $like_count,
            'comment_count' => $comment_count,
            'report_count' => $report_count,
            'share_count' => $share_count,
            'user_liked' => $user_liked,
            'is_admin' => isAdmin()
        ]);
        break;
    
    // ========== Share Functions ==========
    case 'add_share':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to share']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $user_id = $_SESSION['user_id'];
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        if (addShare($resource_id, $user_id)) {
            $share_count = getShareCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Shared successfully', 'share_count' => $share_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to share']);
        }
        break;
    
    case 'get_shares':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? $_GET['resource_id'] ?? 0;
        
        if (empty($resource_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid resource']);
            exit;
        }
        
        $shares = getShares($resource_id);
        echo json_encode(['success' => true, 'shares' => $shares]);
        break;
    
    // ========== Fake Like Functions (Admin Only) ==========
    case 'add_fake_like':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $profile_picture = trim($_POST['profile_picture'] ?? '');
        $custom_timestamp = $_POST['custom_timestamp'] ?? date('Y-m-d H:i:s');
        
        if (empty($resource_id) || empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Resource ID, Name, and Email are required']);
            exit;
        }
        
        if (addFakeLike($resource_id, $name, $email, $profile_picture, $custom_timestamp)) {
            $like_count = getLikeCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Fake like added', 'like_count' => $like_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add fake like']);
        }
        break;
    
    // ========== Fake Share Functions (Admin Only) ==========
    case 'add_fake_share':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $resource_id = $_POST['resource_id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($resource_id) || empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Resource ID, Name, and Email are required']);
            exit;
        }
        
        if (addFakeShare($resource_id, $name, $email)) {
            $share_count = getShareCount($resource_id);
            echo json_encode(['success' => true, 'message' => 'Fake share added', 'share_count' => $share_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add fake share']);
        }
        break;
    
    // ========== AI Resources with Download Link ==========
    case 'add_resource_with_download':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prompt_text = trim($_POST['prompt_text'] ?? '');
        $category = $_POST['category'] ?? 'ai';
        $image_url = trim($_POST['image_url'] ?? '');
        $download_link = trim($_POST['download_link'] ?? '');
        
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            exit;
        }
        
        if (addAIResourceWithDownload($title, $description, $prompt_text, $category, $image_url, $download_link)) {
            echo json_encode(['success' => true, 'message' => 'Resource added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add resource']);
        }
        break;
    
    case 'update_resource_with_download':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prompt_text = trim($_POST['prompt_text'] ?? '');
        $category = $_POST['category'] ?? 'ai';
        $image_url = trim($_POST['image_url'] ?? '');
        $download_link = trim($_POST['download_link'] ?? '');
        
        if (empty($title) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Title and ID are required']);
            exit;
        }
        
        if (updateAIResourceWithDownload($id, $title, $description, $prompt_text, $category, $image_url, $download_link)) {
            echo json_encode(['success' => true, 'message' => 'Resource updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update resource']);
        }
        break;
    
    // ========== Web Tools (Admin Only) ==========
    case 'add_web_tool':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? 'tools';
        $image_url = trim($_POST['image_url'] ?? '');
        $download_link = trim($_POST['download_link'] ?? '');
        
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            exit;
        }
        
        if (addWebTool($title, $description, $category, $image_url, $download_link)) {
            echo json_encode(['success' => true, 'message' => 'Web tool added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add web tool']);
        }
        break;
    
    case 'update_web_tool':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? 'tools';
        $image_url = trim($_POST['image_url'] ?? '');
        $download_link = trim($_POST['download_link'] ?? '');
        
        if (empty($title) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Title and ID are required']);
            exit;
        }
        
        if (updateWebTool($id, $title, $description, $category, $image_url, $download_link)) {
            echo json_encode(['success' => true, 'message' => 'Web tool updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update web tool']);
        }
        break;
    
    case 'delete_web_tool':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        
        if (deleteWebTool($id)) {
            echo json_encode(['success' => true, 'message' => 'Web tool deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete web tool']);
        }
        break;
    
    // ========== Search ==========
    case 'search':
        $query = trim($_POST['query'] ?? $_GET['query'] ?? '');
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            exit;
        }
        
        $results = searchAIResources($query);
        echo json_encode(['success' => true, 'results' => $results, 'query' => $query]);
        break;
    
    // ========== Category ==========
    case 'get_by_category':
        $category = $_POST['category'] ?? $_GET['category'] ?? '';
        
        if (empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Category is required']);
            exit;
        }
        
        $resources = getAIResourcesByCategory($category);
        echo json_encode(['success' => true, 'resources' => $resources, 'category' => $category]);
        break;
    
    // ========== Google Login ==========
    case 'google_login':
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $picture = trim($_POST['picture'] ?? '');
        
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }
        
        // Check if user exists
        $user = findUserByEmail($email);
        
        if ($user) {
            // Update user info
            if (!empty($picture)) {
                updateGoogleUser($user['id'], $name, $picture);
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            echo json_encode(['success' => true, 'message' => 'Login successful', 'is_new' => false, 'user' => $user]);
        } else {
            // Create new user
            $result = createGoogleUser($name, $email, '', $picture);
            if ($result['success']) {
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                echo json_encode(['success' => true, 'message' => 'Account created', 'is_new' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create account']);
            }
        }
        break;
    
    // ========== Update Profile ==========
    case 'update_profile':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login']);
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $country = $_POST['country'] ?? 'Sri Lanka';
        $district = trim($_POST['district'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $theme = $_POST['theme'] ?? 'darki';
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Name is required']);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        if (updateUserProfile($user_id, $name, $phone, $address, $country, $district, $gender, $theme)) {
            $_SESSION['user_name'] = $name;
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
