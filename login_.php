<?php
// Session එක ආරම්භ වී නැත්නම් ආරම්භ කරන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = 'Please fill in all fields';
        $message_type = 'danger'; // Bootstrap alert styling සඳහා 'danger' යෙදීම සුදුසුය
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            session_write_close();
            header("Location: dashboard.php");
            exit();
        } else {
            $message = 'Invalid email or password';
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechZevron</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body data-theme="classic">
    <!-- Animated Mesh Background -->
    <div class="mesh-bg">
        <div class="mesh1"></div>
        <div class="mesh2"></div>
        <div class="mesh3"></div>
    </div>

    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle">
        <span class="sun">☀️</span>
        <span class="moon">🌙</span>
    </button>

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
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            
            <div class="nav-auth">
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="page-header reveal">
                <h1>Welcome Back</h1>
                <p>Login to your TechZevron account</p>
            </div>

            <div class="form-container reveal">
                <h2>Login</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form id="loginForm" method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn-submit">Login</button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-nav">
        <ul>
            <li><a href="index.php"><span>🏠</span>Home</a></li>
            <li><a href="register.php"><span>📝</span>Register</a></li>
        </ul>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> TechZevron. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
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