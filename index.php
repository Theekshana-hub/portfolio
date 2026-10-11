<?php
require_once 'includes/functions.php';

$profile    = getProfile();
$skills     = getSkills();
$projects   = getProjects();
$experience = getExperience();
$education  = getEducation();

/* Safe output helper (guarded so it never clashes with functions.php) */
if (!function_exists('h')) {
    function h($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$fullName  = $profile['full_name'] ?? 'Portfolio';
$firstName = explode(' ', trim($fullName))[0];
$title     = $profile['title'] ?? 'Developer';
$tagline   = $profile['tagline'] ?? 'Building amazing digital experiences';
$photo     = $profile['photo'] ?? 'default-avatar.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($fullName); ?> | <?php echo h($title); ?></title>
    <meta name="description" content="<?php echo h($profile['tagline'] ?? 'Professional Portfolio'); ?>">
    <meta name="theme-color" content="#070b18">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo h($fullName); ?> | <?php echo h($title); ?>">
    <meta property="og:description" content="<?php echo h($tagline); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Page-specific styles (extras on top of style.css) -->
    <style>
        /* Alternate section background */
        .section-alt {
            background: linear-gradient(180deg, transparent 0%, rgba(30, 39, 66, 0.35) 15%, rgba(30, 39, 66, 0.35) 85%, transparent 100%);
        }

        /* Hero scroll indicator */
        .scroll-down {
            position: absolute;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-decoration: none;
            text-transform: uppercase;
            opacity: 0;
            animation: fadeUp 1s var(--ease) forwards 1.4s;
        }
        .scroll-down .mouse {
            width: 24px;
            height: 38px;
            border: 2px solid rgba(165, 180, 252, 0.5);
            border-radius: 14px;
            position: relative;
        }
        .scroll-down .mouse::before {
            content: '';
            position: absolute;
            top: 7px;
            left: 50%;
            width: 4px;
            height: 8px;
            margin-left: -2px;
            border-radius: 4px;
            background: var(--secondary);
            animation: wheel 1.8s ease-in-out infinite;
        }
        @keyframes wheel {
            0%   { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(14px); }
        }
        @media (max-height: 700px), (max-width: 992px) {
            .scroll-down { display: none; }
        }

        /* Tech marquee strip */
        .tech-strip {
            position: relative;
            overflow: hidden;
            padding: 1.5rem 0;
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.02);
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent);
            mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent);
        }
        .tech-track {
            display: flex;
            gap: 1rem;
            width: max-content;
            animation: marquee 40s linear infinite;
        }
        .tech-strip:hover .tech-track {
            animation-play-state: paused;
        }
        .tech-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 22px;
            border-radius: 50px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            color: var(--gray);
            font-size: 0.92rem;
            font-weight: 500;
            white-space: nowrap;
            transition: var(--transition);
        }
        .tech-pill i {
            color: var(--secondary);
        }
        .tech-pill:hover {
            color: #fff;
            border-color: rgba(99, 102, 241, 0.6);
            background: rgba(99, 102, 241, 0.14);
        }
        @keyframes marquee {
            to { transform: translateX(-50%); }
        }

        /* Experience / Education two-column layout */
        .exp-edu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3.5rem;
        }
        .col-title {
            font-family: var(--font-display);
            font-size: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .col-title i {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            background: var(--gradient);
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(99, 102, 241, 0.4);
        }
        .exp-edu-grid .timeline-item { padding-left: 54px; }
        .empty-note {
            color: var(--gray);
            padding: 1.5rem;
            border: 1px dashed var(--glass-border);
            border-radius: var(--radius);
            text-align: center;
        }
        @media (max-width: 992px) {
            .exp-edu-grid { grid-template-columns: 1fr; }
        }

        /* Contact info links */
        .contact-info-item p a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }
        .contact-info-item p a:hover {
            color: var(--primary-light);
        }
        .contact-social {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
        }
        .btn-block { width: 100%; justify-content: center; }
        .cv-wrap { margin-top: 1.6rem; }
        .stats-spacing { margin-top: 4.5rem; }
        .footer .social-links { justify-content: center; }

        /* ===== Contact: mobile responsive ===== */
        .contact-grid > * { min-width: 0; }
        .contact-info-card,
        .contact-form { width: 100%; }
        .contact-info-item > div:last-child { min-width: 0; }
        .contact-info-item p {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        /* 16px inputs stop iOS Safari from zooming on focus */
        .contact-form .form-group input,
        .contact-form .form-group textarea {
            font-size: 1rem;
            -webkit-appearance: none;
            appearance: none;
        }
        .contact-form .btn-block { min-height: 54px; }
        .contact-social { flex-wrap: wrap; }

        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            /* form first on tablets / phones, contact details after */
            .contact-form { order: -1; }
            /* side-slide animations cause sideways scroll on small screens, use fade-up instead */
            .contact-grid [data-aos]:not(.aos-animate) {
                transform: translateY(40px);
            }
        }

        @media (max-width: 768px) {
            #contact.section { padding: 64px 0; }
            .contact-info-card,
            .contact-form {
                padding: 1.6rem;
                border-radius: 18px;
            }
            .contact-form .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .contact-info-item {
                gap: 0.9rem;
                margin-bottom: 1.3rem;
            }
            .contact-icon {
                width: 46px;
                height: 46px;
                border-radius: 13px;
                font-size: 1.05rem;
            }
            .contact-social {
                margin-top: 1.5rem;
                padding-top: 1.3rem;
                justify-content: center;
            }
            .back-to-top {
                right: 16px;
                bottom: calc(16px + env(safe-area-inset-bottom, 0px));
                width: 44px;
                height: 44px;
            }
        }

        @media (max-width: 480px) {
            #contact .section-container { padding: 0 1.1rem; }
            #contact .section-title { letter-spacing: -1px; }
            #contact .section-subtitle { font-size: 1rem; }
            .contact-info-card,
            .contact-form { padding: 1.25rem; }
            .contact-info-item h4 { font-size: 0.8rem; }
            .contact-info-item p { font-size: 0.95rem; }
            .contact-form .form-group { margin-bottom: 1.1rem; }
            .contact-form .form-group input,
            .contact-form .form-group textarea { padding: 13px 15px; }
            .contact-social a {
                width: 44px;
                height: 44px;
            }
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            right: 26px;
            bottom: 26px;
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            color: #fff;
            background: var(--gradient);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            cursor: pointer;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px) scale(0.8);
            transition: var(--transition);
        }
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .back-to-top:hover {
            transform: translateY(-5px) scale(1.06);
            box-shadow: 0 16px 40px rgba(99, 102, 241, 0.65);
        }
    </style>
