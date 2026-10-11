/* ============================================
   SUPER PORTFOLIO - main.js (animation upgrade)
   ============================================ */

const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isTouch = window.matchMedia('(pointer: coarse)').matches;
const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => [...c.querySelectorAll(s)];
const lerp = (a, b, n) => (1 - n) * a + n * b;
const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
const easeOutCubic = t => 1 - Math.pow(1 - t, 3);
const easeOutExpo = t => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

// ========== PRELOADER (counter + curtain) ==========
function initPreloader() {
    const pre = $('#preloader');
    const done = () => document.body.classList.add('loaded');
    if (!pre) return done();

    const num = document.createElement('div');
    num.className = 'loader-count';
    num.textContent = '0';
    pre.appendChild(num);

    const loadP = new Promise(res => {
        if (document.readyState === 'complete') res();
        else window.addEventListener('load', res, { once: true });
    });
    const countP = new Promise(res => {
        if (prefersReduced) { num.textContent = '100'; return res(); }
        const t0 = performance.now();
        (function tick(now) {
            const t = clamp((now - t0) / 1400, 0, 1);
            num.textContent = Math.round(easeOutCubic(t) * 100);
            t < 1 ? requestAnimationFrame(tick) : res();
        })(t0);
    });

    let finished = false;
    const finish = () => {
        if (finished) return;
        finished = true;
        pre.classList.add('hide');
        done();
        setTimeout(() => pre.remove(), 1200);
    };
    Promise.all([loadP, countP]).then(() => setTimeout(finish, 150));
    setTimeout(finish, 4000); // safety
}

// ========== CUSTOM CURSOR (smooth trailing ring) ==========
function initCursor() {
    if (isTouch || prefersReduced) return;
    const ring = document.createElement('div');
    ring.className = 'custom-cursor';
    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    document.body.append(ring, dot);
    document.body.classList.add('has-cursor');

    let mx = innerWidth / 2, my = innerHeight / 2, rx = mx, ry = my;

    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        dot.style.transform = `translate3d(${mx}px, ${my}px, 0) translate(-50%, -50%)`;
        ring.classList.add('is-on'); dot.classList.add('is-on');
    });
    (function loop() {
        rx = lerp(rx, mx, 0.16);
        ry = lerp(ry, my, 0.16);
        ring.style.transform = `translate3d(${rx}px, ${ry}px, 0) translate(-50%, -50%)`;
        requestAnimationFrame(loop);
    })();

    document.addEventListener('mouseover', e => {
        const hit = e.target.closest('a, button, .btn, .project-card, .skill-card, input, textarea, .marquee-item');
        ring.classList.toggle('cursor-hover', !!hit);
    });
    document.addEventListener('mousedown', () => ring.classList.add('cursor-down'));
    document.addEventListener('mouseup', () => ring.classList.remove('cursor-down'));
    document.documentElement.addEventListener('mouseleave', () => { ring.classList.remove('is-on'); dot.classList.remove('is-on'); });
}

// ========== CLICK RING ==========
function initClickRing() {
    if (prefersReduced) return;
    document.addEventListener('click', e => {
        const r = document.createElement('div');
        r.className = 'click-ring';
        r.style.left = e.clientX + 'px';
        r.style.top = e.clientY + 'px';
        document.body.appendChild(r);
        setTimeout(() => r.remove(), 650);
    });
}

// ========== HERO: SPLIT TEXT ==========
function initSplitText() {
    const h1 = $('.hero h1');
    if (!h1) return;
    h1.setAttribute('aria-label', h1.textContent.trim());
    let i = 0;
    const wrap = text => {
        const frag = document.createDocumentFragment();
        text.split(/(\s+)/).forEach(tok => {
            if (!tok) return;
            if (/^\s+$/.test(tok)) { frag.appendChild(document.createTextNode(' ')); return; }
            const word = document.createElement('span');
            word.className = 'word';
            word.setAttribute('aria-hidden', 'true');
            [...tok].forEach(ch => {
                const s = document.createElement('span');
                s.className = 'char';
                s.style.setProperty('--i', i++);
                s.textContent = ch;
                word.appendChild(s);
            });
            frag.appendChild(word);
        });
        return frag;
    };
    const walk = node => [...node.childNodes].forEach(n => {
        if (n.nodeType === 3) n.replaceWith(wrap(n.textContent));
        else if (n.nodeType === 1) walk(n);
    });
    walk(h1);
    h1.classList.add('split');
}

