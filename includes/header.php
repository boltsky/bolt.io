<?php
// Code to check cookies for the remember me functionality
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login']) && isset($_COOKIE['remember_token'])) {
    // Verify remember me cookie
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_COOKIE['user_login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
   
    if ($user && password_verify($_COOKIE['remember_token'], $user['remember_token'])) {
        // Re-establish session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['names'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['avatar_path'] = $user['avatar_path'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Cyber Defence Forum</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    
    <style>
        :root {
            --primary-bg: #0b0f2f;
            --secondary-bg: #1a1e3b;
            --accent-color: #6a5acd;
        }

        body {
            background-color: var(--primary-bg);
            color: #fff;
        }

        .navbar {
            background-color: var(--secondary-bg) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .navbar::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, var(--accent-color), var(--secondary-bg));
            z-index: -1;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: #fff !important;
        }

        .nav-link {
            color: #fff !important;
            padding: 10px 15px !important;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .nav-link:hover {
            background-color: var(--accent-color);
            transform: scale(1.1);
        }

        .avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: var(--secondary-bg);
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Cyber Defence Forum</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <img src="<?php echo isset($_SESSION['avatar_path']) ? $_SESSION['avatar_path'] : 'default-avatar.png'; ?>" 
                             class="avatar-img" 
                             data-bs-toggle="dropdown" 
                             aria-expanded="false">
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Manage Account</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>