</head>
<body>

<!-- Scroll progress bar -->
<div class="scroll-progress" aria-hidden="true"></div>

<!-- Preloader -->
<div id="preloader">
    <div class="loader"></div>
</div>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="#home" class="logo"><?php echo h($firstName); ?>.</a>
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
            <h1>Hi, I'm <span class="gradient-text"><?php echo h($profile['full_name'] ?? 'Your Name'); ?></span></h1>
            <p class="title">
                <span class="typing-text" data-texts="IT Undergraduate|Software Engineering Student|Web Developer|Full Stack Enthusiast|Problem Solver"></span>
            </p>
            <p class="tagline"><?php echo h($tagline); ?></p>
            <div class="hero-btns">
                <a href="#projects" class="btn btn-primary">
                    <i class="fas fa-briefcase"></i> View My Work
                </a>
                <?php if (!empty($profile['cv_file'])): ?>
                <a href="assets/uploads/<?php echo h($profile['cv_file']); ?>" class="btn btn-outline" download>
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
                <img src="assets/images/<?php echo h($photo); ?>"
                     alt="<?php echo h($profile['full_name'] ?? 'Profile'); ?>"
                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name'] ?? 'User'); ?>&size=400&background=6366f1&color=fff&bold=true'">
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

    <a href="#about" class="scroll-down" aria-label="Scroll to about section">
        <span class="mouse"></span>
        <span>Scroll</span>
    </a>
</section>