// ========== HERO: FLOATING CODE SYMBOLS ==========
function initCodeFloat() {
    const hero = $('.hero');
    if (!hero || prefersReduced) return;
    const symbols = ['</>', '{ }', '=>', '#', '$_', '<div>', 'const', ';', '[ ]', '&&', 'php', 'js', 'css', 'git', '0101', 'fn()'];
    const count = window.innerWidth < 768 ? 8 : 16;
    for (let n = 0; n < count; n++) {
        const s = document.createElement('span');
        s.className = 'code-float';
        s.textContent = symbols[n % symbols.length];
        s.style.setProperty('--x', Math.random() * 100 + '%');
        s.style.setProperty('--s', (0.8 + Math.random() * 1.1).toFixed(2) + 'rem');
        s.style.setProperty('--d', (14 + Math.random() * 14).toFixed(1) + 's');
        s.style.setProperty('--dl', (-Math.random() * 25).toFixed(1) + 's');
        hero.appendChild(s);
    }
}

// ========== HERO: SCROLL HINT ==========
function initScrollHint() {
    const hero = $('.hero');
    if (!hero) return;
    const a = document.createElement('a');
    a.href = '#about';
    a.className = 'scroll-hint';
    a.innerHTML = '<span class="mouse"><i></i></span><small>Scroll</small>';
    hero.appendChild(a);
}

// ========== HERO: MOUSE PARALLAX (CSS vars) ==========
function initHeroParallax() {
    const hero = $('.hero');
    if (!hero || isTouch || prefersReduced) return;
    hero.addEventListener('mousemove', e => {
        const r = hero.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        hero.style.setProperty('--px', (x * 2).toFixed(3));
        hero.style.setProperty('--py', (y * 2).toFixed(3));
    });
    hero.addEventListener('mouseleave', () => {
        hero.style.setProperty('--px', 0);
        hero.style.setProperty('--py', 0);
    });
}

// ========== PARTICLES (interactive, DPR aware, pauses offscreen) ==========
function createParticles() {
    const hero = $('.hero');
    if (!hero || prefersReduced) return;
    const canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:absolute;inset:0;pointer-events:none;z-index:0;';
    hero.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    let w = 0, h = 0, particles = [], running = true, rafId;
    const mouse = { x: -9999, y: -9999 };

    const make = () => ({
        x: Math.random() * w,
        y: Math.random() * h,
        size: Math.random() * 2.5 + 0.6,
        vx: (Math.random() - 0.5) * 0.6,
        vy: (Math.random() - 0.5) * 0.6,
        o: Math.random() * 0.5 + 0.2,
        cyan: Math.random() > 0.7
    });
    function resize() {
        w = hero.offsetWidth; h = hero.offsetHeight;
        canvas.width = w * dpr; canvas.height = h * dpr;
        canvas.style.width = w + 'px'; canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        const count = clamp(Math.floor((w * h) / 14000), 25, 110);
        particles = Array.from({ length: count }, make);
    }
    function frame() {
        if (!running) return;
        ctx.clearRect(0, 0, w, h);
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            // mouse repel
            const dx = p.x - mouse.x, dy = p.y - mouse.y;
            const d = Math.hypot(dx, dy);
            if (d < 140 && d > 0) {
                const f = (140 - d) / 140;
                p.vx += (dx / d) * f * 0.35;
                p.vy += (dy / d) * f * 0.35;
            }
            p.vx *= 0.985; p.vy *= 0.985;
            // keep drifting
            if (Math.abs(p.vx) < 0.15) p.vx += (Math.random() - 0.5) * 0.02;
            if (Math.abs(p.vy) < 0.15) p.vy += (Math.random() - 0.5) * 0.02;
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0 || p.x > w) p.vx *= -1;
            if (p.y < 0 || p.y > h) p.vy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fillStyle = p.cyan ? `rgba(6,182,212,${p.o})` : `rgba(99,102,241,${p.o})`;
            ctx.fill();

            for (let j = i + 1; j < particles.length; j++) {
                const q = particles[j];
                const dist = Math.hypot(p.x - q.x, p.y - q.y);
                if (dist < 130) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = `rgba(99,102,241,${0.14 * (1 - dist / 130)})`;
                    ctx.lineWidth = 0.6;
                    ctx.stroke();
                }
            }
            // lines to mouse
            const md = Math.hypot(p.x - mouse.x, p.y - mouse.y);
            if (md < 180) {
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                ctx.lineTo(mouse.x, mouse.y);
                ctx.strokeStyle = `rgba(6,182,212,${0.35 * (1 - md / 180)})`;
                ctx.lineWidth = 0.8;
                ctx.stroke();
            }
        }
        rafId = requestAnimationFrame(frame);
    }

    hero.addEventListener('mousemove', e => {
        const r = hero.getBoundingClientRect();
        mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top;
    });
    hero.addEventListener('mouseleave', () => { mouse.x = mouse.y = -9999; });

    let t;
    window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(resize, 200); });

    new IntersectionObserver(([entry]) => {
        const visible = entry.isIntersecting;
        if (visible && !running) { running = true; frame(); }
        else if (!visible) { running = false; cancelAnimationFrame(rafId); }
    }).observe(hero);

    resize();
    frame();
}

