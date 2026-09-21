<?php
$ok_count   = count(array_filter($programs, function ($p) { return $p->validation['status'] == 'ok'; }));
$warn_count = count($programs) - $ok_count;
$grand_sks  = array_sum(array_map(function ($p) { return $p->total_sks; }, $programs));
$grand_mk   = array_sum(array_map(function ($p) { return $p->total_courses; }, $programs));
$max_sks    = max(array_merge([1], array_map(function ($p) { return $p->total_sks; }, $programs)));
?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Strategic View</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Dashboard <em class="italic text-gold-muted">Kurikulum</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Pemantauan beban SKS, distribusi semester, dan validasi standar Dikti per prodi.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- 🔥 Export CSV semua prodi -->
        <a href="<?= base_url('admin/curriculum/export_all_csv') ?>" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2" title="Export semua prodi">
            <i class="fas fa-file-csv"></i>Export All
        </a>
        <a href="<?= base_url('admin/courses') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-book"></i>Kelola Mata Kuliah
        </a>
    </div>
</div>

<!-- Alert jika ada yang tidak sesuai -->
<?php if ($warn_count > 0): ?>
<div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 px-5 py-4 mb-8 flex items-start gap-3 flash-anim">
    <i class="fas fa-exclamation-triangle mt-0.5"></i>
    <div class="text-sm">
        <strong><?= $warn_count ?> program studi</strong> memiliki beban SKS di luar rentang standar. Periksa kartu bertanda peringatan di bawah.
    </div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="mission-card bg-white p-5 text-center">
        <div class="stat-num text-4xl text-navy"><?= count($programs) ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-2">Program Studi</p>
    </div>
    <div class="mission-card bg-white p-5 text-center">
        <div class="stat-num text-4xl text-green-600"><?= $ok_count ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-2">Sesuai Standar</p>
    </div>
    <div class="mission-card bg-white p-5 text-center">
        <div class="stat-num text-4xl text-navy"><?= number_format($grand_mk) ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-2">Total Mata Kuliah</p>
    </div>
    <div class="mission-card bg-white p-5 text-center">
        <div class="stat-num text-4xl text-gold-muted"><?= number_format($grand_sks) ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-2">Total SKS</p>
    </div>
</div>

<!-- 🔥 FITUR GILA: Visual Chart (Bar Chart SKS per Prodi) -->
<?php if (count($programs) > 1): ?>
<div class="bg-white border border-gray-200 p-8 md:p-10 mb-10">
    <p class="editorial-label text-gold-muted mb-3">Visual Overview</p>
    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy mb-8">Beban SKS antar <em class="italic text-gold-muted">Program Studi</em></h2>
    <div class="space-y-6">
        <?php foreach ($programs as $p): 
            $std = $standards[$p->degree] ?? NULL;
            $v = $p->validation;
            $bar_color = $v['status'] === 'ok' ? 'from-green-500 to-green-600' : 
                        ($v['status'] === 'low' ? 'from-amber-500 to-amber-600' : 
                        ($v['status'] === 'high' ? 'from-red-500 to-red-600' : 'from-gray-400 to-gray-500'));
        ?>
        <div class="group">
            <div class="flex justify-between items-baseline mb-2">
                <div class="flex items-center gap-2">
                    <span class="font-serif text-base text-navy group-hover:text-gold-muted transition"><?= html_escape($p->name) ?></span>
                    <span class="text-xs text-slate">(<?= html_escape($p->degree) ?>)</span>
                </div>
                <div class="flex items-center gap-3">
                    <?php if ($std): ?>
                    <span class="text-xs text-slate">Target: <?= $std['label'] ?></span>
                    <?php endif; ?>
                    <span class="font-mono text-xs text-slate font-semibold"><?= $p->total_sks ?> SKS</span>
                </div>
            </div>
            <div class="relative h-8 bg-ivory-warm overflow-hidden">
                <div class="h-full bg-gradient-to-r <?= $bar_color ?> group-hover:opacity-80 transition-all duration-500 flex items-center justify-end pr-3" style="width: <?= round(($p->total_sks / $max_sks) * 100) ?>%">
                    <span class="text-xs text-white font-bold"><?= $p->total_sks ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Program Cards -->