<!-- Tech Marquee -->
<?php if (!empty($skills)): ?>
<div class="tech-strip" aria-label="Technologies I work with">
    <div class="tech-track">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
            <?php foreach ($skills as $skill): ?>
            <span class="tech-pill"<?php echo $loop === 1 ? ' aria-hidden="true"' : ''; ?>>
                <i class="<?php echo h($skill['icon'] ?? 'fas fa-code'); ?>"></i>
                <?php echo h($skill['skill_name'] ?? ''); ?>
            </span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

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
                <img src="assets/images/<?php echo h($photo); ?>"
                     alt="About <?php echo h($firstName); ?>"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name'] ?? 'User'); ?>&size=500&background=1e293b&color=6366f1&bold=true'">
            </div>
            <div class="about-content" data-aos="fade-left">
                <h3><?php echo h($profile['title'] ?? 'Full Stack Developer'); ?></h3>
                <p><?php echo nl2br(h($profile['about_text'] ?? 'Passionate developer creating amazing digital experiences.')); ?></p>
                <div class="about-info">
                    <?php if (!empty($profile['email'])): ?>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo h($profile['email']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['phone'])): ?>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo h($profile['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['location'])): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo h($profile['location']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="social-links">
                    <?php if (!empty($profile['github_url'])): ?>
                    <a href="<?php echo h($profile['github_url']); ?>" target="_blank" rel="noopener noreferrer" title="GitHub" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['linkedin_url'])): ?>
                    <a href="<?php echo h($profile['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" title="LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['twitter_url'])): ?>
                    <a href="<?php echo h($profile['twitter_url']); ?>" target="_blank" rel="noopener noreferrer" title="Twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['facebook_url'])): ?>
                    <a href="<?php echo h($profile['facebook_url']); ?>" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['instagram_url'])): ?>
                    <a href="<?php echo h($profile['instagram_url']); ?>" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
                <div class="lang-badges">
                    <span class="lang-badge"><i class="fas fa-language"></i> English</span>
                    <span class="lang-badge"><i class="fas fa-language"></i> Sinhala</span>
                    <span class="lang-badge"><i class="fas fa-language"></i> Tamil</span>
                </div>
                <?php if (!empty($profile['cv_file'])): ?>
                <div class="cv-wrap">
                    <a href="assets/uploads/<?php echo h($profile['cv_file']); ?>" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download CV
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row stats-spacing" data-aos="fade-up">
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo max(count($projects), 1); ?>" data-suffix="+">0</div>
                <div class="stat-label">Projects</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo max(count($skills), 1); ?>" data-suffix="+">0</div>
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
<section class="section section-alt" id="skills">
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
                        <i class="<?php echo h($skill['icon'] ?? 'fas fa-code'); ?>"></i>
                    </div>
                    <div>
                        <div class="skill-name"><?php echo h($skill['skill_name'] ?? ''); ?></div>
                        <div class="skill-category"><?php echo h($skill['skill_category'] ?? ''); ?></div>
                    </div>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" data-level="<?php echo (int)($skill['skill_level'] ?? 0); ?>"></div>
                </div>
                <div class="skill-level"><?php echo (int)($skill['skill_level'] ?? 0); ?>%</div>
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
                    <img src="assets/images/<?php echo h($project['image'] ?? ''); ?>"
                         alt="<?php echo h($project['title'] ?? 'Project'); ?>"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/600x400/0e1428/6366f1?text=<?php echo urlencode($project['title'] ?? 'Project'); ?>'">
                    <div class="project-overlay">
                        <?php if (!empty($project['project_url']) && $project['project_url'] !== '#'): ?>
                        <a href="<?php echo h($project['project_url']); ?>" target="_blank" rel="noopener noreferrer" title="Live Demo" aria-label="Live demo"><i class="fas fa-external-link-alt"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($project['github_url'])): ?>
                        <a href="<?php echo h($project['github_url']); ?>" target="_blank" rel="noopener noreferrer" title="GitHub" aria-label="GitHub repository"><i class="fab fa-github"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="project-body">
                    <div class="project-category"><?php echo h($project['category'] ?? ''); ?></div>
                    <h3 class="project-title"><?php echo h($project['title'] ?? ''); ?></h3>
                    <p class="project-desc"><?php echo h($project['short_desc'] ?? substr($project['description'] ?? '', 0, 100)); ?></p>
                    <?php if (!empty($project['tech_stack'])): ?>
                    <div class="project-tech">
                        <?php foreach (explode(',', $project['tech_stack']) as $tech): ?>
                        <span class="tech-tag"><?php echo h(trim($tech)); ?></span>
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
<section class="section section-alt" id="experience">
    <div class="section-container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">Career</span>
            <h2 class="section-title">Experience &amp; Education</h2>
            <p class="section-subtitle">My professional journey</p>
        </div>

        <div class="exp-edu-grid">
            <!-- Experience -->
            <div data-aos="fade-right">
                <h3 class="col-title"><i class="fas fa-briefcase"></i> Experience</h3>
                <div class="timeline">
                    <?php if (empty($experience)): ?>
                        <p class="empty-note">Experience details coming soon.</p>
                    <?php else: ?>
                        <?php foreach ($experience as $exp): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-card">
                                <div class="timeline-date"><?php echo h($exp['start_date'] ?? ''); ?> - <?php echo h($exp['end_date'] ?? ''); ?></div>
                                <div class="timeline-title"><?php echo h($exp['position'] ?? ''); ?></div>
                                <div class="timeline-company"><?php echo h($exp['company'] ?? ''); ?></div>
                                <p class="timeline-desc"><?php echo h($exp['description'] ?? ''); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Education -->
            <div data-aos="fade-left">
                <h3 class="col-title"><i class="fas fa-graduation-cap"></i> Education</h3>
                <div class="timeline">
                    <?php if (empty($education)): ?>
                        <p class="empty-note">Education details coming soon.</p>
                    <?php else: ?>
                        <?php foreach ($education as $edu): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-card">
                                <div class="timeline-date"><?php echo h($edu['start_year'] ?? ''); ?> - <?php echo h($edu['end_year'] ?? ''); ?></div>
                                <div class="timeline-title"><?php echo h($edu['degree'] ?? ''); ?></div>
                                <div class="timeline-company">
                                    <?php echo h($edu['institution'] ?? ''); ?><?php if (!empty($edu['field'])): ?> - <?php echo h($edu['field']); ?><?php endif; ?>
                                </div>
                                <p class="timeline-desc"><?php echo h($edu['description'] ?? ''); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        <p><a href="mailto:<?php echo h($profile['email']); ?>"><?php echo h($profile['email']); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['phone'])): ?>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Phone</h4>
                        <p><a href="tel:<?php echo h(preg_replace('/[^\d+]/', '', $profile['phone'])); ?>"><?php echo h($profile['phone']); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['location'])): ?>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Location</h4>
                        <p><?php echo h($profile['location']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <div class="social-links contact-social">
                    <?php if (!empty($profile['github_url'])): ?>
                    <a href="<?php echo h($profile['github_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['linkedin_url'])): ?>
                    <a href="<?php echo h($profile['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['twitter_url'])): ?>
                    <a href="<?php echo h($profile['twitter_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="contact-form" data-aos="fade-left">
                <form id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cf-name">Your Name</label>
                            <input type="text" id="cf-name" name="name" required placeholder="John Doe" autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="cf-email">Your Email</label>
                            <input type="email" id="cf-email" name="email" required placeholder="john@example.com" autocomplete="email" inputmode="email" autocapitalize="off" spellcheck="false">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cf-subject">Subject</label>
                        <input type="text" id="cf-subject" name="subject" placeholder="Project Inquiry" enterkeyhint="next">
                    </div>
                    <div class="form-group">
                        <label for="cf-message">Message</label>
                        <textarea id="cf-message" name="message" rows="5" required placeholder="Tell me about your project..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
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
        <div class="footer-logo"><?php echo h($firstName); ?>.</div>
        <p class="footer-text">Building digital experiences with passion and precision.</p>
        <div class="social-links">
            <?php if (!empty($profile['github_url'])): ?>
            <a href="<?php echo h($profile['github_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fab fa-github"></i></a>
            <?php endif; ?>
            <?php if (!empty($profile['linkedin_url'])): ?>
            <a href="<?php echo h($profile['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <?php endif; ?>
            <?php if (!empty($profile['twitter_url'])): ?>
            <a href="<?php echo h($profile['twitter_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <?php endif; ?>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> <?php echo h($fullName); ?>. All rights reserved.
        </div>
    </div>
</footer>

<!-- Back to top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
/* Small UI helpers: back-to-top button + scroll progress (safe if main.js already handles progress) */
(function () {
    var btn = document.getElementById('backToTop');
    var bar = document.querySelector('.scroll-progress');
    var ticking = false;

    function update() {
        var y = window.pageYOffset || document.documentElement.scrollTop;
        var max = document.documentElement.scrollHeight - window.innerHeight;
        if (btn) btn.classList.toggle('show', y > 600);
        if (bar && max > 0) bar.style.width = Math.min(100, (y / max) * 100) + '%';
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });

    if (btn) {
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    update();
})();
</script>

<script src="assets/js/main.js"></script>
</body>
</html>