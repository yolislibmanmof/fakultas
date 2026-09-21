<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Signal Lost | Mission Control</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,600;9..144,800&family=Inter:wght@300;400;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html,body { height:100%; }
    body {
        background:#061420; color:#F7F5F0;
        font-family:'Inter',sans-serif;
        overflow-x:hidden;
        display:flex; align-items:center; justify-content:center;
        position:relative;
    }
    ::selection { background:#C9A227; color:#0B2239; }

    /* ===== BACKGROUND ===== */
    .grid-bg {
        position:fixed; inset:0; pointer-events:none;
        background-image:
            linear-gradient(rgba(201,162,39,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(201,162,39,.05) 1px, transparent 1px);
        background-size:56px 56px;
        animation:gridMove 30s linear infinite;
    }
    @keyframes gridMove { to { background-position:56px 56px; } }
    .glow {
        position:fixed; inset:0; pointer-events:none;
        background:
            radial-gradient(ellipse at 15% 20%, rgba(201,162,39,.14) 0%, transparent 45%),
            radial-gradient(ellipse at 85% 80%, rgba(19,51,79,.5) 0%, transparent 50%);
    }
    .noise {
        position:fixed; inset:0; pointer-events:none; opacity:.05;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='120' height='120' filter='url(%23n)' opacity='0.6'/%3E%3C/svg%3E");
    }
    .corner { position:fixed; width:64px; height:64px; border-color:rgba(201,162,39,.5); border-style:solid; border-width:0; }
    .corner.tl { top:20px; left:20px; border-top-width:2px; border-left-width:2px; }
    .corner.br { bottom:20px; right:20px; border-bottom-width:2px; border-right-width:2px; }

    main { position:relative; z-index:2; text-align:center; padding:2rem 1.25rem; max-width:760px; width:100%; }

    /* ===== STATUS CHIPS ===== */
    .chips { display:flex; gap:.5rem; justify-content:center; flex-wrap:wrap; margin-bottom:2rem; }
    .chip {
        font-family:'JetBrains Mono',monospace; font-size:.62rem; letter-spacing:.2em;
        text-transform:uppercase; padding:.45rem .9rem;
        border:1px solid rgba(201,162,39,.35); color:#C9A227;
        background:rgba(201,162,39,.07);
        animation:chipIn .6s cubic-bezier(.22,1,.36,1) both;
    }
    .chip.red { border-color:rgba(255,77,77,.4); color:#ff6b6b; background:rgba(255,77,77,.07); animation-delay:.15s; }
    .chip:nth-child(3) { animation-delay:.3s; }
    @keyframes chipIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }

    /* ===== RADAR + 404 ===== */
    .stage { position:relative; display:inline-block; margin-bottom:1.5rem; }
    .radar { width:min(300px,60vw); height:auto; display:block; margin:0 auto; }
    .radar .ring { fill:none; stroke:rgba(201,162,39,.25); stroke-width:1; }
    .radar .cross { stroke:rgba(201,162,39,.15); stroke-width:1; }
    .radar .sweep { transform-origin:100px 100px; animation:rot 4s linear infinite; }
    .radar .sweepfill { fill:rgba(201,162,39,.2); }
    @keyframes rot { to { transform:rotate(360deg); } }
    .blip { fill:#C9A227; animation:blip 4s infinite; }
    .blip.b2 { animation-delay:1.3s; } .blip.b3 { animation-delay:2.6s; }
    @keyframes blip { 0%,100% { opacity:0; } 8% { opacity:1; } 40% { opacity:.15; } }

    .glitch {
        position:absolute; top:50%; left:50%; transform:translate(-50%,-52%);
        font-family:'Fraunces',serif; font-weight:800;
        font-size:clamp(5rem,18vw,9rem); line-height:.9; color:#F7F5F0;
        text-shadow:0 0 40px rgba(201,162,39,.3);
    }
    .glitch::before, .glitch::after {
        content:attr(data-text); position:absolute; inset:0; overflow:hidden;
    }
    .glitch::before { color:#C9A227; clip-path:inset(0 0 60% 0); transform:translate(-3px,-2px); animation:gl1 2.8s infinite steps(1,end); opacity:.9; }
    .glitch::after  { color:#ff4d4d; clip-path:inset(55% 0 0 0); transform:translate(3px,2px);  animation:gl2 3.4s infinite steps(1,end); opacity:.85; }
    @keyframes gl1 {
        0%,84%,100% { clip-path:inset(0 0 62% 0); transform:translate(-2px,-2px); }
        86% { clip-path:inset(10% 0 35% 0); transform:translate(-7px,1px); }
        90% { clip-path:inset(30% 0 15% 0); transform:translate(5px,-1px); }
        94% { clip-path:inset(0 0 70% 0); transform:translate(-4px,2px); }
    }
    @keyframes gl2 {
        0%,80%,100% { clip-path:inset(58% 0 0 0); transform:translate(2px,2px); }
        84% { clip-path:inset(40% 0 20% 0); transform:translate(7px,-1px); }
        90% { clip-path:inset(70% 0 0 0); transform:translate(-5px,1px); }
    }

    h1 {
        font-family:'Fraunces',serif; font-weight:300;
        font-size:clamp(1.6rem,5vw,2.6rem); letter-spacing:.02em; margin-bottom:.75rem;
    }
    h1 em { color:#C9A227; font-style:italic; }
    .sub { color:rgba(247,245,240,.6); font-size:.95rem; line-height:1.7; max-width:520px; margin:0 auto 2rem; }

    /* ===== TERMINAL ===== */
    .terminal {
        text-align:left; max-width:520px; margin:0 auto 2rem;
        background:rgba(11,34,57,.6); border:1px solid rgba(201,162,39,.25);
        backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        font-family:'JetBrains Mono',monospace; font-size:.75rem; line-height:1.9;
        box-shadow:0 16px 40px rgba(0,0,0,.35);
    }
    .term-bar {
        display:flex; align-items:center; gap:.4rem;
        padding:.5rem .8rem; border-bottom:1px solid rgba(201,162,39,.2);
    }
    .term-bar i { width:9px; height:9px; border-radius:50%; }
    .term-bar .r{background:#ff5f56}.term-bar .y{background:#ffbd2e}.term-bar .g{background:#27c93f}
    .term-bar span { margin-left:auto; font-size:.6rem; letter-spacing:.15em; color:rgba(247,245,240,.4); }
    .term-body { padding:.9rem 1rem; min-height:120px; color:rgba(247,245,240,.85); }
    .term-body .ok { color:#27c93f; } .term-body .warn { color:#C9A227; } .term-body .err { color:#ff6b6b; }
    .cursor { display:inline-block; width:7px; height:13px; background:#C9A227; vertical-align:-2px; animation:blink 1s steps(1) infinite; }
    @keyframes blink { 50% { opacity:0; } }

    /* ===== BUTTONS ===== */
    .actions { display:flex; gap:.75rem; justify-content:center; flex-wrap:wrap; }
    .btn {
        display:inline-flex; align-items:center; gap:.5rem;
        font-size:.68rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase;
        padding:.95rem 1.6rem; text-decoration:none; transition:all .35s cubic-bezier(.22,1,.36,1);
        position:relative; overflow:hidden;
    }
    .btn.gold { background:linear-gradient(135deg,#C9A227,#D4AF37,#B8941F); color:#0B2239; box-shadow:0 4px 20px rgba(201,162,39,.3); }
    .btn.gold:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(201,162,39,.45); }
    .btn.ghost { border:1px solid rgba(247,245,240,.3); color:#F7F5F0; }
    .btn.ghost:hover { background:rgba(247,245,240,.1); transform:translateY(-3px); }
    .hint { margin-top:1.5rem; font-family:'JetBrains Mono',monospace; font-size:.6rem; letter-spacing:.18em; color:rgba(247,245,240,.35); text-transform:uppercase; }
    .hint b { color:#C9A227; }
</style>
</head>
<body>

<div class="grid-bg"></div>
<div class="glow"></div>
<div class="noise"></div>
<div class="corner tl"></div>
<div class="corner br"></div>

<main>
    <div class="chips">
        <span class="chip red"><i>●</i> SIGNAL LOST</span>
        <span class="chip">CODE: 404</span>
        <span class="chip">NODE: FIK-01</span>
    </div>

    <div class="stage">
        <svg class="radar" viewBox="0 0 200 200">
            <circle class="ring" cx="100" cy="100" r="96"/>
            <circle class="ring" cx="100" cy="100" r="64"/>
            <circle class="ring" cx="100" cy="100" r="32"/>
            <line class="cross" x1="4" y1="100" x2="196" y2="100"/>
            <line class="cross" x1="100" y1="4" x2="100" y2="196"/>
            <g class="sweep"><path class="sweepfill" d="M100 100 L100 4 A96 96 0 0 1 167 31 Z"/></g>
            <circle class="blip"    cx="132" cy="66"  r="3"/>
            <circle class="blip b2" cx="66"  cy="122" r="3"/>
            <circle class="blip b3" cx="122" cy="140" r="2.5"/>
        </svg>
        <div class="glitch" data-text="404">404</div>
    </div>

    <h1><?= htmlspecialchars($heading) ?> — <em>Halaman tidak ditemukan</em></h1>
    <p class="sub">
        Koordinat yang Anda tuju berada di luar jangkauan radar kami.
        Kemungkinan halaman telah dipindahkan, dihapus, atau tidak pernah ada.
    </p>

    <div class="terminal">
        <div class="term-bar">
            <i class="r"></i><i class="y"></i><i class="g"></i>
            <span>root@mission-control: ~</span>
        </div>
        <div class="term-body" id="termBody"></div>
    </div>

    <div class="actions">
        <a class="btn gold" href="/">⌂ Return to Base</a>
        <a class="btn ghost" href="javascript:history.back()">↩ Go Back</a>
    </div>

    <p class="hint">Tekan <b>R</b> = return to base &nbsp;·&nbsp; <b>B</b> = go back</p>
</main>

<script>
(function(){
    var MSG = <?= json_encode(trim(strip_tags($message))) ?>;
    var lines = [
        ['> INITIATING DEEP SCAN...', ''],
        ['> TARGET ROUTE ......... ', 'err', 'NOT FOUND'],
        ['> ' + MSG, 'warn'],
        ['> SUGGESTION ........... ', 'ok', 'RETURN TO BASE'],
        ['> _', '']
    ];
    var body = document.getElementById('termBody');
    var li = 0;

    function typeLine() {
        if (li >= lines.length) return;
        var row = document.createElement('div');
        body.appendChild(row);
        var item = lines[li];
        var prefix = item[0];
        var cls = item[1] || '';
        var suffix = item[2] || '';
        var ci = 0;

        (function tick(){
            if (ci <= prefix.length) {
                row.innerHTML = esc(prefix.slice(0, ci)) + (ci < prefix.length ? '<span class="cursor"></span>' : '');
                ci++;
                setTimeout(tick, 14);
            } else if (suffix) {
                row.innerHTML = esc(prefix) + ' <span class="' + cls + '">' + esc(suffix) + '</span>';
                next();
            } else {
                row.innerHTML = esc(prefix) + (li === lines.length - 1 ? '<span class="cursor"></span>' : '');
                next();
            }
        })();

        function next(){ li++; setTimeout(typeLine, 260); }
    }

    function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

    setTimeout(typeLine, 500);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e){
        if (e.key === 'r' || e.key === 'R') window.location.href = '/';
        if (e.key === 'b' || e.key === 'B' || e.key === 'Escape') history.back();
    });
})();
</script>
</body>
</html>