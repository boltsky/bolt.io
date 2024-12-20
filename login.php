<?php 
require "includes/header.php";
require_once("config/dbhandler.php");

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate required fields
        if (empty($_POST['email']) || empty($_POST['pwd'])) {
            throw new Exception("All fields are required");
        }

        // Sanitize inputs
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check user credentials
        $stmt = $conn->prepare("SELECT id, names, email, password, avatar_path FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Inside the try block, after password verification and before redirect
if ($user && password_verify($_POST['pwd'], $user['password'])) {
    // Start session and store user data
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['names'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['avatar_path'] = $user['avatar_path'];
    
    // Add remember me functionality here
    if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
        // Set secure cookies that last for 30 days
        setcookie("user_login", $user['email'], [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Store a secure token in the database and cookie
        $token = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        
        // Update or insert remember_token in your users table
        $updateToken = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $updateToken->execute([$tokenHash, $user['id']]);
        
        setcookie("remember_token", $token, [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    // Redirect to dashboard
    header("Location: index.php");
    exit();
} else {
    throw new Exception("Invalid email or password");
}


    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<style>
    .login-form {
        background-color: #1a1e3b;
        padding: 20px;
        border-radius: 5px;
        max-width: 400px;
        margin: 40px auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .login-form h2 {
        margin-bottom: 20px;
        color: #fff;
        text-align: center;
    }
    .login-form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #555;
        border-radius: 5px;
        background-color: #0b0f2f;
        color: #fff;
    }
    .login-form button {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: #6a5acd;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .login-form button:hover {
        background-color: #5a4dbd;
    }
    .error-message {
        color: #ff4444;
        margin-bottom: 10px;
        text-align: center;
    }
    .success-message {
        color: #00C851;
        margin-bottom: 10px;
        text-align: center;
    }
    .form-footer {
        margin-top: 15px;
        text-align: center;
        color: #fff;
    }
    .form-footer a {
        color: #6a5acd;
        text-decoration: none;
    }
    .form-footer a:hover {
        text-decoration: underline;
    }
    .remember-me {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: #fff;
    }
    .remember-me input {
        width: auto;
        margin-right: 10px;
    }
</style>

<div class="login-form">
    <h2>Login</h2>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()">
        <input placeholder="Email" type="email" name="email" id="email" required />
        <input placeholder="Password" type="password" name="pwd" id="pwd" required />
        
        <div class="remember-me">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>

        <button type="submit">Login</button>

        <div class="form-footer">
            <p>Forgot your password? <a href="reset_password.php">Reset it here</a></p>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </form>
</div>

<script>
function validateForm() {
    const email = document.getElementById('email');
    const pwd = document.getElementById('pwd');
    
    if (!email.value || !pwd.value) {
        alert("All fields are required!");
        return false;
    }
    
    // Basic email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        alert("Please enter a valid email address!");
        return false;
    }
    
    return true;
}
</script>

<?php require "includes/footer.php"; ?>
