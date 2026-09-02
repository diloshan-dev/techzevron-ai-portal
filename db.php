<?php
// Database Configuration
$host = 'localhost';
$dbname = 'website01';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// YouTube API Configuration
define('YOUTUBE_API_KEY', 'AIzaSyAOTCjAov8bRgNZk2DcsL0XK1yHgUM6MZM');
define('YOUTUBE_CHANNEL_ID', 'UC2Xvqo5QjpeR1xp9CKsPMWA');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'techzevron@gmail.com';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getCurrentUser() {
    global $conn;
    if (isLoggedIn()) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// ========== YouTube API Functions ==========

function getYouTubeChannelStats() {
    $apiKey = YOUTUBE_API_KEY;
    $channelId = YOUTUBE_CHANNEL_ID;
    
    $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&id=$channelId&key=$apiKey";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['items'][0])) {
        $stats = $data['items'][0]['statistics'];
        $snippet = $data['items'][0]['snippet'];
        
        return [
            'subscribers' => isset($stats['subscriberCount']) ? formatNumber($stats['subscriberCount']) : '0',
            'subscribers_raw' => isset($stats['subscriberCount']) ? $stats['subscriberCount'] : '0',
            'views' => isset($stats['viewCount']) ? formatNumber($stats['viewCount']) : '0',
            'views_raw' => isset($stats['viewCount']) ? $stats['viewCount'] : '0',
            'videos' => isset($stats['videoCount']) ? formatNumber($stats['videoCount']) : '0',
            'videos_raw' => isset($stats['videoCount']) ? $stats['videoCount'] : '0',
            'title' => $snippet['title'],
            'description' => $snippet['description'],
            'thumbnail' => isset($snippet['thumbnails']['high']['url']) ? $snippet['thumbnails']['high']['url'] : ''
        ];
    }
    
    return null;
}

function getMostLikedVideo() {
    $apiKey = YOUTUBE_API_KEY;
    $channelId = YOUTUBE_CHANNEL_ID;
    
    $url = "https://www.googleapis.com/youtube/v3/search?key=$apiKey&channelId=$channelId&part=snippet,id&order=date&maxResults=50&type=video";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!isset($data['items'])) {
        return null;
    }
    
    $videoIds = [];
    foreach ($data['items'] as $item) {
        if (isset($item['id']['videoId'])) {
            $videoIds[] = $item['id']['videoId'];
        }
    }
    
    if (empty($videoIds)) {
        return null;
    }
    
    $videoIdsStr = implode(',', array_slice($videoIds, 0, 50));
    $url = "https://www.googleapis.com/youtube/v3/videos?part=statistics,snippet&id=$videoIdsStr&key=$apiKey";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $videosData = json_decode($response, true);
    
    if (!isset($videosData['items'])) {
        return null;
    }
    
    $mostLiked = null;
    $maxLikes = 0;
    
    foreach ($videosData['items'] as $video) {
        $likes = isset($video['statistics']['likeCount']) ? intval($video['statistics']['likeCount']) : 0;
        if ($likes > $maxLikes) {
            $maxLikes = $likes;
            $mostLiked = [
                'video_id' => $video['id'],
                'title' => $video['snippet']['title'],
                'thumbnail' => isset($video['snippet']['thumbnails']['medium']['url']) ? $video['snippet']['thumbnails']['medium']['url'] : '',
                'likes' => formatNumber($likes),
                'likes_raw' => $likes,
                'views' => isset($video['statistics']['viewCount']) ? formatNumber($video['statistics']['viewCount']) : '0',
                'url' => 'https://www.youtube.com/watch?v=' . $video['id']
            ];
        }
    }
    
    return $mostLiked;
}

function getLatestVideo() {
    $apiKey = YOUTUBE_API_KEY;
    $channelId = YOUTUBE_CHANNEL_ID;
    
    $url = "https://www.googleapis.com/youtube/v3/search?key=$apiKey&channelId=$channelId&part=snippet,id&order=date&maxResults=1&type=video";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['items'][0])) {
        $item = $data['items'][0];
        return [
            'video_id' => $item['id']['videoId'],
            'title' => $item['snippet']['title'],
            'thumbnail' => isset($item['snippet']['thumbnails']['medium']['url']) ? $item['snippet']['thumbnails']['medium']['url'] : '',
            'published' => $item['snippet']['publishedAt'],
            'url' => 'https://www.youtube.com/watch?v=' . $item['id']['videoId']
        ];
    }
    
    return null;
}

function formatNumber($num) {
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    } elseif ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }
    return $num;
}

// ========== Existing Functions ==========

