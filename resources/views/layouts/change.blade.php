{{-- resources/views/layouts/change.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Management - @yield('title', 'Gestion des changements')</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0a0e1a;
            --surface: #111827;
            --surface2: #1a2234;
            --surface3: #222d42;
            --border: #2a3a5c;
            --border2: #3a4f78;
            --accent: #3b82f6;
            --accent2: #60a5fa;
            --accent-glow: rgba(59,130,246,0.18);
            --green: #10b981;
            --green-bg: rgba(16,185,129,0.12);
            --red: #ef4444;
            --red-bg: rgba(239,68,68,0.12);
            --yellow: #f59e0b;
            --yellow-bg: rgba(245,158,11,0.12);
            --purple: #8b5cf6;
            --purple-bg: rgba(139,92,246,0.12);
            --text: #e2e8f0;
            --text2: #94a3b8;
            --text3: #64748b;
            --font: 'IBM Plex Sans', sans-serif;
            --mono: 'IBM Plex Mono', monospace;
            --radius: 8px;
            --radius2: 12px;
        }

        body { 
            font-family: var(--font); 
            background: var(--bg); 
            color: var(--text); 
            min-height: 100vh; 
            margin: 0;
        }

        .app { 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
        }

        /* Topbar */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-brand { display: flex; align-items: center; gap: 10px; }
        .topbar-logo {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
        }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text); letter-spacing: 0.3px; }
        .topbar-sub { font-size: 11px; color: var(--text3); font-family: var(--mono); margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }

        /* Role Badge */
        .role-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            font-family: var(--mono);
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid;
            background: transparent;
        }
        .role-badge:hover { transform: translateY(-1px); }
        .role-N1  { background: var(--accent-glow); color: var(--accent2); border-color: var(--accent); }
        .role-N2  { background: var(--green-bg);    color: var(--green);   border-color: var(--green); }
        .role-N3  { background: var(--purple-bg);   color: var(--purple);  border-color: var(--purple); }

        /* Main Layout */
        .main { flex: 1; display: flex; }
        .sidebar {
            width: 240px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px 0;
            flex-shrink: 0;
            overflow-y: auto;
        }
        .sidebar-section { padding: 0 16px 8px; }
        .sidebar-label { 
            font-size: 10px; 
            color: var(--text3); 
            font-family: var(--mono); 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 12px 8px 6px; 
        }
        .sidebar-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 13px;
            color: var(--text2);
            transition: all 0.15s;
            margin-bottom: 2px;
            text-decoration: none;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .sidebar-item:hover { background: var(--surface2); color: var(--text); }
        .sidebar-item.active { 
            background: var(--accent-glow); 
            color: var(--accent2); 
            border-left: 2px solid var(--accent); 
        }
        .sidebar-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .sidebar-count {
            margin-left: auto;
            background: var(--surface3);
            color: var(--text3);
            font-size: 10px;
            font-family: var(--mono);
            padding: 1px 6px;
            border-radius: 10px;
        }
        .sidebar-count.has { background: var(--accent-glow); color: var(--accent2); }

        /* Content */
        .content { flex: 1; padding: 28px; overflow-y: auto; max-height: calc(100vh - 56px); }
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: var(--text); }
        .page-sub { font-size: 13px; color: var(--text3); margin-top: 4px; }

        /* Cards */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius2);
            padding: 20px;
            margin-bottom: 16px;
            transition: border-color 0.2s;
        }
        .card:hover { border-color: var(--border2); }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title { 
            font-size: 13px; 
            font-weight: 600; 
            color: var(--text2); 
            text-transform: uppercase; 
            letter-spacing: 0.8px; 
            font-family: var(--mono); 
        }
        .card-num { 
            font-size: 11px; 
            color: var(--text3); 
            font-family: var(--mono); 
            background: var(--surface2); 
            padding: 2px 8px; 
            border-radius: 4px; 
        }

        /* Form Elements */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        .form-full { grid-column: 1 / -1; }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { 
            font-size: 11px; 
            font-weight: 600; 
            color: var(--text3); 
            text-transform: uppercase; 
            letter-spacing: 0.6px; 
            font-family: var(--mono); 
        }
        .field label span.req { color: var(--red); margin-left: 2px; }
        .field input, .field select, .field textarea {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 12px;
            font-size: 13px;
            color: var(--text);
            font-family: var(--font);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            width: 100%;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        .field input:disabled, .field select:disabled, .field textarea:disabled {
            opacity: 0.5; cursor: not-allowed;
        }
        .field textarea { resize: vertical; min-height: 80px; line-height: 1.6; }
        .field select option { background: var(--surface2); color: var(--text); }

        /* Impact Select */
        .impact-select {
            display: flex; gap: 8px; flex-wrap: wrap;
        }
        .impact-opt {
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s;
            color: var(--text2);
            background: var(--surface2);
            font-family: var(--mono);
        }
        .impact-opt:hover { border-color: var(--border2); }
        .impact-opt.sel-faible  { background: var(--green-bg);  color: var(--green);  border-color: var(--green); }
        .impact-opt.sel-moyen   { background: var(--yellow-bg); color: var(--yellow); border-color: var(--yellow); }
        .impact-opt.sel-élevé   { background: var(--red-bg);    color: var(--red);    border-color: var(--red); }

        /* Buttons */
        .btn-row { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; flex-wrap: wrap; }
        .btn {
            padding: 9px 20px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid;
            transition: all 0.2s;
            font-family: var(--font);
            display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-primary { 
            background: var(--accent); 
            border-color: var(--accent); 
            color: #fff; 
            box-shadow: 0 2px 12px rgba(59,130,246,0.3); 
        }
        .btn-primary:hover { background: var(--accent2); border-color: var(--accent2); }
        .btn-ghost { 
            background: transparent; 
            border-color: var(--border2); 
            color: var(--text2); 
        }
        .btn-ghost:hover { background: var(--surface2); color: var(--text); }
        .btn-green { 
            background: var(--green); 
            border-color: var(--green); 
            color: #fff; 
            box-shadow: 0 2px 12px rgba(16,185,129,0.3); 
        }
        .btn-red   { 
            background: var(--red);   
            border-color: var(--red);   
            color: #fff; 
            box-shadow: 0 2px 12px rgba(239,68,68,0.3); 
        }
        .btn-yellow { background: var(--yellow); border-color: var(--yellow); color: #000; }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

        /* Ticket List */
        .ticket-list { display: flex; flex-direction: column; gap: 10px; }
        .ticket-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius2);
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 16px;
            text-decoration: none;
            color: inherit;
        }
        .ticket-card:hover { border-color: var(--accent); background: var(--surface2); }
        .ticket-id { 
            font-family: var(--mono); 
            font-size: 12px; 
            color: var(--accent2); 
            font-weight: 600; 
            white-space: nowrap; 
        }
        .ticket-info { flex: 1; min-width: 0; }
        .ticket-title-text { 
            font-size: 14px; 
            font-weight: 500; 
            color: var(--text); 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
        .ticket-meta { 
            font-size: 11px; 
            color: var(--text3); 
            margin-top: 2px; 
            font-family: var(--mono); 
        }
        .ticket-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        /* Status Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            font-family: var(--mono);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid;
            white-space: nowrap;
        }
        .badge-draft    { background: rgba(100,116,139,0.15); color: #94a3b8; border-color: #475569; }
        .badge-pending  { background: var(--yellow-bg); color: var(--yellow); border-color: var(--yellow); }
        .badge-approved { background: var(--green-bg);  color: var(--green);  border-color: var(--green); }
        .badge-rejected { background: var(--red-bg);    color: var(--red);    border-color: var(--red); }
        .badge-closed   { background: var(--purple-bg); color: var(--purple); border-color: var(--purple); }
        .badge-incident { background: var(--red-bg);    color: var(--red);    border-color: var(--red); }

        /* Type Badge */
        .type-std { background: rgba(59,130,246,0.12); color: #60a5fa; border-color: #3b82f6; }
        .type-nrm { background: rgba(16,185,129,0.12); color: #10b981; border-color: #10b981; }
        .type-urg { background: rgba(239,68,68,0.12);  color: #ef4444; border-color: #ef4444; }

        /* File Upload */
        .upload-zone {
            border: 2px dashed var(--border2);
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--surface2);
        }
        .upload-zone:hover { border-color: var(--accent); background: var(--accent-glow); }
        .upload-zone input { display: none; }
        .upload-text { font-size: 12px; color: var(--text3); margin-top: 6px; }
        .upload-files { display: flex; flex-direction: column; gap: 6px; margin-top: 10px; }
        .upload-file-item {
            display: flex; align-items: center; gap: 8px;
            background: var(--surface3); border-radius: 6px; padding: 6px 10px;
            font-size: 12px; color: var(--text2); font-family: var(--mono);
        }
        .upload-file-remove { margin-left: auto; color: var(--red); cursor: pointer; font-size: 14px; }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            border: 1px solid;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 16px;
        }
        .alert-info    { background: var(--accent-glow); border-color: var(--accent); color: var(--accent2); }
        .alert-success { background: var(--green-bg); border-color: var(--green); color: var(--green); }
        .alert-warning { background: var(--yellow-bg); border-color: var(--yellow); color: var(--yellow); }
        .alert-error   { background: var(--red-bg); border-color: var(--red); color: var(--red); }

        /* Timeline */
        .timeline { padding: 8px 0; }
        .timeline-item { display: flex; gap: 12px; padding-bottom: 16px; position: relative; }
        .timeline-item:not(:last-child)::before {
            content: ''; position: absolute; left: 11px; top: 22px;
            width: 1px; bottom: 0; background: var(--border);
        }
        .timeline-dot {
            width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; border: 2px solid;
        }
        .timeline-content { flex: 1; }
        .timeline-action { font-size: 13px; font-weight: 500; color: var(--text); }
        .timeline-by { 
            font-size: 11px; 
            color: var(--text3); 
            font-family: var(--mono); 
            margin-top: 2px; 
        }
        .timeline-note { 
            font-size: 12px; 
            color: var(--text2); 
            margin-top: 4px; 
            font-style: italic; 
        }

        /* Readonly Field */
        .readonly-field {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 12px;
            font-size: 13px;
            color: var(--text2);
            font-family: var(--font);
            min-height: 38px;
            word-break: break-word;
        }
        .readonly-field.mono { font-family: var(--mono); font-size: 12px; }

        .section-readonly { background: rgba(255,255,255,0.01); }

        /* Ticket Number */
        .ticket-num-display {
            font-family: var(--mono);
            font-size: 22px;
            font-weight: 700;
            color: var(--accent2);
            background: var(--accent-glow);
            border: 1px solid var(--accent);
            border-radius: var(--radius);
            padding: 10px 20px;
            display: inline-block;
            margin-bottom: 16px;
        }

        /* Empty State */
        .empty { text-align: center; padding: 60px 20px; color: var(--text3); }
        .empty-icon { font-size: 36px; margin-bottom: 12px; opacity: 0.5; }
        .empty-text { font-size: 14px; }

        /* Role Selector */
        .role-selector {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: var(--bg);
            background-image: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.06) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.06) 0%, transparent 50%);
        }
        .role-selector-inner { text-align: center; max-width: 560px; padding: 40px 20px; }
        .role-selector-title { font-size: 28px; font-weight: 700; margin-bottom: 6px; }
        .role-selector-sub { 
            font-size: 14px; 
            color: var(--text3); 
            margin-bottom: 36px; 
            font-family: var(--mono); 
        }
        .role-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .role-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius2);
            padding: 24px 16px;
            cursor: pointer;
            transition: all 0.25s;
            text-align: center;
        }
        .role-card:hover { transform: translateY(-4px); }
        .role-card-icon { font-size: 28px; margin-bottom: 10px; }
        .role-card-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; font-family: var(--mono); }
        .role-card-desc { 
            font-size: 11px; 
            color: var(--text3); 
            line-height: 1.5; 
            white-space: pre-line; 
        }
        .role-card-N1 { border-color: var(--accent); box-shadow: 0 0 20px rgba(59,130,246,0.1); }
        .role-card-N1:hover { box-shadow: 0 8px 30px rgba(59,130,246,0.2); border-color: var(--accent2); }
        .role-card-N2 { border-color: var(--green); box-shadow: 0 0 20px rgba(16,185,129,0.1); }
        .role-card-N2:hover { box-shadow: 0 8px 30px rgba(16,185,129,0.2); }
        .role-card-N3 { border-color: var(--purple); box-shadow: 0 0 20px rgba(139,92,246,0.1); }
        .role-card-N3:hover { box-shadow: 0 8px 30px rgba(139,92,246,0.2); }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.7);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000; padding: 20px;
            backdrop-filter: blur(4px);
        }
        .modal {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius2);
            padding: 24px;
            max-width: 480px; width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .modal-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
        .modal-body { 
            font-size: 13px; 
            color: var(--text2); 
            line-height: 1.6; 
            margin-bottom: 20px; 
        }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app">
        @if(session('change_role'))
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-brand">
                    <div class="topbar-logo">CM</div>
                    <div>
                        <div class="topbar-title">Change Management</div>
                        <div class="topbar-sub">Système de gestion des changements</div>
                    </div>
                </div>
                <div class="topbar-right">
                    <div style="font-size: 12px; color: var(--text3); font-family: var(--mono);">
                        @if(isset($pendingCount) && $pendingCount > 0)
                            <span style="color: var(--yellow); margin-right: 12px;">⚠️ {{ $pendingCount }} en attente</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('change.role.clear') }}" id="role-form" style="display: inline;">
                        @csrf
                        <button type="submit" class="role-badge role-{{ session('change_role') }}" style="border: none;">
                            {{ session('change_role') === 'N1' ? '📝' : (session('change_role') === 'N2' ? '⚙️' : '🔐') }} 
                            {{ session('change_role') }} · Changer de rôle
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="main">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>