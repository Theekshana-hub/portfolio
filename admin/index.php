<?php
require_once '../includes/functions.php';
requireLogin();

$profile = getProfile();
$skills = getSkills(false);
$projects = getProjects(false);
$experience = getExperience(false);
$education = getEducation(false);
$unread = getUnreadCount();

$conn = getDBConnection();
$msgCount = $conn->query("SELECT COUNT(*) as c FROM messages")->fetch_assoc()['c'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-cube"></i> Portfolio Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile & CV</a>
            <a href="skills.php"><i class="fas fa-code"></i> Skills</a>
            <a href="projects.php"><i class="fas fa-briefcase"></i> Projects</a>
            <a href="experience.php"><i class="fas fa-building"></i> Experience</a>
            <a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread): ?><span class="badge badge-danger" style="margin-left:auto;"><?php echo $unread; ?></span><?php endif; ?></a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn btn-outline btn-sm" style="width:100%;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:1rem;">
                <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
            </div>
            <div class="topbar-right">
                <span style="color:var(--gray);">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
            </div>
        </div>

        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($projects); ?></h3>
                        <p>Projects</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-code"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($skills); ?></h3>
                        <p>Skills</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-building"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($experience); ?></h3>
                        <p>Experience</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-envelope"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $msgCount; ?></h3>
                        <p>Messages <?php if($unread): ?>(<?php echo $unread; ?> new)<?php endif; ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-user"></i> Profile Overview</h2>
                    <a href="profile.php" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit Profile</a>
                </div>
                <div class="card-body">
                    <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap;">
                        <img src="../assets/images/<?php echo htmlspecialchars($profile['photo'] ?? 'default.jpg'); ?>" 
                             class="preview-img-lg"
                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name']??'User'); ?>&size=150&background=6366f1&color=fff'">
                        <div>
                            <h3 style="font-size:1.5rem;margin-bottom:0.3rem;"><?php echo htmlspecialchars($profile['full_name'] ?? ''); ?></h3>
                            <p style="color:var(--primary);font-weight:600;margin-bottom:0.5rem;"><?php echo htmlspecialchars($profile['title'] ?? ''); ?></p>
                            <p style="color:var(--gray);margin-bottom:0.3rem;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($profile['email'] ?? ''); ?></p>
                            <p style="color:var(--gray);margin-bottom:0.3rem;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($profile['phone'] ?? ''); ?></p>
                            <p style="color:var(--gray);"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($profile['location'] ?? ''); ?></p>
                            <?php if (!empty($profile['cv_file'])): ?>
                            <p style="margin-top:0.8rem;">
                                <span class="badge badge-success"><i class="fas fa-file-pdf"></i> CV Uploaded</span>
                                <a href="../assets/uploads/<?php echo htmlspecialchars($profile['cv_file']); ?>" target="_blank" class="btn btn-sm btn-outline" style="margin-left:8px;">View CV</a>
                            </p>
                            <?php else: ?>
                            <p style="margin-top:0.8rem;"><span class="badge badge-warning">No CV uploaded</span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-briefcase"></i> Recent Projects</h2>
                    <a href="projects.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Manage</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Featured</th>
                                    <th>Visible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($projects, 0, 5) as $p): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($p['category']); ?></span></td>
                                    <td><?php echo $p['is_featured'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                    <td><?php echo $p['is_visible'] ? '<span class="badge badge-success">Visible</span>' : '<span class="badge badge-danger">Hidden</span>'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($projects)): ?>
                                <tr><td colspan="4" style="text-align:center;color:var(--gray);">No projects yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
