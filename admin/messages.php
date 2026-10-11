<?php
require_once '../includes/functions.php';
requireLogin();

$conn = getDBConnection();

// Mark as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $conn->query("UPDATE messages SET is_read = 1 WHERE id = $id");
    header('Location: messages.php');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $id");
    setFlash('success', 'Message deleted!');
    header('Location: messages.php');
    exit;
}

// Mark all as read
if (isset($_GET['readall'])) {
    $conn->query("UPDATE messages SET is_read = 1");
    setFlash('success', 'All messages marked as read!');
    header('Location: messages.php');
    exit;
}

$messages = [];
$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$viewMsg = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $viewMsg = $conn->query("SELECT * FROM messages WHERE id = $id")->fetch_assoc();
    if ($viewMsg && !$viewMsg['is_read']) {
        $conn->query("UPDATE messages SET is_read = 1 WHERE id = $id");
        $viewMsg['is_read'] = 1;
    }
}

$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Admin</title>
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
            <a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a>
            <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages <?php if($unread): ?><span class="badge badge-danger"><?php echo $unread; ?></span><?php endif; ?></a>
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
                <h1>Messages</h1>
            </div>
            <?php if ($unread > 0): ?>
            <a href="messages.php?readall=1" class="btn btn-sm btn-primary">Mark All Read</a>
            <?php endif; ?>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <?php if ($viewMsg): ?>
            <div class="card">
                <div class="card-header">
                    <h2>Message from <?php echo htmlspecialchars($viewMsg['name']); ?></h2>
                    <a href="messages.php" class="btn btn-outline btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <p style="margin-bottom:0.5rem;"><strong>From:</strong> <?php echo htmlspecialchars($viewMsg['name']); ?> &lt;<?php echo htmlspecialchars($viewMsg['email']); ?>&gt;</p>
                    <p style="margin-bottom:0.5rem;"><strong>Subject:</strong> <?php echo htmlspecialchars($viewMsg['subject'] ?: '(No subject)'); ?></p>
                    <p style="margin-bottom:1rem;color:var(--gray);"><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($viewMsg['created_at'])); ?></p>
                    <div style="background:var(--dark);padding:1.5rem;border-radius:12px;border:1px solid var(--dark-3);line-height:1.8;">
                        <?php echo nl2br(htmlspecialchars($viewMsg['message'])); ?>
                    </div>
                    <div style="margin-top:1.5rem;display:flex;gap:0.5rem;">
                        <a href="mailto:<?php echo htmlspecialchars($viewMsg['email']); ?>" class="btn btn-primary btn-sm"><i class="fas fa-reply"></i> Reply via Email</a>
                        <a href="messages.php?delete=<?php echo $viewMsg['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                </div>
            </div>
            <?php else: ?>

            <div class="card">
                <div class="card-header"><h2>All Messages (<?php echo count($messages); ?>)</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $m): ?>
                                <tr style="<?php echo !$m['is_read'] ? 'background:rgba(99,102,241,0.05);' : ''; ?>">
                                    <td>
                                        <?php if (!$m['is_read']): ?>
                                        <span class="badge badge-warning">New</span>
                                        <?php else: ?>
                                        <span class="badge badge-success">Read</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($m['email']); ?></td>
                                    <td><?php echo htmlspecialchars($m['subject'] ?: '-'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($m['created_at'])); ?></td>
                                    <td class="action-btns">
                                        <a href="messages.php?view=<?php echo $m['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        <a href="messages.php?delete=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($messages)): ?>
                                <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:2rem;">No messages yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
