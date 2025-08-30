<?php
// Include necessary files
require('connection.php');
$hospital_name = $password = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get hospital name and password from form
    $hospital_name = safe_input($_POST['hospital_name'] ?? '');
    $password = safe_input($_POST['password'] ?? '');

    // First, get the hospital details using the hospital name
    $stmt = $conn->prepare("SELECT `Hospital_ID`, `UserID`, `Name`, `Approval_Status` FROM `hospital` WHERE `Name` = ? LIMIT 1");
    if($stmt) {
        $stmt->bind_param('s', $hospital_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0) {
            $hospital = $result->fetch_assoc();
            
            // Check if hospital is approved
            if($hospital['Approval_Status'] != 'Approved') {
                $status = $hospital['Approval_Status'];
                if($status == 'Pending') {
                    echo "Your hospital registration is pending approval. Please wait for admin approval.";
                } else if($status == 'Rejected') {
                    echo "Your hospital registration was rejected. Please contact admin for details.";
                } else {
                    echo "Your hospital account is not active. Please contact admin.";
                }
                exit();
            }
            
            // Now get the user details using the UserID from hospital table
            $user_id = $hospital['UserID'];
            $stmt = $conn->prepare("SELECT `id`, `name`, `email`, `password`, `role_id` FROM `users` WHERE `id` = ? LIMIT 1");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $user_result = $stmt->get_result();
            
            if($user_result && $user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                
                // Verify the password
                if(password_verify($password, $user['password'])) {
                    // For hospital logins, check if hospital is approved (already checked above)
                    // Verify this user has hospital role
                    if($user['role_id'] != 2) {
                        echo "This account is not registered as a hospital. Please use the patient login.";
                        exit();
                    }
                    
                    // Set session variables
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role_id'] = $user['role_id'];
                    $_SESSION['is_hospital'] = true;
                    $_SESSION['hospital_id'] = $hospital['Hospital_ID'];
                    $_SESSION['hospital_name'] = $hospital['Name'];

                    // Redirect to hospital dashboard
                    header("Location: hospital/index.php");
                    exit();
                } else {
                    echo "Incorrect password";
                }
            } else {
                echo "No user found for this hospital";
            }
        } else {
            echo "No hospital found with this name";
        }
    } else {
        // show DB prepare error when debugging
        if(isset($_GET['debug']) && $_GET['debug'] == '1') {
            echo "DB prepare failed: " . $conn->error;
        } else {
            echo "Database error occurred";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Hospital Login</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: May 30 2023 with Bootstrap v5.3.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/covid19.png" alt="">
                  <span class="d-none d-lg-block">Hospital Portal</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Hospital Login</h5>
                    <p class="text-center small">Enter your hospital name and password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" method="post" novalidate>
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Hospital Name</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="hospital_name" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your hospital name.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have a hospital account? <a href="register_hospital.php">Register Hospital</a></p>
                      <p class="small mb-0"><a href="login.php">Patient Login</a></p>
                    </div>
                  </form>

                </div>
              </div>

          

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>