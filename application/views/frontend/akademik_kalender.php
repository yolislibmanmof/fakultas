<?php
$EN = (get_site_lang() == 'en');
$today = date('Y-m-d');
$next_event = NULL;
$days_until_next = 0;

// Cari next event dengan proper date comparison
foreach ($events as $e) {
    if (strtotime($e->start_date) >= strtotime($today)) {
        $next_event = $e;
        $days_until_next = ceil((strtotime($e->start_date) - strtotime($today)) / 86400);
        break;
    }
}

// Group events by month untuk calendar view
$events_by_month = [];
foreach ($events as $e) {
    $month_key = date('Y-m', strtotime($e->start_date));
    if (!isset($events_by_month[$month_key])) $events_by_month[$month_key] = [];
    $events_by_month[$month_key][] = $e;
}
?>

<style>
/* ===== 🔥 COUNTDOWN ===== */
.countdown-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 400px; }
.countdown-unit { text-align: center; }
.countdown-num { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; line-height: 1; color: #C9A227; }
.countdown-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(247,245,240,0.6); margin-top: 4px; }

/* ===== 🔥 FILTER CHIPS ===== */
.cat-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border: 1px solid #e5e7eb; background: #fff;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    cursor: pointer; transition: all .2s;
}
.cat-chip:hover { border-color: #C9A227; }
.cat-chip.active { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }
.cat-chip .chip-dot { width: 8px; height: 8px; border-radius: 50%; }

/* ===== 🔥 PAST EVENTS TOGGLE ===== */
.past-toggle {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 16px; background: #f9fafb; border: 1px solid #e5e7eb;
    font-size: 12px; color: #64748b; cursor: pointer; transition: all .2s;
}
.past-toggle:hover { border-color: #C9A227; }
.past-toggle input { accent-color: #C9A227; }

/* ===== 🔥 VIEW TOGGLE ===== */
.view-toggle { display: flex; gap: 4px; }
.view-btn {
    padding: 8px 14px; background: #fff; border: 1px solid #e5e7eb;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    cursor: pointer; transition: all .2s;
}
.view-btn.active { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }
.view-btn:hover:not(.active) { border-color: #C9A227; }

/* ===== 🔥 CALENDAR GRID VIEW ===== */
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; background: #e5e7eb; }
.cal-header { background: #0B2239; color: #F7F5F0; padding: 8px; text-align: center; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; }
.cal-day { background: #fff; min-height: 80px; padding: 8px; position: relative; transition: all .2s; }
.cal-day:hover { background: #F7F5F0; }
.cal-day.empty { background: #f9fafb; }
.cal-day .day-num { font-family: 'Fraunces', serif; font-size: 1.25rem; color: #0B2239; }
.cal-day.today { background: #fef3c7; }
.cal-day.today .day-num { color: #C9A227; }
.cal-event {
    font-size: 9px; padding: 2px 4px; margin-top: 4px; border-radius: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ===== 🔥 SEARCH ===== */
.cal-search {
    width: 100%; max-width: 300px; padding: 10px 14px 10px 40px;
    border: 2px solid #e5e7eb; background: #fff; font-size: 13px;
    transition: border-color .2s;
}
.cal-search:focus { outline: none; border-color: #C9A227; }

/* ===== 🔥 HIDDEN PAST ===== */
.past-event { display: none; }
.show-past .past-event { display: flex; }

/* ===== 🔥 TIMELINE ITEM ===== */
.timeline-item { transition: opacity .3s; }
.timeline-item.hidden { display: none; }
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="absolute -bottom-16 -left-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">KA</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="max-w-3xl">
                <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Academic Calendar' : 'Kalender Akademik' ?></p>
                <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                    <?= $EN ? 'Academic <em class="italic text-gold-muted">Calendar</em>' : 'Kalender <em class="italic text-gold-muted">Akademik</em>' ?>
                </h1>
                <p class="text-slate mt-6 text-lg leading-relaxed">
                    <?= $EN ? 'The complete map of academic activities, examinations, and holidays for the current academic year.' : 'Peta lengkap kegiatan akademik, ujian, dan libur untuk tahun ajaran berjalan.' ?>
                </p>
            </div>
            <div class="hidden md:block text-right">
                <div class="font-serif text-8xl font-light text-navy/10 leading-none"><?= count($events) ?></div>
                <p class="editorial-label text-slate mt-2"><?= $EN ? 'Scheduled Events' : 'Jadwal' ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ CALENDAR ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-5xl">

        <?php if (empty($events)): ?>
            <div class="text-center py-24 bg-white border border-gray-200">
                <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No academic schedule published yet.' : 'Belum ada jadwal akademik yang dipublikasikan.' ?></p>
            </div>
        <?php else: ?>

        <!-- 🔥 Next Event Spotlight dengan Countdown -->
        <?php if ($next_event):
            $ne_start = strtotime($next_event->start_date);
        ?>
        <div class="bg-navy text-ivory p-8 md:p-10 relative overflow-hidden mb-14 rv">
            <div class="absolute inset-0 hero-pattern"></div>
            <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold/40"></div>
            <div class="absolute bottom-0 right-0 w-20 h-20 border-b-2 border-r-2 border-gold/40"></div>
            <div class="relative flex flex-col md:flex-row md:items-center gap-8">
                <div class="bg-gold text-navy text-center px-6 py-5 flex-shrink-0">
                    <div class="font-serif text-4xl md:text-5xl font-light leading-none"><?= date('d', $ne_start) ?></div>
                    <div class="text-[10px] uppercase tracking-editorial font-bold mt-1"><?= date('M Y', $ne_start) ?></div>
                </div>
                <div class="flex-1">
                    <p class="editorial-label text-gold mb-3"><i class="fas fa-bolt mr-1"></i><?= $EN ? 'Upcoming Event' : 'Agenda Terdekat' ?></p>
                    <h2 class="font-serif text-2xl md:text-3xl font-light tracking-[-0.01em] leading-tight"><?= html_escape($next_event->event_name) ?></h2>
                    
                    <!-- 🔥 LIVE COUNTDOWN -->
                    <div class="mt-6 countdown-grid" id="countdownGrid" data-target="<?= date('Y-m-d', $ne_start) ?>">
                        <div class="countdown-unit">
                            <div class="countdown-num" id="cd-days">--</div>
                            <div class="countdown-label"><?= $EN ? 'Days' : 'Hari' ?></div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-num" id="cd-hours">--</div>
                            <div class="countdown-label"><?= $EN ? 'Hours' : 'Jam' ?></div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-num" id="cd-mins">--</div>
                            <div class="countdown-label"><?= $EN ? 'Minutes' : 'Menit' ?></div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-num" id="cd-secs">--</div>
                            <div class="countdown-label"><?= $EN ? 'Seconds' : 'Detik' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 🔥 Filter + Search + View Toggle -->
        <div class="flex flex-wrap items-center gap-4 mb-8 pb-6 border-b border-gray-200">
            <span class="editorial-label text-slate"><?= $EN ? 'Filter' : 'Filter' ?>:</span>
            <button class="cat-chip active" data-cat="all">
                <span class="chip-dot bg-gray-400"></span>
                <?= $EN ? 'All' : 'Semua' ?>
            </button>
            <button class="cat-chip" data-cat="pmb">
                <span class="chip-dot bg-green-500"></span>
                PMB
            </button>
            <button class="cat-chip" data-cat="akademik">
                <span class="chip-dot bg-blue-500"></span>
                <?= $EN ? 'Academic' : 'Akademik' ?>
            </button>
            <button class="cat-chip" data-cat="ujian">
                <span class="chip-dot bg-yellow-500"></span>
                <?= $EN ? 'Exams' : 'Ujian' ?>
            </button>
            <button class="cat-chip" data-cat="libur">
                <span class="chip-dot bg-gray-500"></span>
                <?= $EN ? 'Holiday' : 'Libur' ?>
            </button>
            
            <div class="flex-1"></div>
            
            <!-- 🔥 Search -->
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate/50"></i>
                <input type="text" class="cal-search" id="calSearch" placeholder="<?= $EN ? 'Search...' : 'Cari...' ?>">
            </div>
            
            <!-- 🔥 View Toggle -->
            <div class="view-toggle">
                <button class="view-btn active" data-view="timeline">
                    <i class="fas fa-list"></i>
                </button>
                <button class="view-btn" data-view="calendar">
                    <i class="fas fa-calendar"></i>
                </button>
            </div>
            
            <!-- 🔥 Export iCal -->
            <a href="<?= base_url('akademik/calendar/export') ?>" class="export-btn" title="Export to Google Calendar / Outlook">
                <i class="fas fa-download"></i>
                iCal
            </a>
        </div>

        <!-- 🔥 Past Events Toggle -->
        <label class="past-toggle mb-6 inline-flex">
            <input type="checkbox" id="showPast">
            <span><?= $EN ? 'Show past events' : 'Tampilkan agenda yang sudah lewat' ?></span>
        </label>

        <!-- ===== TIMELINE VIEW ===== -->
        <div id="timelineView" class="space-y-0">
            <?php foreach ($events as $i => $e):
                $badge_config = [
                    'pmb'      => ['label' => 'PMB', 'class' => 'bg-green-500 text-ivory', 'dot' => 'bg-green-500'],
                    'akademik' => ['label' => $EN ? 'Academic' : 'Akademik', 'class' => 'bg-blue-500 text-ivory', 'dot' => 'bg-blue-500'],
                    'ujian'    => ['label' => $EN ? 'Exams' : 'Ujian', 'class' => 'bg-yellow-500 text-navy', 'dot' => 'bg-yellow-500'],
                    'libur'    => ['label' => $EN ? 'Holiday' : 'Libur', 'class' => 'bg-gray-500 text-ivory', 'dot' => 'bg-gray-500'],
                ];
                $badge = $badge_config[$e->category] ?? $badge_config['akademik'];
                $start_ts = strtotime($e->start_date);
                $end_ts = strtotime($e->end_date ?? $e->start_date);
                $same_day = (date('Y-m-d', $start_ts) === date('Y-m-d', $end_ts));
                $is_last = ($i === count($events) - 1);
                $is_past = $end_ts < strtotime($today);
            ?>
            <div class="timeline-item flex gap-6 md:gap-10 rv group <?= $is_past ? 'past-event' : '' ?>"
                 data-cat="<?= html_escape($e->category) ?>"
                 data-name="<?= html_escape(strtolower($e->event_name)) ?>"
                 data-date="<?= date('Y-m-d', $start_ts) ?>">
                <!-- Spine + Medallion -->
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-20 md:w-24 bg-navy text-ivory text-center px-3 py-4 relative z-10">
                        <div class="font-serif text-2xl md:text-3xl font-light leading-none"><?= date('d', $start_ts) ?></div>
                        <div class="text-[9px] uppercase tracking-editorial text-gold mt-1"><?= date('M Y', $start_ts) ?></div>
                    </div>
                    <?php if (!$is_last): ?>
                    <div class="w-px flex-1 bg-gray-200 relative">
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full <?= $badge['dot'] ?> -mt-1"></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 pb-12 <?= $is_last ? 'pb-0' : '' ?>">
                    <div class="bg-white border border-gray-200 p-6 md:p-8 hover:border-gold/40 hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            <span class="<?= $badge['class'] ?> text-[10px] uppercase tracking-editorial font-bold px-3 py-1"><?= $badge['label'] ?></span>
                            <span class="w-8 h-px bg-gold"></span>
                            <span class="text-xs font-mono text-slate">
                                <?= date('d M Y', $start_ts) ?><?= !$same_day ? ' → ' . date('d M Y', $end_ts) : '' ?>
                            </span>
                            <?php if (!$same_day):
                                $duration = ceil(($end_ts - $start_ts) / 86400) + 1;
                            ?>
                            <span class="ml-auto text-right">
                                <span class="font-serif text-2xl font-light text-gold-muted leading-none"><?= $duration ?></span>
                                <span class="text-[9px] uppercase tracking-editorial text-slate ml-1"><?= $EN ? 'Days' : 'Hari' ?></span>
                            </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-serif text-xl md:text-2xl font-light text-navy leading-snug tracking-[-0.01em] group-hover:text-gold-muted transition">
                            <?= html_escape($e->event_name) ?>
                        </h3>
                        <?php if ($e->description): ?>
                            <p class="text-sm text-slate mt-3 leading-relaxed"><?= html_escape($e->description) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ===== 🔥 CALENDAR GRID VIEW ===== -->
        <div id="calendarView" class="hidden">
            <?php foreach ($events_by_month as $month_key => $month_events): 
                $month_ts = strtotime($month_key . '-01');
                $days_in_month = date('t', $month_ts);
                $first_day = date('w', $month_ts); // 0 = Sunday
            ?>
            <div class="mb-8">
                <h3 class="font-serif text-2xl font-light text-navy mb-4">
                    <?= date('F Y', $month_ts) ?>
                </h3>
                <div class="cal-grid">
                    <!-- Headers -->
                    <div class="cal-header"><?= $EN ? 'Sun' : 'Min' ?></div>
                    <div class="cal-header"><?= $EN ? 'Mon' : 'Sen' ?></div>
                    <div class="cal-header"><?= $EN ? 'Tue' : 'Sel' ?></div>
                    <div class="cal-header"><?= $EN ? 'Wed' : 'Rab' ?></div>
                    <div class="cal-header"><?= $EN ? 'Thu' : 'Kam' ?></div>
                    <div class="cal-header"><?= $EN ? 'Fri' : 'Jum' ?></div>
                    <div class="cal-header"><?= $EN ? 'Sat' : 'Sab' ?></div>
                    
                    <!-- Empty cells before first day -->
                    <?php for ($i = 0; $i < $first_day; $i++): ?>
                    <div class="cal-day empty"></div>
                    <?php endfor; ?>
                    
                    <!-- Days -->
                    <?php for ($d = 1; $d <= $days_in_month; $d++):
                        $date_str = $month_key . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                        $is_today = $date_str === $today;
                        $day_events = array_filter($month_events, function($e) use ($date_str) {
                            return $e->start_date <= $date_str && ($e->end_date ?? $e->start_date) >= $date_str;
                        });
                    ?>
                    <div class="cal-day <?= $is_today ? 'today' : '' ?>">
                        <div class="day-num"><?= $d ?></div>
                        <?php foreach (array_slice($day_events, 0, 2) as $de):
                            $bg = ['pmb'=>'bg-green-100 text-green-800','akademik'=>'bg-blue-100 text-blue-800','ujian'=>'bg-yellow-100 text-yellow-800','libur'=>'bg-gray-100 text-gray-800'];
                        ?>
                        <div class="cal-event <?= $bg[$de->category] ?? 'bg-gray-100 text-gray-800' ?>">
                            <?= html_escape(character_limiter($de->event_name, 20)) ?>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($day_events) > 2): ?>
                        <div class="text-[9px] text-slate mt-1">+<?= count($day_events) - 2 ?> more</div>
                        <?php endif; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer Note -->
        <div class="mt-12 p-6 border-l-4 border-gold bg-ivory-warm/50">
            <p class="text-xs text-slate leading-relaxed">
                <i class="fas fa-info-circle text-gold-muted mr-2"></i>
                <?= $EN ? 'The schedule may change according to faculty policy. Monitor the' : 'Jadwal dapat berubah sesuai kebijakan fakultas. Pantau halaman' ?>
                <a href="<?= base_url('berita') ?>" class="text-navy font-semibold underline hover:text-gold-muted transition"><?= $EN ? 'News' : 'Berita' ?></a>
                <?= $EN ? 'page for official announcements.' : 'untuk pengumuman resmi.' ?>
            </p>
        </div>

        <?php endif; ?>

    </div>
</section>

<script>
(function(){
    // ===== 🔥 COUNTDOWN TIMER =====
    var countdownGrid = document.getElementById('countdownGrid');
    if (countdownGrid) {
        var targetDate = new Date(countdownGrid.dataset.target + 'T00:00:00');
        
        function updateCountdown() {
            var now = new Date();
            var diff = targetDate - now;
            
            if (diff <= 0) {
                document.getElementById('cd-days').textContent = '0';
                document.getElementById('cd-hours').textContent = '0';
                document.getElementById('cd-mins').textContent = '0';
                document.getElementById('cd-secs').textContent = '0';
                return;
            }
            
            var days = Math.floor(diff / 86400000);
            var hours = Math.floor((diff % 86400000) / 3600000);
            var mins = Math.floor((diff % 3600000) / 60000);
            var secs = Math.floor((diff % 60000) / 1000);
            
            document.getElementById('cd-days').textContent = days;
            document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('cd-mins').textContent = String(mins).padStart(2, '0');
            document.getElementById('cd-secs').textContent = String(secs).padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // ===== 🔥 FILTER CHIPS =====
    var catChips = document.querySelectorAll('.cat-chip');
    var timelineItems = document.querySelectorAll('.timeline-item');
    var searchInput = document.getElementById('calSearch');
    var currentCat = 'all';
    var showPast = document.getElementById('showPast');

    function applyFilters() {
        var query = (searchInput ? searchInput.value : '').toLowerCase();
        var pastVisible = showPast ? showPast.checked : false;
        
        timelineItems.forEach(function(item) {
            var matchCat = currentCat === 'all' || item.dataset.cat === currentCat;
            var matchSearch = !query || item.dataset.name.indexOf(query) > -1;
            var isPast = item.classList.contains('past-event');
            var show = matchCat && matchSearch && (!isPast || pastVisible);
            
            item.style.display = show ? '' : 'none';
        });
    }

    catChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            catChips.forEach(function(c) { c.classList.remove('active'); });
            chip.classList.add('active');
            currentCat = chip.dataset.cat;
            applyFilters();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (showPast) showPast.addEventListener('change', function() {
        document.getElementById('timelineView').classList.toggle('show-past', this.checked);
        applyFilters();
    });

    // ===== 🔥 VIEW TOGGLE =====
    var viewBtns = document.querySelectorAll('.view-btn');
    var timelineView = document.getElementById('timelineView');
    var calendarView = document.getElementById('calendarView');

    viewBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            viewBtns.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            
            if (btn.dataset.view === 'timeline') {
                timelineView.classList.remove('hidden');
                calendarView.classList.add('hidden');
            } else {
                timelineView.classList.add('hidden');
                calendarView.classList.remove('hidden');
            }
        });
    });

    // ===== REVEAL ANIMATION =====
    var io = new IntersectionObserver(function(es) {
        es.forEach(function(en) {
            if (!en.isIntersecting) return;
            en.target.classList.add('rv-in');
            io.unobserve(en.target);
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.rv').forEach(function(el) { io.observe(el); });
})();
</script>