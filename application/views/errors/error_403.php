<?php
$EN = (function_exists('get_site_lang') && get_site_lang() == 'en');
$message = $message ?? 'Access Denied';
?>
<!DOCTYPE html>
<html lang="<?= $EN ? 'en' : 'id' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — <?= html_escape($message) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --navy: #0B2239; --navy-deep: #061420; --ivory: #F7F5F0;
            --gold: #C9A227; --red: #dc2626; --red-soft: #fecaca;
        }
        body {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: radial-gradient(ellipse at center, #13334F 0%, #061420 100%);
            font-family: 'Inter', system-ui, sans-serif; color: var(--ivory);
            overflow: hidden; position: relative;
        }
        .font-serif { font-family: 'Fraunces', serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Grid pattern background */
        body::before {
            content: ''; position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(201,162,39,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,162,39,.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Radar sweep */
        .radar {
            position: absolute; inset: 0; pointer-events: none;
            background: conic-gradient(from 0deg at 50% 50%,
                transparent 0deg,
                rgba(220,38,38,.18) 20deg,
                transparent 40deg,
                transparent 360deg);
            animation: radarSpin 6s linear infinite;
            mix-blend-mode: screen;
        }
        @keyframes radarSpin { to { transform: rotate(360deg); } }

        /* Scanline CRT */
        .scanline {
            position: absolute; inset: 0; pointer-events: none;
            background: repeating-linear-gradient(
                0deg,
                rgba(0,0,0,.15) 0px,
                rgba(0,0,0,.15) 1px,
                transparent 1px,
                transparent 3px
            );
            opacity: .3;
        }

        .wrap { position: relative; z-index: 2; text-align: center; padding: 2rem; max-width: 640px; }

        /* Vault door */
        .vault {
            position: relative; width: 180px; height: 180px;
            margin: 0 auto 2.5rem; border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #1e3a5f, #0B2239);
            border: 6px solid #C9A227;
            box-shadow: 0 0 60px rgba(220,38,38,.25), inset 0 0 40px rgba(0,0,0,.5);
            animation: vaultPulse 3s ease-in-out infinite;
        }
        @keyframes vaultPulse {
            0%,100% { box-shadow: 0 0 60px rgba(220,38,38,.25), inset 0 0 40px rgba(0,0,0,.5); }
            50%     { box-shadow: 0 0 90px rgba(220,38,38,.5), inset 0 0 40px rgba(0,0,0,.5); }
        }
        .vault::before, .vault::after {
            content: ''; position: absolute; border: 2px solid rgba(201,162,39,.4);
            border-radius: 50%;
        }
        .vault::before { inset: 12px; }
        .vault::after  { inset: 26px; border-style: dashed; animation: vaultSpin 20s linear infinite; }
        @keyframes vaultSpin { to { transform: rotate(360deg); } }

        .vault-lock {
            position: absolute; inset: 0; display: flex;
            align-items: center; justify-content: center;
            font-size: 3.5rem; color: var(--red);
            animation: lockShake 4s ease-in-out infinite;
        }
        @keyframes lockShake {
            0%,90%,100% { transform: translateX(0); }
            92%,96% { transform: translateX(-4px); }
            94%,98% { transform: translateX(4px); }
        }

        /* 403 glitch text */
        .code-403 {
            font-family: 'Fraunces', serif; font-weight: 700;
            font-size: clamp(5rem, 15vw, 9rem); line-height: 1;
            color: var(--gold); letter-spacing: -.04em;
            position: relative; display: inline-block; margin-bottom: 1rem;
        }
        .code-403::before, .code-403::after {
            content: '403'; position: absolute; inset: 0;
        }
        .code-403::before { color: var(--red); animation: glitch1 3s infinite; clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%); }
        .code-403::after  { color: #60a5fa; animation: glitch2 3s infinite; clip-path: polygon(0 55%, 100% 55%, 100% 100%, 0 100%); }
        @keyframes glitch1 {
            0%,90%,100% { transform: translate(0,0); }
            92% { transform: translate(-3px, 1px); }
            94% { transform: translate(2px, -1px); }
            96% { transform: translate(-1px, 2px); }
        }
        @keyframes glitch2 {
            0%,90%,100% { transform: translate(0,0); }
            92% { transform: translate(3px, -1px); }
            94% { transform: translate(-2px, 1px); }
            96% { transform: translate(1px, -2px); }
        }

        .label {
            font-family: 'JetBrains Mono', monospace; font-size: 11px;
            letter-spacing: .4em; text-transform: uppercase; color: var(--red);
            font-weight: 700; margin-bottom: 1rem;
        }
        .label::before, .label::after { content: '//'; margin: 0 .5rem; color: rgba(220,38,38,.5); }

        h1 {
            font-family: 'Fraunces', serif; font-weight: 300;
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            line-height: 1.2; letter-spacing: -.02em; margin-bottom: 1rem;
        }
        h1 em { font-style: italic; color: var(--gold); font-weight: 500; }

        .msg {
            color: rgba(247,245,240,.7); font-size: 1rem;
            line-height: 1.7; max-width: 480px; margin: 0 auto 2.5rem;
        }

        /* Terminal line */
        .terminal {
            background: rgba(6,20,32,.6); border: 1px solid rgba(201,162,39,.3);
            padding: 1rem 1.5rem; font-family: 'JetBrains Mono', monospace;
            font-size: 12px; color: rgba(247,245,240,.7); text-align: left;
            margin-bottom: 2.5rem; max-width: 480px; margin-left: auto; margin-right: auto;
            backdrop-filter: blur(8px);
        }
        .terminal .prompt { color: var(--gold); }
        .terminal .cmd { color: #60a5fa; }
        .terminal .err { color: var(--red); }
        .cursor { display: inline-block; width: 8px; height: 14px; background: var(--gold); animation: cursorBlink 1s steps(1) infinite; vertical-align: middle; margin-left: 2px; }
        @keyframes cursorBlink { 50% { opacity: 0; } }

        /* Buttons */
        .btn-group { display: flex; flex-wrap: wrap; gap: .75rem; justify-content: center; }
        .btn {
            padding: .9rem 2rem; font-family: 'JetBrains Mono', monospace;
            font-size: 11px; letter-spacing: .25em; text-transform: uppercase;
            font-weight: 700; text-decoration: none; display: inline-flex;
            align-items: center; gap: .5rem; transition: all .3s;
            position: relative; overflow: hidden;
        }
        .btn-gold { background: var(--gold); color: var(--navy); border: 1px solid var(--gold); }
        .btn-gold:hover { background: #B8941F; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(201,162,39,.4); }
        .btn-ghost { background: transparent; color: var(--ivory); border: 1px solid rgba(247,245,240,.25); }
        .btn-ghost:hover { border-color: var(--gold); color: var(--gold); }

        /* Corner brackets */
        .corner { position: absolute; width: 40px; height: 40px; border: 2px solid rgba(220,38,38,.5); }
        .corner.tl { top: 2rem; left: 2rem; border-right: 0; border-bottom: 0; }
        .corner.tr { top: 2rem; right: 2rem; border-left: 0; border-bottom: 0; }
        .corner.bl { bottom: 2rem; left: 2rem; border-right: 0; border-top: 0; }
        .corner.br { bottom: 2rem; right: 2rem; border-left: 0; border-top: 0; }

        /* Floating labels */
        .float-label {
            position: absolute; font-family: 'JetBrains Mono', monospace;
            font-size: 9px; letter-spacing: .3em; text-transform: uppercase;
            color: rgba(220,38,38,.4); font-weight: 700;
        }
        .float-label.top { top: 3rem; left: 50%; transform: translateX(-50%); }
        .float-label.bottom { bottom: 3rem; left: 50%; transform: translateX(-50%); }
    </style>
</head>
<body>
    <div class="radar"></div>
    <div class="scanline"></div>

    <div class="corner tl"></div>
    <div class="corner tr"></div>
    <div class="corner bl"></div>
    <div class="corner br"></div>

    <span class="float-label top">SECURITY BREACH DETECTED</span>
    <span class="float-label bottom">SESSION TERMINATED</span>

    <div class="wrap">
        <div class="vault">
            <div class="vault-lock"><i class="fas fa-lock"></i></div>
        </div>

        <div class="code-403">403</div>
        <div class="label"><?= $EN ? 'Access Denied' : 'Akses Ditolak' ?></div>

        <h1><?= $EN ? 'Restricted <em>Zone</em>' : 'Zona <em>Terbatas</em>' ?></h1>

        <p class="msg"><?= html_escape($message) ?></p>

        <div class="terminal">
            <div><span class="prompt">visitor@faculty</span>:<span class="cmd">~</span>$ <span id="termCmd"></span><span class="cursor"></span></div>
            <div class="err" id="termErr" style="margin-top:.5rem; opacity:0;"></div>
        </div>

        <div class="btn-group">
            <a href="<?= base_url() ?>" class="btn btn-gold">
                <i class="fas fa-home"></i><?= $EN ? 'Back Home' : 'Kembali' ?>
            </a>
            <a href="<?= base_url('auth/login') ?>" class="btn btn-ghost">
                <i class="fas fa-sign-in-alt"></i><?= $EN ? 'Login' : 'Masuk' ?>
            </a>
        </div>
    </div>

    <script>
    (function(){
        var cmd = document.getElementById('termCmd');
        var err = document.getElementById('termErr');
        var text = 'curl -I /restricted/resource';
        var i = 0;
        function type(){
            if (i <= text.length) {
                cmd.textContent = text.slice(0, i);
                i++;
                setTimeout(type, 60 + Math.random() * 40);
            } else {
                setTimeout(function(){
                    err.style.opacity = '1';
                    err.textContent = '→ HTTP/1.1 403 Forbidden — Permission denied';
                }, 400);
            }
        }
        setTimeout(type, 600);
    })();
    </script>
</body>
</html>