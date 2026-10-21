<?php
session_start();
include "../config/db.php";

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, role, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password,$user['password'])) {
                $_SESSION['user_id']   =$user['id'];
                $_SESSION['full_name'] =$user['full_name'];
                $_SESSION['role']      =$user['role'];

                // Redirect based on role (matching uppercase values)
                if ($user['role'] === 'admin') { header("Location: ../admin/admin_dashboard.php"); exit(); } elseif ($user['role'] === 'ARTIST') {
                    header("Location: ../artist/artist_dashboard.php");
                    exit();
                } else {
                    header("Location: ../users/customer.php");
                    exit();
                }
            } else {
                $error_msg = "Invalid email or password.";
            }
        } else {
            $error_msg = "Invalid email or password.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #000000;
            color: #e4e4e7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #121212;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            width: 100%;
            max-width: 420px;
            padding: 40px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
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

        .form-control.password-input {
            padding-right: 42px;
        }

        .form-control:focus {
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Password view icon button */
        .toggle-password {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #71717a;
            cursor: pointer;
            font-size: 0.95rem;
            outline: none;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #ffffff;
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

    <div class="login-card">
        <div class="brand-header">
            <div class="brand-title">HLR INK <span>STUDIO</span></div>
            <p class="brand-subtitle">Sign in to manage your appointments</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control password-input" placeholder="••••••••" required>
                   
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="../auth/register.php">Register</a>
        </div>

        <!-- Back to Home Link -->
        <a href="../index.php" class="back-home">Back to Home</a>
    </div>


</body>

</html>