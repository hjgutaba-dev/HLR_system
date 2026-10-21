<?php
session_start();
include "../config/db.php";

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $password  = trim($_POST["password"]);
    $role      = "customer";

    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $error_msg = "An account with this email already exists.";
        mysqli_stmt_close($check_stmt);
    } else {
        mysqli_stmt_close($check_stmt);

        $profile_image = null;
        if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === 0) {
            $ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $allowed = ["jpg", "jpeg", "png", "webp"];

            if (in_array($ext, $allowed)) {
                $new_name = "user_" . time() . "_" . rand(1000, 9999) . "." . $ext;
                $target_dir = "../uploads/profile_image/";

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_dir . $new_name)) {
                    $profile_image = $new_name;
                }
            } else {
                $error_msg = "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
            }
        }

        if (empty($error_msg)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, phone, role, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $email, $phone, $role, $hashed_password, $profile_image);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: ../auth/login.php?msg=registered");
                exit();
            } else {
                $error_msg = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HLR INK STUDIO</title>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #000000;
            color: #e4e4e7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .register-card {
            background: #121212;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            width: 100%;
            max-width: 480px;
            padding: 40px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            position: relative;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .brand-title span {
            color: #888891;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: #888891;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper > i {
            position: absolute;
            left: 14px;
            color: #71717a;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px 12px 42px;
            background: #18181d;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: rgba(255, 255, 255, 0.4);
        }

        .file-control {
            padding: 10px 14px 10px 42px;
        }

        .file-control::-webkit-file-upload-button {
            background: #ffffff;
            color: #000000;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .file-control::-webkit-file-upload-button:hover {
            background: #d4d4d8;
        }

        .btn-submit {
            width: 100%;
            background: #ffffff;
            color: #000000;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #d4d4d8;
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #888891;
        }

        .auth-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #888891;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 15px;
            transition: color 0.2s ease;
        }

        .back-home:hover {
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="register-card">
         <div class="brand-header">
            <div class="brand-title">HLR INK <span>STUDIO</span></div>
            <p class="brand-subtitle">Create an account to get started</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="09123456789" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Photo</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-image"></i>
                    <input type="file" id="profile_image" name="profile_image" class="form-control file-control" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-user-plus"></i> Register
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login</a>
        </div>

        <!-- Back to Home Link -->
        <a href="../index.php" class="back-home">Back to Home</a>
    </div>

</body>

</html>