<?php
require_once 'db.php';
requireLogin();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'] ?? '';
    $district = $_POST['district'] ?? '';
    $country = $_POST['country'] ?? 'Sri Lanka';
    
    if (!empty($phone)) {
        $phoneRegex = '/^\+94[0-9]{9}$/';
        if (!preg_match($phoneRegex, $phone)) {
            $message = 'Please enter phone with country code (e.g., +94770020184)';
            $message_type = 'error';
        }
    }
    
    if (empty($message)) {
        $profileImage = $_FILES['profile_image'] ?? null;
        $imagePath = null;
        
        if ($profileImage && !empty($profileImage['name'])) {
            $uploadResult = uploadProfileImage($profileImage);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['filename'];
                $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, gender = ?, district = ?, country = ?, profile_image = ? WHERE id = ?");
                $stmt->execute([$name, $phone, $address, $gender, $district, $country, $imagePath, $_SESSION['user_id']]);
            } else {
                $message = $uploadResult['message'];
                $message_type = 'error';
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, gender = ?, district = ?, country = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $address, $gender, $district, $country, $_SESSION['user_id']]);
        }
        
        if (empty($message)) {
            $message = 'Profile updated successfully!';
            $message_type = 'success';
            $_SESSION['user_name'] = $name;
        }
    }
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php">Home</a></li>
                <li><a href="ai-prompts.php">AI Prompts</a></li>
                <li><a href="web-tools.php">Web Tools</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            
            <div class="nav-auth">
                <span style="margin-right: 8px; color: var(--primary); font-size: 14px;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                <a href="logout.php" class="btn-register">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="page-header reveal">
                <h1>My Profile</h1>
                <p>View and update your profile information</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> reveal">
                    <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-container reveal">
                <h2>Edit Profile</h2>
                
                <form id="profileForm" method="POST" action="" enctype="multipart/form-data">
                    <!-- Profile Image -->
                    <div class="profile-upload">
                        <div class="profile-image-container">
                            <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-image" id="profilePreview">
                            <?php else: ?>
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2366748b'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" alt="Profile" class="profile-image" id="profilePreview">
                            <?php endif; ?>
                        </div>
                        <label for="profile_image" class="profile-upload-btn" id="uploadBtn">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                    </div>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>

                    <!-- Email (readonly) -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly style="background: rgba(255,255,255,0.05);">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone (with country code)</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+94770020184">
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Your address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- Country -->
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country">
                            <option value="Sri Lanka" <?php echo ($user['country'] ?? 'Sri Lanka') == 'Sri Lanka' ? 'selected' : ''; ?>>Sri Lanka</option>
                            <option value="India" <?php echo ($user['country'] ?? '') == 'India' ? 'selected' : ''; ?>>India</option>
                            <option value="USA" <?php echo ($user['country'] ?? '') == 'USA' ? 'selected' : ''; ?>>United States</option>
                            <option value="UK" <?php echo ($user['country'] ?? '') == 'UK' ? 'selected' : ''; ?>>United Kingdom</option>
                            <option value="Australia" <?php echo ($user['country'] ?? '') == 'Australia' ? 'selected' : ''; ?>>Australia</option>
                            <option value="Canada" <?php echo ($user['country'] ?? '') == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                            <option value="Germany" <?php echo ($user['country'] ?? '') == 'Germany' ? 'selected' : ''; ?>>Germany</option>
                            <option value="France" <?php echo ($user['country'] ?? '') == 'France' ? 'selected' : ''; ?>>France</option>
                            <option value="Japan" <?php echo ($user['country'] ?? '') == 'Japan' ? 'selected' : ''; ?>>Japan</option>
                            <option value="Singapore" <?php echo ($user['country'] ?? '') == 'Singapore' ? 'selected' : ''; ?>>Singapore</option>
                            <option value="Malaysia" <?php echo ($user['country'] ?? '') == 'Malaysia' ? 'selected' : ''; ?>>Malaysia</option>
                            <option value="Other" <?php echo ($user['country'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div class="form-group">
                        <label for="district">District</label>
                        <select id="district" name="district">
                            <option value="">Select District</option>
                            <option value="colombo" <?php echo ($user['district'] ?? '') == 'colombo' ? 'selected' : ''; ?>>Colombo</option>
                            <option value="kandy" <?php echo ($user['district'] ?? '') == 'kandy' ? 'selected' : ''; ?>>Kandy</option>
                            <option value="galle" <?php echo ($user['district'] ?? '') == 'galle' ? 'selected' : ''; ?>>Galle</option>
                            <option value="jaffna" <?php echo ($user['district'] ?? '') == 'jaffna' ? 'selected' : ''; ?>>Jaffna</option>
                            <option value="anuradhapura" <?php echo ($user['district'] ?? '') == 'anuradhapura' ? 'selected' : ''; ?>>Anuradhapura</option>
                            <option value="ratnapura" <?php echo ($user['district'] ?? '') == 'ratnapura' ? 'selected' : ''; ?>>Ratnapura</option>
                            <option value="gampaha" <?php echo ($user['district'] ?? '') == 'gampaha' ? 'selected' : ''; ?>>Gampaha</option>
                            <option value="kegalle" <?php echo ($user['district'] ?? '') == 'kegalle' ? 'selected' : ''; ?>>Kegalle</option>
                            <option value="badulla" <?php echo ($user['district'] ?? '') == 'badulla' ? 'selected' : me:''; ?>>Badulla</option>
                            <option value="matale" <?php echo ($user['district'] ?? '') == 'matale' ? 'selected' : ''; ?>>Matale</option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label>Gender</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="gender" value="male" <?php echo ($user['gender'] ?? '') == 'male' ? 'checked' : ''; ?>> <span>Male</span>
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female" <?php echo ($user['gender'] ?? '') == 'female' ? 'checked' : ''; ?>> <span>Female</span>
                            </label>
                            <label>
                                <input type="radio" name="gender" value="other" <?php echo ($user['gender'] ?? '') == 'other' ? 'checked' : ''; ?>> <span>Other</span>
                            </label>
                        </div>
                    </div>

                    <!-- Skills Display -->
                    <?php if (!empty($user['skills'])): ?>
                    <div class="form-group">
                        <label>Your Skills</label>
                        <div class="skills-container">
                            <?php 
                            $skills = explode(',', $user['skills']);
                            foreach ($skills as $skill) {
                                echo '<span class="skill-tag">' . htmlspecialchars(trim($skill)) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" name="update_profile" class="btn-submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-nav">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i>Dashboard</a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Profile image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
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