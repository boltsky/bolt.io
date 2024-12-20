<?php 
require "includes/header.php";
require_once("config/dbhandler.php");

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (empty($_POST['names']) || empty($_POST['email']) || empty($_POST['pwd']) || empty($_POST['confirm_pwd'])) {
            throw new Exception("All fields are required");
        }

        if ($_POST['pwd'] !== $_POST['confirm_pwd']) {
            throw new Exception("Passwords do not match");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        $names = htmlspecialchars($_POST['names']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);

        $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        if ($checkEmail->rowCount() > 0) {
            throw new Exception("Email already registered");
        }

        $avatarPath = null;
        if (isset($_FILES['avator']) && $_FILES['avator']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $_FILES['avator']['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG and GIF allowed.");
            }

            $uploadDir = "Images/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($_FILES['avator']['name'], PATHINFO_EXTENSION);
            $avatarPath = $uploadDir . uniqid() . '_' . time() . '.' . $extension;

            if (!move_uploaded_file($_FILES['avator']['tmp_name'], $avatarPath)) {
                throw new Exception("Failed to upload image");
            }
        }

        $insert = $conn->prepare("
            INSERT INTO users (names, email, password, avatar_path) 
            VALUES (?, ?, ?, ?)
        ");
        
        $insert->execute([$names, $email, $pwd, $avatarPath]);
        $success = "Registration successful!";
        
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<style>
    .register-form {
        background-color: #1a1e3b;
        padding: 20px;
        border-radius: 5px;
        max-width: 400px;
        margin: 40px auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .register-form h2 {
        margin-bottom: 20px;
        color: #fff;
        text-align: center;
    }
    .register-form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #555;
        border-radius: 5px;
        background-color: #0b0f2f;
        color: #fff;
    }
    .register-form button {
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
    .register-form button:hover {
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
    .form-label {
        color: #fff;
        margin-bottom: 5px;
        display: block;
    }
</style>

<div class="register-form">
    <h2>Register</h2>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input placeholder="Names" type="text" name="names" required />
        <input placeholder="Email" type="email" name="email" required />
        <input placeholder="Password" type="password" name="pwd" id="pwd" required />
        <input placeholder="Confirm Password" type="password" name="confirm_pwd" id="confirm_pwd" required />
        <label class="form-label">Upload Image</label>
        <input accept="image/*" id="avatar" type="file" name="avator" />
        <button type="submit">Register</button>
        <div class="form-footer">
            <p>Already a Member ? <a href="login.php">Login here</a></p>
        </div>
    </form>
</div>

<script>
function validateForm() {
    const pwd = document.getElementById('pwd');
    const confirmPwd = document.getElementById('confirm_pwd');
    
    if (pwd.value !== confirmPwd.value) {
        alert("Passwords do not match!");
        return false;
    }
    
    if (pwd.value.length < 8) {
        alert("Password must be at least 8 characters long!");
        return false;
    }
    
    return true;
}
</script>

<?php require "includes/footer.php"; ?>
