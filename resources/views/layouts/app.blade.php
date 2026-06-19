<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion des stagiaires')</title>
    <style>
        :root {
            --navy: #17324d;
            --blue: #2368a2;
            --sky: #eaf3fa;
            --green: #16836d;
            --red: #b42318;
            --ink: #1f2937;
            --muted: #667085;
            --line: #e4e7ec;
            --paper: #ffffff;
            --background: #f5f7fa;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, "Segoe UI", Arial, sans-serif; color: var(--ink); background: var(--background); }
        a { color: inherit; text-decoration: none; }
        .topbar { background: var(--navy); color: white; }
        .nav { max-width: 1180px; margin: auto; min-height: 68px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .brand { font-size: 19px; font-weight: 800; letter-spacing: .2px; }
        .brand small { display: block; font-size: 11px; font-weight: 500; opacity: .72; margin-top: 2px; }
        .container { max-width: 1180px; margin: 0 auto; padding: 32px 24px 56px; }
        .page-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 24px; }
        h1 { margin: 0 0 7px; color: var(--navy); font-size: 29px; }
        h2 { color: var(--navy); }
        .subtitle { color: var(--muted); margin: 0; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; border: 0; border-radius: 9px; padding: 10px 16px; font-weight: 700; cursor: pointer; transition: .15s ease; }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--blue); color: white; }
        .btn-success { background: var(--green); color: white; }
        .btn-light { background: white; color: var(--navy); border: 1px solid var(--line); }
        .btn-danger { background: #fff0ee; color: var(--red); }
        .btn-sm { min-height: 34px; padding: 7px 10px; font-size: 13px; }
        .cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
        .stat, .panel { background: var(--paper); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 5px 18px rgba(23,50,77,.05); }
        .stat { padding: 20px; }
        .stat-label { color: var(--muted); font-size: 13px; font-weight: 700; }
        .stat-value { color: var(--navy); font-size: 32px; font-weight: 850; margin-top: 5px; }
        .panel { overflow: hidden; }
        .panel-body { padding: 24px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { background: #edf3f8; color: var(--navy); font-size: 12px; text-transform: uppercase; letter-spacing: .055em; text-align: left; }
        th, td { padding: 16px 18px; border-right: 1px solid #d9e1e8; border-bottom: 1px solid #d9e1e8; vertical-align: middle; }
        th:last-child, td:last-child { border-right: 0; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr { position: relative; cursor: pointer; transition: background .15s ease, box-shadow .15s ease; }
        tbody tr:nth-child(even) { background: #fbfcfd; }
        tbody tr:hover, tbody tr:focus-visible { background: #edf6fc; box-shadow: inset 4px 0 0 var(--blue); outline: none; }
        tbody td { line-height: 1.45; }
        .cell-title { font-weight: 750; color: #344054; }
        .period { white-space: nowrap; font-weight: 700; color: #344054; }
        .name { font-weight: 800; color: var(--navy); }
        .name:hover { color: var(--blue); text-decoration: underline; }
        .muted { color: var(--muted); font-size: 13px; }
        .row-hint { margin-top: 5px; color: var(--blue); font-size: 11px; font-weight: 700; }
        .actions { display: flex; gap: 7px; flex-wrap: wrap; }
        .alert { padding: 13px 16px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #eaf8f4; color: #116451; border: 1px solid #b9e6da; }
        .alert-error { background: #fff0ee; color: #912018; border: 1px solid #f7c7c2; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .field.full { grid-column: 1 / -1; }
        .hidden { display: none !important; }
        label { display: block; font-size: 13px; font-weight: 750; color: #344054; margin-bottom: 7px; }
        input, select { width: 100%; min-height: 44px; border: 1px solid #d0d5dd; border-radius: 9px; padding: 10px 12px; font: inherit; outline: none; background: white; }
        input:focus, select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(35,104,162,.12); }
        .hint { display: block; color: var(--muted); font-size: 12px; margin-top: 6px; }
        .error { display: block; color: var(--red); font-size: 12px; margin-top: 5px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); }
        .details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0; }
        .detail { padding: 18px 20px; border-bottom: 1px solid var(--line); }
        .detail:nth-child(odd) { border-right: 1px solid var(--line); }
        .detail-label { color: var(--muted); font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 7px; }
        .detail-value { font-weight: 700; line-height: 1.5; }
        .empty { text-align: center; padding: 60px 20px; color: var(--muted); }
        .pagination { padding: 16px; }
        .pagination nav { display: flex; justify-content: space-between; align-items: center; gap: 14px; color: var(--muted); font-size: 13px; }
        .pagination nav > div:last-child { display: flex; align-items: center; gap: 8px; }
        .pagination a, .pagination span { display: inline-flex; min-width: 34px; min-height: 34px; align-items: center; justify-content: center; padding: 6px 9px; border: 1px solid var(--line); border-radius: 7px; background: white; }
        .pagination svg { width: 16px; height: 16px; }
        @media (max-width: 760px) {
            .page-head { flex-direction: column; }
            .cards, .form-grid, .details { grid-template-columns: 1fr; }
            .field.full { grid-column: auto; }
            .detail:nth-child(odd) { border-right: 0; }
            .nav { padding: 0 16px; }
            .container { padding: 24px 16px 40px; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <nav class="nav">
            <a href="{{ route('stagiaires.index') }}" class="brand">
                Gestion des stagiaires
                <small>Agence Urbaine d’Oujda</small>
            </a>
            <a href="{{ route('stagiaires.create') }}" class="btn btn-light">+ Nouveau stagiaire</a>
        </nav>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
