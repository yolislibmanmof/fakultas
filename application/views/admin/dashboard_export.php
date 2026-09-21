<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mission Control — Export Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300;500;700&family=Inter:wght@400;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; color:#0B2239; background:#fff; padding:40px; max-width:900px; margin:0 auto; }
        .font-serif { font-family:'Fraunces',serif; }
        .font-mono { font-family:'JetBrains Mono',monospace; }

        header { border-bottom:3px solid #C9A227; padding-bottom:1.5rem; margin-bottom:2rem; }
        header h1 { font-family:'Fraunces',serif; font-size:2.2rem; font-weight:500; letter-spacing:-.02em; }
        header h1 em { color:#C9A227; font-style:italic; }
        header .meta { font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.15em; color:#64748b; text-transform:uppercase; margin-top:.5rem; }

        h2 { font-family:'Fraunces',serif; font-size:1.3rem; font-weight:500; margin:2rem 0 1rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb; }
        h2 em { color:#C9A227; font-style:italic; }

        .stats-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem; }
        .stat-box { border:1px solid #e5e7eb; border-top:3px solid #C9A227; padding:1rem; }
        .stat-box .num { font-family:'Fraunces',serif; font-size:2rem; font-weight:600; color:#0B2239; line-height:1; }
        .stat-box .label { font-family:'JetBrains Mono',monospace; font-size:9px; letter-spacing:.2em; color:#64748b; text-transform:uppercase; margin-top:.35rem; }

        table { width:100%; border-collapse:collapse; font-size:.85rem; }
        th { text-align:left; font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.15em; color:#64748b; text-transform:uppercase; padding:.6rem .4rem; border-bottom:2px solid #0B2239; }
        td { padding:.6rem .4rem; border-bottom:1px solid #f1f5f9; }
        tr:last-child td { border-bottom:0; }
        .rank { display:inline-block; width:22px; height:22px; background:#0B2239; color:#C9A227; font-family:'Fraunces',serif; font-weight:600; font-size:11px; text-align:center; line-height:22px; }

        footer { margin-top:3rem; padding-top:1rem; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; font-family:'JetBrains Mono',monospace; font-size:10px; color:#64748b; letter-spacing:.1em; text-transform:uppercase; }
        footer .sig { text-align:right; }
        footer .sig-line { border-top:1px solid #0B2239; width:140px; margin-top:3rem; padding-top:.25rem; }

        .no-print { position:fixed; top:16px; right:16px; background:#C9A227; color:#0B2239; padding:.7rem 1.4rem; font-weight:700; font-size:12px; letter-spacing:.15em; text-transform:uppercase; cursor:pointer; border:none; box-shadow:0 4px 16px rgba(201,162,39,.4); }
        .no-print:hover { background:#0B2239; color:#C9A227; }

        @media print {
            .no-print { display:none; }
            body { padding:20px; }
            header { page-break-after:avoid; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">
        <i class="fas fa-print mr-2"></i> Print / Save as PDF
    </button>

    <header>
        <h1>Mission Control <em>Report</em></h1>
        <p class="meta"><?= site_name() ?> · Dashboard Export · <?= date('d F Y · H:i') ?> WIB</p>
    </header>

    <h2>Executive <em>Summary</em></h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="num"><?= $stats['posts_published'] ?></div>
            <div class="label">Artikel Terbit</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $stats['lecturers'] ?></div>
            <div class="label">Dosen Aktif</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $stats['programs'] ?></div>
            <div class="label">Program Studi</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $stats['research'] ?></div>
            <div class="label">Riset</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $stats['alumni_total'] ?></div>
            <div class="label">Alumni</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= $stats['documents'] ?></div>
            <div class="label">Dokumen</div>
        </div>
        <div class="stat-box">
            <div class="num"><?= number_format($stats['total_views']) ?></div>
            <div class="label">Total Views</div>
        </div>
        <div class="stat-box">
            <div class="num" style="color:#C9A227"><?= date('Y') ?></div>
            <div class="label">Periode</div>
        </div>
    </div>

    <h2>Top <em>Performing</em> Articles</h2>
    <?php if (empty($top_articles)): ?>
        <p style="color:#64748b; font-size:.9rem;">Belum ada data artikel.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Judul Artikel</th>
                <th style="width:120px">Kategori</th>
                <th style="width:100px; text-align:right">Views</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($top_articles as $i => $a): ?>
            <tr>
                <td><span class="rank"><?= $i + 1 ?></span></td>
                <td><?= html_escape($a->title) ?></td>
                <td><?= html_escape($a->category_name ?? '-') ?></td>
                <td style="text-align:right; font-family:'JetBrains Mono',monospace; font-weight:600"><?= number_format($a->views ?? 0) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <footer>
        <span>Generated by Mission Control · <?= date('Y') ?></span>
        <div class="sig">
            <div class="sig-line">Administrator</div>
        </div>
    </footer>
</body>
</html>