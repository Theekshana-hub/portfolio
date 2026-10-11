<?php
require_once '../includes/functions.php';
requireLogin();

$conn = getDBConnection();

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $skill_name = clean($_POST['skill_name'] ?? '');
        $skill_level = (int)($_POST['skill_level'] ?? 80);
        $skill_category = clean($_POST['skill_category'] ?? 'Technical');
        $icon = clean($_POST['icon'] ?? 'fas fa-code');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_visible = isset($_POST['is_visible']) ? 1 : 0;
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO skills (skill_name, skill_level, skill_category, icon, display_order, is_visible) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sisisi", $skill_name, $skill_level, $skill_category, $icon, $display_order, $is_visible);
            $stmt->execute();
            setFlash('success', 'Skill added successfully!');
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE skills SET skill_name=?, skill_level=?, skill_category=?, icon=?, display_order=?, is_visible=? WHERE id=?");
            $stmt->bind_param("sisisii", $skill_name, $skill_level, $skill_category, $icon, $display_order, $is_visible, $id);
            $stmt->execute();
            setFlash('success', 'Skill updated successfully!');
        }
        $stmt->close();
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM skills WHERE id = $id");
        setFlash('success', 'Skill deleted!');
    }
    
    header('Location: skills.php');
    exit;
}

$skills = getSkills(false);
$editSkill = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM skills WHERE id = $id");
    $editSkill = $result->fetch_assoc();
}
$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-cube"></i> Portfolio Admin</h2></div>
        <nav class="sidebar-nav">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile & CV</a>
            <a href="skills.php" class="active"><i class="fas fa-code"></i> Skills</a>
            <a href="projects.php"><i class="fas fa-briefcase"></i> Projects</a>
            <a href="experience.php"><i class="fas fa-building"></i> Experience</a>
            <a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread): ?><span class="badge badge-danger"><?php echo $unread; ?></span><?php endif; ?></a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn btn-outline btn-sm" style="width:100%;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:1rem;">
                <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
                <h1>Manage Skills</h1>
            </div>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="card">
                <div class="card-header">
                    <h2><?php echo $editSkill ? 'Edit Skill' : 'Add New Skill'; ?></h2>
                    <?php if ($editSkill): ?>
                    <a href="skills.php" class="btn btn-outline btn-sm">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $editSkill ? 'edit' : 'add'; ?>">
                        <?php if ($editSkill): ?><input type="hidden" name="id" value="<?php echo $editSkill['id']; ?>"><?php endif; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Skill Name *</label>
                                <input type="text" name="skill_name" required value="<?php echo htmlspecialchars($editSkill['skill_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Level (0-100)</label>
                                <input type="number" name="skill_level" min="0" max="100" value="<?php echo $editSkill['skill_level'] ?? 80; ?>">
                            </div>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="skill_category">
                                    <?php foreach (['Technical','Frontend','Backend','Design','Tools','Other'] as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo (($editSkill['skill_category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Icon (Font Awesome class)</label>
                                <input type="text" name="icon" value="<?php echo htmlspecialchars($editSkill['icon'] ?? 'fas fa-code'); ?>" placeholder="fab fa-php">
                            </div>
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="display_order" value="<?php echo $editSkill['display_order'] ?? 0; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_visible" value="1" <?php echo (!$editSkill || $editSkill['is_visible']) ? 'checked' : ''; ?>> Visible on website</label>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editSkill ? 'Update' : 'Add'; ?> Skill</button>
                    </form>
                </div>
            </div>

            <!-- Skills List -->
            <div class="card">
                <div class="card-header"><h2>All Skills (<?php echo count($skills); ?>)</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Icon</th>
                                    <th>Name</th>
                                    <th>Level</th>
                                    <th>Category</th>
                                    <th>Order</th>
                                    <th>Visible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skills as $s): ?>
                                <tr>
                                    <td><i class="<?php echo htmlspecialchars($s['icon']); ?>" style="font-size:1.2rem;color:var(--primary);"></i></td>
                                    <td><strong><?php echo htmlspecialchars($s['skill_name']); ?></strong></td>
                                    <td><?php echo $s['skill_level']; ?>%</td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($s['skill_category']); ?></span></td>
                                    <td><?php echo $s['display_order']; ?></td>
                                    <td><?php echo $s['is_visible'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                    <td class="action-btns">
                                        <a href="skills.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this skill?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
<?php $conn->close(); ?>
