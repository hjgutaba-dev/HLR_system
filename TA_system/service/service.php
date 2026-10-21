<?php
session_start();

include "../config/db.php";

// Admin access control
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// Query services table
$sql = "SELECT * FROM services ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Check if query failed
if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - PATIK STUDIO</title>
</head>

<body>

    <h1>PATIK STUDIO</h1>
    <h2>Patik Services</h2>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($service = mysqli_fetch_assoc($result)) {
    ?>

        <hr>

        <h3>
            <?php echo ($service["service_name"]); ?>
        </h3>

        <p>
            <?php echo ($service["description"]); ?>
        </p>

            
        <!-- <a href="../auth/appointment.php">
            Book Appointment
        </a> -->

    <?php
        }
    } else {
    ?>

        <p>No services available at the moment.</p>

    <?php
    }
    ?>

    <br><br>

    <!-- Redirect based on user role -->
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") { ?>
        <a href="../admin/admin_dashboard.php">Back to Admin Dashboard</a>
    <?php } else { ?>
        <a href="../users/customer.php">Back to Dashboard</a>
    <?php } ?>

</body>

</html>