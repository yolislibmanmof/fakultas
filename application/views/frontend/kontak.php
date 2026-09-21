<?php
$EN = (get_site_lang() == 'en');
$CI =& get_instance();
$CI->load->model('Setting_model');
$s = $CI->Setting_model;
$phone   = $s->get('contact_phone', '(021) 123-4567');
$email   = $s->get('contact_email', 'info@fakultas.ac.id');
$address = $s->get('contact_address', 'Jl. Pendidikan No. 1, Indonesia');
$map     = $s->get('contact_map_embed', '');
$wa_num  = $s->get('whatsapp_number', ''); // format: 628xxx (tanpa + atau 0)
$old     = $this->session->flashdata('contact_old') ?: [];

// 🔥 Office hours check (real-time)
$now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$day = (int)$now->format('N'); // 1=Mon, 7=Sun
$hour = (int)$now->format('H');
$is_open = ($day >= 1 && $day <= 5 && $hour >= 8 && $hour < 16);
?>

<style>
.kt-card { background:#fff; border:1px solid #e5e7eb; border-top:3px solid #C9A227; padding:1.75rem; text-align:center; transition:all .4s cubic-bezier(.22,1,.36,1); position: relative; }
.kt-card:hover { transform:translateY(-6px); box-shadow:0 16px 32px rgba(11,34,57,.12); }
.kt-icon { width:56px; height:56px; margin:0 auto 1rem; background:linear-gradient(135deg,#C9A227,#B8941F); color:#0B2239; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border-radius:50%; transition:transform .4s; }
.kt-card:hover .kt-icon { transform:rotate(-8deg) scale(1.1); }
.kt-input { width:100%; padding:.9rem 1.1rem; background:#fff; border:2px solid #e5e7eb; color:#0B2239; transition:border-color .3s; }
.kt-input:focus { outline:none; border-color:#C9A227; }
.kt-reveal { opacity:0; transform:translateY(24px); transition:all .9s cubic-bezier(.22,1,.36,1); }
.kt-reveal.in { opacity:1; transform:none; }
.social-btn { width:44px; height:44px; display:flex; align-items:center; justify-content:center; border:1px solid #e5e7eb; color:#64748b; transition:all .3s; }
.social-btn:hover { background:#0B2239; color:#C9A227; border-color:#0B2239; transform:translateY(-4px); }

/* ===== 🔥 OFFICE STATUS ===== */
.office-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 2px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
.office-status.open { background: rgba(16,185,129,.1); color: #10b981; }
.office-status.closed { background: rgba(239,68,68,.1); color: #ef4444; }
.office-status .pulse { width: 8px; height: 8px; border-radius: 50%; animation: ktPulse 2s infinite; }
.office-status.open .pulse { background: #10b981; }
.office-status.closed .pulse { background: #ef4444; }
@keyframes ktPulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

/* ===== 🔥 COPY BUTTON ===== */
.copy-btn {
    position: absolute; top: 12px; right: 12px;
    width: 32px; height: 32px; background: transparent;
    border: 1px solid #e5e7eb; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; cursor: pointer; transition: all .2s;
    font-size: 11px;
}
.copy-btn:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; }
.copy-btn.copied { background: #10b981; color: #fff; border-color: #10b981; }

/* ===== 🔥 CHAR COUNTER ===== */
.char-counter { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #94a3b8; margin-top: 6px; text-align: right; }
.char-counter.warn { color: #f59e0b; }
.char-counter.danger { color: #ef4444; }

/* ===== 🔥 VALIDATION HINTS ===== */
.field-hint { font-size: 11px; color: #64748b; margin-top: 4px; min-height: 16px; }
.field-hint.error { color: #ef4444; }
.field-hint.success { color: #10b981; }
.kt-input.invalid { border-color: #ef4444; }
.kt-input.valid { border-color: #10b981; }

/* ===== 🔥 FAQ ===== */
.kt-faq-item { border-bottom: 1px solid #e5e7eb; }
.kt-faq-item:last-child { border: 0; }
.kt-faq-q { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; cursor: pointer; transition: color .2s; }
.kt-faq-q:hover { color: #C9A227; }
.kt-faq-q h4 { font-family: 'Fraunces', serif; font-size: 1rem; font-weight: 500; color: #0B2239; flex: 1; }
.kt-faq-q .faq-icon { color: #C9A227; transition: transform .3s; }
.kt-faq-item.open .faq-icon { transform: rotate(180deg); }
.kt-faq-a { max-height: 0; overflow: hidden; transition: max-height .4s ease; }
.kt-faq-item.open .kt-faq-a { max-height: 300px; }
.kt-faq-a p { padding-bottom: 16px; color: #475569; line-height: 1.7; font-size: 14px; }

/* ===== 🔥 WHATSAPP FAB ===== */
.wa-fab {
    position: fixed; bottom: 24px; right: 24px; z-index: 80;
    width: 56px; height: 56px; background: #25D366; color: #fff;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; cursor: pointer;
    box-shadow: 0 8px 24px rgba(37,211,102,.4);
    transition: all .3s; border: none; text-decoration: none;
    animation: waBounce 2s ease-in-out infinite;
}
.wa-fab:hover { transform: scale(1.1); background: #128C7E; color: #fff; }
@keyframes waBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
.wa-fab .wa-tooltip {
    position: absolute; right: 70px; top: 50%; transform: translateY(-50%);
    background: #0B2239; color: #F7F5F0; padding: 8px 14px;
    font-size: 12px; font-weight: 500; white-space: nowrap;
    border-radius: 4px; pointer-events: none;
    opacity: 0; transition: opacity .3s;
}
.wa-fab:hover .wa-tooltip { opacity: 1; }
.wa-fab .wa-tooltip::after {
    content: ''; position: absolute; left: 100%; top: 50%;
    transform: translateY(-50%); border: 6px solid transparent;
    border-left-color: #0B2239;
}

/* ===== 🔥 TOAST ===== */
.kt-toast {
    position: fixed; bottom: 90px; right: 24px; z-index: 100;
    background: #0B2239; color: #F7F5F0;
    padding: 12px 20px; font-size: 13px;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s;
}
.kt-toast.show { transform: translateX(0); }
.kt-toast i { color: #C9A227; margin-right: 8px; }

/* ===== 🔥 DRAFT BADGE ===== */
.draft-badge {
    display: none; align-items: center; gap: 6px;
    padding: 4px 10px; background: #fef3c7;
    color: #92400e; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
}
.draft-badge.visible { display: inline-flex; }
.draft-badge button { background: none; border: none; color: #92400e; cursor: pointer; font-size: 11px; }
</style>

<!-- ============ HERO ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-16 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">K</div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <p class="editorial-label text-gold mb-5"><?= $EN ? 'Get in Touch' : 'Hubungi Kami' ?></p>
        <h1 class="font-serif font-light text-4xl md:text-6xl tracking-[-0.02em] leading-[1.05] max-w-3xl text-balance">
            <?= $EN ? 'We\'d love to <em class="italic text-gold">hear</em> from you' : 'Kami siap <em class="italic text-gold">mendengar</em> Anda' ?>
        </h1>
        <p class="text-ivory/70 mt-6 text-lg max-w-2xl"><?= $EN ? 'Questions about admissions, programs, or partnerships? Our team is ready to help.' : 'Pertanyaan seputar penerimaan, program studi, atau kemitraan? Tim kami siap membantu.' ?></p>
    </div>
</section>

<!-- ============ INFO CARDS ============ -->
<section class="py-16 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-6">
            <div class="kt-card kt-reveal relative">
                <button type="button" class="copy-btn" onclick="copyText('<?= html_escape($phone) ?>', this)" title="<?= $EN ? 'Copy phone' : 'Salin telepon' ?>">
                    <i class="fas fa-copy"></i>
                </button>
                <div class="kt-icon"><i class="fas fa-phone"></i></div>
                <h3 class="font-serif text-lg font-medium text-navy mb-1"><?= $EN ? 'Phone' : 'Telepon' ?></h3>
                <p class="text-sm text-slate"><?= html_escape($phone) ?></p>
            </div>
            <div class="kt-card kt-reveal relative" style="transition-delay:.1s">
                <button type="button" class="copy-btn" onclick="copyText('<?= html_escape($email) ?>', this)" title="<?= $EN ? 'Copy email' : 'Salin email' ?>">
                    <i class="fas fa-copy"></i>
                </button>
                <div class="kt-icon"><i class="fas fa-envelope"></i></div>
                <h3 class="font-serif text-lg font-medium text-navy mb-1">Email</h3>
                <p class="text-sm text-slate"><?= html_escape($email) ?></p>
            </div>
            <div class="kt-card kt-reveal relative" style="transition-delay:.2s">
                <div class="kt-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3 class="font-serif text-lg font-medium text-navy mb-1"><?= $EN ? 'Address' : 'Alamat' ?></h3>
                <p class="text-sm text-slate"><?= html_escape($address) ?></p>
            </div>
            <div class="kt-card kt-reveal" style="transition-delay:.3s">
                <div class="kt-icon"><i class="fas fa-clock"></i></div>
                <h3 class="font-serif text-lg font-medium text-navy mb-2"><?= $EN ? 'Office Hours' : 'Jam Operasional' ?></h3>
                <p class="text-sm text-slate mb-2"><?= $EN ? 'Mon–Fri, 08.00–16.00' : 'Sen–Jum, 08.00–16.00' ?></p>
                <!-- 🔥 LIVE STATUS -->
                <span class="office-status <?= $is_open ? 'open' : 'closed' ?>">
                    <span class="pulse"></span>
                    <?= $is_open ? ($EN ? 'Open Now' : 'Buka Sekarang') : ($EN ? 'Closed' : 'Tutup') ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============ 🔥 FAQ SECTION ============ -->
<section class="py-16 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6 max-w-3xl">
        <div class="text-center mb-10 kt-reveal">
            <p class="editorial-label text-gold-muted mb-3">FAQ</p>
            <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]">
                <?= $EN ? 'Frequently Asked <em class="italic text-gold-muted">Questions</em>' : 'Pertanyaan <em class="italic text-gold-muted">Umum</em>' ?>
            </h2>
        </div>
        <div class="bg-ivory border border-gray-200 p-8 kt-reveal">
            <?php
            $faqs = [
                [$EN ? 'How to apply for admission?' : 'Bagaimana cara mendaftar?',
                 $EN ? 'Visit our PMB page for complete admission requirements and online registration.' : 'Kunjungi halaman PMB untuk syarat lengkap dan pendaftaran online.'],
                [$EN ? 'What scholarships are available?' : 'Beasiswa apa saja yang tersedia?',
                 $EN ? 'We offer various scholarships including merit-based, need-based, and external partnerships. Check the Campus Life page.' : 'Kami menawarkan berbagai beasiswa termasuk prestasi, bantuan finansial, dan kemitraan eksternal. Cek halaman Kemahasiswaan.'],
                [$EN ? 'How to request official documents?' : 'Bagaimana cara meminta dokumen resmi?',
                 $EN ? 'Visit our Download Vault for common forms, or contact the faculty office for specific documents.' : 'Kunjungi Pusat Unduhan untuk formulir umum, atau hubungi tata usaha untuk dokumen spesifik.'],
                [$EN ? 'What are the office hours for student services?' : 'Kapan jam layanan mahasiswa?',
                 $EN ? 'Monday to Friday, 08:00 to 16:00 WIB. Closed on weekends and public holidays.' : 'Senin hingga Jumat, 08:00 hingga 16:00 WIB. Tutup pada akhir pekan dan hari libur nasional.'],
            ];
            foreach ($faqs as $faq):
            ?>
            <div class="kt-faq-item">
                <div class="kt-faq-q" onclick="toggleFaq(this)">
                    <h4><?= $faq[0] ?></h4>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </div>
                <div class="kt-faq-a"><p><?= $faq[1] ?></p></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ FORM + MAP ============ -->
<section class="py-16 md:py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12">

            <!-- Form -->
            <div class="kt-reveal">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-3">
                    <p class="editorial-label text-gold-muted"><?= $EN ? 'Send a Message' : 'Kirim Pesan' ?></p>
                    <!-- 🔥 DRAFT BADGE -->
                    <span class="draft-badge" id="draftBadge">
                        <i class="fas fa-save"></i>
                        <span><?= $EN ? 'Draft saved' : 'Draft tersimpan' ?></span>
                        <button type="button" onclick="clearDraft()" title="Clear"><i class="fas fa-times"></i></button>
                    </span>
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8">
                    <?= $EN ? 'Drop us a <em class="italic text-gold-muted">line</em>' : 'Tulis <em class="italic text-gold-muted">pesan</em> Anda' ?>
                </h2>

                <?php if ($this->session->flashdata('contact_success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 mb-6 text-sm">
                    <i class="fas fa-check-circle mr-2"></i><?= $EN ? 'Message sent! We will reply shortly.' : 'Pesan terkirim! Kami akan segera membalas.' ?>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('contact_errors')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 mb-6 text-sm">
                    <?= $this->session->flashdata('contact_errors') ?>
                </div>
                <?php endif; ?>

                <form action="<?= base_url('kontak/submit') ?>" method="POST" class="space-y-5" id="contactForm" novalidate>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Name' : 'Nama' ?> *</label>
                            <input type="text" name="name" id="f_name" required value="<?= html_escape($old['name'] ?? '') ?>" class="kt-input" placeholder="<?= $EN ? 'Your name' : 'Nama Anda' ?>" autocomplete="name">
                            <p class="field-hint" id="hint_name"></p>
                        </div>
                        <div>
                            <label class="block editorial-label text-navy mb-2">Email *</label>
                            <input type="email" name="email" id="f_email" required value="<?= html_escape($old['email'] ?? '') ?>" class="kt-input" placeholder="nama@email.com" autocomplete="email">
                            <p class="field-hint" id="hint_email"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Subject' : 'Subjek' ?></label>
                        <input type="text" name="subject" id="f_subject" value="<?= html_escape($old['subject'] ?? '') ?>" class="kt-input" placeholder="<?= $EN ? 'e.g. Admission inquiry' : 'mis. Informasi penerimaan' ?>" list="subjectSuggestions">
                        <datalist id="subjectSuggestions">
                            <option value="<?= $EN ? 'Admission Inquiry' : 'Informasi Penerimaan' ?>">
                            <option value="<?= $EN ? 'Scholarship Information' : 'Informasi Beasiswa' ?>">
                            <option value="<?= $EN ? 'Partnership Proposal' : 'Proposal Kerjasama' ?>">
                            <option value="<?= $EN ? 'Academic Question' : 'Pertanyaan Akademik' ?>">
                            <option value="<?= $EN ? 'General Inquiry' : 'Pertanyaan Umum' ?>">
                        </datalist>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Message' : 'Pesan' ?> *</label>
                        <textarea name="message" id="f_message" required rows="6" maxlength="1000" class="kt-input" placeholder="<?= $EN ? 'Write your message...' : 'Tulis pesan Anda...' ?>"><?= html_escape($old['message'] ?? '') ?></textarea>
                        <!-- 🔥 CHAR COUNTER -->
                        <p class="char-counter" id="charCounter">0 / 1000</p>
                    </div>
                    <button type="submit" class="btn-gold px-10 py-4 font-semibold uppercase tracking-editorial text-xs">
                        <i class="fas fa-paper-plane mr-2"></i><?= $EN ? 'Send Message' : 'Kirim Pesan' ?>
                    </button>
                </form>
            </div>

            <!-- Map + Social -->
            <div class="kt-reveal" style="transition-delay:.15s">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Find Us' : 'Temukan Kami' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8">
                    <?= $EN ? 'Our <em class="italic text-gold-muted">location</em>' : 'Lokasi <em class="italic text-gold-muted">kami</em>' ?>
                </h2>

                <?php if ($map): ?>
                <div class="border border-gray-200 overflow-hidden" style="aspect-ratio:4/3">
                    <iframe src="<?= html_escape($map) ?>" width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen></iframe>
                </div>
                <?php else: ?>
                <div class="border border-gray-200 bg-navy text-ivory flex items-center justify-center" style="aspect-ratio:4/3">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-5xl text-gold/40 mb-4"></i>
                        <p class="text-ivory/60 text-sm"><?= $EN ? 'Map not configured yet.' : 'Peta belum dikonfigurasi.' ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-8">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Follow Us' : 'Ikuti Kami' ?></p>
                    <div class="flex gap-3">
                        <?php
                        $socials = [
                            ['fa-facebook-f', $s->get('facebook_url','')],
                            ['fa-instagram', $s->get('instagram_url','')],
                            ['fa-linkedin-in', $s->get('linkedin_url','')],
                            ['fa-youtube', $s->get('youtube_url','')],
                            ['fa-twitter', $s->get('twitter_url','')],
                            ['fa-tiktok', $s->get('tiktok_url','')],
                        ];
                        foreach ($socials as $soc): if (!empty($soc[1])): ?>
                        <a href="<?= html_escape($soc[1]) ?>" target="_blank" rel="noopener" class="social-btn"><i class="fab <?= $soc[0] ?>"></i></a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 🔥 WHATSAPP FAB -->
<?php if (!empty($wa_num)): ?>
<a href="https://wa.me/<?= html_escape($wa_num) ?>?text=<?= urlencode($EN ? 'Hello, I would like to inquire about...' : 'Halo, saya ingin bertanya tentang...') ?>" target="_blank" rel="noopener" class="wa-fab" title="WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="wa-tooltip"><?= $EN ? 'Chat with us' : 'Chat dengan kami' ?></span>
</a>
<?php endif; ?>

<!-- 🔥 TOAST -->
<div class="kt-toast" id="ktToast"><i class="fas fa-check-circle"></i><span id="ktToastMsg"></span></div>

<script>
(function(){
    // ===== REVEAL =====
    var obs = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.kt-reveal').forEach(function(el){ obs.observe(el); });

    // ===== 🔥 TOAST =====
    function showToast(msg){
        var t = document.getElementById('ktToast');
        document.getElementById('ktToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }
    window.showToast = showToast;

    // ===== 🔥 COPY TO CLIPBOARD =====
    window.copyText = function(text, btn){
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function(){
                btn.classList.add('copied');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                showToast('<?= $EN ? "Copied!" : "Tersalin!" ?>');
                setTimeout(function(){
                    btn.classList.remove('copied');
                    btn.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            });
        }
    };

    // ===== 🔥 FAQ ACCORDION =====
    window.toggleFaq = function(el){
        var item = el.closest('.kt-faq-item');
        var wasOpen = item.classList.contains('open');
        document.querySelectorAll('.kt-faq-item').forEach(function(f){ f.classList.remove('open'); });
        if (!wasOpen) item.classList.add('open');
    };

    // ===== 🔥 CHARACTER COUNTER =====
    var msgField = document.getElementById('f_message');
    var counter = document.getElementById('charCounter');
    if (msgField && counter) {
        function updCounter(){
            var len = msgField.value.length;
            counter.textContent = len + ' / 1000';
            counter.classList.remove('warn', 'danger');
            if (len > 900) counter.classList.add('danger');
            else if (len > 700) counter.classList.add('warn');
        }
        msgField.addEventListener('input', updCounter);
        updCounter();
    }

    // ===== 🔥 CLIENT-SIDE VALIDATION =====
    var nameField = document.getElementById('f_name');
    var emailField = document.getElementById('f_email');
    
    function validateField(field, hintId, validator){
        var hint = document.getElementById(hintId);
        if (!hint) return;
        field.addEventListener('blur', function(){
            var val = field.value.trim();
            if (!val) {
                field.classList.remove('valid', 'invalid');
                hint.textContent = '';
                hint.classList.remove('error', 'success');
                return;
            }
            var result = validator(val);
            if (result === true) {
                field.classList.remove('invalid');
                field.classList.add('valid');
                hint.textContent = '✓ <?= $EN ? "Valid" : "Valid" ?>';
                hint.classList.remove('error');
                hint.classList.add('success');
            } else {
                field.classList.remove('valid');
                field.classList.add('invalid');
                hint.textContent = '✗ ' + result;
                hint.classList.remove('success');
                hint.classList.add('error');
            }
        });
    }
    
    if (nameField) validateField(nameField, 'hint_name', function(v){
        if (v.length < 2) return '<?= $EN ? "Minimum 2 characters" : "Minimal 2 karakter" ?>';
        if (!/^[a-zA-Z\s.'-]+$/.test(v)) return '<?= $EN ? "Only letters allowed" : "Hanya huruf yang diizinkan" ?>';
        return true;
    });
    
    if (emailField) validateField(emailField, 'hint_email', function(v){
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return '<?= $EN ? "Invalid email format" : "Format email tidak valid" ?>';
        return true;
    });

    // ===== 🔥 FORM DRAFT AUTO-SAVE =====
    var form = document.getElementById('contactForm');
    var draftBadge = document.getElementById('draftBadge');
    var draftKey = 'contact_form_draft';
    
    function saveDraft(){
        if (!form) return;
        var data = {
            name: form.name.value,
            email: form.email.value,
            subject: form.subject.value,
            message: form.message.value,
            saved_at: Date.now()
        };
        // Only save if at least one field has content
        if (data.name || data.email || data.subject || data.message) {
            localStorage.setItem(draftKey, JSON.stringify(data));
            draftBadge.classList.add('visible');
        } else {
            localStorage.removeItem(draftKey);
            draftBadge.classList.remove('visible');
        }
    }
    
    function loadDraft(){
        if (!form) return;
        var saved = localStorage.getItem(draftKey);
        if (!saved) return;
        try {
            var data = JSON.parse(saved);
            // Only load if draft is less than 7 days old
            if (Date.now() - data.saved_at > 7 * 24 * 60 * 60 * 1000) {
                localStorage.removeItem(draftKey);
                return;
            }
            if (data.name) form.name.value = data.name;
            if (data.email) form.email.value = data.email;
            if (data.subject) form.subject.value = data.subject;
            if (data.message) form.message.value = data.message;
            draftBadge.classList.add('visible');
            // Trigger counter update
            if (msgField) msgField.dispatchEvent(new Event('input'));
        } catch(e){}
    }
    
    window.clearDraft = function(){
        localStorage.removeItem(draftKey);
        draftBadge.classList.remove('visible');
        if (form) form.reset();
        showToast('<?= $EN ? "Draft cleared" : "Draft dihapus" ?>');
    };
    
    if (form) {
        form.addEventListener('input', function(){
            clearTimeout(window._draftTimer);
            window._draftTimer = setTimeout(saveDraft, 800);
        });
        form.addEventListener('submit', function(){
            localStorage.removeItem(draftKey);
        });
        // Load draft on page load (hanya jika tidak ada flashdata old)
        <?php if (empty($old)): ?>
        loadDraft();
        <?php endif; ?>
    }
})();
</script>