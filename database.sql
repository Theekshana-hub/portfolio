-- Portfolio Database Schema - Theekshana Nipun
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    title VARCHAR(150) NOT NULL,
    tagline TEXT,
    about_text TEXT,
    email VARCHAR(100),
    phone VARCHAR(30),
    location VARCHAR(150),
    photo VARCHAR(255) DEFAULT 'profile.jpg',
    cv_file VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    twitter_url VARCHAR(255),
    facebook_url VARCHAR(255),
    instagram_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    skill_level INT DEFAULT 80,
    skill_category VARCHAR(50) DEFAULT 'Technical',
    icon VARCHAR(50) DEFAULT 'fas fa-code',
    display_order INT DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    short_desc VARCHAR(255),
    image VARCHAR(255) DEFAULT 'project-default.jpg',
    tech_stack VARCHAR(255),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    category VARCHAR(50) DEFAULT 'Web',
    is_featured TINYINT(1) DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(150) NOT NULL,
    start_date VARCHAR(20),
    end_date VARCHAR(20) DEFAULT 'Present',
    description TEXT,
    is_visible TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(150) NOT NULL,
    degree VARCHAR(150) NOT NULL,
    field VARCHAR(100),
    start_year VARCHAR(10),
    end_year VARCHAR(10),
    description TEXT,
    is_visible TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Theekshananipun237@gmail.com');

INSERT INTO profile (full_name, title, tagline, about_text, email, phone, location, photo, cv_file, github_url, linkedin_url) VALUES 
('Theekshana Nipun', 'IT Undergraduate | Software Engineering', 
'Passionate about technology, web development & building innovative software solutions',
'I am H.M.T.N. Jayawardana, an enthusiastic IT undergraduate currently pursuing a Higher Diploma in Software Engineering at ICBT Campus, offered in collaboration with Cardiff Metropolitan University, UK. I am passionate about technology and software development, with growing skills in programming, web development, and database management. I enjoy learning new technologies, solving complex problems, and working collaboratively to create efficient and user-friendly software solutions. My ambition is to build a successful career as a software engineer by continuously improving my technical knowledge, gaining practical experience, and contributing to innovative projects in the IT industry.',
'Theekshananipun237@gmail.com', '076-8829084', 'Kathupotha Road, Kadahapola, Horombawa',
'profile.jpg',
'Theekshana_Nipun_CV.pdf',
'https://github.com/Theekshana-hub',
'https://www.linkedin.com/in/theekshana-nipun-092690297');

INSERT INTO skills (skill_name, skill_level, skill_category, icon, display_order) VALUES
('HTML5', 95, 'Frontend', 'fab fa-html5', 1),
('CSS3', 92, 'Frontend', 'fab fa-css3-alt', 2),
('JavaScript', 90, 'Frontend', 'fab fa-js', 3),
('Bootstrap', 90, 'Frontend', 'fab fa-bootstrap', 4),
('PHP', 88, 'Backend', 'fab fa-php', 5),
('MySQL', 88, 'Backend', 'fas fa-database', 6),
('Node.js', 80, 'Backend', 'fab fa-node-js', 7),
('Express.js', 78, 'Backend', 'fas fa-server', 8),
('MongoDB', 75, 'Backend', 'fas fa-leaf', 9),
('Java', 85, 'Programming', 'fab fa-java', 10),
('C++', 80, 'Programming', 'fas fa-code', 11),
('C#', 78, 'Programming', 'fas fa-code', 12),
('Dart', 70, 'Programming', 'fas fa-mobile-alt', 13),
('Git & GitHub', 90, 'Tools', 'fab fa-git-alt', 14),
('Figma', 75, 'Design', 'fab fa-figma', 15),
('Firebase', 72, 'Backend', 'fas fa-fire', 16);

INSERT INTO projects (title, description, short_desc, tech_stack, project_url, category, is_featured, display_order) VALUES
('Bella Beauty Salon', 'Complete full-stack web solution for Bella Beauty Salon with an online booking system and automated SMS notifications sent to customers phone numbers. Features include service management, appointment booking, and admin panel.', 'Online booking system with SMS notifications', 'HTML, CSS, JavaScript, Bootstrap, PHP, MySQL', 'https://salon23.html-5.me/', 'Web', 1, 1),
('Eye Care Web Application', 'Complete full-stack web application for eye care services. Includes patient management, appointment booking, and service information with a modern responsive design.', 'Full-stack eye care management system', 'HTML, CSS, JavaScript, Bootstrap, PHP, MySQL', 'https://icare.42web.io/', 'Web', 1, 2),
('SkillPro Institute Platform', 'Designed and implemented a complete full-stack web solution suitable for the SkillPro Institute. Features course management, student registration, and administrative tools.', 'Full-stack institute management platform', 'HTML, CSS, JavaScript, Bootstrap, PHP, MySQL', '#', 'Web', 1, 3),
('Online Booking Mobile App', 'Mobile app with room booking, activities booking, view history features, and an integrated admin panel. Built for seamless hotel/resort booking experience.', 'Room & activities booking mobile app', 'Java, MySQL, XML', '#', 'Mobile', 1, 4),
('Personal Portfolio Website', 'Designed and developed a responsive portfolio website with modern UI/UX features and interactive functionality. Showcases projects and skills with smooth animations.', 'Modern responsive portfolio with UI/UX', 'HTML, CSS, JavaScript, Bootstrap', 'https://theekshana-hub.github.io/theekshana-njpun.github.io/', 'Web', 0, 5);

INSERT INTO education (institution, degree, field, start_year, end_year, description, display_order) VALUES
('Cardiff Metropolitan University (ICBT Campus)', 'Higher Diploma', 'Computing and Software Engineering', '2025', 'Present', 'Currently pursuing Higher Diploma in Computing and Software Engineering at ICBT Campus in collaboration with Cardiff Metropolitan University, UK.', 1),
('Mayurapada Central College, Narammala', 'G.C.E. Advanced Level (A/L)', '', '2024', '2024', 'Completed G.C.E. Advanced Level examinations.', 2),
('Mayurapada Central College, Narammala', 'G.C.E. Ordinary Level (O/L)', '', '2019', '2019', 'Completed G.C.E. Ordinary Level examinations.', 3);

INSERT INTO experience (company, position, start_date, end_date, description, display_order) VALUES
('Freelance / Personal Projects', 'Full Stack Web Developer', '2024', 'Present', 'Developed multiple full-stack web applications including salon booking systems, eye care platforms, and institute management solutions using PHP, MySQL, and modern frontend technologies.', 1),
('Mobile App Development', 'Android Developer', '2024', '2024', 'Developed an Online Booking Mobile App with room booking, activities booking, history features and admin panel using Java and MySQL.', 2);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Theekshana Nipun | IT Undergraduate'),
('site_description', 'Portfolio of Theekshana Nipun - IT Undergraduate specializing in Software Engineering, Web Development and Mobile Apps'),
('primary_color', '#6366f1'),
('show_projects', '1'),
('show_skills', '1'),
('show_experience', '1'),
('show_education', '1'),
('show_contact', '1');
