<?php
session_start();
// Jika sudah login, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$status = $_GET['status'] ?? '';
$messages = [
    'failed' => 'Username atau password salah!',
    'empty' => 'Harap isi semua field!',
    'error' => 'Terjadi kesalahan sistem.',
    'logout' => 'Anda telah berhasil logout.',
    'err_pass' => 'Password salah!',
    'err_user' => 'Username tidak ditemukan!'
];
?>
<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRIS Aradea Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-bg: #f8f9fa;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .login-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            border-bottom: none;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(76, 201, 240, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(67, 97, 238, 0.3);
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
            padding-left: 5px;
        }
        
        .footer-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="d-flex align-items-center h-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-header card-header-custom">
                        <div class="logo-container">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                        <h3 class="mb-1">HRIS System</h3>
                        <p class="mb-0 opacity-75">Aradea Store Management</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (isset($messages[$status])): ?>
                        <div class="alert alert-<?= $status == 'logout' ? 'success' : 'danger' ?> alert-custom alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= $status == 'logout' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                            <?= htmlspecialchars($messages[$status]) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <h4 class="card-title text-center mb-4">Masuk ke Akun Anda</h4>
                        
                        <form action="login_proses.php" method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">
                                    <i class="fas fa-user me-1"></i> Username
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-at text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Masukkan username"
                                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                           required
                                           autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-login btn-lg text-white">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="#" class="text-decoration-none">
                                    <small><i class="fas fa-question-circle me-1"></i> Lupa password?</small>
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer text-center py-3 footer-text">
                        <small>
                            &copy; <?= date('Y') ?> Aradea Store. Hak Cipta Dilindungi.
                            <br>
                            <span class="text-muted">v2.0.0</span>
                        </small>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <div class="d-flex justify-content-center gap-3">
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block shadow-sm">
                                <i class="fas fa-shield-alt text-primary fa-lg"></i>
                            </div>
                            <p class="mt-2 mb-0 small">Keamanan Terjamin</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block shadow-sm">
                                <i class="fas fa-bolt text-success fa-lg"></i>
                            </div>
                            <p class="mt-2 mb-0 small">Cepat & Responsif</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block shadow-sm">
                                <i class="fas fa-mobile-alt text-info fa-lg"></i>
                            </div>
                            <p class="mt-2 mb-0 small">Mobile Friendly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Harap isi username dan password!');
            }
        });
        
        // Auto-hide alert after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>