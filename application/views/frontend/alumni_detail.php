<?php $EN = (get_site_lang() == 'en'); ?>

<!-- JSON-LD Person Schema (SEO) -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $a->full_name,
    'jobTitle' => $a->current_position ?? '',
    'worksFor' => ['@type' => 'Organization', 'name' => $a->company ?? ''],
    'alumniOf' => ['@type' => 'CollegeOrUniversity', 'name' => site_name()],
    'email' => $a->email ?? '',
    'sameAs' => array_values(array_filter([$a->linkedin_url ?? '', $a->website_url ?? ''])),
]) ?>
</script>

<!-- ============ PROFILE HEADER ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <a href="<?= base_url('alumni') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-10">
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            <?= $EN ? 'Alumni Directory' : 'Direktori Alumni' ?>
        </a>

        <div class="flex flex-col md:flex-row md:items-center gap-8">
            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden bg-navy-light border-2 border-gold flex items-center justify-center font-serif text-4xl text-gold flex-shrink-0">
                <?php if ($a->photo): ?>
                    <img src="<?= base_url('assets/uploads/' . $a->photo) ?>" class="w-full h-full object-cover" alt="">
                <?php else: ?>
                    <?= strtoupper(substr($a->full_name, 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="bg-gold text-navy text-[10px] uppercase tracking-editorial px-3 py-1.5 font-bold">Class of <?= $a->graduation_year ?></span>
                    <?php if ($a->prodi_name): ?>
                    <span class="border border-gold/50 text-gold text-[10px] uppercase tracking-editorial px-3 py-1.5 font-semibold"><?= html_escape($a->prodi_name) ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="font-serif font-light text-4xl md:text-6xl tracking-[-0.02em] leading-[1.05] mb-4"><?= html_escape($a->full_name) ?></h1>
                <p class="text-ivory/80 text-lg">
                    <?php if ($a->current_position): ?><?= html_escape($a->current_position) ?><?php endif; ?>
                    <?php if ($a->company): ?> <span class="text-gold font-semibold">@ <?= html_escape($a->company) ?></span><?php endif; ?>
                </p>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex md:flex-col gap-2 flex-shrink-0">
                <button type="button" onclick="downloadVCard()" class="btn-gold px-5 py-3 text-[10px] uppercase tracking-editorial font-bold" title="<?= $EN ? 'Save contact' : 'Simpan kontak' ?>">
                    <i class="fas fa-address-card mr-2"></i>vCard
                </button>
                <button type="button" onclick="window.print()" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-5 py-3 text-[10px] uppercase tracking-editorial font-bold transition">
                    <i class="fas fa-print mr-2"></i><?= $EN ? 'Print' : 'Cetak' ?>
                </button>
                <button type="button" onclick="toggleSharePanel()" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-5 py-3 text-[10px] uppercase tracking-editorial font-bold transition">
                    <i class="fas fa-share-alt mr-2"></i>Share
                </button>
            </div>
        </div>

        <!-- SHARE PANEL -->
        <div id="sharePanel" class="hidden mt-6 bg-ivory/10 backdrop-blur border border-ivory/20 p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="shareWA()" class="bg-green-600 text-ivory px-4 py-2 text-[10px] uppercase tracking-editorial font-bold hover:bg-green-500 transition"><i class="fab fa-whatsapp mr-1"></i>WhatsApp</button>
                <button type="button" onclick="shareLinkedIn()" class="bg-blue-700 text-ivory px-4 py-2 text-[10px] uppercase tracking-editorial font-bold hover:bg-blue-600 transition"><i class="fab fa-linkedin-in mr-1"></i>LinkedIn</button>
                <button type="button" onclick="shareEmail()" class="bg-slate-600 text-ivory px-4 py-2 text-[10px] uppercase tracking-editorial font-bold hover:bg-slate-500 transition"><i class="fas fa-envelope mr-1"></i>Email</button>
                <button type="button" onclick="copyProfileLink()" class="border border-ivory/40 text-ivory px-4 py-2 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-link mr-1"></i><?= $EN ? 'Copy Link' : 'Salin Link' ?></button>
            </div>
        </div>
    </div>
</section>

<!-- ============ PROFILE BODY ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12 max-w-6xl mx-auto">

            <!-- Main -->
            <div class="lg:col-span-2 space-y-8">
                <?php if ($a->bio): ?>
                <div class="bg-white border border-gray-200 p-8 md:p-10 fade-in">
                    <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'About' : 'Tentang' ?></p>
                    <p class="text-gray-700 leading-[1.9] text-[16px]"><?= nl2br(html_escape($a->bio)) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($achievement_lines)): ?>
                <div class="bg-white border border-gray-200 p-8 md:p-10 fade-in">
                    <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Highlights & Achievements' : 'Pencapaian & Prestasi' ?></p>
                    <div class="space-y-4">
                        <?php foreach ($achievement_lines as $li => $line): ?>
                        <div class="flex gap-5 items-start">
                            <span class="font-serif text-2xl font-light text-gold-muted leading-none flex-shrink-0 w-8"><?= str_pad($li + 1, 2, '0', STR_PAD_LEFT) ?></span>
                            <p class="text-gray-700 leading-relaxed pt-0.5"><?= html_escape($line) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside>
                <div class="sticky top-32 space-y-6">
                    <div class="bg-white border border-gray-200 p-8">
                        <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Contact & Location' : 'Kontak & Lokasi' ?></p>
                        <ul class="space-y-4 text-sm">
                            <li class="flex gap-3"><i class="fas fa-envelope text-gold-muted mt-1"></i><a href="mailto:<?= html_escape($a->email) ?>" class="text-navy hover:text-gold transition break-all"><?= html_escape($a->email) ?></a></li>
                            <?php if ($a->phone): ?><li class="flex gap-3"><i class="fas fa-phone text-gold-muted mt-1"></i><span class="text-slate"><?= html_escape($a->phone) ?></span></li><?php endif; ?>
                            <?php if ($a->city || $a->country): ?><li class="flex gap-3"><i class="fas fa-map-marker-alt text-gold-muted mt-1"></i><span class="text-slate"><?= html_escape($a->city ?? '') ?><?= $a->country ? ', ' . html_escape($a->country) : '' ?></span></li><?php endif; ?>
                            <?php if ($a->industry): ?><li class="flex gap-3"><i class="fas fa-briefcase text-gold-muted mt-1"></i><span class="text-slate"><?= html_escape($a->industry) ?></span></li><?php endif; ?>
                        </ul>
                        <div class="flex gap-2 mt-6 pt-6 border-t border-gray-100">
                            <?php if ($a->linkedin_url): ?>
                            <a href="<?= html_escape($a->linkedin_url) ?>" target="_blank" rel="noopener" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition"><i class="fab fa-linkedin-in mr-1"></i>LinkedIn</a>
                            <?php endif; ?>
                            <?php if ($a->website_url): ?>
                            <a href="<?= html_escape($a->website_url) ?>" target="_blank" rel="noopener" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition"><i class="fas fa-globe mr-1"></i>Website</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-navy text-ivory p-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gold/10 rounded-full blur-2xl"></div>
                        <p class="editorial-label text-gold mb-4"><?= $EN ? 'Alumni Network' : 'Jaringan Alumni' ?></p>
                        <h4 class="font-serif text-xl font-light mb-5 leading-snug"><?= $EN ? 'Explore other alumni profiles' : 'Jelajahi profil alumni lainnya' ?></h4>
                        <a href="<?= base_url('alumni') ?>" class="btn-gold inline-block px-6 py-3 font-semibold uppercase tracking-editorial text-xs"><?= $EN ? 'Back to Directory' : 'Ke Direktori' ?></a>
                    </div>
                </div>
            </aside>

        </div>

        <!-- RELATED ALUMNI (defensive — muncul jika controller mengirim $related_alumni) -->
        <?php if (!empty($related_alumni)): ?>
        <div class="max-w-6xl mx-auto mt-16">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Same Program' : 'Prodi yang Sama' ?></p>
            <h2 class="font-serif text-3xl font-light text-navy mb-8"><?= $EN ? 'More <em class="italic text-gold-muted">Alumni</em>' : 'Alumni <em class="italic text-gold-muted">Lainnya</em>' ?></h2>
            <div class="grid md:grid-cols-4 gap-5">
                <?php foreach ($related_alumni as $ra): ?>
                <a href="<?= base_url('alumni/view/' . $ra->id) ?>" class="bg-white border border-gray-200 p-6 text-center hover:border-gold/40 hover:shadow-lg transition group">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-navy text-gold flex items-center justify-center font-serif text-xl mx-auto mb-4 border-2 border-gold/30">
                        <?php if (!empty($ra->photo)): ?>
                            <img src="<?= base_url('assets/uploads/' . $ra->photo) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <?= strtoupper(substr($ra->full_name, 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition text-sm"><?= html_escape($ra->full_name) ?></p>
                    <p class="text-[10px] text-slate mt-1 uppercase tracking-wider">Class of <?= $ra->graduation_year ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Hidden data untuk vCard & Share -->
<div id="alumniProfile" class="hidden"
     data-name="<?= html_escape($a->full_name) ?>"
     data-position="<?= html_escape($a->current_position ?? '') ?>"
     data-company="<?= html_escape($a->company ?? '') ?>"
     data-email="<?= html_escape($a->email ?? '') ?>"
     data-phone="<?= html_escape($a->phone ?? '') ?>"
     data-city="<?= html_escape($a->city ?? '') ?>"
     data-country="<?= html_escape($a->country ?? '') ?>"
     data-linkedin="<?= html_escape($a->linkedin_url ?? '') ?>"
     data-website="<?= html_escape($a->website_url ?? '') ?>"
     data-prodi="<?= html_escape($a->prodi_name ?? '') ?>"
     data-year="<?= html_escape($a->graduation_year ?? '') ?>"></div>

<div id="alToast" class="fixed bottom-24 right-6 z-[100] px-5 py-3 bg-navy text-ivory text-sm shadow-xl transition-transform translate-x-[120%]" style="transition:transform .3s"><i class="fas fa-check-circle text-gold mr-2"></i><span id="alToastMsg"></span></div>

<script>
(function(){
    var d = document.getElementById('alumniProfile').dataset;
    var url = window.location.href;
    var name = d.name || 'Alumni';

    function vEsc(s){ return (s || '').replace(/\\/g,'\\\\').replace(/\n/g,'\\n').replace(/,/g,'\\,').replace(/;/g,'\\;'); }

    function toast(msg){
        var t = document.getElementById('alToast');
        document.getElementById('alToastMsg').textContent = msg;
        t.style.transform = 'translateX(0)';
        setTimeout(function(){ t.style.transform = 'translateX(120%)'; }, 2500);
    }

    // ===== vCard DOWNLOAD (client-side) =====
    window.downloadVCard = function(){
        var L = ['BEGIN:VCARD', 'VERSION:3.0', 'FN:' + vEsc(d.name)];
        var parts = (d.name || '').split(' ');
        var last = parts.shift() || '';
        L.push('N:' + vEsc(last) + ';' + vEsc(parts.join(' ')) + ';;;');
        if (d.position) L.push('TITLE:' + vEsc(d.position));
        if (d.company) L.push('ORG:' + vEsc(d.company));
        if (d.email) L.push('EMAIL;TYPE=INTERNET:' + d.email);
        if (d.phone) L.push('TEL;TYPE=CELL:' + d.phone);
        if (d.city || d.country) L.push('ADR;TYPE=HOME:;;' + vEsc(d.city) + ';;;' + vEsc(d.country));
        if (d.linkedin) L.push('URL:' + d.linkedin);
        else if (d.website) L.push('URL:' + d.website);
        L.push('NOTE:' + vEsc((d.prodi ? d.prodi + ' — ' : '') + 'Class of ' + d.year));
        L.push('END:VCARD');
        var blob = new Blob([L.join('\r\n')], { type: 'text/vcard' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = (d.name || 'alumni').replace(/\s+/g, '_') + '.vcf';
        a.click();
        toast('vCard downloaded!');
    };

    // ===== SHARE =====
    window.toggleSharePanel = function(){
        document.getElementById('sharePanel').classList.toggle('hidden');
    };
    window.shareWA = function(){
        window.open('https://wa.me/?text=' + encodeURIComponent('Profil Alumni: ' + name + ' — ' + url), '_blank');
    };
    window.shareLinkedIn = function(){
        window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url), '_blank');
    };
    window.shareEmail = function(){
        window.location.href = 'mailto:?subject=' + encodeURIComponent('Profil Alumni: ' + name) + '&body=' + encodeURIComponent(url);
    };
    window.copyProfileLink = function(){
        if (navigator.clipboard) { navigator.clipboard.writeText(url); toast('<?= $EN ? "Link copied!" : "Link disalin!" ?>'); }
    };
})();
</script>