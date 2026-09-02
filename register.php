<?php
require_once 'db.php';

$message = '';
$message_type = '';

// Default values set කිරීම
$name = '';
$email = '';
$phone = '';
$address = '';
$gender = '';
$district = '';
$country = 'Sri Lanka';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Submit කළ Data variable වලට ලබා ගැනීම (Form data persist කිරීම සඳහා)
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $district = $_POST['district'] ?? '';
    $country = $_POST['country'] ?? 'Sri Lanka';
    
    // Handle profile image upload
    $profileImage = $_FILES['profile_image'] ?? null;
    $imagePath = null;
    
    if ($profileImage && $profileImage['name']) {
        $uploadResult = uploadProfileImage($profileImage);
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filename'];
        }
    }
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $message = 'Please fill in all required fields';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $message_type = 'error';
    } elseif (!empty($phone)) {
        $phoneRegex = '/^\+94[0-9]{9}$/';
        if (!preg_match($phoneRegex, $phone)) {
            $message = 'Please enter phone with country code (e.g., +94770020184)';
            $message_type = 'error';
        }
    }
    
    if (empty($message) && strlen($password) < 8) {
        $message = 'Password must be at least 8 characters';
        $message_type = 'error';
    }
    
    if (empty($message)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $message = 'This email is already registered';
            $message_type = 'error';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, gender, country, district, comment, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            try {
                $stmt->execute([$name, $email, $hashed_password, $phone, $address, $gender, $country, $district, '', $imagePath]);
                
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                sendWelcomeEmail($email, $name);
                
                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                $message = 'Registration failed. Please try again.';
                $message_type = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechZevron</title>
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
        
        input, textarea {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }
        
        /* Register Page Specific Styles */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .register-hero {
            text-align: center;
            padding: 40px 20px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .register-hero::before {
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
        
        .register-hero h1 {
            font-size: 38px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--text-primary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .form-container {
            max-width: 550px;
            margin: 0 auto;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 40px;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .form-container h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
            color: var(--text-primary);
        }
        
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 15px;
            transition: var(--transition);
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px var(--glow-color), 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-muted);
        }
        
        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23667eea' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }
        
        .form-group select option {
            background: var(--secondary);
            color: var(--text-primary);
            padding: 12px;
        }
        
        /* Radio & Checkbox Groups */
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
        }
        
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-transform: none;
            font-size: 15px;
            color: var(--text-secondary);
            padding: 10px 20px;
            border-radius: 25px;
            transition: var(--transition);
            margin: 0;
            border: 1px solid transparent;
        }
        
        .radio-group label:hover {
            background: rgba(14, 165, 233, 0.1);
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .radio-group input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        
        /* Profile Image Upload */
        .profile-upload {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .profile-image-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
        }
        
        .profile-image-wrapper .profile-ring {
            position: absolute;
            top: -8px;
            left: -8px;
            width: calc(100% + 16px);
            height: calc(100% + 16px);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff, #ff0000, #ffff00, #0000ff);
            background-size: 300% 300%;
            animation: neonRotate 3s linear infinite;
            z-index: 0;
            filter: blur(4px);
            opacity: 0.8;
        }
        
        @keyframes neonRotate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .profile-upload .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }
        
        .profile-upload-btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .profile-upload-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* Submit Button with Glow */
        .btn-submit {
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px var(--glow-color);
        }
        
        .btn-submit:hover::before {
            left: 100%;
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        /* Password Strength */
        .password-strength {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .password-strength-bar.weak { width: 33%; background: #ef4444; }
        .password-strength-bar.medium { width: 66%; background: #f59e0b; }
        .password-strength-bar.strong { width: 100%; background: #22c55e; }
        
        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-weight: 500;
            animation: fadeInUp 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid var(--accent);
            color: var(--accent);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            color: #ef4444;
        }
        
        /* Form Footer */
        .form-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .form-footer p {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        /* Glowing Input Border Animation */
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 0 var(--glow-color); }
            50% { box-shadow: 0 0 20px 2px var(--glow-color); }
        }
        
        .form-group input:focus,
        .form-group select:focus {
            animation: glowPulse 2s infinite;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .form-container {
                padding: 25px;
                margin: 0 15px;
            }
            
            .register-hero h1 {
                font-size: 28px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="register-hero reveal">
                <h1>Create Account</h1>
                <p style="color: var(--text-secondary); font-size: 16px;">Join TechZevron today</p>
            </div>

            <div class="form-container reveal">
                <h2><i class="fas fa-user-plus"></i> Register</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form id="registerForm" method="POST" action="" enctype="multipart/form-data">
                    <!-- Profile Image Upload -->
                    <div class="profile-upload">
                        <div class="profile-image-wrapper">
                            <div class="profile-ring"></div>
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2366748b'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" alt="Profile" class="profile-image" id="profilePreview">
                        </div>
                        <label for="profile_image" class="profile-upload-btn">
                            <i class="fas fa-camera"></i> Upload Photo
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                    </div>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter your full name" required>
                        <small id="namePreview" style="color: var(--text-muted); font-size: 12px; display: block; margin-top: 5px;"></small>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone (with country code)</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="+94770020184">
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Your full address" rows="2"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                        <div id="password-match-msg" style="font-size: 13px; margin-top: 5px;"></div>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label>Gender</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="gender" value="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> onchange="updateNamePrefix()"> 
                                <span>👨 Male</span>
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> onchange="updateNamePrefix()"> 
                                <span>👩 Female</span>
                            </label>
                            <label>
                                <input type="radio" name="gender" value="other" <?php echo ($gender === 'other') ? 'checked' : ''; ?> onchange="updateNamePrefix()"> 
                                <span>🧑 Other</span>
                            </label>
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country" onchange="toggleDistrict()">
                            <?php
                            $countries = ["Sri Lanka", "India", "United States", "United Kingdom", "Canada", "Australia", "Germany", "France", "Japan", "China", "Russia", "Brazil", "Mexico", "South Korea", "Singapore", "Malaysia", "Thailand", "Indonesia", "Pakistan", "Bangladesh", "Nepal", "Afghanistan", "Saudi Arabia", "United Arab Emirates", "Qatar", "Kuwait", "Bahrain", "Oman", "South Africa", "Egypt", "Nigeria", "Kenya", "Italy", "Spain", "Portugal", "Netherlands", "Belgium", "Switzerland", "Austria", "Poland", "Sweden", "Norway", "Denmark", "Finland", "Ireland", "New Zealand", "Philippines", "Vietnam", "Other"];
                            foreach ($countries as $c) {
                                $selected = ($country === $c) ? 'selected' : '';
                                echo "<option value=\"$c\" $selected>$c</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- District (shows only for Sri Lanka) -->
                    <div class="form-group" id="districtGroup" style="<?php echo ($country === 'Sri Lanka') ? 'display: block;' : 'display: none;'; ?>">
                        <label for="district">District</label>
                        <select id="district" name="district">
                            <option value="">Select District</option>
                            <?php
                            $districts = ["colombo", "kandy", "galle", "jaffna", "anuradhapura", "ratnapura", "matale", "gampaha", "kegalle", "badulla", "kurunegala", "puttalam", "avissawella", "hambantota", "trincomalee", "batticaloa", "vanni"];
                            foreach ($districts as $d) {
                                $selected = ($district === $d) ? 'selected' : '';
                                echo "<option value=\"$d\" $selected>" . ucfirst($d) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

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
        
        // Show/hide district based on country selection
        function toggleDistrict() {
            const countrySelect = document.getElementById('country');
            const districtGroup = document.getElementById('districtGroup');
            
            if (countrySelect.value === 'Sri Lanka') {
                districtGroup.style.display = 'block';
            } else {
                districtGroup.style.display = 'none';
                document.getElementById('district').value = '';
            }
        }
        
        // Name prefix based on gender
        function updateNamePrefix() {
            const gender = document.querySelector('input[name="gender"]:checked');
            const nameInput = document.getElementById('name');
            const namePreview = document.getElementById('namePreview');
            
            if (gender && nameInput.value) {
                let prefix = '';
                if (gender.value === 'male') {
                    prefix = 'Mr. ';
                } else if (gender.value === 'female') {
                    prefix = 'Ms. ';
                } else {
                    prefix = '';
                }
                
                // Extract just the name without any prefix
                let cleanName = nameInput.value.replace(/^(Mr\.|Ms\.|Mrs\.)\s*/i, '');
                
                if (prefix && !nameInput.value.startsWith(prefix)) {
                    namePreview.textContent = 'Will be saved as: ' + prefix + cleanName;
                } else {
                    namePreview.textContent = '';
                }
            }
        }
        
        // Add prefix on name input change
        document.getElementById('name').addEventListener('input', function() {
            updateNamePrefix();
        });
        
        // Add prefix when typing after gender selected
        document.getElementById('name').addEventListener('blur', function() {
            const gender = document.querySelector('input[name="gender"]:checked');
            if (gender) {
                let currentName = this.value;
                let cleanName = currentName.replace(/^(Mr\.|Ms\.|Mrs\.)\s*/i, '');
                
                if (gender.value === 'male' && !currentName.startsWith('Mr. ')) {
                    this.value = 'Mr. ' + cleanName;
                } else if (gender.value === 'female' && !currentName.startsWith('Ms. ') && !currentName.startsWith('Mrs. ')) {
                    this.value = 'Ms. ' + cleanName;
                }
                updateNamePrefix();
            }
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            strengthBar.className = 'password-strength-bar';
            
            if (password.length >= 6) {
                if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                    strengthBar.classList.add('strong');
                } else if (password.length >= 6) {
                    strengthBar.classList.add('medium');
                }
            } else if (password.length > 0) {
                strengthBar.classList.add('weak');
            }
        });
        
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

        document.getElementById('confirm_password').addEventListener('input', function() {
            let pass = document.getElementById('password').value;
            let confirmPass = this.value;
            let message = document.getElementById('password-match-msg');

            if (pass === confirmPass) {
                message.innerHTML = 'Password Matched ✅';
                message.style.color = '#00ff00';
            } else {
                message.innerHTML = 'Password Not Match ❌';
                message.style.color = '#ff0000';
            }
        });

        // Scroll Reveal
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