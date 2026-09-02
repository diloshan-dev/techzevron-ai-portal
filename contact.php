<?php
// Session එක start වී නැත්නම් ආරම්භ කරන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message_text)) {
        $message = 'Please fill in all required fields';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $message_type = 'error';
    } else {
        // Login නොවී සිටියද message එක database එකට save වේ.
        $user_id = (function_exists('isLoggedIn') && isLoggedIn()) ? ($_SESSION['user_id'] ?? null) : null;
        
        try {
            $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message_text, $user_id]);
            
            if (function_exists('notifyAdmin')) {
                notifyAdmin($name, $email, $subject, $message_text);
            }
            
            $message = 'Thank you for contacting us! Your message has been sent.';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
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
        
        /* Contact Page Specific Styles */
        .contact-hero {
            text-align: center;
            padding: 50px 20px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }
        
        .contact-hero::before {
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
        
        /* Bounce Animation for Contact Us Title */
        @keyframes bounceTitle {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .contact-hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--text-primary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            animation: bounceTitle 2s infinite; /* Title Bounce Effect */
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
            align-items: start;
        }
        
        /* Contact Info Card */
        .contact-info {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 35px;
            position: relative;
            overflow: hidden;
        }
        
        .contact-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary), var(--accent));
        }
        
        .contact-info h3 {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .contact-info h3 i {
            color: var(--primary);
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-sm);
            margin-bottom: 15px;
            transition: var(--transition);
            border: 1px solid transparent;
        }
        
        .contact-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border);
            transform: translateX(5px);
        }
        
        .contact-item-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            flex-shrink: 0;
            font-size: 18px;
        }
        
        .contact-item-content strong {
            display: block;
            color: var(--text-primary);
            margin-bottom: 4px;
            font-size: 15px;
        }
        
        .contact-item-content p {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        
        /* YouTube Button */
        .youtube-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 24px;
            background: linear-gradient(45deg, #ff0000, #ff4444);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            margin-top: 20px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .youtube-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .youtube-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.4);
        }
        
        .youtube-btn:hover::before {
            left: 100%;
        }
        
        /* Contact Form */
        .contact-form-container {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 35px;
            position: relative;
            overflow: hidden;
        }
        
        .contact-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, var(--primary), transparent);
            opacity: 0.1;
            filter: blur(40px);
        }
        
        .contact-form-container h3 {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .contact-form-container h3 i {
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
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
        
        .form-group textarea {
            height: 140px;
            resize: vertical;
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
        
        /* Success/Error Messages */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 25px;
            font-weight: 500;
            animation: fadeInUp 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
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
        
        /* Floating Labels Effect */
        .form-group {
            position: relative;
        }
        
        .form-group input:focus + label,
        .form-group textarea:focus + label {
            color: var(--primary);
        }
        
        /* Glowing Input Border Animation */
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 0 var(--glow-color); }
            50% { box-shadow: 0 0 20px 2px var(--glow-color); }
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            animation: glowPulse 2s infinite;
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .contact-hero h1 {
                font-size: 32px;
            }
            
            .contact-info,
            .contact-form-container {
                padding: 25px;
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

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 30px;">
            <a href="index.php" class="logo">Tech<span>Zevron</span></a>
            
            <div class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" style="display: flex !important; cursor: pointer;">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-auth">
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <span class="user-greeting"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                    <a href="dashboard.php" class="btn-login">Dashboard</a>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Slide-out Sidebar -->
    <div class="sidebar" id="sidebar" style="width: 80%; max-width: 400px; right: -80%;">
        <div class="sidebar-header" style="padding: 30px; text-align: center; border-bottom: 1px solid var(--glass-border); position: relative;">
            <div class="sidebar-logo-wrapper" style="width: 100px; height: 100px; margin: 0 auto 15px; position: relative;">
                <div class="sidebar-glow" style="position: absolute; top: -8px; left: -8px; width: calc(100% + 16px); height: calc(100% + 16px); border-radius: 50%; background: linear-gradient(45deg, #ff0000, #ffff00, #0000ff); background-size: 300% 300%; animation: rainbowMove 2s linear infinite; z-index: 0; filter: blur(8px);"></div>
                <img src="https://cdn.githubraw.com/spdilshan14-web/photos/main/Diloshan.jpg" alt="TechZevron" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; border: 3px solid #fff;">
            </div>
            <a href="index.php" class="logo" style="font-size: 22px;">Tech<span>Zevron</span></a>
            <span class="sidebar-close" onclick="toggleSidebar()" style="position: absolute; top: 20px; right: 20px; font-size: 32px; cursor: pointer;">&times;</span>
        </div>
        
        <ul class="sidebar-menu" style="padding: 20px 0; flex: 1; overflow-y: auto;">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="ai-prompts.php"><i class="fas fa-robot"></i> AI Prompts</a></li>
            <li><a href="web-tools.php"><i class="fas fa-tools"></i> Web Tools</a></li>
            <li><a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="#" onclick="openThemeModal()"><i class="fas fa-palette"></i> Themes</a></li>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <li><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="sidebar-footer" style="padding: 25px; border-top: 1px solid var(--glass-border); text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px; margin: 0;">Developed by P. Diloshan</p>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="contact-hero reveal">
                <h1>Contact Us</h1>
                <p style="color: var(--text-secondary); font-size: 18px;">We'd love to hear from you</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" style="max-width: 800px; margin: 0 auto 30px;">
                    <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info reveal">
                    <h3><i class="fas fa-address-card"></i> Get in Touch</h3>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon">
                            <i class="fas fa-user" style="color: white;"></i>
                        </div>
                        <div class="contact-item-content">
                            <strong>Name</strong>
                            <p>P. Diloshan</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon">
                            <i class="fas fa-map-marker-alt" style="color: white;"></i>
                        </div>
                        <div class="contact-item-content">
                            <strong>Address</strong>
                            <p>New Meehitiya Mahawala Waththa<br>Ratnapura, Sri Lanka</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon">
                            <i class="fas fa-envelope" style="color: white;"></i>
                        </div>
                        <div class="contact-item-content">
                            <strong>Email</strong>
                            <p>techzevron@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon">
                            <i class="fas fa-phone" style="color: white;"></i>
                        </div>
                        <div class="contact-item-content">
                            <strong>Phone</strong>
                            <p>+94 77 002 0184<br>+94 72 161 7577</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon">
                            <i class="fas fa-clock" style="color: white;"></i>
                        </div>
                        <div class="contact-item-content">
                            <strong>Business Hours</strong>
                            <p>Monday - Friday: 9AM - 6PM<br>Saturday: 9AM - 1PM</p>
                        </div>
                    </div>
                    
                    <a href="https://www.youtube.com/channel/UC2Xvqo5QjpeR1xp9CKsPMWA?sub_confirmation=1" target="_blank" class="youtube-btn">
                        <i class="fab fa-youtube"></i> Subscribe on YouTube
                    </a>
                </div>
                
                <!-- Contact Form -->
                <div class="contact-form-container reveal">
                    <h3><i class="fas fa-paper-plane"></i> Send us a Message</h3>
                    
                    <!-- PHP action empty නිසා එකම page එකට POST වේ -->
                    <form id="contactForm" method="POST" action="">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="Enter subject">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" placeholder="Enter your message" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="developed-by">
                <p>Developed by <a href="#">P. Diloshan</a></p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ❤️ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // EmailJS Initialization
        (function() {
            emailjs.init("LQ6KRD2AY1zkZJGt4"); // Your EmailJS Public Key
        })();
        
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            // Form Submit Event (Standard PHP POST submit එක පවත්වා ගනිමින් EmailJS මගින් Email යැවීම)
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value || 'No Subject';
            const message = document.getElementById('message').value;
            const currentTime = new Date().toLocaleString();
            
            const templateParams = {
                from_name: name,
                from_email: email,
                subject: subject,
                message: message,
                time: currentTime
            };
            
            // සටහන: 'YOUR_SERVICE_ID' වෙනුවට ඔබේ EmailJS Service ID එක ඇතුළත් කරන්න (උදා: 'service_abc123')
            emailjs.send('YOUR_SERVICE_ID', 'template_l9ui58k', templateParams)
                .then(function(response) {
                    console.log('EMAILJS SUCCESS!', response.status, response.text);
                })
                .catch(function(error) {
                    console.log('EMAILJS FAILED...', error);
                });
        });

        // Sidebar and UI helper functions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            if (sidebar && overlay) {
                const isOpen = sidebar.classList.contains('active');
                if (isOpen) {
                    sidebar.classList.remove('active');
                    sidebar.style.right = '-80%';
                    overlay.classList.remove('active');
                } else {
                    sidebar.classList.add('active');
                    sidebar.style.right = '0';
                    overlay.classList.add('active');
                }
            }
            if (menuToggle) menuToggle.classList.toggle('active');
        }
    </script>
</body>
</html>