# Super Portfolio Website - PHP + MySQL

Modern, fully responsive portfolio website with Admin Dashboard, animations, CV upload/download, and complete content management.

## Features

### Frontend (Portfolio Site)
- Beautiful modern dark theme design
- Smooth scroll animations (AOS-style)
- Typing effect on hero section
- Particle background animation
- Skill progress bars with animation
- Projects showcase with hover effects
- Experience & Education timeline
- Contact form (saves to database)
- CV download button
- Fully mobile responsive
- Social media links

### Admin Dashboard
- Secure login system
- Edit Profile (name, title, about, photo, contact info)
- Upload / Replace CV (PDF, DOC, DOCX)
- Manage Skills (add, edit, delete, hide/show)
- Manage Projects / Software (add, edit, delete, feature, hide/show)
- Manage Experience
- Manage Education
- View & manage contact messages
- Mobile responsive admin panel

## Setup Instructions

### 1. Requirements
- PHP 7.4+ (with mysqli extension)
- MySQL 5.7+ / MariaDB
- Apache / Nginx web server
- Or use XAMPP / WAMP / Laragon

### 2. Installation

1. **Copy files** to your web server directory:
   ```
   htdocs/portfolio/   (XAMPP)
   or
   www/portfolio/      (WAMP)
   ```

2. **Create database**:
   - Open phpMyAdmin
   - Import the file: `database.sql`
   - Or run the SQL manually

3. **Configure database** (if needed):
   - Edit `config/database.php`
   - Change DB_HOST, DB_USER, DB_PASS if different from defaults

4. **Set folder permissions**:
   ```
   assets/images/  → writable (755 or 777)
   assets/uploads/ → writable (755 or 777)
   ```

5. **Access the site**:
   - Portfolio: `http://localhost/portfolio/`
   - Admin: `http://localhost/portfolio/admin/`

### 3. Default Login
- **Username:** `admin`
- **Password:** `admin123`

**Important:** Change the password after first login!

### 4. How to Use Admin Panel

1. Login at `/admin/`
2. Go to **Profile & CV** → Update your name, photo, about text, upload CV
3. Go to **Skills** → Add your skills with levels
4. Go to **Projects** → Add your projects/software
5. Go to **Experience** → Add work history
6. Go to **Education** → Add education details
7. View **Messages** when someone contacts you

### 5. CV Download
- Upload CV from Admin → Profile & CV
- Users can download it from the homepage (Download CV button)

## File Structure

```
portfolio/
├── admin/
│   ├── index.php          (Dashboard)
│   ├── login.php
│   ├── logout.php
│   ├── profile.php        (Edit profile + CV upload)
│   ├── skills.php
│   ├── projects.php
│   ├── experience.php
│   ├── education.php
│   └── messages.php
├── assets/
│   ├── css/
│   │   ├── style.css      (Frontend styles + animations)
│   │   └── admin.css
│   ├── js/
│   │   └── main.js        (Animations, particles, form)
│   ├── images/            (Profile & project photos)
│   └── uploads/           (CV files)
├── config/
│   └── database.php
├── includes/
│   └── functions.php
├── index.php              (Main portfolio page)
├── contact_handler.php
├── database.sql
└── README.md
```

## Customization Tips

- Change colors: Edit CSS variables in `assets/css/style.css` (`:root`)
- Change primary color gradient in both CSS files
- Add more Font Awesome icons for skills
- Project images: upload via admin or place in `assets/images/`

## Security Notes

- Change default admin password immediately
- In production, use strong passwords
- Consider adding CSRF tokens for forms
- Keep PHP and MySQL updated

---
Made with ❤️ - Super Portfolio System
?>
