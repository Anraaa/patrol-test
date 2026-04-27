<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => ['@refreshComponent' => '$refresh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@refresh-component' => '$refresh']); ?>
    <?php
        $data = $this->getData();

        // ── Bangun calendarData ──────────────────────────────────────────────
        $calendarData = [];
        $picColors    = [];
        $colorPalette = ['sky', 'emerald', 'violet', 'amber', 'rose', 'teal', 'indigo', 'orange'];
        $colorIdx     = 0;
        $usersWithData = [];

        foreach ($data['table_data'] as $row) {
            $userName = $row['user_name'];

            for ($day = 1; $day <= $data['days_in_month']; $day++) {
                $dayInfo = $row['daily_data'][$day] ?? null;
                if (!$dayInfo) continue;

                if (!in_array($userName, $usersWithData)) {
                    $usersWithData[] = $userName;
                }

                if (!isset($picColors[$userName])) {
                    $picColors[$userName] = $colorIdx++ % count($colorPalette);
                }

                if (!isset($calendarData[$day][$userName])) {
                    $calendarData[$day][$userName] = [
                        'patrol_count'   => 0,
                        'missed_count'   => 0,
                        'total_assigned' => 0,
                        'color_index'    => $picColors[$userName],
                    ];
                }

                foreach ($data['shifts'] as $shift) {
                    $status = $dayInfo['shifts_status'][$shift->id] ?? -1;
                    if ($status === 1) {
                        $calendarData[$day][$userName]['patrol_count']++;
                        $calendarData[$day][$userName]['total_assigned']++;
                    } elseif ($status === 0) {
                        $calendarData[$day][$userName]['missed_count']++;
                        $calendarData[$day][$userName]['total_assigned']++;
                    }
                }
            }
        }

        $totalPatrolMonth  = 0;
        $totalMissedMonth  = 0;
        $totalDaysActive   = 0;
        foreach ($calendarData as $day => $pics) {
            $hasAny = false;
            foreach ($pics as $picData) {
                $totalPatrolMonth += $picData['patrol_count'];
                $totalMissedMonth += $picData['missed_count'];
                if ($picData['patrol_count'] > 0) $hasAny = true;
            }
            if ($hasAny) $totalDaysActive++;
        }
        $totalAssigned = $totalPatrolMonth + $totalMissedMonth;
        $ratePercent   = $totalAssigned > 0 ? round(($totalPatrolMonth / $totalAssigned) * 100) : 0;

        $firstDayOfMonth = \Carbon\Carbon::create($data['year'], $data['month'], 1);
        $startBlank      = $firstDayOfMonth->dayOfWeek;
        $dayNames        = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        $badgeBg = [
            'sky'     => ['bg' => 'bg-sky-100 dark:bg-sky-900/50',       'text' => 'text-sky-700 dark:text-sky-300',       'dot' => 'bg-sky-500',     'border' => 'border-sky-300 dark:border-sky-700', 'gradient' => 'from-sky-400 to-sky-600',     'header' => 'from-sky-400 via-sky-500 to-cyan-500'],
            'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/50','text' => 'text-emerald-700 dark:text-emerald-300','dot' => 'bg-emerald-500', 'border' => 'border-emerald-300 dark:border-emerald-700', 'gradient' => 'from-emerald-400 to-emerald-600', 'header' => 'from-emerald-400 via-emerald-500 to-teal-500'],
            'violet'  => ['bg' => 'bg-violet-100 dark:bg-violet-900/50', 'text' => 'text-violet-700 dark:text-violet-300',  'dot' => 'bg-violet-500',  'border' => 'border-violet-300 dark:border-violet-700', 'gradient' => 'from-violet-400 to-violet-600',  'header' => 'from-violet-400 via-violet-500 to-purple-600'],
            'amber'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/50',   'text' => 'text-amber-700 dark:text-amber-300',   'dot' => 'bg-amber-500',   'border' => 'border-amber-300 dark:border-amber-700', 'gradient' => 'from-amber-400 to-amber-600',   'header' => 'from-amber-400 via-amber-500 to-orange-500'],
            'rose'    => ['bg' => 'bg-rose-100 dark:bg-rose-900/50',     'text' => 'text-rose-700 dark:text-rose-300',     'dot' => 'bg-rose-500',    'border' => 'border-rose-300 dark:border-rose-700', 'gradient' => 'from-rose-400 to-rose-600',     'header' => 'from-rose-400 via-rose-500 to-pink-600'],
            'teal'    => ['bg' => 'bg-teal-100 dark:bg-teal-900/50',     'text' => 'text-teal-700 dark:text-teal-300',     'dot' => 'bg-teal-500',    'border' => 'border-teal-300 dark:border-teal-700', 'gradient' => 'from-teal-400 to-teal-600',     'header' => 'from-teal-400 via-teal-500 to-cyan-600'],
            'indigo'  => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/50', 'text' => 'text-indigo-700 dark:text-indigo-300', 'dot' => 'bg-indigo-500',  'border' => 'border-indigo-300 dark:border-indigo-700', 'gradient' => 'from-indigo-400 to-indigo-600', 'header' => 'from-indigo-400 via-indigo-500 to-blue-600'],
            'orange'  => ['bg' => 'bg-orange-100 dark:bg-orange-900/50', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500',  'border' => 'border-orange-300 dark:border-orange-700', 'gradient' => 'from-orange-400 to-orange-600', 'header' => 'from-orange-400 via-orange-500 to-red-500'],
        ];
        $badgeKeys = array_keys($badgeBg);

        $avatarSolid = [
            'sky'     => 'bg-gradient-to-br from-sky-400 to-sky-600',
            'emerald' => 'bg-gradient-to-br from-emerald-400 to-emerald-600',
            'violet'  => 'bg-gradient-to-br from-violet-400 to-violet-600',
            'amber'   => 'bg-gradient-to-br from-amber-400 to-amber-600',
            'rose'    => 'bg-gradient-to-br from-rose-400 to-rose-600',
            'teal'    => 'bg-gradient-to-br from-teal-400 to-teal-600',
            'indigo'  => 'bg-gradient-to-br from-indigo-400 to-indigo-600',
            'orange'  => 'bg-gradient-to-br from-orange-400 to-orange-600',
        ];

        $monthNameId = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
    ?>

    <style>
        /* ===== ANIMATIONS ===== */
        @keyframes rainbowShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes floatBubble {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            33%      { transform: translateY(-12px) rotate(5deg); opacity: 0.9; }
            66%      { transform: translateY(-6px) rotate(-3deg); opacity: 0.75; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(24px); }
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.92); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes softPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.85; transform: scale(1.04); }
        }

        /* ===== HERO HEADER ===== */
        .hero-header {
            background: linear-gradient(135deg,
                #667eea 0%, #764ba2 20%, #f093fb 40%,
                #4facfe 60%, #00f2fe 80%, #43e97b 100%);
            background-size: 300% 300%;
            animation: rainbowShift 8s ease infinite;
            position: relative;
            overflow: hidden;
        }
        .hero-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.15);
        }
        /* Dark mode hero - slightly more saturated overlay for depth */
        .dark .hero-header::before {
            background: rgba(0,0,0,0.30);
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            animation: floatBubble ease-in-out infinite;
        }
        .dark .bubble {
            background: rgba(255,255,255,0.08);
        }

        /* ===== STAT CARDS ===== */
        .stat-card-colorful {
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: none !important;
        }
        .stat-card-colorful::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -40%;
            width: 80%;
            height: 80%;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            pointer-events: none;
        }
        .dark .stat-card-colorful::after {
            background: rgba(255,255,255,0.06);
        }
        .stat-card-colorful:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 24px 40px -12px rgba(0,0,0,0.35);
        }
        .stat-card-colorful .stat-icon {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.3);
            transition: transform 0.3s ease;
        }
        .dark .stat-card-colorful .stat-icon {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .stat-card-colorful:hover .stat-icon {
            transform: scale(1.12) rotate(5deg);
        }

        /* ===== PAGE BACKGROUND ===== */
        .page-bg {
            background: linear-gradient(135deg, #f8faff 0%, #faf5ff 50%, #fff1f8 100%);
        }
        .dark .page-bg {
            background: linear-gradient(135deg, #0f1117 0%, #111827 50%, #0d1117 100%);
        }

        /* ===== SECTION CARDS ===== */
        .section-card {
            background: linear-gradient(135deg, #f8faff 0%, #faf5ff 50%, #fff1f8 100%);
            border: 1px solid rgba(99,102,241,0.15);
        }
        .dark .section-card {
            background: linear-gradient(135deg, #1e1b2e 0%, #1a1625 50%, #1c1521 100%);
            border: 1px solid rgba(99,102,241,0.2);
        }

        /* ===== SECTION HEADER ===== */
        .section-header-gradient {
            background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(168,85,247,0.06) 50%, rgba(236,72,153,0.04) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.12);
        }
        .dark .section-header-gradient {
            background: linear-gradient(135deg, rgba(99,102,241,0.18) 0%, rgba(168,85,247,0.14) 50%, rgba(236,72,153,0.1) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.25);
        }

        /* ===== TABLE ===== */
        .table-bg {
            background: white;
        }
        .dark .table-bg {
            background: #141624;
        }
        .table-header-row {
            background: linear-gradient(90deg, #eef2ff 0%, #f5f3ff 50%, #fdf2f8 100%);
        }
        .dark .table-header-row {
            background: linear-gradient(90deg, #1e1b3a 0%, #1d1830 50%, #1e1523 100%);
        }
        .table-footer-row {
            background: linear-gradient(90deg, #eef2ff 0%, #f5f3ff 50%, #fdf2f8 100%);
        }
        .dark .table-footer-row {
            background: linear-gradient(90deg, #1e1b3a 0%, #1d1830 50%, #1e1523 100%);
        }
        .table-row-hover:hover td {
            background: rgba(99,102,241,0.04) !important;
        }
        .dark .table-row-hover:hover td {
            background: rgba(99,102,241,0.10) !important;
        }
        .table-divider {
            border-color: rgba(99,102,241,0.08);
        }
        .dark .table-divider {
            border-color: rgba(99,102,241,0.15);
        }

        /* ===== CALENDAR ===== */
        .calendar-card {
            background: white;
            border: 1px solid rgba(99,102,241,0.15);
        }
        .dark .calendar-card {
            background: #141624;
            border: 1px solid rgba(99,102,241,0.2);
        }

        .calendar-header-bg {
            background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 50%, #fdf2f8 100%);
            border-bottom: 1px solid rgba(99,102,241,0.12);
        }
        .dark .calendar-header-bg {
            background: linear-gradient(135deg, rgba(49,46,129,0.35) 0%, rgba(76,29,149,0.28) 50%, rgba(131,24,67,0.2) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.25);
        }

        .cal-cell {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            background: white;
            border-bottom: 1px solid rgba(99,102,241,0.07);
            border-right: 1px solid rgba(99,102,241,0.07);
        }
        .dark .cal-cell {
            background: #141624;
            border-bottom: 1px solid rgba(99,102,241,0.12);
            border-right: 1px solid rgba(99,102,241,0.12);
        }
        .cal-cell:hover {
            background: rgb(239 246 255 / 0.98) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -6px rgba(99, 102, 241, 0.25);
            z-index: 10;
            position: relative;
        }
        .dark .cal-cell:hover {
            background: rgba(30, 27, 75, 0.6) !important;
            box-shadow: 0 8px 20px -6px rgba(99, 102, 241, 0.35);
        }
        .cal-cell.selected {
            box-shadow: inset 0 0 0 2px #818cf8, 0 6px 20px -6px rgba(99, 102, 241, 0.4);
            background: rgb(238 242 255 / 0.98) !important;
            transform: scale(0.99);
            z-index: 10;
        }
        .dark .cal-cell.selected {
            background: rgba(30, 27, 75, 0.5) !important;
            box-shadow: inset 0 0 0 2px #818cf8, 0 6px 20px -6px rgba(129, 140, 248, 0.5);
        }

        /* Weekend cells */
        .weekend-cell {
            background: linear-gradient(135deg, rgba(253,242,248,0.85) 0%, rgba(245,243,255,0.7) 100%) !important;
        }
        .dark .weekend-cell {
            background: linear-gradient(135deg, rgba(131,24,67,0.12) 0%, rgba(76,29,149,0.1) 100%) !important;
        }

        /* Blank cells */
        .blank-cell {
            background: linear-gradient(135deg, rgba(249,250,251,0.7) 0%, rgba(243,244,246,0.4) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.07);
            border-right: 1px solid rgba(99,102,241,0.07);
        }
        .dark .blank-cell {
            background: linear-gradient(135deg, rgba(15,17,28,0.8) 0%, rgba(17,19,31,0.6) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.12);
            border-right: 1px solid rgba(99,102,241,0.12);
        }

        /* Day header grid */
        .day-header-cell {
            border-bottom: 1px solid rgba(99,102,241,0.1);
        }
        .dark .day-header-cell {
            border-bottom: 1px solid rgba(99,102,241,0.2);
        }

        /* ===== CALENDAR HEADER LEGEND CHIPS ===== */
        .legend-chip {
            background: rgba(255,255,255,0.9);
            border: 1px solid;
            backdrop-filter: blur(4px);
        }
        .dark .legend-chip {
            background: rgba(20,22,36,0.85);
        }

        /* ===== LOCATION CARDS ===== */
        .loc-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .loc-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .loc-card:hover::before { opacity: 1; }
        .loc-card:hover {
            transform: translateY(-4px) scale(1.005);
            box-shadow: 0 20px 40px -12px rgba(99,102,241,0.3);
        }
        .dark .loc-card:hover {
            box-shadow: 0 20px 40px -12px rgba(99,102,241,0.4);
        }

        /* Location card backgrounds */
        .loc-bg-0  { background: linear-gradient(135deg, #eff6ff 0%, #ecfeff 100%); border: 1px solid #bfdbfe; }
        .loc-bg-1  { background: linear-gradient(135deg, #f5f3ff 0%, #faf5ff 100%); border: 1px solid #ddd6fe; }
        .loc-bg-2  { background: linear-gradient(135deg, #ecfdf5 0%, #f0fdfa 100%); border: 1px solid #a7f3d0; }
        .loc-bg-3  { background: linear-gradient(135deg, #fff1f2 0%, #fdf2f8 100%); border: 1px solid #fecdd3; }
        .loc-bg-4  { background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 100%); border: 1px solid #fde68a; }
        .loc-bg-5  { background: linear-gradient(135deg, #eef2ff 0%, #eff6ff 100%); border: 1px solid #c7d2fe; }
        .loc-bg-6  { background: linear-gradient(135deg, #f0fdfa 0%, #ecfdf5 100%); border: 1px solid #99f6e4; }
        .loc-bg-7  { background: linear-gradient(135deg, #fdf4ff 0%, #f5f3ff 100%); border: 1px solid #e9d5ff; }
        .loc-bg-8  { background: linear-gradient(135deg, #f0f9ff 0%, #eef2ff 100%); border: 1px solid #bae6fd; }
        .loc-bg-9  { background: linear-gradient(135deg, #fff7ed 0%, #fff1f2 100%); border: 1px solid #fed7aa; }
        .loc-bg-10 { background: linear-gradient(135deg, #ecfeff 0%, #f0f9ff 100%); border: 1px solid #a5f3fc; }
        .loc-bg-11 { background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 100%); border: 1px solid #fbcfe8; }

        .dark .loc-bg-0  { background: linear-gradient(135deg, rgba(30,58,138,0.25) 0%, rgba(22,78,99,0.2) 100%); border: 1px solid rgba(59,130,246,0.25); }
        .dark .loc-bg-1  { background: linear-gradient(135deg, rgba(76,29,149,0.25) 0%, rgba(88,28,135,0.2) 100%); border: 1px solid rgba(139,92,246,0.25); }
        .dark .loc-bg-2  { background: linear-gradient(135deg, rgba(6,78,59,0.25) 0%, rgba(19,78,74,0.2) 100%); border: 1px solid rgba(16,185,129,0.25); }
        .dark .loc-bg-3  { background: linear-gradient(135deg, rgba(136,19,55,0.25) 0%, rgba(131,24,67,0.2) 100%); border: 1px solid rgba(244,63,94,0.25); }
        .dark .loc-bg-4  { background: linear-gradient(135deg, rgba(120,53,15,0.25) 0%, rgba(124,45,18,0.2) 100%); border: 1px solid rgba(245,158,11,0.25); }
        .dark .loc-bg-5  { background: linear-gradient(135deg, rgba(30,27,75,0.25) 0%, rgba(30,58,138,0.2) 100%); border: 1px solid rgba(99,102,241,0.25); }
        .dark .loc-bg-6  { background: linear-gradient(135deg, rgba(19,78,74,0.25) 0%, rgba(6,78,59,0.2) 100%); border: 1px solid rgba(20,184,166,0.25); }
        .dark .loc-bg-7  { background: linear-gradient(135deg, rgba(88,28,135,0.25) 0%, rgba(76,29,149,0.2) 100%); border: 1px solid rgba(168,85,247,0.25); }
        .dark .loc-bg-8  { background: linear-gradient(135deg, rgba(12,74,110,0.25) 0%, rgba(30,27,75,0.2) 100%); border: 1px solid rgba(14,165,233,0.25); }
        .dark .loc-bg-9  { background: linear-gradient(135deg, rgba(124,45,18,0.25) 0%, rgba(136,19,55,0.2) 100%); border: 1px solid rgba(249,115,22,0.25); }
        .dark .loc-bg-10 { background: linear-gradient(135deg, rgba(22,78,99,0.25) 0%, rgba(12,74,110,0.2) 100%); border: 1px solid rgba(6,182,212,0.25); }
        .dark .loc-bg-11 { background: linear-gradient(135deg, rgba(131,24,67,0.25) 0%, rgba(136,19,55,0.2) 100%); border: 1px solid rgba(236,72,153,0.25); }

        /* Loc stat inner cards */
        .loc-stat-inner {
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.6);
        }
        .dark .loc-stat-inner {
            background: rgba(20,22,36,0.7);
            border: 1px solid rgba(99,102,241,0.15);
        }

        /* ===== PIC LEGEND CARD ===== */
        .pic-legend-card {
            background: linear-gradient(135deg, #f8faff 0%, #faf5ff 50%, #fff1f8 100%);
            border: 1px solid rgba(99,102,241,0.15);
        }
        .dark .pic-legend-card {
            background: linear-gradient(135deg, #1e1b2e 0%, #1a1625 50%, #1c1521 100%);
            border: 1px solid rgba(99,102,241,0.2);
        }

        /* ===== DETAIL PANEL ===== */
        .detail-panel {
            border: 1px solid rgba(99,102,241,0.25);
        }
        .dark .detail-panel {
            border: 1px solid rgba(99,102,241,0.35);
        }
        .detail-body-bg {
            background: linear-gradient(135deg, #f8faff 0%, #faf5ff 50%, #fff1f8 100%);
        }
        .dark .detail-body-bg {
            background: linear-gradient(135deg, #1e1b2e 0%, #1a1625 50%, #1c1521 100%);
        }

        /* Detail chip */
        .detail-chip {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .dark .detail-chip {
            background: linear-gradient(135deg, #1e1b2e, #1a1625) !important;
        }
        .detail-chip:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 16px 30px -10px rgba(0,0,0,0.3);
        }

        /* Chip inner stats bg */
        .chip-stat-bg {
            background: rgba(255,255,255,0.8);
        }
        .dark .chip-stat-bg {
            background: rgba(20,22,36,0.8);
        }

        /* ===== PIC BADGE ===== */
        .pic-badge {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .pic-badge:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 20px -6px rgba(0,0,0,0.25);
        }

        /* ===== PROGRESS ===== */
        .progress-bar {
            transition: width 0.9s cubic-bezier(0.65, 0, 0.35, 1);
        }

        /* ===== TODAY BADGE ===== */
        .today-badge {
            animation: softPulse 2s ease-in-out infinite;
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            box-shadow: 0 0 0 3px rgba(129,140,248,0.35), 0 4px 12px rgba(99,102,241,0.4);
        }

        /* ===== SCROLLBAR ===== */
        .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: rgba(99,102,241,0.08); border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.35); border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.6); }
        .dark .custom-scroll::-webkit-scrollbar-track { background: rgba(99,102,241,0.12); }
        .dark .custom-scroll::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); }
        .dark .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.65); }

        /* ===== FILTER RESET BUTTON ===== */
        .filter-reset-btn {
            background: rgba(254,202,202,0.8);
            color: #dc2626;
            border: 1px solid rgba(252,165,165,0.5);
        }
        .dark .filter-reset-btn {
            background: rgba(127,29,29,0.35);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.3);
        }
        .filter-reset-btn:hover {
            background: rgba(254,202,202,1);
        }
        .dark .filter-reset-btn:hover {
            background: rgba(127,29,29,0.55);
        }

        /* ===== SELECT FILTERS ===== */
        .filter-select {
            background: rgba(255,255,255,0.92);
            color: #1f2937;
            border: none;
            border-radius: 0.75rem;
        }
        .dark .filter-select {
            background: rgba(30,27,75,0.85);
            color: #e2e8f0;
        }
        .dark .filter-select option {
            background: #1e1b4b;
            color: #e2e8f0;
        }

        /* ===== EMPTY STATE ===== */
        .empty-icon-bg {
            background: linear-gradient(135deg, #eef2ff, #f5f3ff);
        }
        .dark .empty-icon-bg {
            background: linear-gradient(135deg, rgba(30,27,75,0.5), rgba(76,29,149,0.3));
        }

        /* ===== FOCUS ===== */
        .cal-cell:focus-visible {
            outline: 2px solid #818cf8;
            outline-offset: 2px;
            border-radius: 0.5rem;
        }

        /* ===== RESPONSIVE CALENDAR CELLS ===== */
        @media (max-width: 640px) {
            .cal-cell {
                min-height: 80px;
                padding: 0.4rem;
            }
            .cal-cell-date {
                font-size: 0.65rem;
                height: 1.4rem;
                width: 1.4rem;
            }
            .pic-badge-cal span {
                max-width: 60px;
            }
        }

        /* ===== TABLE TEXT COLORS (dark mode) ===== */
        .dark .th-indigo   { color: #a5b4fc; }
        .dark .th-violet   { color: #c4b5fd; }
        .dark .th-emerald  { color: #6ee7b7; }
        .dark .th-amber    { color: #fcd34d; }
        .dark .th-blue     { color: #93c5fd; }
        .dark .th-rose     { color: #fda4af; }

        .th-indigo  { color: #4338ca; }
        .th-violet  { color: #6d28d9; }
        .th-emerald { color: #065f46; }
        .th-amber   { color: #92400e; }
        .th-blue    { color: #1d4ed8; }
        .th-rose    { color: #9f1239; }

        /* ===== STAT CARD TEXT ===== */
        .stat-sub-text { color: rgba(255,255,255,0.75); }
        .dark .stat-sub-text { color: rgba(255,255,255,0.6); }

        /* ===== PROGRESS BAR TRACK ===== */
        .progress-track {
            background: rgba(255,255,255,0.25);
        }
        .dark .progress-track {
            background: rgba(255,255,255,0.15);
        }

        /* ===== CELL BADGE BG (calendar) ===== */
        .dark .cal-badge-inner-bg {
            background: rgba(20,22,36,0.7);
        }
        .cal-badge-inner-bg {
            background: rgba(255,255,255,0.65);
        }

        /* ===== HEADER FILTER WRAPPER ===== */
        .filter-wrapper {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
        }
        .dark .filter-wrapper {
            background: rgba(0,0,0,0.2);
        }
    </style>

    <div class="space-y-6 custom-scroll">

        
        <div class="hero-header rounded-3xl p-6 lg:p-8 shadow-2xl">
            <div class="bubble w-24 h-24 top-[-20px] right-[10%]" style="animation-duration:6s;animation-delay:0s;"></div>
            <div class="bubble w-16 h-16 bottom-[-10px] left-[15%]" style="animation-duration:8s;animation-delay:1s;"></div>
            <div class="bubble w-10 h-10 top-[30%] left-[5%]" style="animation-duration:5s;animation-delay:0.5s;"></div>
            <div class="bubble w-20 h-20 bottom-[10%] right-[20%]" style="animation-duration:7s;animation-delay:2s;"></div>
            <div class="bubble w-8 h-8 top-[10%] right-[40%]" style="animation-duration:9s;animation-delay:1.5s;"></div>

            <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 shadow-lg shadow-black/20 flex-shrink-0">
                            <svg class="h-7 w-7 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight drop-shadow-md lg:text-4xl">
                                Monitoring Patrol
                            </h1>
                            <p class="text-white/75 text-sm font-medium mt-0.5">
                                Distribusi aktivitas patrol harian
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                            <?php echo e($totalPatrolMonth); ?> Patrol Selesai
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <span class="h-2 w-2 rounded-full bg-rose-300"></span>
                            <?php echo e($totalMissedMonth); ?> Missed
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                            <?php echo e($ratePercent); ?>% Rate
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="filter-wrapper flex items-center gap-2 rounded-2xl shadow-lg p-2">
                        <select wire:model.live="selectedMonth"
                            class="filter-select rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-white/50 transition-all cursor-pointer shadow-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getMonths(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>" <?php if($num == $data['month']): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <div class="h-6 w-px bg-white/40"></div>
                        <select wire:model.live="selectedYear"
                            class="filter-select rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-white/50 transition-all cursor-pointer shadow-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getYears(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php if($year == $data['year']): echo 'selected'; endif; ?>><?php echo e($year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            
            <div class="stat-card-colorful rounded-2xl p-5 shadow-xl" style="background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:rgba(219,234,254,0.9)">User Aktif</p>
                        <div class="flex items-baseline gap-2">
                            <?php
                                $activeUsersCount = count(array_filter($data['table_data'], fn($row) => !empty(array_filter($row['daily_data'], fn($day) => in_array(1, $day['shifts_status'])))));
                            ?>
                            <span class="text-4xl font-black text-white"><?php echo e($activeUsersCount); ?></span>
                            <span class="text-xs font-semibold" style="color:rgba(186,230,253,0.85)">dari <?php echo e(count($data['users'])); ?></span>
                        </div>
                    </div>
                    <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl shadow-md flex-shrink-0">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold mb-3" style="color:rgba(186,230,253,0.8)">User yang sudah melakukan patrol</p>
                <?php
                    $totalUsers2 = count($data['users']);
                    $activeRate2 = $totalUsers2 > 0 ? round(($activeUsersCount / $totalUsers2) * 100) : 0;
                ?>
                <div class="h-1.5 w-full rounded-full overflow-hidden progress-track">
                    <div class="h-full bg-white/70 rounded-full progress-bar" style="width: <?php echo e($activeRate2); ?>%;"></div>
                </div>
            </div>

            
            <div class="stat-card-colorful rounded-2xl p-5 shadow-xl" style="background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);">
                <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:rgba(237,233,254,0.85)">Total Petugas</p>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-baseline gap-2 mb-1">
                            <span class="text-3xl font-black text-white"><?php echo e(count($data['users'])); ?></span>
                            <span class="text-xs font-semibold" style="color:rgba(221,214,254,0.8)">petugas</span>
                        </div>
                        <p class="text-xs font-semibold" style="color:rgba(221,214,254,0.8)">Terdaftar aktif</p>
                    </div>
                    <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl shadow-md">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="stat-card-colorful rounded-2xl p-5 shadow-xl" style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);">
                <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:rgba(209,250,229,0.85)">Titik Patrol</p>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-baseline gap-2 mb-1">
                            <span class="text-3xl font-black text-white"><?php echo e(count($data['locations'])); ?></span>
                            <span class="text-xs font-semibold" style="color:rgba(167,243,208,0.8)">lokasi</span>
                        </div>
                        <p class="text-xs font-semibold" style="color:rgba(167,243,208,0.8)">Wajib patrol</p>
                    </div>
                    <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl shadow-md">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="stat-card-colorful rounded-2xl p-5 shadow-xl" style="background: linear-gradient(135deg, #f43f5e 0%, #fb923c 100%);">
                <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:rgba(254,226,226,0.85)">Rata-rata Kelengkapan</p>
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2 mb-2">
                            <?php
                                $avgCompletion = 0;
                                if (count($data['users']) > 0) {
                                    $totalCompletion = 0;
                                    foreach ($data['table_data'] as $row) {
                                        if ($row['row_span'] > 0) {
                                            $totalCompletion += ($row['locations_patrolled'] / $row['total_locations']) * 100;
                                        }
                                    }
                                    $avgCompletion = round($totalCompletion / count($data['users']));
                                }
                            ?>
                            <span class="text-3xl font-black text-white"><?php echo e($avgCompletion); ?>%</span>
                        </div>
                        <div class="w-full h-2 rounded-full overflow-hidden shadow-inner progress-track">
                            <div class="h-full bg-white/70 rounded-full progress-bar" style="width: <?php echo e($avgCompletion); ?>%;"></div>
                        </div>
                    </div>
                    <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl shadow-md ml-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        <?php
            $picSummary = [];
            foreach ($data['users'] as $user) {
                if (!isset($picColors[$user->name])) continue;
                $picSummary[$user->id] = [
                    'name' => $user->name,
                    'color_idx' => $picColors[$user->name],
                    'total_locations' => count($data['locations']),
                    'locations_visited' => 0,
                    'total_shifts' => count($data['shifts']),
                    'total_assignments' => count($data['locations']) * count($data['shifts']),
                    'patrols_completed' => 0,
                    'patrols_pending' => 0,
                ];
            }

            foreach ($data['users'] as $user) {
                if (!isset($picColors[$user->name])) continue;
                $locationsVisited = \App\Models\Patrol::where('user_id', $user->id)
                    ->whereBetween('patrol_time', [
                        \Carbon\Carbon::create($data['year'], $data['month'], 1),
                        \Carbon\Carbon::create($data['year'], $data['month'], $data['days_in_month'], 23, 59, 59)
                    ])
                    ->whereNotNull('qr_scanned_at')
                    ->distinct('location_id')
                    ->count('location_id');

                $patrolsCompleted = \App\Models\Patrol::where('user_id', $user->id)
                    ->whereBetween('patrol_time', [
                        \Carbon\Carbon::create($data['year'], $data['month'], 1),
                        \Carbon\Carbon::create($data['year'], $data['month'], $data['days_in_month'], 23, 59, 59)
                    ])
                    ->whereNotNull('qr_scanned_at')
                    ->count();

                $patrolsTotal = \App\Models\Patrol::where('user_id', $user->id)
                    ->whereBetween('patrol_time', [
                        \Carbon\Carbon::create($data['year'], $data['month'], 1),
                        \Carbon\Carbon::create($data['year'], $data['month'], $data['days_in_month'], 23, 59, 59)
                    ])
                    ->count();

                $picSummary[$user->id]['locations_visited'] = $locationsVisited;
                $picSummary[$user->id]['patrols_completed'] = $patrolsCompleted;
                $picSummary[$user->id]['patrols_pending']   = $patrolsTotal - $patrolsCompleted;
                $picSummary[$user->id]['patrols_total']     = $patrolsTotal;
            }
        ?>

        <div class="overflow-hidden rounded-2xl shadow-xl section-card">
            <div class="section-header-gradient px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl shadow-md flex-shrink-0" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-base">Ringkasan Petugas Patrol</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Performa individual per bulan</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="table-header-row">
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold th-indigo uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                                    Petugas Patrol
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold th-violet uppercase tracking-wider">Lokasi</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold th-emerald uppercase tracking-wider">Patrol Selesai</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold th-amber uppercase tracking-wider">Tertunda</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold th-blue uppercase tracking-wider">Progres Lokasi</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold th-rose uppercase tracking-wider">Kelengkapan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y table-divider table-bg">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $picSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $colorKey = $badgeKeys[$pic['color_idx'] % count($badgeKeys)];
                                $cs = $badgeBg[$colorKey];
                                $progressPercent = $pic['total_locations'] > 0
                                    ? round(($pic['locations_visited'] / $pic['total_locations']) * 100) : 0;
                                $completionPercent = $pic['patrols_total'] > 0
                                    ? round(($pic['patrols_completed'] / $pic['patrols_total']) * 100) : 0;
                                $initials = collect(explode(' ', $pic['name']))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                                $locPct   = $pic['total_locations'] > 0 ? ($pic['locations_visited'] / $pic['total_locations']) * 100 : 0;
                                $compPct  = $pic['patrols_total'] > 0 ? ($pic['patrols_completed'] / $pic['patrols_total']) * 100 : 0;
                                $overallPercent = round(($locPct + $compPct) / 2);
                                $statusGradient = $overallPercent >= 80
                                    ? 'from-emerald-500 to-teal-500'
                                    : ($overallPercent >= 50 ? 'from-amber-400 to-orange-500' : 'from-rose-500 to-pink-500');
                                $statusIcon = $overallPercent >= 80 ? '✓' : ($overallPercent >= 50 ? '⟳' : '✗');
                            ?>
                            <tr class="table-row-hover transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl <?php echo e($avatarSolid[$colorKey]); ?> text-white text-sm font-black shadow-lg">
                                            <?php echo e($initials); ?>

                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-gray-100"><?php echo e($pic['name']); ?></div>
                                            <div class="text-xs font-semibold <?php echo e($cs['text']); ?>">Petugas Patrol</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-xl font-black <?php echo e($cs['text']); ?>"><?php echo e($pic['locations_visited']); ?></div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500">dari <?php echo e($pic['total_locations']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-xl font-black text-emerald-600 dark:text-emerald-400"><?php echo e($pic['patrols_completed']); ?></div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500">dari <?php echo e($pic['patrols_total']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-xl font-black text-amber-600 dark:text-amber-400"><?php echo e($pic['patrols_pending']); ?></div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500">belum selesai</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-36">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-bold <?php echo e($cs['text']); ?>"><?php echo e($progressPercent); ?>%</span>
                                        </div>
                                        <div class="w-full h-2.5 rounded-full bg-gray-100 dark:bg-gray-700/60 overflow-hidden shadow-inner">
                                            <div class="h-full rounded-full bg-gradient-to-r <?php echo e($cs['gradient']); ?> progress-bar" style="width: <?php echo e($progressPercent); ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-black text-white shadow-md bg-gradient-to-r <?php echo e($statusGradient); ?>">
                                        <span><?php echo e($statusIcon); ?></span>
                                        <span><?php echo e($overallPercent); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-footer-row">
                            <td colspan="6" class="px-6 py-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 shadow-sm"></div>
                                            <span class="text-gray-600 dark:text-gray-300 font-semibold">Selesai (≥80%)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 shadow-sm"></div>
                                            <span class="text-gray-600 dark:text-gray-300 font-semibold">Sedang (50–79%)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-rose-500 to-pink-500 shadow-sm"></div>
                                            <span class="text-gray-600 dark:text-gray-300 font-semibold">Rendah (&lt;50%)</span>
                                        </div>
                                    </div>
                                    <div class="font-bold text-indigo-600 dark:text-indigo-400">
                                        Total: <?php echo e(count($picSummary)); ?> Petugas
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        
        <?php
            $locationPerformance = [];
            $totalUsersLoc = count($data['users']);
            foreach ($data['locations'] as $location) {
                $usersPatrolled = \App\Models\Patrol::where('location_id', $location->id)
                    ->whereBetween('patrol_time', [
                        \Carbon\Carbon::create($data['year'], $data['month'], 1),
                        \Carbon\Carbon::create($data['year'], $data['month'], $data['days_in_month'], 23, 59, 59)
                    ])
                    ->whereNotNull('qr_scanned_at')->distinct('user_id')->count('user_id');
                $totalPatrols = \App\Models\Patrol::where('location_id', $location->id)
                    ->whereBetween('patrol_time', [
                        \Carbon\Carbon::create($data['year'], $data['month'], 1),
                        \Carbon\Carbon::create($data['year'], $data['month'], $data['days_in_month'], 23, 59, 59)
                    ])
                    ->whereNotNull('qr_scanned_at')->count();
                $performancePercent = $totalUsersLoc > 0 ? round(($usersPatrolled / $totalUsersLoc) * 100) : 0;
                $locationPerformance[$location->id] = [
                    'name' => $location->name,
                    'users_patrolled' => $usersPatrolled,
                    'total_users' => $totalUsersLoc,
                    'total_patrols' => $totalPatrols,
                    'performance_percent' => $performancePercent,
                ];
            }
            usort($locationPerformance, fn($a, $b) => $b['performance_percent'] <=> $a['performance_percent']);

            $locGradients = [
                'from-blue-500 to-cyan-400','from-violet-500 to-purple-400','from-emerald-500 to-teal-400',
                'from-rose-500 to-pink-400','from-amber-500 to-orange-400','from-indigo-500 to-blue-400',
                'from-teal-500 to-emerald-400','from-fuchsia-500 to-violet-400','from-sky-500 to-indigo-400',
                'from-orange-500 to-red-400','from-cyan-500 to-sky-400','from-pink-500 to-rose-400',
            ];
            $locTextColors = [
                'text-blue-700 dark:text-blue-300','text-violet-700 dark:text-violet-300',
                'text-emerald-700 dark:text-emerald-300','text-rose-700 dark:text-rose-300',
                'text-amber-700 dark:text-amber-300','text-indigo-700 dark:text-indigo-300',
                'text-teal-700 dark:text-teal-300','text-fuchsia-700 dark:text-fuchsia-300',
                'text-sky-700 dark:text-sky-300','text-orange-700 dark:text-orange-300',
                'text-cyan-700 dark:text-cyan-300','text-pink-700 dark:text-pink-300',
            ];
        ?>

        <div class="overflow-hidden rounded-2xl shadow-xl section-card">
            <div class="section-header-gradient px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl shadow-md flex-shrink-0" style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-base">Performa Lokasi Patrol</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Diurutkan dari performa tertinggi</p>
                    </div>
                </div>
            </div>
            <div class="p-5 page-bg">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locIndex => $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $lci     = $locIndex % count($locGradients);
                            $locGrad = $locGradients[$lci];
                            $locTxt  = $locTextColors[$lci];
                            $remaining = $location['total_users'] - $location['users_patrolled'];
                        ?>
                        <div class="loc-card group relative flex flex-col rounded-xl p-4 shadow-md loc-bg-<?php echo e($lci); ?>">
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-black text-white shadow-md bg-gradient-to-br <?php echo e($locGrad); ?>">
                                    <?php echo e($locIndex + 1); ?>

                                </span>
                            </div>
                            <div class="mb-3 pr-8">
                                <h4 class="font-black text-gray-900 dark:text-gray-100 text-sm"><?php echo e($location['name']); ?></h4>
                                <p class="text-[10px] font-bold <?php echo e($locTxt); ?> uppercase tracking-wider mt-0.5">
                                    <?php echo e($location['users_patrolled']); ?>/<?php echo e($location['total_users']); ?> PIC
                                </p>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-black bg-gradient-to-r <?php echo e($locGrad); ?> bg-clip-text text-transparent">
                                    <?php echo e($location['performance_percent']); ?>%
                                </span>
                            </div>
                            <div class="relative w-full h-3 rounded-full overflow-hidden mb-4 shadow-inner bg-white/60 dark:bg-gray-800/50 border border-white/70 dark:border-gray-700/40">
                                <div class="h-full rounded-full bg-gradient-to-r <?php echo e($locGrad); ?> shadow progress-bar"
                                     style="width: <?php echo e($location['performance_percent']); ?>%;"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="rounded-lg p-2 shadow-sm text-center loc-stat-inner">
                                    <p class="text-sm font-black <?php echo e($locTxt); ?>"><?php echo e($location['users_patrolled']); ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase mt-0.5">Aktif</p>
                                </div>
                                <div class="rounded-lg p-2 shadow-sm text-center loc-stat-inner">
                                    <p class="text-sm font-black <?php echo e($locTxt); ?>"><?php echo e($location['total_patrols']); ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase mt-0.5">Patrol</p>
                                </div>
                                <div class="rounded-lg p-2 shadow-sm text-center loc-stat-inner">
                                    <p class="text-sm font-black <?php echo e($remaining > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'); ?>"><?php echo e($remaining); ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase mt-0.5">Sisa</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($picColors) > 0): ?>
        <div class="overflow-hidden rounded-2xl shadow-xl pic-legend-card">
            <div class="section-header-gradient px-5 py-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #f43f5e, #fb923c);">
                            <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-black text-gray-700 dark:text-gray-200">Daftar Petugas</h3>
                        <span class="text-xs font-bold bg-gradient-to-r from-indigo-500 to-violet-500 text-white px-2.5 py-0.5 rounded-full shadow-sm">
                            <?php echo e(count($picColors)); ?> petugas
                        </span>
                        <span id="filter-info" class="hidden text-xs font-semibold bg-gradient-to-r from-amber-400 to-orange-500 text-white px-2.5 py-0.5 rounded-full shadow-sm">
                            📌 Filter aktif
                        </span>
                    </div>
                    <button id="reset-filter-btn" onclick="resetFilter()" class="hidden px-3 py-1.5 text-xs font-bold rounded-lg filter-reset-btn transition-colors">
                        ✕ Reset Filter
                    </button>
                </div>
            </div>
            <div class="flex flex-wrap gap-2.5 p-4 page-bg">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $picColors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picName => $colorIdx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $colorKey = $badgeKeys[$colorIdx % count($badgeKeys)];
                        $cs       = $badgeBg[$colorKey];
                        $initials = collect(explode(' ', $picName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                    ?>
                    <div class="pic-badge flex items-center gap-2.5 rounded-xl border <?php echo e($cs['border']); ?> <?php echo e($cs['bg']); ?> pl-2 pr-4 py-1.5 shadow-md">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg <?php echo e($avatarSolid[$colorKey]); ?> text-white text-[11px] font-black shadow-md flex-shrink-0">
                            <?php echo e($initials); ?>

                        </span>
                        <span class="text-xs font-bold <?php echo e($cs['text']); ?>"><?php echo e($picName); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="overflow-hidden rounded-2xl shadow-xl calendar-card">

            
            <div class="calendar-header-bg flex flex-col gap-3 px-4 sm:px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl shadow-lg" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <span class="text-xl font-black text-white"><?php echo e($data['month']); ?></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                            <?php echo e($monthNameId[$data['month']]); ?> <?php echo e($data['year']); ?>

                        </h3>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                            <?php echo e($data['days_in_month']); ?> hari &bull; <?php echo e(count($calendarData)); ?> hari aktif
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="legend-chip flex items-center gap-1.5 rounded-lg px-3 py-1.5 shadow border-emerald-200 dark:border-emerald-700">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/40"></span>
                        <span class="text-emerald-700 dark:text-emerald-300">Patrol</span>
                    </span>
                    <span class="legend-chip flex items-center gap-1.5 rounded-lg px-3 py-1.5 shadow border-rose-200 dark:border-rose-700">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-500/40"></span>
                        <span class="text-rose-700 dark:text-rose-300">Missed</span>
                    </span>
                    <span class="legend-chip flex items-center gap-1.5 rounded-lg px-3 py-1.5 shadow border-fuchsia-200 dark:border-fuchsia-700">
                        <span class="inline-block h-4 w-5 rounded bg-fuchsia-50 dark:bg-fuchsia-900/30 border border-fuchsia-200 dark:border-fuchsia-700"></span>
                        <span class="text-fuchsia-700 dark:text-fuchsia-300">Weekend</span>
                    </span>
                </div>
            </div>

            
            <?php
                $dayHeaderColors = [
                    'text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20',
                    'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20',
                    'text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20',
                    'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20',
                    'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20',
                    'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20',
                    'text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20',
                ];
            ?>
            <div class="grid grid-cols-7 day-header-cell">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dayNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $dn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="py-3 text-center <?php echo e($dayHeaderColors[$i]); ?>">
                        <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest"><?php echo e($dn); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="grid grid-cols-7">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($b = 0; $b < $startBlank; $b++): ?>
                    <div class="blank-cell min-h-[80px] sm:min-h-[110px]"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($day = 1; $day <= $data['days_in_month']; $day++): ?>
                    <?php
                        $cellDate   = \Carbon\Carbon::create($data['year'], $data['month'], $day);
                        $isWeekend  = in_array($cellDate->dayOfWeek, [0, 6]);
                        $isToday    = $cellDate->isToday();
                        
                        // FILTER: Hanya ambil petugas dengan patrol_count > 0 untuk hari ini
                        $rawDayPics = $calendarData[$day] ?? [];
                        $dayPics    = array_filter($rawDayPics, fn($p) => $p['patrol_count'] > 0);

                        $showPics   = array_slice($dayPics, 0, 3, true);
                        $extraCount = count($dayPics) - count($showPics);
                        $dayPatrol  = array_sum(array_column($dayPics, 'patrol_count'));
                        $dayMissed  = array_sum(array_column($dayPics, 'missed_count'));
                        $hasData    = count($dayPics) > 0;
                        $healthStatus = $hasData ? ($dayMissed === 0 ? 'success' : ($dayPatrol === 0 ? 'danger' : 'warning')) : 'none';
                    ?>

                    <div
                        onclick="selectDay(<?php echo e($day); ?>)"
                        id="cal-day-<?php echo e($day); ?>"
                        data-day="<?php echo e($day); ?>"
                        tabindex="0"
                        role="button"
                        aria-label="Tanggal <?php echo e($day); ?>, <?php echo e($dayPatrol); ?> patrol selesai"
                        class="cal-cell group min-h-[80px] sm:min-h-[110px] p-2 sm:p-3 cursor-pointer select-none focus:outline-none
                            <?php echo e($isWeekend ? 'weekend-cell' : ''); ?>">

                        <div class="mb-1.5 sm:mb-2 flex items-center justify-between">
                            <span class="cal-cell-date inline-flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-lg text-[10px] sm:text-xs font-black transition-all duration-200
                                <?php echo e($isToday
                                    ? 'today-badge text-white'
                                    : ($isWeekend
                                        ? 'text-rose-400 dark:text-rose-400'
                                        : 'text-gray-700 dark:text-gray-300')); ?>">
                                <?php echo e($day); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasData): ?>
                                <div class="relative flex items-center">
                                    <span class="inline-flex h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full
                                        <?php echo e($healthStatus === 'success' ? 'bg-emerald-500' : ($healthStatus === 'danger' ? 'bg-rose-500' : 'bg-amber-400')); ?>

                                        shadow-md <?php echo e($healthStatus === 'danger' ? 'animate-pulse' : ''); ?>">
                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($healthStatus === 'danger'): ?>
                                        <span class="absolute inset-0 inline-flex h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-rose-400 opacity-60 animate-ping"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasData): ?>
                        <div class="space-y-1 sm:space-y-1.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $showPics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picName => $picData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $colorKey  = $badgeKeys[$picData['color_index'] % count($badgeKeys)];
                                    $cs        = $badgeBg[$colorKey];
                                    $shortName = \Str::limit($picName, 9, '..');
                                    $hasMissed = $picData['missed_count'] > 0;
                                    $percentage = $picData['total_assigned'] > 0
                                        ? round(($picData['patrol_count'] / $picData['total_assigned']) * 100) : 0;
                                ?>
                                <div data-pic-name="<?php echo e($picName); ?>"
                                     class="pic-badge-cal flex items-center justify-between gap-1 rounded-md sm:rounded-lg border <?php echo e($cs['border']); ?> <?php echo e($cs['bg']); ?> px-1.5 sm:px-2 py-1 sm:py-1.5 transition-all duration-150">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full flex-shrink-0 <?php echo e($cs['dot']); ?> <?php echo e($hasMissed ? 'ring-1 ring-rose-400/60' : ''); ?>"></span>
                                        <span class="text-[9px] sm:text-[10px] font-bold truncate <?php echo e($cs['text']); ?>"><?php echo e($shortName); ?></span>
                                    </div>
                                    <div class="flex items-center gap-0.5 sm:gap-1 flex-shrink-0">
                                        <span class="text-[9px] sm:text-[10px] font-black <?php echo e($cs['text']); ?> px-1 py-0.5 rounded cal-badge-inner-bg">
                                            <?php echo e($picData['patrol_count']); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extraCount > 0): ?>
                                <div class="extra-count-badge flex items-center gap-1 px-1.5 sm:px-2 py-1 rounded-md sm:rounded-lg bg-indigo-50 dark:bg-indigo-900/25 border border-indigo-200 dark:border-indigo-700/50">
                                    <svg class="h-2.5 w-2.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-indigo-600 dark:text-indigo-400">+<?php echo e($extraCount); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php
                    $totalCells     = $startBlank + $data['days_in_month'];
                    $trailingBlanks = (7 - ($totalCells % 7)) % 7;
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($t = 0; $t < $trailingBlanks; $t++): ?>
                    <div class="blank-cell min-h-[80px] sm:min-h-[110px]"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div id="cal-detail-panel" class="hidden overflow-hidden rounded-2xl shadow-2xl detail-panel">

            <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);" class="px-4 sm:px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 shadow-md">
                            <div id="cal-detail-dot" class="h-3.5 w-3.5 rounded-full bg-emerald-400 shadow-md shadow-emerald-500/50"></div>
                        </div>
                        <div>
                            <h3 id="cal-detail-title" class="text-sm sm:text-base font-black text-white tracking-tight"></h3>
                            <p class="text-xs text-white/70">Detail aktivitas patrol per petugas</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="cal-detail-badge-patrol"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 pl-2.5 pr-3.5 py-1.5 text-xs font-bold text-white shadow">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            <span id="cal-detail-patrol-count"></span>
                        </span>
                        <span id="cal-detail-badge-missed"
                            class="hidden inline-flex items-center gap-1.5 rounded-xl bg-rose-500/80 backdrop-blur-sm border border-rose-400/50 pl-2.5 pr-3.5 py-1.5 text-xs font-bold text-white shadow">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                            <span id="cal-detail-missed-count"></span>
                        </span>
                        <button onclick="closeDetail()"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-white/70 hover:bg-white/20 hover:text-white transition-all duration-200 active:scale-95"
                            aria-label="Tutup detail">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="cal-detail-body"
                class="grid gap-4 p-4 sm:p-6 sm:grid-cols-2 lg:grid-cols-3 custom-scroll max-h-[500px] overflow-y-auto detail-body-bg">
            </div>
        </div>

    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        const calendarData = <?php echo json_encode($calendarData, 15, 512) ?>;
        const colorPalette = <?php echo json_encode($badgeKeys, 15, 512) ?>;
        const monthYear    = { month: <?php echo e($data['month']); ?>, year: <?php echo e($data['year']); ?> };

        const dayNamesFull = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const monthNamesId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        const avatarColors = {
            sky:     'from-sky-400 to-sky-600',
            emerald: 'from-emerald-400 to-emerald-600',
            violet:  'from-violet-400 to-violet-600',
            amber:   'from-amber-400 to-amber-600',
            rose:    'from-rose-400 to-rose-600',
            teal:    'from-teal-400 to-teal-600',
            indigo:  'from-indigo-400 to-indigo-600',
            orange:  'from-orange-400 to-orange-600',
        };

        const detailBgLight = [
            'from-blue-50 to-cyan-50',
            'from-violet-50 to-purple-50',
            'from-emerald-50 to-teal-50',
            'from-rose-50 to-pink-50',
            'from-amber-50 to-orange-50',
            'from-indigo-50 to-blue-50',
            'from-teal-50 to-emerald-50',
            'from-fuchsia-50 to-violet-50',
        ];
        const detailBorderLight = [
            'border-blue-200','border-violet-200','border-emerald-200','border-rose-200',
            'border-amber-200','border-indigo-200','border-teal-200','border-fuchsia-200',
        ];
        const detailBorderDark = [
            'dark:border-blue-800/50','dark:border-violet-800/50','dark:border-emerald-800/50','dark:border-rose-800/50',
            'dark:border-amber-800/50','dark:border-indigo-800/50','dark:border-teal-800/50','dark:border-fuchsia-800/50',
        ];

        const colorMap = {
            sky:     { bg: 'bg-sky-100 dark:bg-sky-900/50',     text: 'text-sky-700 dark:text-sky-300',     dot: 'bg-sky-500',     border: 'border-sky-300 dark:border-sky-700' },
            emerald: { bg: 'bg-emerald-100 dark:bg-emerald-900/50', text: 'text-emerald-700 dark:text-emerald-300', dot: 'bg-emerald-500', border: 'border-emerald-300 dark:border-emerald-700' },
            violet:  { bg: 'bg-violet-100 dark:bg-violet-900/50',text: 'text-violet-700 dark:text-violet-300', dot: 'bg-violet-500', border: 'border-violet-300 dark:border-violet-700' },
            amber:   { bg: 'bg-amber-100 dark:bg-amber-900/50',  text: 'text-amber-700 dark:text-amber-300', dot: 'bg-amber-500',  border: 'border-amber-300 dark:border-amber-700' },
            rose:    { bg: 'bg-rose-100 dark:bg-rose-900/50',    text: 'text-rose-700 dark:text-rose-300',   dot: 'bg-rose-500',   border: 'border-rose-300 dark:border-rose-700' },
            teal:    { bg: 'bg-teal-100 dark:bg-teal-900/50',    text: 'text-teal-700 dark:text-teal-300',   dot: 'bg-teal-500',   border: 'border-teal-300 dark:border-teal-700' },
            indigo:  { bg: 'bg-indigo-100 dark:bg-indigo-900/50',text: 'text-indigo-700 dark:text-indigo-300',dot: 'bg-indigo-500',border: 'border-indigo-300 dark:border-indigo-700' },
            orange:  { bg: 'bg-orange-100 dark:bg-orange-900/50',text: 'text-orange-700 dark:text-orange-300',dot: 'bg-orange-500',border: 'border-orange-300 dark:border-orange-700' },
        };

        let selectedDay  = null;
        let isAnimating  = false;
        let filteredUser = null;

        document.addEventListener('DOMContentLoaded', function () {
            updateFilterUI();
            document.querySelectorAll('.pic-badge').forEach(badge => {
                badge.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const nameSpan = this.querySelector('span.font-bold');
                    const picName  = nameSpan ? nameSpan.textContent.trim() : '';
                    if (!picName) return;

                    if (filteredUser === picName) {
                        filteredUser = null;
                        this.classList.remove('ring-2','ring-offset-2','ring-indigo-500','scale-105');
                    } else {
                        document.querySelectorAll('.pic-badge').forEach(b => b.classList.remove('ring-2','ring-offset-2','ring-indigo-500','scale-105'));
                        filteredUser = picName;
                        this.classList.add('ring-2','ring-offset-2','ring-indigo-500','scale-105');
                    }
                    updateCalendarFilter();
                });
            });
        });

        function updateCalendarFilter() {
            document.querySelectorAll('.cal-cell').forEach(cell => {
                const dayNum  = parseInt(cell.getAttribute('data-day'));
                const dayData = calendarData[dayNum] || {};

                if (!filteredUser) {
                    cell.style.opacity       = '1';
                    cell.style.pointerEvents = 'auto';
                    cell.querySelectorAll('.dynamic-injected-badge').forEach(el => el.remove());
                    cell.querySelectorAll('[data-pic-name]').forEach(b => b.style.display = '');
                    cell.querySelectorAll('.extra-count-badge').forEach(el => el.style.display = '');
                } else {
                    const picData = dayData[filteredUser];
                    // FILTER: Cek jika petugas ada dan memiliki patrol > 0 pada hari tersebut
                    const hasUserWithPatrol = picData && picData.patrol_count > 0;

                    if (hasUserWithPatrol) {
                        cell.style.opacity       = '1';
                        cell.style.pointerEvents = 'auto';
                        cell.querySelectorAll('[data-pic-name]').forEach(b => {
                            b.style.display = b.getAttribute('data-pic-name') === filteredUser ? '' : 'none';
                        });
                        cell.querySelectorAll('.extra-count-badge').forEach(el => el.style.display = 'none');

                        const existingBadge  = cell.querySelector(`[data-pic-name="${CSS.escape(filteredUser)}"]`);
                        const alreadyInjected = cell.querySelector('.dynamic-injected-badge');
                        
                        if (!existingBadge && !alreadyInjected) {
                            const colorKey = colorPalette[picData.color_index % colorPalette.length];
                            const cs       = colorMap[colorKey] || colorMap['indigo'];
                            const hasMissed   = picData.missed_count > 0;
                            const shortName   = filteredUser.length > 9 ? filteredUser.substring(0,9) + '..' : filteredUser;

                            let container = cell.querySelector('.space-y-1, .space-y-1\\.5');
                            if (!container) {
                                container = document.createElement('div');
                                container.className = 'space-y-1 sm:space-y-1.5';
                                cell.appendChild(container);
                            }

                            const el = document.createElement('div');
                            el.setAttribute('data-pic-name', filteredUser);
                            el.className = `dynamic-injected-badge pic-badge-cal flex items-center justify-between gap-1 rounded-md sm:rounded-lg border ${cs.border} ${cs.bg} px-1.5 sm:px-2 py-1 sm:py-1.5 transition-all duration-150`;
                            el.innerHTML = `
                                <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                    <span class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full flex-shrink-0 ${cs.dot} ${hasMissed ? 'ring-1 ring-rose-400/60' : ''}"></span>
                                    <span class="text-[9px] sm:text-[10px] font-bold truncate ${cs.text}">${escapeHtml(shortName)}</span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-black ${cs.text} px-1 py-0.5 rounded cal-badge-inner-bg">${picData.patrol_count}</span>
                            `;
                            container.appendChild(el);
                        }
                    } else {
                        // Jika 0 patrol, kita sembunyikan semua dan gelapkan cell (dimmed)
                        cell.style.opacity       = '0.25';
                        cell.style.pointerEvents = 'none';
                        cell.querySelectorAll('[data-pic-name]').forEach(b => b.style.display = 'none');
                        cell.querySelectorAll('.extra-count-badge').forEach(el => el.style.display = 'none');
                        cell.querySelectorAll('.dynamic-injected-badge').forEach(el => el.remove());
                    }
                }
            });
            updateFilterUI();
        }

        function updateFilterUI() {
            const info  = document.getElementById('filter-info');
            const btn   = document.getElementById('reset-filter-btn');
            if (filteredUser) {
                info.classList.remove('hidden');
                btn.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
                btn.classList.add('hidden');
            }
        }

        function resetFilter() {
            filteredUser = null;
            document.querySelectorAll('.pic-badge').forEach(b => b.classList.remove('ring-2','ring-offset-2','ring-indigo-500','scale-105'));
            updateCalendarFilter();
        }

        function selectDay(day) {
            if (isAnimating) return;
            if (selectedDay !== null) {
                const prev = document.getElementById('cal-day-' + selectedDay);
                if (prev) prev.classList.remove('selected');
            }
            if (selectedDay === day) { selectedDay = null; closeDetail(); return; }

            selectedDay = day;
            const curr = document.getElementById('cal-day-' + day);
            if (curr) { curr.classList.add('selected'); if (navigator.vibrate) navigator.vibrate(40); }

            const panel       = document.getElementById('cal-detail-panel');
            const title       = document.getElementById('cal-detail-title');
            const dot         = document.getElementById('cal-detail-dot');
            const body        = document.getElementById('cal-detail-body');
            const badgePatrol = document.getElementById('cal-detail-badge-patrol');
            const badgeMissed = document.getElementById('cal-detail-badge-missed');
            const patrolCount = document.getElementById('cal-detail-patrol-count');
            const missedCount = document.getElementById('cal-detail-missed-count');

            const cellDate  = new Date(monthYear.year, monthYear.month - 1, day);
            title.textContent = `${dayNamesFull[cellDate.getDay()]}, ${day} ${monthNamesId[monthYear.month - 1]} ${monthYear.year}`;

            const dayDataRaw = calendarData[day] ?? {};
            
            // FILTER: Tampilkan di Detail Panel hanya petugas dengan patrol_count > 0
            const pics = Object.entries(dayDataRaw).filter(([name, d]) => d.patrol_count > 0);
            
            const totalP  = pics.reduce((s,[,d]) => s + d.patrol_count, 0);
            const totalM  = pics.reduce((s,[,d]) => s + d.missed_count, 0);

            patrolCount.textContent = totalP + ' patrol';
            missedCount.textContent = totalM + ' missed';

            if (totalM === 0) {
                badgeMissed.classList.add('hidden');
                badgeMissed.classList.remove('inline-flex');
            } else {
                badgeMissed.classList.remove('hidden');
                badgeMissed.classList.add('inline-flex');
            }

            dot.className = 'h-3.5 w-3.5 rounded-full shadow-md transition-all duration-300 ';
            if (!pics.length)      dot.className += 'bg-gray-400';
            else if (totalM === 0) dot.className += 'bg-emerald-400 shadow-emerald-500/50';
            else if (totalP === 0) dot.className += 'bg-rose-400 shadow-rose-500/50 animate-pulse';
            else                   dot.className += 'bg-amber-400 shadow-amber-500/50';

            body.style.opacity = '0';
            body.innerHTML     = '';

            if (!pics.length) {
                body.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 shadow-inner empty-icon-bg">
                            <svg class="h-10 w-10 text-indigo-300 dark:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-indigo-400 dark:text-indigo-500">Tidak ada data patrol</p>
                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">untuk hari ini</p>
                    </div>`;
            } else {
                pics.forEach(([name, d], index) => {
                    const colorKey  = colorPalette[d.color_index % colorPalette.length];
                    const avatarCls = avatarColors[colorKey] ?? 'from-gray-400 to-gray-600';
                    const bgIdx     = d.color_index % detailBgLight.length;
                    const initials  = name.split(' ').slice(0,2).map(w => (w[0]||'').toUpperCase()).join('');
                    const hasMissed = d.missed_count > 0;
                    const rateLocal = d.total_assigned > 0 ? Math.round((d.patrol_count / d.total_assigned) * 100) : 0;
                    const rateGrad  = rateLocal === 100 ? 'from-emerald-500 to-teal-500'
                                    : (rateLocal > 50 ? 'from-amber-400 to-orange-500' : 'from-rose-500 to-pink-500');
                    const rateTextColor = rateLocal === 100 ? 'text-emerald-600 dark:text-emerald-400'
                                        : (rateLocal > 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-500 dark:text-rose-400');

                    const chip = document.createElement('div');
                    chip.className = `detail-chip fade-scale-in flex flex-col gap-3 rounded-2xl border ${detailBorderLight[bgIdx]} ${detailBorderDark[bgIdx]} bg-gradient-to-br ${detailBgLight[bgIdx]} p-4 shadow-md`;
                    chip.style.animationDelay      = `${index * 0.06}s`;
                    chip.style.animationFillMode   = 'backwards';
                    chip.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br ${avatarCls} text-white text-base font-black shadow-lg">
                                ${initials}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate mb-1">${escapeHtml(name)}</p>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-700/50">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        ${d.patrol_count}
                                    </span>
                                    ${hasMissed ? `
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 px-2 py-0.5 rounded-full border border-rose-200 dark:border-rose-700/50">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                            ${d.missed_count}
                                        </span>` : ''}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="text-sm font-black text-white px-2.5 py-1 rounded-full shadow-md bg-gradient-to-r ${rateGrad}">${rateLocal}%</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400 font-semibold">Progress Patrol</span>
                                <span class="font-bold ${rateTextColor}">${rateLocal}%</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-white/80 dark:bg-gray-800/60 overflow-hidden shadow-inner border border-white/60 dark:border-gray-700/40">
                                <div class="progress-bar h-full rounded-full bg-gradient-to-r ${rateGrad} shadow-sm" style="width:0%"></div>
                            </div>
                        </div>`;
                    body.appendChild(chip);
                    requestAnimationFrame(() => {
                        const pb = chip.querySelector('.progress-bar');
                        if (pb) pb.style.width = `${rateLocal}%`;
                    });
                });
            }

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                panel.classList.remove('slide-down');
                panel.classList.add('slide-up');
                body.style.opacity = '1';
                setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
            } else {
                body.style.opacity = '1';
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function closeDetail() {
            if (isAnimating) return;
            isAnimating = true;
            if (selectedDay !== null) {
                const cell = document.getElementById('cal-day-' + selectedDay);
                if (cell) cell.classList.remove('selected');
                selectedDay = null;
            }
            const panel = document.getElementById('cal-detail-panel');
            panel.classList.remove('slide-up');
            panel.classList.add('slide-down');
            setTimeout(() => {
                panel.classList.add('hidden');
                panel.classList.remove('slide-down');
                isAnimating = false;
            }, 250);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && selectedDay !== null) closeDetail();
            if (selectedDay !== null && (e.key === 'ArrowLeft' || e.key === 'ArrowRight')) {
                e.preventDefault();
                const newDay = selectedDay + (e.key === 'ArrowRight' ? 1 : -1);
                if (newDay >= 1 && newDay <= <?php echo e($data['days_in_month']); ?>) selectDay(newDay);
            }
        });

        document.addEventListener('click', function (e) {
            const panel  = document.getElementById('cal-detail-panel');
            const target = e.target.closest('.cal-cell');
            const panelT = e.target.closest('#cal-detail-panel');
            if (selectedDay !== null && !target && !panelT && !panel.classList.contains('hidden')) closeDetail();
        });
    </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH /root/gawe/PatrolHR/resources/views/filament/admin/pages/dashboard.blade.php ENDPATH**/ ?>