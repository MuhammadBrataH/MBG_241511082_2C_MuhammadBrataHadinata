<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> | Bahan Baku MBG</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        :root {
            --sidebar-width: 250px;
        }
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50; /* Darker blue/grey for sidebar */
            color: white;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 10px 15px;
            border-left: 5px solid transparent;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #34495e;
            border-left-color: #3498db; /* Bright blue accent */
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-left: calc(var(--sidebar-width) - 20px); /* Adjust for overlap */
            width: calc(100% - var(--sidebar-width) + 20px);
            transition: all 0.3s;
        }
        /* Responsiveness for mobile */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -var(--sidebar-width);
                z-index: 1050;
                box-shadow: 2px 0 5px rgba(0,0,0,0.5);
            }
            .main-content {
                margin-left: 0;
            }
            .navbar {
                 width: 100%;
                 margin-left: 0;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php $role = session()->get('role'); ?>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <h5 class="text-center text-uppercase fw-bold mb-4 text-warning">MBG <?= strtoupper($role) ?></h5>
        <div class="ms-3 mb-2 text-white-50">Navigasi Utama</div>
        
        <!-- Render specific sidebar based on role -->
        <?= $this->renderSection('sidebar') ?>
        
        <div class="mt-auto p-3 text-center">
            <small class="text-white-50">Logged in as:</small>
            <p class="mb-0 fw-bold text-white"><?= session()->get('name') ?? 'User' ?></p>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top mb-4 rounded-3 shadow-sm">
            <div class="container-fluid">
                <!-- Toggle button for mobile -->
                <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <h3 class="navbar-brand mb-0 h1 ms-3"><?= $title ?? 'Dashboard' ?></h3>
                
                <div class="ms-auto d-flex align-items-center">
                    <span class="navbar-text me-3 d-none d-lg-block">
                        Halo, <b><?= session()->get('name') ?></b> (<?= strtoupper($role) ?>)
                    </span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-danger d-flex align-items-center">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="container-fluid">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Custom Scripts -->
    <?= $this->renderSection('scripts') ?>
    
    <script>
        // Sederhana script untuk menangani sidebar di mobile
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.querySelector('[data-bs-target="#sidebarCollapse"]');
            
            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none'); // Sembunyikan/tampilkan di mobile
                });
            }
        });
    </script>
</body>
</html>
