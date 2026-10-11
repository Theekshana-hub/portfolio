<?php
require_once '../includes/functions.php';
requireLogin();

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $title = clean($_POST['title'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $short_desc = clean($_POST['short_desc'] ?? '');
        $tech_stack = clean($_POST['tech_stack'] ?? '');
        $project_url = clean($_POST['project_url'] ?? '');
        $github_url = clean($_POST['github_url'] ?? '');
        $category = clean($_POST['category'] ?? 'Web');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_visible = isset($_POST['is_visible']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        $image = 'project-default.jpg';
        
        if ($action === 'edit') {
            $id = (int)$_POST['id'];
            $existing = $conn->query("SELECT image FROM projects WHERE id = $id")->fetch_assoc();
            $image = $existing['image'] ?? $image;
        }
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload = uploadFile($_FILES['image'], 'images');
            if ($upload['success']) {
                $image = $upload['filename'];
            }
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO projects (title, description, short_desc, image, tech_stack, project_url, github_url, category, is_featured, is_visible, display_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssssiii", $title, $description, $short_desc, $image, $tech_stack, $project_url, $github_url, $category, $is_featured, $is_visible, $display_order);
            $stmt->execute();
            setFlash('success', 'Project added successfully!');
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, short_desc=?, image=?, tech_stack=?, project_url=?, github_url=?, category=?, is_featured=?, is_visible=?, display_order=? WHERE id=?");
            $stmt->bind_param("ssssssssiiii", $title, $description, $short_desc, $image, $tech_stack, $project_url, $github_url, $category, $is_featured, $is_visible, $display_order, $id);
            $stmt->execute();
            setFlash('success', 'Project updated successfully!');
        }
        $stmt->close();
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM projects WHERE id = $id");
        setFlash('success', 'Project deleted!');
    }
    
    header('Location: projects.php');
    exit;
}

$projects = getProjects(false);
$editProject = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM projects WHERE id = $id");
    $editProject = $result->fetch_assoc();
}
$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | Admin</title>
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
            <a href="skills.php"><i class="fas fa-code"></i> Skills</a>
            <a href="projects.php" class="active"><i class="fas fa-briefcase"></i> Projects</a>
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
                <h1>Manage Projects / Software</h1>
            </div>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2><?php echo $editProject ? 'Edit Project' : 'Add New Project / Software'; ?></h2>
                    <?php if ($editProject): ?>
                    <a href="projects.php" class="btn btn-outline btn-sm">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $editProject ? 'edit' : 'add'; ?>">
                        <?php if ($editProject): ?><input type="hidden" name="id" value="<?php echo $editProject['id']; ?>"><?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Project Title *</label>
                                <input type="text" name="title" required value="<?php echo htmlspecialchars($editProject['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category">
                                    <?php foreach (['Web','App','Software','Mobile','UI/UX','Other'] as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo (($editProject['category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Short Description</label>
                            <input type="text" name="short_desc" value="<?php echo htmlspecialchars($editProject['short_desc'] ?? ''); ?>" placeholder="Brief one-line description">
                        </div>
                        <div class="form-group">
                            <label>Full Description</label>
                            <textarea name="description" rows="4"><?php echo htmlspecialchars($editProject['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tech Stack (comma separated)</label>
                            <input type="text" name="tech_stack" value="<?php echo htmlspecialchars($editProject['tech_stack'] ?? ''); ?>" placeholder="PHP, MySQL, JavaScript, React">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Live Project URL</label>
                                <input type="url" name="project_url" value="<?php echo htmlspecialchars($editProject['project_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>GitHub URL</label>
                                <input type="url" name="github_url" value="<?php echo htmlspecialchars($editProject['github_url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Project Image</label>
                                <input type="file" name="image" accept="image/*">
                                <?php if ($editProject && $editProject['image']): ?>
                                <small style="color:var(--gray);">Current: <?php echo htmlspecialchars($editProject['image']); ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="display_order" value="<?php echo $editProject['display_order'] ?? 0; ?>">
                            </div>
                            <div class="form-group" style="display:flex;flex-direction:column;justify-content:center;gap:8px;">
                                <label><input type="checkbox" name="is_featured" value="1" <?php echo (!$editProject || $editProject['is_featured']) ? 'checked' : ''; ?>> Featured</label>
                                <label><input type="checkbox" name="is_visible" value="1" <?php echo (!$editProject || $editProject['is_visible']) ? 'checked' : ''; ?>> Visible on website</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editProject ? 'Update' : 'Add'; ?> Project</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>All Projects (<?php echo count($projects); ?>)</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Tech</th>
                                    <th>Featured</th>
                                    <th>Visible</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($p['category']); ?></span></td>
                                    <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($p['tech_stack']); ?></td>
                                    <td><?php echo $p['is_featured'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                    <td><?php echo $p['is_visible'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">Hidden</span>'; ?></td>
                                    <td><?php echo $p['display_order']; ?></td>
                                    <td class="action-btns">
                                        <a href="projects.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this project?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($projects)): ?>
                                <tr><td colspan="7" style="text-align:center;color:var(--gray);">No projects yet. Add your first project above!</td></tr>
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
<?php $conn->close(); ?>