function uploadProfileImage($file) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $fileName = basename($file["name"]);
    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . "." . $imageFileType;
    $target_file = $target_dir . $newFileName;
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large."];
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp") {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG, GIF & WEBP files are allowed."];
    }
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "filename" => $target_file];
    } else {
        return ["success" => false, "message" => "Sorry, there was an error uploading your file."];
    }
}

function getAllUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getAllMessages() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM messages ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// ========== AI Resources Functions ==========

function getAllAIResources() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ai_resources ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getAIResource($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ai_resources WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addAIResource($title, $description, $prompt_text, $category, $image_url) {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $conn->prepare("INSERT INTO ai_resources (title, description, prompt_text, category, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $prompt_text, $category, $image_url, $user_id]);
}

function updateAIResource($id, $title, $description, $prompt_text, $category, $image_url) {
    global $conn;
    $stmt = $conn->prepare("UPDATE ai_resources SET title = ?, description = ?, prompt_text = ?, category = ?, image_url = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $prompt_text, $category, $image_url, $id]);
}

function deleteAIResource($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM engagement WHERE resource_id = ?");
    $stmt->execute([$id]);
    $stmt = $conn->prepare("DELETE FROM ai_resources WHERE id = ?");
    return $stmt->execute([$id]);
}

// ========== Engagement Functions ==========

function getLikeCount($resource_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND type = 'like'");
    $stmt->execute([$resource_id]);
    return $stmt->fetch()['total'];
}

function getCommentCount($resource_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND (type = 'comment' OR type = 'fake_comment')");
    $stmt->execute([$resource_id]);
    return $stmt->fetch()['total'];
}

function getReportCount($resource_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND type = 'report'");
    $stmt->execute([$resource_id]);
    return $stmt->fetch()['total'];
}

function userHasLiked($resource_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM engagement WHERE resource_id = ? AND user_id = ? AND type = 'like'");
    $stmt->execute([$resource_id, $user_id]);
    return $stmt->rowCount() > 0;
}

function toggleLike($resource_id, $user_id) {
    global $conn;
    if (userHasLiked($resource_id, $user_id)) {
        $stmt = $conn->prepare("DELETE FROM engagement WHERE resource_id = ? AND user_id = ? AND type = 'like'");
        $stmt->execute([$resource_id, $user_id]);
        return ['liked' => false];
    } else {
        $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type) VALUES (?, ?, 'like')");
        $stmt->execute([$resource_id, $user_id]);
        return ['liked' => true];
    }
}

function addComment($resource_id, $user_id, $comment_text) {
    global $conn;
    $user = getCurrentUser();
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, comment_text, user_name, user_email, is_fake) VALUES (?, ?, 'comment', ?, ?, ?, 0)");
    return $stmt->execute([$resource_id, $user_id, $comment_text, $user['name'], $user['email']]);
}

function addFakeComment($resource_id, $name, $email, $comment_text) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, comment_text, user_name, user_email, is_fake) VALUES (?, NULL, 'fake_comment', ?, ?, ?, 1)");
    return $stmt->execute([$resource_id, $comment_text, $name, $email]);
}

function updateComment($comment_id, $comment_text) {
    global $conn;
    $stmt = $conn->prepare("UPDATE engagement SET comment_text = ? WHERE id = ?");
    return $stmt->execute([$comment_id, $comment_id]);
}

function deleteComment($comment_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM engagement WHERE id = ?");
    return $stmt->execute([$comment_id]);
}

function getComments($resource_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT e.*, u.name as commenter_name, u.email as commenter_email, u.profile_image 
        FROM engagement e 
        LEFT JOIN users u ON e.user_id = u.id 
        WHERE e.resource_id = ? AND (e.type = 'comment' OR e.type = 'fake_comment') 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$resource_id]);
    return $stmt->fetchAll();
}

function getLikes($resource_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT e.*, u.name as user_name, u.email as user_email, u.profile_image 
        FROM engagement e 
        LEFT JOIN users u ON e.user_id = u.id 
        WHERE e.resource_id = ? AND e.type = 'like' 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$resource_id]);
    return $stmt->fetchAll();
}

function addReport($resource_id, $user_id, $reason) {
    global $conn;
    $user = getCurrentUser();
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, comment_text, user_name, user_email) VALUES (?, ?, 'report', ?, ?, ?)");
    return $stmt->execute([$resource_id, $user_id, $reason, $user['name'], $user['email']]);
}