// ========== SECTION ORBS (parallax glow blobs) ==========
const orbs = [];
function initOrbs() {
    if (prefersReduced) return;
    $$('.section').forEach(sec => {
        [['orb-a', 0.07], ['orb-b', -0.06]].forEach(([cls, speed]) => {
            const o = document.createElement('div');
            o.className = 'orb ' + cls;
            o.setAttribute('aria-hidden', 'true');
            sec.appendChild(o);
            orbs.push({ el: o, parent: sec, speed });
        });
    });
}

// ========== SKILLS MARQUEE (built from skill cards) ==========
function initMarquee() {
    const grid = $('.skills-grid');
    if (!grid) return;
    const items = $$('.skill-card', grid).map(c => ({
        icon: (c.querySelector('.skill-icon i') || {}).className || '',
        name: (c.querySelector('.skill-name') || {}).textContent || ''
    })).filter(i => i.name);
    if (items.length < 3) return;

    const wrap = document.createElement('div');
    wrap.className = 'marquee';
    wrap.setAttribute('aria-hidden', 'true');

    [false, true].forEach((reverse, idx) => {
        const row = document.createElement('div');
        row.className = 'marquee-row' + (reverse ? ' reverse' : '');
        const track = document.createElement('div');
        track.className = 'marquee-track';
        track.style.animationDuration = (idx ? 46 : 38) + 's';
        const list = reverse ? [...items].reverse() : items;
        const reps = Math.max(1, Math.ceil(14 / list.length));
        for (let half = 0; half < 2; half++) {
            for (let r = 0; r < reps; r++) {
                list.forEach(it => {
                    const span = document.createElement('span');
                    span.className = 'marquee-item';
                    const i = document.createElement('i');
                    i.className = it.icon;
                    span.append(i, document.createTextNode(it.name));
                    track.appendChild(span);
                });
            }
        }
        row.appendChild(track);
        wrap.appendChild(row);
    });
    grid.parentNode.insertBefore(wrap, grid);
}

// ========== NAVBAR / MOBILE MENU ==========
function initNav() {
    const navToggle = $('.nav-toggle');
    const navLinks = $('.nav-links');
    if (navToggle) {
        navToggle.addEventListener('click', () => {
            navToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
        $$('.nav-links a').forEach(link => link.addEventListener('click', () => {
            navToggle.classList.remove('active');
            navLinks.classList.remove('active');
        }));
    }
    // active link via IntersectionObserver
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                $$('.nav-links a').forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + e.target.id));
            }
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    $$('section[id]').forEach(s => obs.observe(s));
}

