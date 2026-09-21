(function () {
    if (!window.HERO_FX) return;
    var tpl = HERO_FX.template || 'minimal';
    var particles = !!HERO_FX.particles;
    var welcome = HERO_FX.welcome || '';

    // ===== PERFORMANCE GUARD =====
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var hero = document.getElementById('heroSection');
    var inView = true, pageVis = !document.hidden;
    function canRun() { return !reduce && inView && pageVis; }

    if (hero && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (en) {
            inView = en[0].isIntersecting;
            hero.classList.toggle('fx-paused', !inView || !pageVis);
        }, { threshold: 0 }).observe(hero);
    }
    document.addEventListener('visibilitychange', function () {
        pageVis = !document.hidden;
        if (hero) hero.classList.toggle('fx-paused', !inView || !pageVis);
    });
    var stPause = document.createElement('style');
    stPause.textContent = '#heroSection.fx-paused *{animation-play-state:paused!important}';
    document.head.appendChild(stPause);

    var raf = window.requestAnimationFrame ? requestAnimationFrame.bind(window) : function (cb) { setTimeout(function () { cb(Date.now()); }, 33); };

    // ===== ROBOT WALKER (interval ringan, pause otomatis) =====
    if (tpl === 'robot' && welcome) {
        var robot = document.getElementById('robot');
        var track = document.getElementById('robotText');
        if (robot && track) {
            var full = welcome + '    ';
            var loop = (full + full + full).split('');
            var pos = 0;
            setInterval(function () {
                if (!canRun()) return;
                track.innerHTML = loop.slice(pos, pos + 60).map(function (c, i) {
                    return '<span style="opacity:' + (i < 10 ? (i / 10) : 1) + '">' + (c === ' ' ? '&nbsp;' : c) + '</span>';
                }).join('');
                pos = (pos + 1) % full.length;
            }, 110);
        }
    }

    // ===== AURORA (CSS-driven, auto-pause) =====
    if (tpl === 'aurora') {
        var layer = document.getElementById('auroraLayer');
        if (layer) {
            var cols = ['rgba(201,162,39,0.4)', 'rgba(19,51,79,0.6)', 'rgba(212,175,55,0.3)'];
            for (var i = 0; i < 3; i++) {
                var b = document.createElement('div');
                b.style.cssText = 'position:absolute;border-radius:50%;filter:blur(80px);opacity:.55;';
                var sz = 300 + i * 80;
                b.style.width = sz + 'px'; b.style.height = sz + 'px';
                b.style.background = cols[i % cols.length];
                b.style.left = (10 + i * 25) + '%';
                b.style.top = (20 + (i % 2) * 30) + '%';
                b.style.animation = 'auroraMove ' + (12 + i * 3) + 's ease-in-out infinite alternate';
                layer.appendChild(b);
            }
            var stA = document.createElement('style');
            stA.textContent = '@keyframes auroraMove{0%{transform:translate(0,0) scale(1)}100%{transform:translate(80px,-50px) scale(1.3)}}';
            document.head.appendChild(stA);
        }
    }

    // ===== CAMPUS ICONS (CSS-driven) =====
    if (tpl === 'campus') {
        var layerC = document.getElementById('campusLayer');
        if (layerC) {
            var icons = ['fa-graduation-cap', 'fa-laptop', 'fa-book', 'fa-flask', 'fa-microscope', 'fa-lightbulb', 'fa-user-graduate', 'fa-book-open'];
            for (var ic = 0; ic < 14; ic++) {
                var s = document.createElement('span');
                s.style.cssText = 'position:absolute;color:' + (ic % 2 === 0 ? 'rgba(201,162,39,0.35)' : 'rgba(247,245,240,0.25)') + ';animation:campusFloat ' + (5 + Math.random() * 6) + 's ease-in-out infinite alternate;';
                s.style.left = (2 + Math.random() * 92) + '%';
                s.style.top = (5 + Math.random() * 85) + '%';
                s.style.fontSize = (16 + Math.random() * 24) + 'px';
                s.style.animationDelay = (-Math.random() * 6) + 's';
                s.innerHTML = '<i class="fas ' + icons[ic % icons.length] + '"></i>';
                layerC.appendChild(s);
            }
            var stC = document.createElement('style');
            stC.textContent = '@keyframes campusFloat{from{transform:translateY(0) rotate(-5deg)}to{transform:translateY(-30px) rotate(8deg)}}';
            document.head.appendChild(stC);
        }
    }

    // ===== WAVES (rAF + frame-skip 30fps) =====
    if (tpl === 'waves') {
        var layerW = document.getElementById('auroraLayer');
        if (layerW) {
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 1200 600');
            svg.setAttribute('preserveAspectRatio', 'none');
            svg.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;';
            var paths = [];
            var configs = [
                { amp: 60, freq: 2, y: 300, phase: 0, color: 'rgba(201,162,39,0.6)', width: 2 },
                { amp: 45, freq: 3, y: 350, phase: 1.5, color: 'rgba(247,245,240,0.3)', width: 1.5 },
                { amp: 35, freq: 4, y: 250, phase: 3, color: 'rgba(201,162,39,0.3)', width: 1 }
            ];
            configs.forEach(function (c) {
                var p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                p.setAttribute('fill', 'none');
                p.setAttribute('stroke', c.color);
                p.setAttribute('stroke-width', c.width);
                svg.appendChild(p);
                paths.push({ el: p, cfg: c });
            });
            layerW.appendChild(svg);

            var wf = 0;
            function draw() {
                raf(draw);
                if (!canRun()) return;
                if (++wf % 2) return; // 30fps
                var t = Date.now() / 1500;
                paths.forEach(function (p) {
                    var d = 'M 0 ' + (p.cfg.y + Math.sin(t + p.cfg.phase) * p.cfg.amp);
                    for (var x = 0; x <= 1200; x += 20) {
                        var y = p.cfg.y + Math.sin((x / 1200) * Math.PI * p.cfg.freq + t + p.cfg.phase) * p.cfg.amp;
                        d += ' L ' + x + ' ' + y;
                    }
                    p.el.setAttribute('d', d);
                });
            }
            draw();
        }
    }

    // ===== MATRIX (rAF + frame-skip + kolom dibatasi) =====
    if (tpl === 'matrix') {
        var layerM = document.getElementById('auroraLayer');
        if (layerM) {
            var chars = '01FTIKACDEFGHIJKLMNPQRSTUVWXYZ0123456789';
            var columns = Math.min(36, Math.floor(window.innerWidth / 40));
            var drops = [];
            for (var m = 0; m < columns; m++) {
                var sp = document.createElement('span');
                sp.style.cssText = 'position:absolute;font-family:"JetBrains Mono",monospace;color:#C9A227;font-size:14px;text-shadow:0 0 8px rgba(201,162,39,.5);letter-spacing:2px;white-space:pre;line-height:1.2;';
                sp.style.left = (m * 40) + 'px';
                sp.style.top = '0';
                layerM.appendChild(sp);
                drops.push({ el: sp, y: -Math.random() * 500, speed: 1 + Math.random() * 2, text: '' });
            }
            var mf = 0;
            function tickM() {
                raf(tickM);
                if (!canRun()) return;
                if (++mf % 2) return; // 30fps
                drops.forEach(function (d) {
                    d.y += d.speed;
                    if (d.y > window.innerHeight) { d.y = -100; d.text = ''; }
                    if (Math.random() < 0.1) {
                        d.text = chars[Math.floor(Math.random() * chars.length)] + '\n' + d.text;
                        if (d.text.length > 300) d.text = d.text.substring(0, 300);
                    }
                    d.el.style.transform = 'translateY(' + d.y + 'px)';
                    d.el.textContent = d.text;
                });
            }
            tickM();
        }
    }

    // ===== GEOMETRIC (CSS-driven) =====
    if (tpl === 'geometric') {
        var layerG = document.getElementById('auroraLayer');
        if (layerG) {
            for (var g = 0; g < 12; g++) {
                var d = document.createElement('div');
                var size = 30 + Math.random() * 70;
                var shape = g % 3;
                var radius = shape === 1 ? '50%' : (shape === 2 ? '4px' : '0');
                d.style.cssText = 'position:absolute;border:1px solid rgba(201,162,39,' + (0.2 + Math.random() * 0.3) + ');width:' + size + 'px;height:' + size + 'px;border-radius:' + radius + ';animation:geoSpin ' + (8 + Math.random() * 12) + 's linear infinite;';
                d.style.left = (Math.random() * 95) + '%';
                d.style.top = (Math.random() * 85) + '%';
                d.style.animationDirection = (g % 2 === 0) ? 'normal' : 'reverse';
                if (shape === 2) d.style.transform = 'rotate(45deg)';
                layerG.appendChild(d);
            }
            var stG = document.createElement('style');
            stG.textContent = '@keyframes geoSpin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
            document.head.appendChild(stG);
        }
    }

    // ===== PARTICLES (36 titik + frame-skip, koneksi <110px) =====
    if (particles) {
        var canvas = document.getElementById('heroParticles');
        if (canvas) {
            var ctx = canvas.getContext('2d');
            var pts = [];
            function resize() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; }
            resize();
            window.addEventListener('resize', resize);
            for (var p = 0; p < 36; p++) {
                pts.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    vx: (Math.random() - 0.5) * 0.4,
                    vy: (Math.random() - 0.5) * 0.4,
                    r: 1 + Math.random() * 1.5
                });
            }
            var pf = 0;
            function tickP() {
                raf(tickP);
                if (!canRun()) return;
                if (++pf % 2) return; // 30fps
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                pts.forEach(function (pt) {
                    pt.x += pt.vx; pt.y += pt.vy;
                    if (pt.x < 0 || pt.x > canvas.width) pt.vx *= -1;
                    if (pt.y < 0 || pt.y > canvas.height) pt.vy *= -1;
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y, pt.r, 0, 6.28);
                    ctx.fillStyle = 'rgba(201,162,39,.55)';
                    ctx.fill();
                });
                for (var i = 0; i < pts.length; i++) {
                    for (var j = i + 1; j < pts.length; j++) {
                        var dx = pts[i].x - pts[j].x;
                        var dy = pts[i].y - pts[j].y;
                        var dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 110) {
                            ctx.beginPath();
                            ctx.moveTo(pts[i].x, pts[i].y);
                            ctx.lineTo(pts[j].x, pts[j].y);
                            ctx.strokeStyle = 'rgba(201,162,39,' + (0.15 * (1 - dist / 110)) + ')';
                            ctx.stroke();
                        }
                    }
                }
            }
            tickP();
        }
    }
})();