function getReports($resource_id = null) {
    global $conn;
    if ($resource_id) {
        $stmt = $conn->prepare("
            SELECT e.*, u.name as user_name, u.email as user_email, r.title as resource_title
            FROM engagement e 
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN ai_resources r ON e.resource_id = r.id
            WHERE e.type = 'report' AND e.resource_id = ?
            ORDER BY e.created_at DESC
        ");
        $stmt->execute([$resource_id]);
    } else {
        $stmt = $conn->prepare("
            SELECT e.*, u.name as user_name, u.email as user_email, r.title as resource_title
            FROM engagement e 
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN ai_resources r ON e.resource_id = r.id
            WHERE e.type = 'report'
            ORDER BY e.created_at DESC
        ");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

// ========== Email Functions ==========

function notifyAdmin($name, $email, $subject, $message) {
    // Simple email notification - in production use PHPMailer or similar
    $admin_email = 'techzevron@gmail.com';
    $headers = "From: noreply@techzevron.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $email_subject = "New Contact Form Submission: " . ($subject ?: 'No Subject');
    $email_body = "New message from website contact form:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Subject: $subject\n";
    $email_body .= "Message:\n$message\n";
    
    // For now, we'll just log the notification
    // In production, uncomment the mail() function
    // mail($admin_email, $email_subject, $email_body, $headers);
    
    return true;
}

function sendWelcomeEmail($email, $name) {
    $subject = "Welcome to TechZevron!";
    $headers = "From: noreply@techzevron.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $message = "Hello $name,\n\n";
    $message .= "Welcome to TechZevron! We're excited to have you on board.\n\n";
    $message .= "Explore our AI resources and tools at: https://techzevron.com\n\n";
    $message .= "Best regards,\n";
    $message .= "TechZevron Team\n";
    
    // For now, we'll just log the notification
    // In production, uncomment the mail() function
    // mail($email, $subject, $message, $headers);
    
    return true;
}

// ========== Google Login Functions ==========

function findUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function createGoogleUser($name, $email, $google_id, $profile_image = null) {
    global $conn;
    $hashed_password = password_hash(uniqid(), PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_image, google_id) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hashed_password, $profile_image, $google_id]);
        return [
            'success' => true,
            'user_id' => $conn->lastInsertId(),
            'is_new' => true
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Failed to create user: ' . $e->getMessage()
        ];
    }
}

function updateGoogleUser($user_id, $name, $profile_image) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, profile_image = ? WHERE id = ?");
    return $stmt->execute([$name, $profile_image, $user_id]);
}

// ========== Search & Category Functions ==========

function searchAIResources($query) {
    global $conn;
    $search = "%$query%";
    $stmt = $conn->prepare("
        SELECT * FROM ai_resources 
        WHERE title LIKE ? OR description LIKE ? OR prompt_text LIKE ? OR category LIKE ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$search, $search, $search, $search]);
    return $stmt->fetchAll();
}

function getAIResourcesByCategory($category) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ai_resources WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

function updateUserProfile($user_id, $name, $phone, $address, $country, $district, $gender, $theme) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, country = ?, district = ?, gender = ?, theme = ? WHERE id = ?");
    return $stmt->execute([$name, $phone, $address, $country, $district, $gender, $theme, $user_id]);
}

// ========== Share Functions ==========

function getShareCount($resource_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND type = 'share'");
    $stmt->execute([$resource_id]);
    return $stmt->fetch()['total'];
}

function addShare($resource_id, $user_id) {
    global $conn;
    $user = getCurrentUser();
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, user_name, user_email, is_fake) VALUES (?, ?, 'share', ?, ?, 0)");
    return $stmt->execute([$resource_id, $user_id, $user['name'], $user['email']]);
}

function addFakeShare($resource_id, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, user_name, user_email, is_fake) VALUES (?, NULL, 'share', ?, ?, 1)");
    return $stmt->execute([$resource_id, $name, $email]);
}

function getShares($resource_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT e.*, u.name as user_name, u.email as user_email, u.profile_image 
        FROM engagement e 
        LEFT JOIN users u ON e.user_id = u.id 
        WHERE e.resource_id = ? AND e.type = 'share' 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$resource_id]);
    return $stmt->fetchAll();
}

// ========== Fake Like Functions ==========

function addFakeLike($resource_id, $name, $email, $profile_picture, $custom_timestamp) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO engagement (resource_id, user_id, type, user_name, user_email, is_fake, created_at) VALUES (?, NULL, 'like', ?, ?, 1, ?)");
    return $stmt->execute([$resource_id, $name, $email, $custom_timestamp]);
}

function getShareCountByResource($resource_id) {
    return getShareCount($resource_id);
}

// ========== AI Resources with Download Link ==========

