<?php
// Start the session
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// Handle signout confirmation
if(isset($_POST['confirm_signout'])) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to the login page
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Out - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signout-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .signout-icon {
            font-size: 5rem;
            color: #f44336;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 10px 30px;
        }
        .btn-outline-secondary {
            padding: 10px 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signout-container">
            <i class="bi bi-box-arrow-right signout-icon"></i>
            <h2>Sign Out</h2>
            <p class="mb-4">Are you sure you want to sign out of your account?</p>
            
            <form method="POST" action="">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="submit"  name="confirm_signout" class="btn btn-primary me-md-2">Yes, Sign Out</button>
                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>