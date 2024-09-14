<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email_check_query = "SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['warning_message'] = "Email or Username already exists!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Registration successful!";
            header('location: login.php');
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">

</head>

<body class="bg-dark text-dark">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-light shadow-sm">
                    <div class="card-header text-center bg-secondary text-white">
                        <h3>Sign Up</h3>
                    </div>
                    <?php if (isset($_SESSION['warning_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['warning_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['warning_message']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>
                    <div class="card-body">
                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-secondary">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="text-center mt-1">
                        <p class="mb-0">Already have an account?</p>
                        <p>
                            <a href="login.php" class="link-primary">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-secondary text-light mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Team Members</h5>
                    <ul class="list-unstyled">
                        <li>นายไชยวัฒน์ มิตรานนท์ 610-02</li>
                        <li>นางสาวพิมพ์ชนก สุขนุ่ม 610-33</li>
                        <li>นางสาววรณัน บุหงาเกษมสุข 610-32</li>
                        <li>นางสาวสิรินดา อยู่เมฆ 610-35</li>
                    </ul>
                </div>

            </div>
            <div class="text-center py-3">
                <small>&copy; 2024 Nugget. All Rights Reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>