// ========== BACK TO TOP (progress ring) ==========
let backTop, backRing;
const RING_C = 138.23;
function initBackTop() {
    backTop = document.createElement('button');
    backTop.className = 'back-top';
    backTop.setAttribute('aria-label', 'Back to top');
    backTop.innerHTML = `
        <svg viewBox="0 0 52 52" aria-hidden="true">
            <circle class="track" cx="26" cy="26" r="22"></circle>
            <circle class="ring" cx="26" cy="26" r="22"></circle>
        </svg>
        <i class="fas fa-arrow-up"></i>`;
    document.body.appendChild(backTop);
    backRing = backTop.querySelector('.ring');
    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// ========== SCROLL EFFECTS (one rAF-throttled handler) ==========
function initScrollEffects() {
    const bar = document.createElement('div');
    bar.className = 'scroll-progress';
    document.body.appendChild(bar);

    const navbar = $('.navbar');
    const navLinks = $('.nav-links');
    const heroImg = $('.hero-image');
    const timelines = $$('.timeline');
    let lastY = window.scrollY, ticking = false;

    function update() {
        const y = window.scrollY;
        const max = document.documentElement.scrollHeight - window.innerHeight;
        const prog = max > 0 ? y / max : 0;
        bar.style.width = prog * 100 + '%';

        if (navbar) {
            navbar.classList.toggle('scrolled', y > 50);
            const menuOpen = navLinks && navLinks.classList.contains('active');
            if (!menuOpen && Math.abs(y - lastY) > 6) navbar.classList.toggle('nav-hidden', y > lastY && y > 500);
            if (y < 300) navbar.classList.remove('nav-hidden');
        }
        lastY = y;

        if (backTop) {
            backTop.classList.toggle('show', y > 600);
            backRing.style.strokeDashoffset = RING_C * (1 - prog);
        }

        if (!prefersReduced) {
            orbs.forEach(o => {
                const r = o.parent.getBoundingClientRect();
                if (r.bottom < -200 || r.top > window.innerHeight + 200) return;
                o.el.style.translate = `0 ${(r.top + r.height / 2 - window.innerHeight / 2) * o.speed}px`;
            });
            if (heroImg && y < window.innerHeight) heroImg.style.translate = `0 ${y * 0.14}px`;
        }

        timelines.forEach(tl => {
            const r = tl.getBoundingClientRect();
            tl.style.setProperty('--tl', clamp((window.innerHeight * 0.65 - r.top) / r.height, 0, 1).toFixed(3));
        });
        ticking = false;
    }
    window.addEventListener('scroll', () => {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    window.addEventListener('resize', update);
    update();
}

// ========== REVEAL ANIMATIONS (AOS-like) ==========
function initAOS() {
    const elements = $$('[data-aos]');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = +entry.target.getAttribute('data-aos-delay') || 0;
                setTimeout(() => entry.target.classList.add('aos-animate'), delay);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    elements.forEach(el => observer.observe(el));
}

// ========== TIMELINE ITEMS LIGHT UP ==========
function initTimelineItems() {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('is-active'); obs.unobserve(e.target); }
        });
    }, { rootMargin: '0px 0px -35% 0px' });
    $$('.timeline-item').forEach(i => obs.observe(i));
}

// ========== TEXT SCRAMBLE ON SECTION TITLES ==========
function scramble(el) {
    const final = el.dataset.text || el.textContent;
    el.dataset.text = final;
    const pool = '!<>-_\\/[]{}=+*^?#01';
    const t0 = performance.now(), dur = 900;
    (function step(now) {
        const p = clamp((now - t0) / dur, 0, 1);
        let out = '';
        for (let i = 0; i < final.length; i++) {
            const c = final[i];
            if (c === ' ') out += ' ';
            else if (i / final.length < p) out += c;
            else out += pool[Math.floor(Math.random() * pool.length)];
        }
        el.textContent = out;
        p < 1 ? requestAnimationFrame(step) : (el.textContent = final);
    })(t0);
}
function initScramble() {
    if (prefersReduced) return;
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { scramble(e.target); obs.unobserve(e.target); }
        });
    }, { threshold: 0.8 });
    $$('.section-title').forEach(t => obs.observe(t));
}

