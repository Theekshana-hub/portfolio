<?php
require_once '../includes/functions.php';
requireLogin();

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $institution = clean($_POST['institution'] ?? '');
        $degree = clean($_POST['degree'] ?? '');
        $field = clean($_POST['field'] ?? '');
        $start_year = clean($_POST['start_year'] ?? '');
        $end_year = clean($_POST['end_year'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_visible = isset($_POST['is_visible']) ? 1 : 0;
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO education (institution, degree, field, start_year, end_year, description, display_order, is_visible) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssii", $institution, $degree, $field, $start_year, $end_year, $description, $display_order, $is_visible);
            $stmt->execute();
            setFlash('success', 'Education added!');
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE education SET institution=?, degree=?, field=?, start_year=?, end_year=?, description=?, display_order=?, is_visible=? WHERE id=?");
            $stmt->bind_param("ssssssiii", $institution, $degree, $field, $start_year, $end_year, $description, $display_order, $is_visible, $id);
            $stmt->execute();
            setFlash('success', 'Education updated!');
        }
        $stmt->close();
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM education WHERE id = $id");
        setFlash('success', 'Education deleted!');
    }
    
    header('Location: education.php');
    exit;
}

$education = getEducation(false);
$editEdu = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editEdu = $conn->query("SELECT * FROM education WHERE id = $id")->fetch_assoc();
}
$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education | Admin</title>
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
            <a href="projects.php"><i class="fas fa-briefcase"></i> Projects</a>
            <a href="experience.php"><i class="fas fa-building"></i> Experience</a>
            <a href="education.php" class="active"><i class="fas fa-graduation-cap"></i> Education</a>
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
                <h1>Manage Education</h1>
            </div>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2><?php echo $editEdu ? 'Edit Education' : 'Add Education'; ?></h2>
                    <?php if ($editEdu): ?><a href="education.php" class="btn btn-outline btn-sm">Cancel</a><?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $editEdu ? 'edit' : 'add'; ?>">
                        <?php if ($editEdu): ?><input type="hidden" name="id" value="<?php echo $editEdu['id']; ?>"><?php endif; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Institution *</label>
                                <input type="text" name="institution" required value="<?php echo htmlspecialchars($editEdu['institution'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Degree *</label>
                                <input type="text" name="degree" required value="<?php echo htmlspecialchars($editEdu['degree'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Field of Study</label>
                                <input type="text" name="field" value="<?php echo htmlspecialchars($editEdu['field'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Start Year</label>
                                <input type="text" name="start_year" value="<?php echo htmlspecialchars($editEdu['start_year'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>End Year</label>
                                <input type="text" name="end_year" value="<?php echo htmlspecialchars($editEdu['end_year'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"><?php echo htmlspecialchars($editEdu['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="display_order" value="<?php echo $editEdu['display_order'] ?? 0; ?>">
                            </div>
                            <div class="form-group" style="display:flex;align-items:center;">
                                <label><input type="checkbox" name="is_visible" value="1" <?php echo (!$editEdu || $editEdu['is_visible']) ? 'checked' : ''; ?>> Visible</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>All Education</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Degree</th><th>Institution</th><th>Period</th><th>Visible</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($education as $e): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($e['degree']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($e['institution']); ?></td>
                                    <td><?php echo htmlspecialchars($e['start_year']); ?> - <?php echo htmlspecialchars($e['end_year']); ?></td>
                                    <td><?php echo $e['is_visible'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                    <td class="action-btns">
                                        <a href="education.php?edit=<?php echo $e['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
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
