<?php
// Include necessary files
require('connection.php');
$email = $password = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if this is a hospital login
    $is_hospital_login = isset($_POST['login_type']) && $_POST['login_type'] == 'hospital';
    
    // allow users to login using email OR username (name)
    $identifier = safe_input($_POST['email'] ?? ''); // form field remains named 'email'
    $password = safe_input($_POST['password'] ?? '');

    $user = null;

    // use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT `id`, `name`, `email`, `password`, `role_id` FROM `users` WHERE `email` = ? OR `name` = ? LIMIT 1");
    if($stmt) {
        $stmt->bind_param('ss', $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
    } else {
        // show DB prepare error when debugging
        if(isset($_GET['debug']) && $_GET['debug'] == '1') {
            echo "DB prepare failed: " . $conn->error;
        }
    }

    if($user) {
        if(password_verify($password, $user['password'])) {
            // For hospital logins, check if hospital is approved
            if($is_hospital_login) {
                // Verify this user has hospital role
                if($user['role_id'] != 2) {
                    echo "This account is not registered as a hospital. Please use the patient login.";
                    exit();
                }
                
                // Check if hospital is approved
                $stmt = $conn->prepare("SELECT `Approval_Status` FROM `hospital` WHERE `UserID` = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($result && $result->num_rows > 0) {
                    $hospital = $result->fetch_assoc();
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
                } else {
                    echo "No hospital record found for this account.";
                    exit();
                }
                $stmt->close();
            }
            
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role_id'] = $user['role_id'];

            // Redirect based on role
            if($user['role_id'] == 1) {
                $_SESSION['is_admin'] = true;
                header("Location: admin/index.php");
                exit();
            } else if($user['role_id'] == 2) {
                
                header("Location: hospital/index.php");
                exit();
            } else {
                $_SESSION['is_patient'] = true;
                header("Location: patient/index.php");
                exit();
            }
        } else {
            echo "Incorrect password";
        }
    } else {
        // no user found
        echo "No user found with this email";
        // optional debug block: try direct query and similar counts when ?debug=1
        if(isset($_GET['debug']) && $_GET['debug'] == '1') {
            echo "\nSearched for identifier: '" . htmlspecialchars($identifier) . "'.\n";
            $dbg = $conn->real_escape_string($identifier);
            $dq = "SELECT id, name, email FROM users WHERE email = '$dbg' OR name = '$dbg'";
            $dr = $conn->query($dq);
            if($dr === false) {
                echo "Direct query error: " . $conn->error;
            } else {
                echo "Direct query rows: " . $dr->num_rows . "\n";
                if($dr->num_rows > 0) {
                    echo "Matching rows (direct query): ";
                    pr($dr->fetch_assoc());
                } else {
                    $like = "SELECT COUNT(*) as c FROM users WHERE name LIKE '%$dbg%' OR email LIKE '%$dbg%'";
                    $lr = $conn->query($like);
                    $count = ($lr && $lr->fetch_assoc()) ? $lr->fetch_assoc()['c'] : 0;
                    echo "Similar rows found: " . $count;
                }
            }
        }
    }
}

// Determine login type from URL parameter
$login_type = isset($_GET['type']) ? $_GET['type'] : '';
$is_hospital_page = ($login_type == 'hospital');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $is_hospital_page ? 'Hospital Login' : 'Login Page'; ?></title>
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
                  <span class="d-none d-lg-block"><?php echo $is_hospital_page ? 'Hospital Portal' : 'Login'; ?></span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4"><?php echo $is_hospital_page ? 'Hospital Login' : 'Login to Your Account'; ?></h5>
                    <p class="text-center small"><?php echo $is_hospital_page ? 'Enter your hospital credentials to login' : 'Enter your username & password to login'; ?></p>
                  </div>

                  <form class="row g-3 needs-validation" method="post" novalidate>
                    <!-- Hidden field to identify login type -->
                    <input type="hidden" name="login_type" value="<?php echo $is_hospital_page ? 'hospital' : ''; ?>">
                    
                    <div class="col-12">
                      <label for="yourUsername" class="form-label"><?php echo $is_hospital_page ? 'Hospital Username' : 'Username'; ?></label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="email" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your <?php echo $is_hospital_page ? 'hospital username' : 'email'; ?>.</div>
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
                      <p class="big mb-0">
                        <?php if($is_hospital_page): ?>
                          Don't have a hospital account? <a href="register_hospital.php">Register Hospital</a>
                        <?php else: ?>
                          Don't have account? <a href="register.php">Create an account</a>
                        <?php endif; ?>
                      </p>
                      <p class="big mb-0">
                        <?php if($is_hospital_page): ?>
                          Want to go back?<a href="index.php">back to index</a>
                        <?php else: ?>
                          <a href="login.php?type=hospital">Hospital Login</a>
                        <?php endif; ?>
                      </p>
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