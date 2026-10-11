<?php
require_once '../includes/functions.php';
requireLogin();

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $company = clean($_POST['company'] ?? '');
        $position = clean($_POST['position'] ?? '');
        $start_date = clean($_POST['start_date'] ?? '');
        $end_date = clean($_POST['end_date'] ?? 'Present');
        $description = clean($_POST['description'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_visible = isset($_POST['is_visible']) ? 1 : 0;
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO experience (company, position, start_date, end_date, description, display_order, is_visible) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssii", $company, $position, $start_date, $end_date, $description, $display_order, $is_visible);
            $stmt->execute();
            setFlash('success', 'Experience added!');
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE experience SET company=?, position=?, start_date=?, end_date=?, description=?, display_order=?, is_visible=? WHERE id=?");
            $stmt->bind_param("sssssiii", $company, $position, $start_date, $end_date, $description, $display_order, $is_visible, $id);
            $stmt->execute();
            setFlash('success', 'Experience updated!');
        }
        $stmt->close();
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM experience WHERE id = $id");
        setFlash('success', 'Experience deleted!');
    }
    
    header('Location: experience.php');
    exit;
}

$experience = getExperience(false);
$editExp = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editExp = $conn->query("SELECT * FROM experience WHERE id = $id")->fetch_assoc();
}
$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experience | Admin</title>
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
            <a href="experience.php" class="active"><i class="fas fa-building"></i> Experience</a>
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
                <h1>Manage Experience</h1>
            </div>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2><?php echo $editExp ? 'Edit Experience' : 'Add Experience'; ?></h2>
                    <?php if ($editExp): ?><a href="experience.php" class="btn btn-outline btn-sm">Cancel</a><?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $editExp ? 'edit' : 'add'; ?>">
                        <?php if ($editExp): ?><input type="hidden" name="id" value="<?php echo $editExp['id']; ?>"><?php endif; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Company *</label>
                                <input type="text" name="company" required value="<?php echo htmlspecialchars($editExp['company'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Position *</label>
                                <input type="text" name="position" required value="<?php echo htmlspecialchars($editExp['position'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="start_date" value="<?php echo htmlspecialchars($editExp['start_date'] ?? ''); ?>" placeholder="2022">
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" name="end_date" value="<?php echo htmlspecialchars($editExp['end_date'] ?? 'Present'); ?>" placeholder="Present">
                            </div>
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="display_order" value="<?php echo $editExp['display_order'] ?? 0; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"><?php echo htmlspecialchars($editExp['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_visible" value="1" <?php echo (!$editExp || $editExp['is_visible']) ? 'checked' : ''; ?>> Visible</label>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>All Experience</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Position</th><th>Company</th><th>Period</th><th>Visible</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($experience as $e): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($e['position']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($e['company']); ?></td>
                                    <td><?php echo htmlspecialchars($e['start_date']); ?> - <?php echo htmlspecialchars($e['end_date']); ?></td>
                                    <td><?php echo $e['is_visible'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                    <td class="action-btns">
                                        <a href="experience.php?edit=<?php echo $e['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
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