<div class="grid md:grid-cols-2 gap-6 mb-14">
    <?php foreach ($programs as $idx => $p):
        $v = $p->validation;
        $badge = [
            'ok'      => 'bg-green-50 text-green-700 border-green-200',
            'low'     => 'bg-amber-50 text-amber-700 border-amber-200',
            'high'    => 'bg-red-50 text-red-700 border-red-200',
            'unknown' => 'bg-gray-100 text-gray-600 border-gray-200',
        ][$v['status']];
        $icon = ['ok' => 'fa-check-circle', 'low' => 'fa-arrow-down', 'high' => 'fa-arrow-up', 'unknown' => 'fa-question'][$v['status']];
        $std = $standards[$p->degree] ?? NULL;
        $pct = $std ? min(100, round(($p->total_sks / $std['max']) * 100)) : 0;
    ?>
    <div class="mission-card bg-white overflow-hidden">
        <div class="p-6 md:p-7">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 bg-navy text-gold flex items-center justify-center flex-shrink-0 font-serif font-bold"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                    <div class="min-w-0">
                        <p class="font-serif font-medium text-navy truncate"><?= html_escape($p->name) ?></p>
                        <p class="text-xs text-slate mt-0.5"><?= html_escape($p->degree) ?> &bull; Akreditasi <?= html_escape($p->accreditation ?? '-') ?></p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 <?= $badge ?> border px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold flex-shrink-0">
                    <i class="fas <?= $icon ?> text-[9px]"></i><?= $v['label'] ?>
                </span>
            </div>

            <!-- SKS meter -->
            <div class="mb-4">
                <div class="flex justify-between items-baseline mb-2">
                    <span class="font-serif text-3xl font-light text-navy"><?= $p->total_sks ?> <span class="text-xs font-sans text-slate">/ <?= $v['range'] ?></span></span>
                    <span class="font-mono text-xs text-slate"><?= $p->total_courses ?> MK &bull; <?= count($p->curriculum) ?> Smt</span>
                </div>
                <div class="h-2.5 bg-ivory-warm overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-navy to-gold" style="width: <?= $pct ?>%"></div>
                </div>
            </div>

            <!-- 🔥 Warnings (jika ada) -->
            <?php if (!empty($p->warnings)): ?>
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-xs text-amber-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Warning:</strong>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    <?php foreach ($p->warnings as $w): ?>
                    <li><?= html_escape($w) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="flex gap-2 pt-4 border-t border-gray-100">
                <button type="button" class="cur-toggle flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition" data-target="cur-<?= $p->id ?>">
                    <i class="fas fa-layer-group mr-1"></i>Struktur Semester
                </button>
                <a href="<?= base_url('admin/curriculum/cetak/' . $p->slug) ?>" target="_blank" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-gold/40 text-gold-muted py-2.5 hover:bg-gold hover:text-navy transition">
                    <i class="fas fa-print mr-1"></i>Cetak / PDF
                </a>
                <!-- 🔥 Export CSV per prodi -->
                <a href="<?= base_url('admin/curriculum/export_csv/' . $p->slug) ?>" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition">
                    <i class="fas fa-file-csv mr-1"></i>CSV
                </a>
            </div>
        </div>

        <!-- Expandable structure -->
        <div id="cur-<?= $p->id ?>" class="cur-panel hidden border-t border-gray-100 bg-ivory-warm/30 px-6 py-5">
            <?php if (empty($p->curriculum)): ?>
                <p class="text-sm text-slate">Belum ada mata kuliah terinput.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($p->curriculum as $smt => $courses):
                    $sks = array_sum(array_map(function ($c) { return (int)$c->sks; }, $courses));
                ?>
                <div class="flex items-center justify-between bg-white border border-gray-200 px-4 py-2.5">
                    <span class="text-sm text-navy"><span class="font-mono text-xs text-gold-muted mr-2">SMT <?= str_pad($smt, 2, '0', STR_PAD_LEFT) ?></span><?= count($courses) ?> mata kuliah</span>
                    <span class="bg-navy text-ivory text-xs px-2.5 py-1 font-semibold"><?= $sks ?> SKS</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.cur-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var panel = document.getElementById(btn.dataset.target);
        if (panel) panel.classList.toggle('hidden');
    });
});
</script>