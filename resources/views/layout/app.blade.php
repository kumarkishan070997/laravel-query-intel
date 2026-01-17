<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QueryIntel Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 0;
        }
        header {
            background: #1f2937;
            color: #fff;
            padding: 15px 20px;
        }
        .container {
            padding: 20px;
        }
        .queryintel-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .queryintel-table th,
        .queryintel-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        .queryintel-table th {
            background: #f3f4f6;
        }
        a {
            color: #2563eb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        .badge-slow {
            background: #fee2e2;
            color: #b91c1c;
        }
        .badge-fast {
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>
<body>

<header>
    <h2>🚀 QueryIntel Dashboard</h2>
</header>

<div class="container">
    @yield('content')
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>
