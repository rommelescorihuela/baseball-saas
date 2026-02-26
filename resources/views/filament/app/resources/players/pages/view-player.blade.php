<x-filament-panels::page>
    <style>
        .dos-wrap {
            background: #0f2123;
            color: #e2e8f0;
            font-family: 'Lexend', 'Outfit', sans-serif;
            border-radius: 1rem;
            overflow: hidden;
        }

        .dos-header {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 229, 255, 0.1);
            background: rgba(15, 33, 35, 0.8);
            backdrop-filter: blur(10px);
            justify-content: space-between;
        }

        .dos-header-title {
            text-align: center;
            flex: 1;
        }

        .dos-header-title h2 {
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #e2e8f0;
            margin: 0;
        }

        .dos-header-title p {
            font-size: 10px;
            color: #00e5ff;
            font-weight: 500;
            letter-spacing: 0.2em;
            margin: 0;
        }

        .dos-back {
            color: #00e5ff;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: none;
            border: none;
        }

        .dos-share {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(0, 229, 255, 0.1);
            color: #00e5ff;
            border: none;
            cursor: pointer;
        }

        .dos-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem 1rem;
        }

        .dos-hero-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 229, 255, 0.1), transparent);
            opacity: 0.3;
        }

        .dos-hero-content {
            position: relative;
            display: flex;
            gap: 1.25rem;
            align-items: center;
        }

        .dos-avatar-wrap {
            position: relative;
        }

        .dos-avatar {
            width: 6rem;
            height: 6rem;
            border-radius: 9999px;
            border: 2px solid #00e5ff;
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
            background: #1A237E;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: #00e5ff;
        }

        .dos-badge-number {
            position: absolute;
            bottom: -4px;
            right: -4px;
            background: #00e5ff;
            color: #0f2123;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
        }

        .dos-player-name {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin: 0;
            color: #e2e8f0;
        }

        .dos-player-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }

        .dos-team-name {
            color: #00e5ff;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .dos-dot {
            height: 4px;
            width: 4px;
            border-radius: 9999px;
            background: #64748b;
        }

        .dos-position {
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .dos-tags {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .dos-tag {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .dos-tag-primary {
            background: rgba(0, 229, 255, 0.2);
            color: #00e5ff;
        }

        .dos-tag-secondary {
            background: rgba(255, 110, 64, 0.2);
            color: #ff6e40;
        }

        .dos-section-title {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 0.75rem;
        }

        .dos-stats-row {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .dos-stats-row::-webkit-scrollbar {
            display: none;
        }

        .dos-stat-card {
            min-width: 120px;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            border-radius: 0.75rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 229, 255, 0.1);
            flex-shrink: 0;
        }

        .dos-stat-card.glow {
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
        }

        .dos-stat-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .dos-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.05em;
        }

        .dos-stat-value.cyan {
            color: #00e5ff;
        }

        .dos-stat-value.white {
            color: #f1f5f9;
        }

        .dos-stat-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #0bda54;
            font-size: 10px;
            font-weight: 700;
        }

        .dos-chart-container {
            position: relative;
            height: 10rem;
            border-radius: 0.75rem;
            background: rgba(26, 35, 126, 0.1);
            border: 1px solid rgba(0, 229, 255, 0.1);
            overflow: hidden;
            padding-top: 1rem;
        }

        .dos-chart-labels {
            position: absolute;
            bottom: 0.5rem;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
            font-size: 8px;
            color: #64748b;
            font-weight: 700;
        }

        .dos-metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .dos-metric-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 229, 255, 0.1);
            border-radius: 0.75rem;
            padding: 0.75rem;
        }

        .dos-metric-card.border-orange {
            border-left: 2px solid #ff6e40;
        }

        .dos-metric-card.border-cyan {
            border-left: 2px solid #00e5ff;
        }

        .dos-metric-label {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .dos-metric-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        .dos-metric-sub {
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .dos-metric-sub.orange {
            color: #ff6e40;
        }

        .dos-metric-sub.cyan {
            color: #00e5ff;
        }

        .dos-metric-sub.gray {
            color: #94a3b8;
        }

        .dos-scout-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 229, 255, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        .dos-scout-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .dos-pulse {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: #00e5ff;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .dos-scout-label {
            font-size: 10px;
            font-weight: 700;
            color: #00e5ff;
            text-transform: uppercase;
        }

        .dos-scout-text {
            font-size: 0.875rem;
            color: #cbd5e1;
            line-height: 1.625;
            font-style: italic;
        }

        .dos-scout-footer {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-top: 1px solid rgba(0, 229, 255, 0.1);
            padding-top: 0.75rem;
        }

        .dos-scout-avatar {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        .dos-scout-meta {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 500;
        }

        .dos-scatter {
            position: relative;
            height: 12rem;
            border-radius: 0.75rem;
            background: rgba(26, 35, 126, 0.1);
            border: 1px solid rgba(0, 229, 255, 0.1);
            overflow: hidden;
            padding: 1rem;
        }

        .dos-scatter-grid {
            position: absolute;
            inset: 0;
            opacity: 0.2;
            background-image: radial-gradient(circle, #00e5ff 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .dos-scatter-dot {
            position: absolute;
            border-radius: 9999px;
        }

        .dos-scatter-dot.cyan {
            background: #00e5ff;
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
        }

        .dos-scatter-dot.orange {
            background: #ff6e40;
            box-shadow: 0 0 15px rgba(255, 110, 64, 0.15);
        }

        .dos-scatter-labels {
            position: absolute;
            left: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 8px;
            color: #64748b;
            padding: 0.5rem 0;
        }

        .dos-content {
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <div class="dos-wrap">
        {{-- HEADER --}}
        <div class="dos-header">
            <a href="{{ \App\Filament\App\Resources\Players\PlayerResource::getUrl('index') }}" class="dos-back">
                <x-heroicon-o-arrow-left style="width:1.5rem;height:1.5rem;" />
            </a>
            <div class="dos-header-title">
                <h2>DiamondOS Analytics</h2>
                <p>PLAYER PERFORMANCE</p>
            </div>
            <div style="width:2.5rem;"></div>
        </div>

        {{-- HERO --}}
        <div class="dos-hero">
            <div class="dos-hero-gradient"></div>
            <div class="dos-hero-content">
                <div class="dos-avatar-wrap">
                    <div class="dos-avatar">
                        {{ strtoupper(substr($record->first_name ?? 'N', 0, 1)) }}{{ strtoupper(substr($record->last_name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="dos-badge-number">#{{ $record->jersey_number ?? $record->number ?? 'N/A' }}</div>
                </div>
                <div>
                    <p class="dos-player-name">{{ $record->first_name }} {{ $record->last_name }}</p>
                    <div class="dos-player-meta">
                        <span class="dos-team-name">{{ $record->team->name ?? 'Free Agent' }}</span>
                        <span class="dos-dot"></span>
                        <span class="dos-position">{{ $record->position ?? 'Unknown' }}</span>
                    </div>
                    <div class="dos-tags">
                        <span class="dos-tag dos-tag-primary">Active</span>
                        @if($record->position === 'P')
                            <span class="dos-tag dos-tag-secondary">Pitcher</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SEASON TOTALS --}}
        <div style="padding: 0 1rem 0.5rem;">
            <h3 class="dos-section-title">Season Totals</h3>
            <div class="dos-stats-row">
                @php
                    $stats = $record->currentStats?->first();
                    $avg = $stats ? number_format($stats->avg ?? 0, 3) : '.000';
                    $obp = $stats ? number_format($stats->obp ?? 0, 3) : '.000';
                    $slg = $stats ? number_format($stats->slg ?? 0, 3) : '.000';
                    $ops = $stats ? number_format($stats->ops ?? 0, 3) : '.000';
                    $hr = $stats->hr ?? 0;
                    $h = $stats->h ?? 0;
                    $rbi = $stats->ci ?? 0;
                @endphp
                <div class="dos-stat-card glow">
                    <p class="dos-stat-label">AVG</p>
                    <p class="dos-stat-value cyan">{{ $avg }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">OBP</p>
                    <p class="dos-stat-value white">{{ $obp }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">SLG</p>
                    <p class="dos-stat-value white">{{ $slg }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">OPS</p>
                    <p class="dos-stat-value cyan">{{ $ops }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">HR</p>
                    <p class="dos-stat-value" style="color:#ff6e40;">{{ $hr }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">H</p>
                    <p class="dos-stat-value white">{{ $h }}</p>
                </div>
                <div class="dos-stat-card">
                    <p class="dos-stat-label">RBI</p>
                    <p class="dos-stat-value white">{{ $rbi }}</p>
                </div>
            </div>
        </div>

        <div class="dos-content">
            {{-- PERFORMANCE TREND --}}
            <div>
                <h3 class="dos-section-title">Season Performance Trend</h3>
                <div class="dos-chart-container">
                    <svg style="width:100%;height:100%;" preserveAspectRatio="none" viewBox="0 0 100 40">
                        <defs>
                            <linearGradient id="grad1" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#00e5ff" stop-opacity="0.3"></stop>
                                <stop offset="100%" stop-color="#00e5ff" stop-opacity="0"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M0,35 Q10,25 20,30 T40,15 T60,20 T80,5 T100,10 L100,40 L0,40 Z" fill="url(#grad1)">
                        </path>
                        <path d="M0,35 Q10,25 20,30 T40,15 T60,20 T80,5 T100,10" fill="none" stroke="#00e5ff"
                            stroke-width="1"></path>
                    </svg>
                    <div class="dos-chart-labels">
                        <span>APR</span><span>MAY</span><span>JUN</span><span>JUL</span><span>AUG</span><span>SEP</span>
                    </div>
                </div>
            </div>

            {{-- ADVANCED METRICS --}}
            <div>
                <h3 class="dos-section-title">Advanced Sabermetrics</h3>
                <div class="dos-metrics-grid">
                    <div class="dos-metric-card border-orange">
                        <p class="dos-metric-label">WAR</p>
                        <p class="dos-metric-value">8.9</p>
                        <p class="dos-metric-sub orange">Elite</p>
                    </div>
                    <div class="dos-metric-card border-cyan">
                        <p class="dos-metric-label">wRC+</p>
                        <p class="dos-metric-value">182</p>
                        <p class="dos-metric-sub cyan">+82% League</p>
                    </div>
                    <div class="dos-metric-card border-orange">
                        <p class="dos-metric-label">Exit Vel</p>
                        <p class="dos-metric-value">96.4</p>
                        <p class="dos-metric-sub gray">MPH (Avg)</p>
                    </div>
                </div>
            </div>

            {{-- SCOUTING REPORT --}}
            <div>
                <h3 class="dos-section-title">Scouting Report</h3>
                <div class="dos-scout-card">
                    <div class="dos-scout-indicator">
                        <span class="dos-pulse"></span>
                        <span class="dos-scout-label">Active Analysis</span>
                    </div>
                    <p class="dos-scout-text">
                        "Player is exhibiting elite bat-to-ball skills with a significant increase in barrel percentage
                        over the last 15 games. Discipline at the plate remains a major strength, forcing pitchers into
                        high-velocity counts where he excels."
                    </p>
                    <div class="dos-scout-footer">
                        <div class="dos-scout-avatar">JD</div>
                        <span class="dos-scout-meta">Head Scout - {{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- SCATTER PLOT --}}
            <div>
                <h3 class="dos-section-title">Pitch Velocity vs Hit Outcome</h3>
                <div class="dos-scatter">
                    <div class="dos-scatter-grid"></div>
                    <div style="position:relative;height:100%;width:100%;">
                        <div class="dos-scatter-labels">
                            <span>105</span><span>95</span><span>85</span><span>75</span>
                        </div>
                        <div class="dos-scatter-dot cyan" style="bottom:40%;left:20%;width:8px;height:8px;"></div>
                        <div class="dos-scatter-dot orange" style="bottom:60%;left:35%;width:12px;height:12px;"></div>
                        <div class="dos-scatter-dot cyan" style="bottom:30%;left:50%;width:8px;height:8px;opacity:0.6;">
                        </div>
                        <div class="dos-scatter-dot orange" style="bottom:75%;left:65%;width:16px;height:16px;"></div>
                        <div class="dos-scatter-dot cyan" style="bottom:45%;left:80%;width:8px;height:8px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div style="height:2rem;"></div>
    </div>
</x-filament-panels::page>