// ========== SKILL BARS ==========
function initSkillBars() {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const bar = entry.target;
            const target = parseInt(bar.getAttribute('data-level')) || 0;
            bar.style.width = target + '%';
            const text = bar.closest('.skill-card').querySelector('.skill-level');
            if (text) {
                const t0 = performance.now(), dur = 1500;
                (function step(now) {
                    const p = clamp((now - t0) / dur, 0, 1);
                    text.textContent = Math.round(target * easeOutCubic(p)) + '%';
                    if (p < 1) requestAnimationFrame(step);
                })(t0);
            }
            obs.unobserve(bar);
        });
    }, { threshold: 0.5 });
    $$('.skill-progress').forEach(b => obs.observe(b));
}

// ========== COUNTER STATS ==========
function initCounters() {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.getAttribute('data-count')) || 0;
            const suffix = el.getAttribute('data-suffix') || '';
            const t0 = performance.now(), dur = 1800;
            (function step(now) {
                const p = clamp((now - t0) / dur, 0, 1);
                el.textContent = Math.round(target * easeOutExpo(p)) + suffix;
                if (p < 1) requestAnimationFrame(step);
                else { el.classList.add('pop'); }
            })(t0);
            obs.unobserve(el);
        });
    }, { threshold: 0.5 });
    $$('[data-count]').forEach(el => obs.observe(el));
}

// ========== TYPING EFFECT ==========
function initTyping() {
    const element = $('.typing-text');
    if (!element) return;
    const texts = element.getAttribute('data-texts').split('|');
    let textIndex = 0, charIndex = 0, isDeleting = false;
    function type() {
        const current = texts[textIndex];
        if (isDeleting) { element.textContent = current.substring(0, charIndex - 1); charIndex--; }
        else { element.textContent = current.substring(0, charIndex + 1); charIndex++; }
        let speed = isDeleting ? 40 : 80;
        if (!isDeleting && charIndex === current.length) { speed = 2000; isDeleting = true; }
        else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            speed = 400;
        }
        setTimeout(type, speed);
    }
    type();
}

// ========== CARD SPOTLIGHT (follows the mouse) ==========
function initSpotlight() {
    if (isTouch) return;
    const sel = '.skill-card, .project-card, .stat-item, .timeline-card, .contact-info-card, .contact-form';
    $$(sel).forEach(c => c.classList.add('spot'));
    document.addEventListener('pointermove', e => {
        const card = e.target.closest && e.target.closest('.spot');
        if (!card) return;
        const r = card.getBoundingClientRect();
        card.style.setProperty('--mx', e.clientX - r.left + 'px');
        card.style.setProperty('--my', e.clientY - r.top + 'px');
    });
}

// ========== 3D TILT ==========
function initTilt() {
    if (isTouch || prefersReduced) return;
    $$('.project-card, .skill-card').forEach(card => {
        let raf;
        card.addEventListener('mouseenter', () => card.classList.add('is-tilting'));
        card.addEventListener('mousemove', e => {
            const r = card.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                card.style.transform = `perspective(900px) rotateX(${(-y * 12).toFixed(2)}deg) rotateY(${(x * 12).toFixed(2)}deg) translateY(-8px) scale(1.02)`;
            });
        });
        card.addEventListener('mouseleave', () => {
            cancelAnimationFrame(raf);
            card.classList.remove('is-tilting');
            card.style.transform = '';
        });
    });
}

