<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Pemantauan Bahan Baku MBG</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style> 
        body { 
            background-color: #f8f9fa; 
        } 
        .card { 
            margin-top: 100px; 
            border: none;
        } 
        .card-header {
            border-radius: .35rem .35rem 0 0 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h4>MBG - Pemantauan Bahan Baku</h4>
                    <p class="mb-0">Akses sebagai Petugas Gudang atau Dapur</p>
                </div>
                <div class="card-body">
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">Login</button>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>Contoh Akun: Gudang A (gudang.a@example.com / gudang.a)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>