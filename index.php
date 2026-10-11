<?php
require_once 'includes/functions.php';

$profile = getProfile();
$skills = getSkills();
$projects = getProjects();
$experience = getExperience();
$education = getEducation();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['full_name'] ?? 'Portfolio'); ?> | <?php echo htmlspecialchars($profile['title'] ?? 'Developer'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($profile['tagline'] ?? 'Professional Portfolio'); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="loader"></div>
</div>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="#home" class="logo"><?php echo htmlspecialchars(explode(' ', $profile['full_name'] ?? 'Portfolio')[0]); ?>.</a>
        <ul class="nav-links">
            <li><a href="#home" class="active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#skills">Skills</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <button class="nav-toggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                Available for new projects
            </div>
            <h1>Hi, I'm <span class="gradient-text"><?php echo htmlspecialchars($profile['full_name'] ?? 'Your Name'); ?></span></h1>
            <p class="title">
                <span class="typing-text" data-texts="IT Undergraduate|Software Engineering Student|Web Developer|Full Stack Enthusiast|Problem Solver"></span>
            </p>
            <p class="tagline"><?php echo htmlspecialchars($profile['tagline'] ?? 'Building amazing digital experiences'); ?></p>
            <div class="hero-btns">
                <a href="#projects" class="btn btn-primary">
                    <i class="fas fa-briefcase"></i> View My Work
                </a>
                <?php if (!empty($profile['cv_file'])): ?>
                <a href="assets/uploads/<?php echo htmlspecialchars($profile['cv_file']); ?>" class="btn btn-outline" download>
                    <i class="fas fa-download"></i> Download CV
                </a>
                <?php else: ?>
                <a href="#contact" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> Contact Me
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-image-wrapper">
                <img src="assets/images/<?php echo htmlspecialchars($profile['photo'] ?? 'default-avatar.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($profile['full_name'] ?? 'Profile'); ?>"
                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name'] ?? 'User'); ?>&size=400&background=6366f1&color=fff&bold=true'">
            </div>
            <div class="floating-card card1">
                <i class="fas fa-code"></i>
                <span>Clean Code</span>
            </div>
            <div class="floating-card card2">
                <i class="fas fa-rocket"></i>
                <span>Fast Delivery</span>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section" id="about">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">About Me</span>
            <h2 class="section-title">Who I Am</h2>
            <p class="section-subtitle">Get to know more about me and my journey</p>
        </div>
        <div class="about-grid">
            <div class="about-image" data-aos="fade-right">
                <img src="assets/images/<?php echo htmlspecialchars($profile['photo'] ?? 'default-avatar.jpg'); ?>" 
                     alt="About"
                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name'] ?? 'User'); ?>&size=500&background=1e293b&color=6366f1&bold=true'">
            </div>
            <div class="about-content" data-aos="fade-left">
                <h3><?php echo htmlspecialchars($profile['title'] ?? 'Full Stack Developer'); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($profile['about_text'] ?? 'Passionate developer creating amazing digital experiences.')); ?></p>
                <div class="about-info">
                    <?php if (!empty($profile['email'])): ?>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($profile['email']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['phone'])): ?>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($profile['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['location'])): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($profile['location']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="social-links">
                    <?php if (!empty($profile['github_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['twitter_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['twitter_url']); ?>" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['facebook_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['facebook_url']); ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['instagram_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['instagram_url']); ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
                <div class="lang-badges">
                    <span class="lang-badge"><i class="fas fa-language"></i> English</span>
                    <span class="lang-badge"><i class="fas fa-language"></i> Sinhala</span>
                    <span class="lang-badge"><i class="fas fa-language"></i> Tamil</span>
                </div>
                <?php if (!empty($profile['cv_file'])): ?>
                <div style="margin-top: 1.5rem;">
                    <a href="assets/uploads/<?php echo htmlspecialchars($profile['cv_file']); ?>" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download CV
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row" data-aos="fade-up" style="margin-top: 4rem;">
            <div class="stat-item">
                <div class="stat-number" data-count="5" data-suffix="+">0</div>
                <div class="stat-label">Projects</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="16" data-suffix="+">0</div>
                <div class="stat-label">Skills</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="3" data-suffix="">0</div>
                <div class="stat-label">Languages</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="2" data-suffix="+">0</div>
                <div class="stat-label">Years Learning</div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="section" id="skills" style="background: rgba(30,41,59,0.3);">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">My Skills</span>
            <h2 class="section-title">What I Know</h2>
            <p class="section-subtitle">Technologies and tools I work with</p>
        </div>
        <div class="skills-grid">
            <?php foreach ($skills as $i => $skill): ?>
            <div class="skill-card" data-aos="fade-up" data-aos-delay="<?php echo ($i % 4) * 100; ?>">
                <div class="skill-header">
                    <div class="skill-icon">
                        <i class="<?php echo htmlspecialchars($skill['icon']); ?>"></i>
                    </div>
                    <div>
                        <div class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                        <div class="skill-category"><?php echo htmlspecialchars($skill['skill_category']); ?></div>
                    </div>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" data-level="<?php echo (int)$skill['skill_level']; ?>"></div>
                </div>
                <div class="skill-level"><?php echo (int)$skill['skill_level']; ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section class="section" id="projects">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">My Work</span>
            <h2 class="section-title">Featured Projects</h2>
            <p class="section-subtitle">Some of the projects I've worked on</p>
        </div>
        <div class="projects-grid">
            <?php foreach ($projects as $i => $project): ?>
            <div class="project-card" data-aos="zoom-in" data-aos-delay="<?php echo ($i % 3) * 100; ?>">
                <div class="project-image">
                    <img src="assets/images/<?php echo htmlspecialchars($project['image']); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>"
                         onerror="this.src='https://placehold.co/600x400/1e293b/6366f1?text=<?php echo urlencode($project['title']); ?>'">
                    <div class="project-overlay">
                        <?php if (!empty($project['project_url']) && $project['project_url'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" title="Live Demo"><i class="fas fa-external-link-alt"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($project['github_url'])): ?>
                        <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="project-body">
                    <div class="project-category"><?php echo htmlspecialchars($project['category']); ?></div>
                    <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="project-desc"><?php echo htmlspecialchars($project['short_desc'] ?? substr($project['description'], 0, 100)); ?></p>
                    <?php if (!empty($project['tech_stack'])): ?>
                    <div class="project-tech">
                        <?php foreach (explode(',', $project['tech_stack']) as $tech): ?>
                        <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Experience Section -->
<section class="section" id="experience" style="background: rgba(30,41,59,0.3);">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Career</span>
            <h2 class="section-title">Experience & Education</h2>
            <p class="section-subtitle">My professional journey</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;" class="exp-edu-grid">
            <!-- Experience -->
            <div data-aos="fade-right">
                <h3 style="font-size: 1.4rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-briefcase" style="color: var(--primary-light);"></i> Experience
                </h3>
                <div class="timeline">
                    <?php foreach ($experience as $exp): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <div class="timeline-date"><?php echo htmlspecialchars($exp['start_date']); ?> - <?php echo htmlspecialchars($exp['end_date']); ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                            <div class="timeline-company"><?php echo htmlspecialchars($exp['company']); ?></div>
                            <p class="timeline-desc"><?php echo htmlspecialchars($exp['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Education -->
            <div data-aos="fade-left">
                <h3 style="font-size: 1.4rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-graduation-cap" style="color: var(--primary-light);"></i> Education
                </h3>
                <div class="timeline">
                    <?php foreach ($education as $edu): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <div class="timeline-date"><?php echo htmlspecialchars($edu['start_year']); ?> - <?php echo htmlspecialchars($edu['end_year']); ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($edu['degree']); ?></div>
                            <div class="timeline-company"><?php echo htmlspecialchars($edu['institution']); ?><?php if($edu['field']): ?> - <?php echo htmlspecialchars($edu['field']); ?><?php endif; ?></div>
                            <p class="timeline-desc"><?php echo htmlspecialchars($edu['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section" id="contact">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Get In Touch</span>
            <h2 class="section-title">Contact Me</h2>
            <p class="section-subtitle">Have a project in mind? Let's talk!</p>
        </div>
        <div class="contact-grid">
            <div class="contact-info-card" data-aos="fade-right">
                <?php if (!empty($profile['email'])): ?>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p><?php echo htmlspecialchars($profile['email']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['phone'])): ?>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Phone</h4>
                        <p><?php echo htmlspecialchars($profile['phone']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['location'])): ?>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Location</h4>
                        <p><?php echo htmlspecialchars($profile['location']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <div class="social-links" style="margin-top: 1.5rem;">
                    <?php if (!empty($profile['github_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank"><i class="fab fa-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['twitter_url'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['twitter_url']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="contact-form" data-aos="fade-left">
                <form id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label>Your Email</label>
                            <input type="email" name="email" required placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" placeholder="Project Inquiry">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" required placeholder="Tell me about your project..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo"><?php echo htmlspecialchars(explode(' ', $profile['full_name'] ?? 'Portfolio')[0]); ?>.</div>
        <p class="footer-text">Building digital experiences with passion and precision.</p>
        <div class="social-links" style="justify-content: center;">
            <?php if (!empty($profile['github_url'])): ?>
            <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank"><i class="fab fa-github"></i></a>
            <?php endif; ?>
            <?php if (!empty($profile['linkedin_url'])): ?>
            <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            <?php endif; ?>
            <?php if (!empty($profile['twitter_url'])): ?>
            <a href="<?php echo htmlspecialchars($profile['twitter_url']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
            <?php endif; ?>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['full_name'] ?? 'Portfolio'); ?>. All rights reserved.
        </div>
    </div>
</footer>

<style>
@media (max-width: 768px) {
    .exp-edu-grid { grid-template-columns: 1fr !important; }
}
</style>

<script src="assets/js/main.js"></script>
</body>
</html>
