-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255) DEFAULT NULL,
    gender VARCHAR(10),
    country VARCHAR(50) DEFAULT 'Sri Lanka',
    district VARCHAR(50),
    comment TEXT,
    theme VARCHAR(10) DEFAULT 'light',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages table for contact form
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    user_id INT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Resources table
CREATE TABLE IF NOT EXISTS ai_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    prompt_text TEXT,
    category VARCHAR(50) DEFAULT 'ai',
    image_url VARCHAR(255),
    download_link VARCHAR(500) DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Engagement table (likes, comments, reports)
CREATE TABLE IF NOT EXISTS engagement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    type ENUM('like', 'comment', 'report', 'fake_comment') NOT NULL,
    comment_text TEXT,
    user_name VARCHAR(100) DEFAULT NULL,
    user_email VARCHAR(100) DEFAULT NULL,
    is_fake TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (resource_id, user_id, type)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, phone, address, gender, country, district, theme) 
VALUES ('Admin', 'techzevron@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94770020184', 'New meehitiya mahawala waththa, Ratnapura, Sri Lanka', 'male', 'Sri Lanka', 'ratnapura', 'light');

-- Insert sample AI resources
INSERT INTO ai_resources (title, description, prompt_text, category, image_url) VALUES
('Master ChatGPT', 'Unlock 500+ expert-level prompts for business, coding, and marketing.', 'Create a comprehensive marketing strategy for a tech startup...', 'ai', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400'),
('AI Image Mastery', 'Generate stunning 4K realistic images with these hand-crafted text prompts.', 'A majestic lion with golden mane standing on rocky cliff at sunset, hyperrealistic, 8K...', 'design', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400'),
('Developer Snippets', 'Copy-paste professional CSS & JS animations for your next web project.', '.gradient-text { background: linear-gradient(90deg, #667eea, #764ba2); -webkit-background-clip: text; }', 'coding', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400');