// ========== MAGNETIC BUTTONS + RIPPLE ==========
function initButtons() {
    $$('.btn').forEach(btn => {
        btn.addEventListener('click', e => {
            const r = btn.getBoundingClientRect();
            const size = Math.max(r.width, r.height) * 2;
            const s = document.createElement('span');
            s.className = 'ripple';
            s.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - r.left - size / 2}px;top:${e.clientY - r.top - size / 2}px`;
            btn.appendChild(s);
            setTimeout(() => s.remove(), 750);
        });
    });
    if (isTouch || prefersReduced) return;
    $$('.btn, .social-links a').forEach(el => {
        el.addEventListener('mousemove', e => {
            const r = el.getBoundingClientRect();
            const x = e.clientX - r.left - r.width / 2;
            const y = e.clientY - r.top - r.height / 2;
            el.style.translate = `${x * 0.25}px ${y * 0.3}px`;
        });
        el.addEventListener('mouseleave', () => { el.style.translate = ''; });
    });
}

// ========== SMOOTH SCROLL ==========
function initSmoothScroll() {
    $$('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const id = this.getAttribute('href');
            if (id.length < 2) return;
            const target = $(id);
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
}

// ========== CONFETTI ==========
function confetti(ox, oy) {
    if (prefersReduced) return;
    const c = document.createElement('canvas');
    c.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:100000';
    c.width = innerWidth; c.height = innerHeight;
    document.body.appendChild(c);
    const ctx = c.getContext('2d');
    const colors = ['#6366f1', '#06b6d4', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'];
    const ps = Array.from({ length: 120 }, () => ({
        x: ox, y: oy,
        vx: (Math.random() - 0.5) * 16,
        vy: Math.random() * -15 - 4,
        s: Math.random() * 8 + 4,
        r: Math.random() * 6.28,
        vr: (Math.random() - 0.5) * 0.4,
        c: colors[(Math.random() * colors.length) | 0]
    }));
    let f = 0;
    (function loop() {
        ctx.clearRect(0, 0, c.width, c.height);
        ps.forEach(p => {
            p.vy += 0.35; p.vx *= 0.99;
            p.x += p.vx; p.y += p.vy; p.r += p.vr;
            ctx.save();
            ctx.translate(p.x, p.y); ctx.rotate(p.r);
            ctx.globalAlpha = Math.max(0, 1 - f / 150);
            ctx.fillStyle = p.c;
            ctx.fillRect(-p.s / 2, -p.s / 4, p.s, p.s / 2);
            ctx.restore();
        });
        f++;
        f < 150 ? requestAnimationFrame(loop) : c.remove();
    })();
}

// ========== TOAST ==========
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const icon = type === 'success' ? '✓' : '!';
    toast.style.cssText = `
        position: fixed; bottom: 100px; right: 30px; z-index: 100001;
        padding: 16px 24px; border-radius: 12px; font-weight: 600;
        font-size: 0.95rem; color: white; transform: translateY(100px) scale(.9);
        opacity: 0; transition: all 0.5s cubic-bezier(0.34,1.56,0.64,1);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        display: flex; align-items: center; gap: 10px;
        background: ${type === 'success' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)'};
    `;
    const badge = document.createElement('span');
    badge.textContent = icon;
    badge.style.cssText = 'width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,.25);display:grid;place-items:center;font-size:.85rem;';
    toast.append(badge, document.createTextNode(message));
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.transform = 'translateY(0) scale(1)'; toast.style.opacity = '1'; }, 100);
    setTimeout(() => {
        toast.style.transform = 'translateY(100px) scale(.9)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3500);
}

// ========== CONTACT FORM ==========
function initContactForm() {
    const form = $('#contactForm');
    if (!form) return;
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled = true;
        const formData = new FormData(this);
        try {
            const response = await fetch('contact_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Message sent successfully!', 'success');
                const r = btn.getBoundingClientRect();
                confetti(r.left + r.width / 2, r.top + r.height / 2);
                this.reset();
            } else {
                showToast(result.message || 'Something went wrong', 'error');
                shake(this);
            }
        } catch (error) {
            showToast('Failed to send message. Please try again.', 'error');
            shake(this);
        }
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
    function shake(el) {
        el.classList.remove('shake');
        void el.offsetWidth;
        el.classList.add('shake');
    }
}

// ========== TAB TITLE FUN ==========
function initTabTitle() {
    const original = document.title;
    document.addEventListener('visibilitychange', () => {
        document.title = document.hidden ? 'Come back! 👋' : original;
    });
}

// ========== INIT ==========
initPreloader();
document.addEventListener('DOMContentLoaded', () => {
    initSplitText();
    initCursor();
    initClickRing();
    initNav();
    initBackTop();
    initOrbs();
    initMarquee();       // must run before spotlight/tilt/skill bars so it only reads the cards
    initAOS();
    initTimelineItems();
    initScramble();
    initSkillBars();
    initCounters();
    initTyping();
    initCodeFloat();
    initScrollHint();
    initHeroParallax();
    createParticles();
    initSpotlight();
    initTilt();
    initButtons();
    initSmoothScroll();
    initContactForm();
    initTabTitle();
    initScrollEffects();
});