function addAIResourceWithDownload($title, $description, $prompt_text, $category, $image_url, $download_link) {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $conn->prepare("INSERT INTO ai_resources (title, description, prompt_text, category, image_url, download_link, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $prompt_text, $category, $image_url, $download_link, $user_id]);
}

function updateAIResourceWithDownload($id, $title, $description, $prompt_text, $category, $image_url, $download_link) {
    global $conn;
    $stmt = $conn->prepare("UPDATE ai_resources SET title = ?, description = ?, prompt_text = ?, category = ?, image_url = ?, download_link = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $prompt_text, $category, $image_url, $download_link, $id]);
}

// ========== Web Tools Functions ==========

function getAllWebTools() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM web_tools ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getWebTool($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM web_tools WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addWebTool($title, $description, $category, $image_url, $download_link) {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $conn->prepare("INSERT INTO web_tools (title, description, category, image_url, download_link, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $category, $image_url, $download_link, $user_id]);
}

function updateWebTool($id, $title, $description, $category, $image_url, $download_link) {
    global $conn;
    $stmt = $conn->prepare("UPDATE web_tools SET title = ?, description = ?, category = ?, image_url = ?, download_link = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $category, $image_url, $download_link, $id]);
}

function deleteWebTool($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM engagement WHERE resource_id = ?");
    $stmt->execute([$id]);
    $stmt = $conn->prepare("DELETE FROM web_tools WHERE id = ?");
    return $stmt->execute([$id]);
}

function getWebToolEngagement($tool_id) {
    global $conn;
    $like_count = 0;
    $comment_count = 0;
    $share_count = 0;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND type = 'like'");
    $stmt->execute([$tool_id]);
    $like_count = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND (type = 'comment' OR type = 'fake_comment')");
    $stmt->execute([$tool_id]);
    $comment_count = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE resource_id = ? AND type = 'share'");
    $stmt->execute([$tool_id]);
    $share_count = $stmt->fetch()['total'];
    
    return [
        'likes' => $like_count,
        'comments' => $comment_count,
        'shares' => $share_count
    ];
}

// ========== Admin Dashboard Functions ==========

function getAdminStats() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM messages");
    $stmt->execute();
    $total_messages = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ai_resources");
    $stmt->execute();
    $total_resources = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE type = 'like'");
    $stmt->execute();
    $total_likes = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE type = 'comment' OR type = 'fake_comment'");
    $stmt->execute();
    $total_comments = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE type = 'share'");
    $stmt->execute();
    $total_shares = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM engagement WHERE type = 'report'");
    $stmt->execute();
    $total_reports = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt->execute();
    $new_users_month = $stmt->fetch()['total'];
    
    return [
        'total_users' => $total_users,
        'total_messages' => $total_messages,
        'total_resources' => $total_resources,
        'total_likes' => $total_likes,
        'total_comments' => $total_comments,
        'total_shares' => $total_shares,
        'total_reports' => $total_reports,
        'new_users_month' => $new_users_month
    ];
}

function getAllEngagement() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            r.id as resource_id,
            r.title as resource_title,
            r.category,
            COUNT(DISTINCT CASE WHEN e.type = 'like' THEN e.id END) as likes,
            COUNT(DISTINCT CASE WHEN e.type = 'comment' THEN e.id END) as comments,
            COUNT(DISTINCT CASE WHEN e.type = 'share' THEN e.id END) as shares,
            COUNT(DISTINCT CASE WHEN e.type = 'report' THEN e.id END) as reports
        FROM ai_resources r
        LEFT JOIN engagement e ON r.id = e.resource_id
        GROUP BY r.id, r.title, r.category
        ORDER BY likes DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function updateSiteSettings($site_name, $site_description, $maintenance_mode) {
    global $conn;
    
    $conn->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('site_name', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$site_name, $site_name]);
    
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('site_description', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$site_description, $site_description]);
    
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('maintenance_mode', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$maintenance_mode ? '1' : '0', $maintenance_mode ? '1' : '0']);
    
    return true;
}

function getSiteSettings() {
    global $conn;
    
    $conn->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings");
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    $settings = [];
    foreach ($results as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

function getLatestYouTubeVideos($maxResults = 10) {
    $apiKey = YOUTUBE_API_KEY;
    $channelId = YOUTUBE_CHANNEL_ID;
    
    $url = "https://www.googleapis.com/youtube/v3/search?key=$apiKey&channelId=$channelId&part=snippet,id&order=date&maxResults=5&type=video";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!isset($data['items']) || empty($data['items'])) {
        return [];
    }
    
    $videos = [];
    foreach ($data['items'] as $item) {
        $videos[] = [
            'video_id' => $item['id']['videoId'],
            'title' => $item['snippet']['title'],
            'thumbnail' => isset($item['snippet']['thumbnails']['medium']['url']) ? $item['snippet']['thumbnails']['medium']['url'] : '',
            'published' => $item['snippet']['publishedAt']
        ];
    }
    
    return $videos;
}
?>
