<?php
require_once '../includes/functions.php';
requireLogin();

$profile = getProfile();
$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($_POST['full_name'] ?? '');
    $title = clean($_POST['title'] ?? '');
    $tagline = clean($_POST['tagline'] ?? '');
    $about_text = clean($_POST['about_text'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $location = clean($_POST['location'] ?? '');
    $github_url = clean($_POST['github_url'] ?? '');
    $linkedin_url = clean($_POST['linkedin_url'] ?? '');
    $twitter_url = clean($_POST['twitter_url'] ?? '');
    $facebook_url = clean($_POST['facebook_url'] ?? '');
    $instagram_url = clean($_POST['instagram_url'] ?? '');
    
    $photo = $profile['photo'];
    $cv_file = $profile['cv_file'];
    
    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $upload = uploadFile($_FILES['photo'], 'images');
        if ($upload['success']) {
            $photo = $upload['filename'];
        }
    }
    
    // Handle CV upload
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === 0) {
        $upload = uploadFile($_FILES['cv_file'], 'uploads');
        if ($upload['success']) {
            $cv_file = $upload['filename'];
        }
    }
    
    $stmt = $conn->prepare("UPDATE profile SET full_name=?, title=?, tagline=?, about_text=?, email=?, phone=?, location=?, photo=?, cv_file=?, github_url=?, linkedin_url=?, twitter_url=?, facebook_url=?, instagram_url=? WHERE id=?");
    $id = $profile['id'];
    $stmt->bind_param("ssssssssssssssi", $full_name, $title, $tagline, $about_text, $email, $phone, $location, $photo, $cv_file, $github_url, $linkedin_url, $twitter_url, $facebook_url, $instagram_url, $id);
    
    if ($stmt->execute()) {
        setFlash('success', 'Profile updated successfully!');
    } else {
        setFlash('danger', 'Failed to update profile');
    }
    $stmt->close();
    header('Location: profile.php');
    exit;
}

$profile = getProfile(); // refresh
$unread = getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Admin</title>
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
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile & CV</a>
            <a href="skills.php"><i class="fas fa-code"></i> Skills</a>
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
                <h1>Profile & CV</h1>
            </div>
        </div>
        <div class="content-area">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header"><h2>Personal Information</h2></div>
                    <div class="card-body">
                        <div style="display:flex;gap:2rem;margin-bottom:1.5rem;align-items:center;flex-wrap:wrap;">
                            <img src="../assets/images/<?php echo htmlspecialchars($profile['photo'] ?? ''); ?>" class="preview-img-lg"
                                 onerror="this.src='https://ui-avatars.com/api/?name=User&size=150&background=6366f1&color=fff'" id="photoPreview">
                            <div>
                                <div class="form-group">
                                    <label>Profile Photo</label>
                                    <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="full_name" required value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Title / Position *</label>
                                <input type="text" name="title" required value="<?php echo htmlspecialchars($profile['title'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tagline</label>
                            <input type="text" name="tagline" value="<?php echo htmlspecialchars($profile['tagline'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>About Me</label>
                            <textarea name="about_text" rows="5"><?php echo htmlspecialchars($profile['about_text'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h2>CV / Resume</h2></div>
                    <div class="card-body">
                        <?php if (!empty($profile['cv_file'])): ?>
                        <p style="margin-bottom:1rem;">
                            <span class="badge badge-success"><i class="fas fa-file-pdf"></i> Current CV:</span>
                            <a href="../assets/uploads/<?php echo htmlspecialchars($profile['cv_file']); ?>" target="_blank" style="color:var(--primary);margin-left:8px;">
                                <?php echo htmlspecialchars($profile['cv_file']); ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Upload New CV (PDF, DOC, DOCX - Max 5MB)</label>
                            <input type="file" name="cv_file" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h2>Social Links</h2></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fab fa-github"></i> GitHub URL</label>
                                <input type="url" name="github_url" value="<?php echo htmlspecialchars($profile['github_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                                <input type="url" name="linkedin_url" value="<?php echo htmlspecialchars($profile['linkedin_url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fab fa-twitter"></i> Twitter URL</label>
                                <input type="url" name="twitter_url" value="<?php echo htmlspecialchars($profile['twitter_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fab fa-facebook"></i> Facebook URL</label>
                                <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($profile['facebook_url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-instagram"></i> Instagram URL</label>
                            <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($profile['instagram_url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:14px 40px;">
                    <i class="fas fa-save"></i> Save Profile
                </button>
            </form>
        </div>
    </div>
</div>
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
<?php $conn->close(); ?>
