<!DOCTYPE html>
<html>
<head>
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin:0;
            padding:0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .header {
            background: linear-gradient(90deg, #007bff 60%, #00f2fe 100%);
            color:#fff;
            padding:30px 0 18px 0;
            text-align:center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header h2 {
            font-size:2.2rem;
            letter-spacing:2px;
            margin-bottom:0;
        }
        .menu {
            background:#fff;
            padding:16px 0;
            text-align:center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .menu a {
            margin:0 18px;
            text-decoration:none;
            color:#007bff;
            font-weight:500;
            font-size:1.1rem;
            transition: color 0.2s;
        }
        .menu a:hover {
            color:#0056b3;
        }
        .content {
            flex: 1;
            padding:32px 18px;
            min-height:300px;
            max-width:900px;
            margin:0 auto;
        }
        .footer {
            background:#333;
            color:#fff;
            text-align:center;
            padding:18px 0;
            font-size:1rem;
            margin-top:auto;
        }
        /* Card style for dashboard, admin, student */
        .card {
            background:#fff;
            border-radius:12px;
            box-shadow:0 2px 12px rgba(0,0,0,0.07);
            padding:24px;
            margin-bottom:24px;
        }
        .table {
            width:100%;
            border-collapse:collapse;
            margin-bottom:24px;
        }
        .table th, .table td {
            border:1px solid #e3e3e3;
            padding:12px 8px;
            text-align:left;
        }
        .table th {
            background:#007bff;
            color:#fff;
        }
        .btn {
            display:inline-block;
            padding:8px 18px;
            background:#007bff;
            color:#fff;
            border-radius:6px;
            text-decoration:none;
            font-size:1rem;
            transition:background 0.2s;
        }
        .btn:hover {
            background:#0056b3;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>My Website</h2>
    </div>

    <div class="menu">
        <a href="<?= base_url('Home') ?>">Home</a>
        <a href="<?= base_url('login') ?>">Login</a>
    </div>

    <div class="content">
        <?= $content ?>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> My Website</p>
