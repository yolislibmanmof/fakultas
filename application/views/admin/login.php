<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Control — Secure Access</title>
    <meta name="description" content="Faculty Admin Terminal - Secure Login">
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700;9..144,800;9..144,900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': '#0B2239', 'navy-deep': '#061420', 'navy-light': '#13334F',
                        'ivory': '#F7F5F0', 'gold': '#C9A227', 'gold-muted': '#B8941F',
                    },
                    fontFamily: {
                        'serif': ['Fraunces', 'Georgia', 'serif'],
                        'sans': ['Inter', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'monospace'],
                    },
                    letterSpacing: { 'editorial': '0.2em', 'wide-xl': '0.35em' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html, body { height: 100%; }
        body { font-family: 'Inter', sans-serif; background: #061420; }
        .font-serif { font-family: 'Fraunces', serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        @media (min-width: 1024px) and (min-height: 620px) {
            body { overflow: hidden; }
        }

        /* 🔥 REDUCE MOTION SUPPORT */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        .login-bg {
            background:
                radial-gradient(ellipse at 20% 20%, rgba(201,162,39,0.18) 0%, transparent 45%),
                radial-gradient(ellipse at 85% 80%, rgba(19,51,79,0.45) 0%, transparent 50%),
                linear-gradient(135deg, #0B2239 0%, #061420 100%);
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(201,162,39,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,162,39,0.06) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridMove 40s linear infinite;
        }
        @keyframes gridMove { from { background-position: 0 0; } to { background-position: 60px 60px; } }

        .scanline {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(201,162,39,0.04) 50%, transparent 100%);
            background-size: 100% 4px;
            pointer-events: none;
            animation: scan 8s linear infinite;
        }
        @keyframes scan { from { background-position: 0 -100%; } to { background-position: 0 200%; } }

        .editorial-label { font-size: 0.68rem; letter-spacing: 0.25em; text-transform: uppercase; font-weight: 600; }

        input:focus { outline: none; border-color: #C9A227 !important; box-shadow: 0 0 0 3px rgba(201,162,39,0.25); }

        .btn-login {
            background: linear-gradient(135deg, #C9A227 0%, #D4AF37 50%, #B8941F 100%);
            color: #0B2239;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
            box-shadow: 0 4px 20px rgba(201,162,39,0.25);
        }
        .btn-login::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.8s;
        }
        .btn-login:hover::before { transform: translateX(100%); }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(201,162,39,0.4); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: .7; cursor: not-allowed; transform: none; }
        .btn-login.loading .btn-text { opacity: 0; }
        .btn-login.loading .btn-spinner { opacity: 1; }
        .btn-spinner { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s; }
        .btn-spinner i { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .oversize-text {
            font-family: 'Fraunces', serif;
            font-size: clamp(4rem, 13vw, 11rem);
            line-height: 0.85;
            font-weight: 300;
            letter-spacing: -0.05em;
            color: rgba(247,245,240,0.035);
            transition: transform .3s ease-out;
            will-change: transform;
        }

        .typewriter::after { content: '|'; color: #C9A227; font-weight: 300; animation: blink 1s infinite; margin-left: 2px; }
        @keyframes blink { 50% { opacity: 0; } }

        .corner { opacity: 0; animation: cornerIn 1s cubic-bezier(.22,1,.36,1) forwards; }
        .corner.tl { animation-delay: .2s; }
        .corner.br { animation-delay: .4s; }
        .corner.form-corner { animation-delay: .6s; }
        @keyframes cornerIn { from { opacity: 0; transform: scale(.6); } to { opacity: 1; transform: scale(1); } }

        .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 12px #10b981; animation: statusPulse 2s ease-in-out infinite; }
        @keyframes statusPulse { 0%,100% { opacity: 1; } 50% { opacity: .5; } }

        .form-card { opacity: 0; transform: translateY(30px); animation: cardIn 1s cubic-bezier(.22,1,.36,1) .3s forwards; }
        @keyframes cardIn { to { opacity: 1; transform: none; } }
        .left-col { opacity: 0; transform: translateX(-30px); animation: cardIn 1s cubic-bezier(.22,1,.36,1) .15s forwards; }

        .pwd-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; transition: color .2s; padding: 4px; }
        .pwd-toggle:hover { color: #C9A227; }

        .flash-anim { animation: flashIn .5s cubic-bezier(.22,1,.36,1); }
        @keyframes flashIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }

        ::selection { background: #C9A227; color: #0B2239; }
        #loginCanvas { position: absolute; inset: 0; z-index: 1; pointer-events: none; }

        .sec-chip { background: rgba(201,162,39,0.08); border: 1px solid rgba(201,162,39,0.25); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        .feat-icon { transition: all .4s cubic-bezier(.22,1,.36,1); }
        .feat-item:hover .feat-icon { background: #C9A227; color: #0B2239; transform: rotate(-5deg) scale(1.08); }

        .field-wrap:focus-within .field-icon { color: #C9A227; }
        .field-icon { transition: color .3s; }

        /* 🔥 CAPS LOCK WARNING */
        .caps-warning {
            display: none;
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            color: #f59e0b;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .caps-warning.visible { display: flex; align-items: center; gap: 4px; }

        /* 🔥 PASSWORD STRENGTH METER */
        .pwd-strength {
            height: 3px;
            background: #e5e7eb;
            margin-top: 6px;
            border-radius: 2px;
            overflow: hidden;
        }
        .pwd-strength-bar {
            height: 100%;
            transition: width .3s, background .3s;
            width: 0%;
        }
        .pwd-strength-bar.weak { width: 33%; background: #ef4444; }
        .pwd-strength-bar.medium { width: 66%; background: #f59e0b; }
        .pwd-strength-bar.strong { width: 100%; background: #10b981; }

        /* 🔥 FAILED ATTEMPTS BADGE */
        .failed-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* 🔥 REMEMBER ME CHECKBOX */
        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #475569;
        }
        .remember-check input {
            width: 16px;
            height: 16px;
            accent-color: #C9A227;
            cursor: pointer;
        }

        /* 🔥 2FA READY BADGE */
        .tfa-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 6px;
            background: rgba(16,185,129,0.1);
            color: #10b981;
            border-radius: 2px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        @media (max-height: 820px) {
            .fit-hide { display: none !important; }
            .fit-tight { margin-bottom: 1rem !important; }
            .fit-head { font-size: 2.5rem !important; }
        }
        @media (max-height: 700px) {
            .fit-head { font-size: 2rem !important; }
            .fit-card { padding: 1.5rem !important; }
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center relative overflow-hidden">

    <canvas id="loginCanvas"></canvas>
    <div class="absolute inset-0 grid-pattern pointer-events-none"></div>
    <div class="scanline pointer-events-none"></div>

    <div id="ovTop" class="absolute top-0 left-0 oversize-text pointer-events-none select-none">FAKULTAS</div>
    <div id="ovBot" class="absolute bottom-0 right-0 oversize-text pointer-events-none select-none">MC · <?= date('y') ?></div>

    <div class="corner tl absolute top-6 left-6 w-16 h-16 border-l-2 border-t-2 border-gold/50"></div>
    <div class="corner br absolute bottom-6 right-6 w-16 h-16 border-r-2 border-b-2 border-gold/50"></div>

    <div class="absolute top-5 right-24 z-20 sec-chip px-4 py-2 items-center gap-2 hidden md:flex">
        <span class="status-dot"></span>
        <span class="font-mono text-[10px] tracking-wider text-gold">SECURE · TLS 1.3</span>
        <!-- 🔥 2FA READY BADGE -->
        <span class="tfa-badge"><i class="fas fa-shield-halved"></i> 2FA Ready</span>
    </div>

    <div class="container mx-auto px-6 py-6 lg:py-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center max-w-6xl mx-auto">

            <!-- LEFT -->
            <div class="left-col hidden lg:block text-ivory">
                <div class="flex items-center gap-4 fit-tight mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-gold to-gold-muted text-navy flex items-center justify-center font-serif font-bold text-xl shadow-lg shadow-gold/20">F</div>
                    <div class="leading-tight">
                        <h1 class="font-serif font-semibold text-xl tracking-tight">Fakultas Teknik</h1>
                        <p class="text-[10px] uppercase tracking-editorial text-ivory/50">&amp; Ilmu Komputer</p>
                    </div>
                </div>

                <p class="editorial-label text-gold mb-4">Restricted Access · Terminal 01</p>
                <h2 class="fit-head font-serif text-4xl xl:text-6xl font-light leading-[1.05] mb-5 tracking-tight">
                    Welcome to<br>
                    <em class="italic text-gold typewriter" id="twText"></em>
                </h2>
                <p class="text-ivory/70 text-base xl:text-lg leading-relaxed mb-7 max-w-md">
                    Platform terpusat untuk mengelola seluruh ekosistem digital fakultas — dari konten akademik, riset, hingga data alumni.
                </p>

                <div class="space-y-3 border-t border-ivory/10 pt-5">
                    <div class="feat-item flex items-center gap-4 text-sm cursor-default">
                        <div class="feat-icon w-10 h-10 border border-ivory/20 flex items-center justify-center text-gold flex-shrink-0">
                            <i class="fas fa-shield-halved text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-ivory text-sm">Secured Access</p>
                            <p class="text-ivory/50 text-xs">Perlindungan brute-force &amp; CSRF aktif</p>
                        </div>
                    </div>
                    <div class="feat-item flex items-center gap-4 text-sm cursor-default">
                        <div class="feat-icon w-10 h-10 border border-ivory/20 flex items-center justify-center text-gold flex-shrink-0">
                            <i class="fas fa-user-shield text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-ivory text-sm">Authorized Personnel</p>
                            <p class="text-ivory/50 text-xs">Hanya untuk staf &amp; administrator resmi</p>
                        </div>
                    </div>
                    <div class="feat-item flex items-center gap-4 text-sm cursor-default">
                        <div class="feat-icon w-10 h-10 border border-ivory/20 flex items-center justify-center text-gold flex-shrink-0">
                            <i class="fas fa-clock-rotate-left text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-ivory text-sm">Audit Trail</p>
                            <p class="text-ivory/50 text-xs">Setiap aktivitas tercatat &amp; dapat dilacak</p>
                        </div>
                    </div>
                </div>

                <div class="fit-hide mt-6 pt-4 border-t border-ivory/10 flex items-center justify-between text-[10px] font-mono uppercase tracking-wider text-ivory/40">
                    <span>v<?= date('Y') ?>.1 · CI/3.x</span>
                    <span class="flex items-center gap-2"><i class="fas fa-server"></i> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache' ?></span>
                </div>
            </div>

            <!-- RIGHT: FORM -->
            <div class="form-card relative max-w-md mx-auto lg:mx-0 w-full">
                <div class="fit-card bg-ivory p-7 md:p-9 shadow-2xl relative">
                    <div class="corner form-corner absolute -top-3 -right-3 w-14 h-14 border-t-2 border-r-2 border-gold"></div>
                    <div class="absolute -bottom-3 -left-3 w-10 h-10 border-b-2 border-l-2 border-gold/40"></div>

                    <div class="lg:hidden flex items-center gap-3 mb-6 pb-4 border-b border-navy/10">
                        <div class="w-10 h-10 bg-navy text-gold flex items-center justify-center font-serif font-bold text-lg">F</div>
                        <div class="leading-tight">
                            <h1 class="font-serif font-semibold text-navy">Mission Control</h1>
                            <p class="text-[10px] uppercase tracking-editorial text-slate">Faculty Admin Terminal</p>
                        </div>
                    </div>

                    <div class="fit-tight mb-6">
                        <p class="editorial-label text-gold-muted mb-1.5">Sign In</p>
                        <h1 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-tight leading-tight">
                            Masuk ke <em class="italic">akun</em> Anda
                        </h1>
                        <!-- 🔥 FAILED ATTEMPTS BADGE -->
                        <?php 
                        $failed_count = $this->session->userdata('login_attempts') ?? 0;
                        if ($failed_count > 0): 
                        ?>
                        <div class="mt-2">
                            <span class="failed-badge">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= $failed_count ?> percobaan gagal
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="flash-anim bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 mb-5 flex items-start gap-3 text-sm" role="alert">
                            <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                            <span><?= $this->session->flashdata('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="flash-anim bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 mb-5 flex items-start gap-3 text-sm" role="alert">
                            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                            <span><?= $this->session->flashdata('success') ?></span>
                        </div>
                    <?php endif; ?>

                    <?= form_open('auth/login', ['class' => 'space-y-4', 'id' => 'loginForm', 'autocomplete' => 'on']) ?>
                        <div>
                            <label class="block editorial-label text-navy mb-1.5" for="usernameInput">Username</label>
                            <div class="field-wrap relative">
                                <i class="fas fa-user field-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate text-sm"></i>
                                <input type="text" name="username" id="usernameInput" required autocomplete="username"
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-navy/15 text-navy placeholder:text-slate/60 transition font-medium"
                                    placeholder="Masukkan username"
                                    aria-label="Username"
                                    aria-required="true">
                            </div>
                        </div>

                        <div>
                            <label class="block editorial-label text-navy mb-1.5" for="passwordInput">Password</label>
                            <div class="field-wrap relative">
                                <i class="fas fa-lock field-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate text-sm"></i>
                                <input type="password" name="password" id="passwordInput" required autocomplete="current-password"
                                    class="w-full pl-11 pr-24 py-3 bg-white border border-navy/15 text-navy placeholder:text-slate/60 transition font-medium"
                                    placeholder="Masukkan password"
                                    aria-label="Password"
                                    aria-required="true">
                                <!-- 🔥 CAPS LOCK WARNING -->
                                <span class="caps-warning" id="capsWarning">
                                    <i class="fas fa-exclamation-circle"></i> CAPS
                                </span>
                                <span class="pwd-toggle" id="pwdToggle" title="Tampilkan password" role="button" tabindex="0" aria-label="Toggle password visibility">
                                    <i class="fas fa-eye text-sm" id="pwdIcon"></i>
                                </span>
                            </div>
                            <!-- 🔥 PASSWORD STRENGTH METER -->
                            <div class="pwd-strength" id="pwdStrength">
                                <div class="pwd-strength-bar" id="pwdStrengthBar"></div>
                            </div>
                        </div>

                        <!-- 🔥 REMEMBER ME -->
                        <div class="flex items-center justify-between">
                            <label class="remember-check">
                                <input type="checkbox" name="remember" value="1" id="rememberMe">
                                <span>Ingat saya</span>
                            </label>
                            <a href="<?= base_url('auth/forgot') ?>" class="text-xs text-gold-muted hover:text-gold font-semibold transition">
                                Lupa password?
                            </a>
                        </div>

                        <button type="submit" class="btn-login w-full py-3.5 font-semibold uppercase tracking-editorial text-xs relative" id="loginBtn" aria-label="Login to Mission Control">
                            <span class="btn-text"><i class="fas fa-arrow-right-to-bracket mr-2"></i>Masuk ke Mission Control</span>
                            <span class="btn-spinner"><i class="fas fa-circle-notch mr-2"></i>Memverifikasi...</span>
                        </button>
                    <?= form_close() ?>

                    <div class="fit-hide mt-6 pt-4 border-t border-navy/10">
                        <div class="sec-chip border-navy/10 bg-gold/5 flex items-start gap-3 p-2.5">
                            <i class="fas fa-info-circle text-gold-muted mt-0.5 text-sm"></i>
                            <p class="text-xs text-slate leading-relaxed">
                                Akun default: <span class="font-mono text-navy font-semibold">admin</span> / <span class="font-mono text-navy font-semibold">admin123</span>
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-center text-[10px] font-mono tracking-wider text-ivory/30 mt-3 uppercase">
                    <i class="fas fa-lock mr-1"></i> End-to-end encrypted session
                </p>
            </div>
        </div>

        <div class="fit-hide mt-6 text-center text-ivory/30 text-[11px] font-mono tracking-wider uppercase">
            <p>&copy; <?= date('Y') ?> Fakultas Teknik &amp; Ilmu Komputer · Mission Control</p>
        </div>
    </div>

<script>
(function(){
    // ===== TYPEWRITER =====
    var tw = document.getElementById('twText');
    if (tw) {
        var words = ['Mission Control.', 'The Faculty Hub.', 'Your Command Center.'];
        var wi = 0, ci = 0, deleting = false;
        function type() {
            var w = words[wi];
            if (!deleting) {
                tw.textContent = w.substring(0, ci + 1); ci++;
                if (ci === w.length) { deleting = true; setTimeout(type, 2200); return; }
                setTimeout(type, 85);
            } else {
                tw.textContent = w.substring(0, ci - 1); ci--;
                if (ci === 0) { deleting = false; wi = (wi + 1) % words.length; setTimeout(type, 400); return; }
                setTimeout(type, 40);
            }
        }
        setTimeout(type, 800);
    }

    // ===== PARALLAX =====
    var ovTop = document.getElementById('ovTop'), ovBot = document.getElementById('ovBot');
    if (ovTop && ovBot && window.innerWidth > 900) {
        document.addEventListener('mousemove', function(e){
            var x = (e.clientX / window.innerWidth - 0.5) * 2;
            var y = (e.clientY / window.innerHeight - 0.5) * 2;
            ovTop.style.transform = 'translate(' + (x * 20) + 'px, ' + (y * 15) + 'px)';
            ovBot.style.transform = 'translate(' + (-x * 25) + 'px, ' + (-y * 18) + 'px)';
        });
    }

    // ===== 🔥 CAPS LOCK DETECTOR =====
    var pwdInput = document.getElementById('passwordInput');
    var capsWarning = document.getElementById('capsWarning');
    if (pwdInput && capsWarning) {
        pwdInput.addEventListener('keyup', function(e) {
            var capsLock = e.getModifierState && e.getModifierState('CapsLock');
            capsWarning.classList.toggle('visible', capsLock);
        });
    }

    // ===== 🔥 PASSWORD STRENGTH METER =====
    var pwdStrengthBar = document.getElementById('pwdStrengthBar');
    if (pwdInput && pwdStrengthBar) {
        pwdInput.addEventListener('input', function() {
            var pwd = pwdInput.value;
            var strength = 0;
            
            if (pwd.length >= 6) strength++;
            if (pwd.length >= 10) strength++;
            if (/[A-Z]/.test(pwd)) strength++;
            if (/[0-9]/.test(pwd)) strength++;
            if (/[^A-Za-z0-9]/.test(pwd)) strength++;
            
            pwdStrengthBar.className = 'pwd-strength-bar';
            if (pwd.length === 0) {
                pwdStrengthBar.style.width = '0%';
            } else if (strength <= 2) {
                pwdStrengthBar.classList.add('weak');
            } else if (strength <= 3) {
                pwdStrengthBar.classList.add('medium');
            } else {
                pwdStrengthBar.classList.add('strong');
            }
        });
    }

    // ===== PASSWORD TOGGLE =====
    var pwdToggle = document.getElementById('pwdToggle'), pwdIcon = document.getElementById('pwdIcon');
    if (pwdToggle && pwdInput) {
        pwdToggle.addEventListener('click', function(){
            if (pwdInput.type === 'password') { 
                pwdInput.type = 'text'; 
                pwdIcon.classList.replace('fa-eye', 'fa-eye-slash'); 
                pwdToggle.setAttribute('aria-label', 'Hide password');
            } else { 
                pwdInput.type = 'password'; 
                pwdIcon.classList.replace('fa-eye-slash', 'fa-eye'); 
                pwdToggle.setAttribute('aria-label', 'Show password');
            }
        });
        // Keyboard support
        pwdToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                pwdToggle.click();
            }
        });
    }

    // ===== LOADING STATE =====
    var form = document.getElementById('loginForm'), btn = document.getElementById('loginBtn');
    if (form && btn) {
        form.addEventListener('submit', function(e){ 
            // Prevent double submit
            if (btn.disabled) {
                e.preventDefault();
                return false;
            }
            btn.classList.add('loading'); 
            btn.disabled = true; 
        });
    }

    // ===== AUTO FOCUS =====
    var un = document.getElementById('usernameInput');
    if (un && !un.value) setTimeout(function(){ un.focus(); }, 900);

    // ===== 🔥 REMEMBER ME PERSISTENCE =====
    var rememberMe = document.getElementById('rememberMe');
    if (rememberMe) {
        var saved = localStorage.getItem('login_remember');
        if (saved === 'true') {
            rememberMe.checked = true;
        }
        rememberMe.addEventListener('change', function() {
            localStorage.setItem('login_remember', this.checked);
        });
    }

    // ===== CANVAS CONSTELLATION (Lazy Load) =====
    var canvas = document.getElementById('loginCanvas');
    if (!canvas) return;
    
    // Lazy load - only init if visible
    var observer = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting) {
            initCanvas();
            observer.disconnect();
        }
    });
    observer.observe(canvas);

    function initCanvas() {
        var ctx = canvas.getContext('2d');
        function resize(){ canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
        resize(); window.addEventListener('resize', resize);

        var pts = [], N = Math.min(45, Math.floor(window.innerWidth / 28));
        for (var i = 0; i < N; i++) pts.push({ x: Math.random()*canvas.width, y: Math.random()*canvas.height, vx:(Math.random()-.5)*.25, vy:(Math.random()-.5)*.25, r: Math.random()*1.5+.5 });

        var mx = -9999, my = -9999;
        document.addEventListener('mousemove', function(e){ mx = e.clientX; my = e.clientY; });

        var frame = 0;
        function tick() {
            requestAnimationFrame(tick);
            if (document.hidden) return;
            if (++frame % 2) return;
            ctx.clearRect(0,0,canvas.width,canvas.height);
            pts.forEach(function(p){
                p.x += p.vx; p.y += p.vy;
                if (p.x<0||p.x>canvas.width) p.vx*=-1;
                if (p.y<0||p.y>canvas.height) p.vy*=-1;
                var dx=p.x-mx, dy=p.y-my, d=Math.sqrt(dx*dx+dy*dy);
                if (d<120){ p.x+=dx/d*1.5; p.y+=dy/d*1.5; }
                ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,6.28); ctx.fillStyle='rgba(201,162,39,.55)'; ctx.fill();
            });
            for (var i=0;i<pts.length;i++) for (var j=i+1;j<pts.length;j++){
                var dx=pts[i].x-pts[j].x, dy=pts[i].y-pts[j].y, d=Math.sqrt(dx*dx+dy*dy);
                if (d<140){ ctx.beginPath(); ctx.moveTo(pts[i].x,pts[i].y); ctx.lineTo(pts[j].x,pts[j].y); ctx.strokeStyle='rgba(201,162,39,'+(0.18*(1-d/140))+')'; ctx.lineWidth=.5; ctx.stroke(); }
            }
        }
        tick();
    }
})();
</script>
</body>
</html>