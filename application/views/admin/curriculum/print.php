<?php $p = $program; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kurikulum <?= html_escape($p->name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Georgia, 'Times New Roman', serif; color: #111; padding: 40px; font-size: 13px; position: relative; }
        
        /* 🔥 Watermark DRAFT (jika SKS tidak sesuai) */
        <?php if ($p->validation['status'] !== 'ok'): ?>
        body::before {
            content: 'DRAFT';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.1);
            z-index: -1;
            letter-spacing: 20px;
        }
        <?php endif; ?>
        
        .head { text-align: center; border-bottom: 3px double #111; padding-bottom: 16px; margin-bottom: 24px; }
        .head h1 { font-size: 20px; letter-spacing: 1px; text-transform: uppercase; }
        .head p { font-size: 12px; margin-top: 4px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; }
        .meta div { margin-bottom: 3px; }
        h2 { font-size: 14px; margin: 18px 0 8px; text-transform: uppercase; letter-spacing: 1px; border-left: 4px solid #111; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; font-size: 12px; }
        th { background: #eee; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
        td.code { font-family: 'Courier New', monospace; font-size: 11px; }
        td.sks, th.sks { text-align: center; width: 60px; }
        .total { text-align: right; font-weight: bold; margin: 4px 0 10px; }
        .sign { margin-top: 48px; display: flex; justify-content: space-between; align-items: flex-end; }
        .sign div { text-align: center; font-size: 12px; }
        .sign .space { height: 70px; }
        
        /* 🔥 QR Code verification */
        .qr-section { text-align: center; }
        .qr-box { display: inline-block; padding: 10px; border: 2px solid #111; }
        .qr-placeholder { width: 80px; height: 80px; background: #000; position: relative; }
        .qr-placeholder::before, .qr-placeholder::after {
            content: ''; position: absolute; background: #fff;
        }
        .qr-placeholder::before {
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            background: repeating-linear-gradient(0deg, #000 0px, #000 3px, #fff 3px, #fff 6px);
        }
        .qr-label { font-size: 9px; margin-top: 5px; color: #666; }
        
        .no-print { position: fixed; top: 12px; right: 12px; }
        .no-print button { background: #0B2239; color: #fff; border: 0; padding: 10px 18px; font-size: 12px; cursor: pointer; letter-spacing: 1px; text-transform: uppercase; }
        @media print { .no-print { display: none; } body { padding: 20px; } }
    </style>
</head>
<body>

<div class="no-print"><button onclick="window.print()"><i>🖨</i> Cetak / Simpan PDF</button></div>

<div class="head">
    <h1><?= html_escape(site_name()) ?></h1>
    <p>Kurikulum & Struktur Mata Kuliah</p>
</div>

<div class="meta">
    <div>
        <div><strong>Program Studi:</strong> <?= html_escape($p->name) ?></div>
        <div><strong>Jenjang:</strong> <?= html_escape($p->degree) ?></div>
        <div><strong>Akreditasi:</strong> <?= html_escape($p->accreditation ?? '-') ?></div>
        <div><strong>Tanggal Cetak:</strong> <?= date('d F Y') ?></div>
    </div>
    <div style="text-align:right">
        <div><strong>Total SKS:</strong> <?= $p->total_sks ?></div>
        <div><strong>Semester:</strong> <?= count($p->curriculum) ?></div>
        <div><strong>Mata Kuliah:</strong> <?= $p->total_courses ?></div>
        <div><strong>Status:</strong> <?= $p->validation['label'] ?></div>
    </div>
</div>

<?php if (empty($p->curriculum)): ?>
    <p>Belum ada mata kuliah terinput untuk program studi ini.</p>
<?php else: ?>
    <?php foreach ($p->curriculum as $smt => $courses):
        $sks = array_sum(array_map(function ($c) { return (int)$c->sks; }, $courses));
    ?>
    <h2>Semester <?= $smt ?></h2>
    <table>
        <thead>
            <tr><th style="width:40px">No</th><th style="width:110px">Kode</th><th>Mata Kuliah</th><th style="width:90px">Jenis</th><th class="sks">SKS</th></tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($courses as $c): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="code"><?= html_escape($c->course_code) ?></td>
                <td><?= html_escape($c->name) ?></td>
                <td><?= ucfirst($c->course_type ?? 'wajib') ?></td>
                <td class="sks"><?= $c->sks ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">Subtotal: <?= $sks ?> SKS</div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="sign">
    <!-- 🔥 QR Code verification -->
    <div class="qr-section">
        <div class="qr-box">
            <div class="qr-placeholder"></div>
        </div>
        <p class="qr-label">Scan untuk verifikasi</p>
    </div>
    
    <div>
        <p>Ketua Program Studi,</p>
        <div class="space"></div>
        <p><strong><?= html_escape($p->head_of_study_program ?? '____________________') ?></strong></p>
    </div>
</div>

</body>
</html>