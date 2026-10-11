<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Sanitize input
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if admin is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Get profile data
function getProfile() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM profile LIMIT 1");
    $profile = $result->fetch_assoc();
    $conn->close();
    return $profile;
}

// Get all skills
function getSkills($visibleOnly = true) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM skills";
    if ($visibleOnly) $sql .= " WHERE is_visible = 1";
    $sql .= " ORDER BY display_order ASC, skill_name ASC";
    $result = $conn->query($sql);
    $skills = [];
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
    $conn->close();
    return $skills;
}

// Get all projects
function getProjects($visibleOnly = true, $featuredOnly = false) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM projects";
    $conditions = [];
    if ($visibleOnly) $conditions[] = "is_visible = 1";
    if ($featuredOnly) $conditions[] = "is_featured = 1";
    if (!empty($conditions)) $sql .= " WHERE " . implode(" AND ", $conditions);
    $sql .= " ORDER BY display_order ASC, created_at DESC";
    $result = $conn->query($sql);
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $conn->close();
    return $projects;
}

// Get experience
function getExperience($visibleOnly = true) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM experience";
    if ($visibleOnly) $sql .= " WHERE is_visible = 1";
    $sql .= " ORDER BY display_order ASC";
    $result = $conn->query($sql);
    $exp = [];
    while ($row = $result->fetch_assoc()) {
        $exp[] = $row;
    }
    $conn->close();
    return $exp;
}

// Get education
function getEducation($visibleOnly = true) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM education";
    if ($visibleOnly) $sql .= " WHERE is_visible = 1";
    $sql .= " ORDER BY display_order ASC";
    $result = $conn->query($sql);
    $edu = [];
    while ($row = $result->fetch_assoc()) {
        $edu[] = $row;
    }
    $conn->close();
    return $edu;
}

// Get setting value
function getSetting($key, $default = '') {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $conn->close();
        return $row['setting_value'];
    }
    $conn->close();
    return $default;
}

// Upload file helper
function uploadFile($file, $folder = 'uploads') {
    $targetDir = __DIR__ . '/../assets/' . $folder . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Allowed types
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];
    if (!in_array($fileType, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Max 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large (max 5MB)'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'filename' => $fileName];
    }
    return ['success' => false, 'message' => 'Upload failed'];
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Count unread messages
function getUnreadCount() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT COUNT(*) as count FROM messages WHERE is_read = 0");
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['count'];
}
?>
