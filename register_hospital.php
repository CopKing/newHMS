<?php
// Include connection file directly instead of header.php
require('connection.php');
// Initialize variables
$name = $email = $password = $confirm_password = '';
$hospital_name = $address = $location = '';
$registration_success = false;
$error = '';

// Check if form is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user form data
    $name = safe_input($_POST['name']);
    $email = safe_input($_POST['email']);
    $password = safe_input($_POST['password']);
    $confirm_password = safe_input($_POST['confirm_password']); // Now we capture this
    
    // Get hospital form data
    $hospital_name = safe_input($_POST['hospital_name']);
    $address = safe_input($_POST['address']);
    $location = safe_input($_POST['location']);
    
    // Validate passwords match
    if($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } 
    // Validate required fields
    else if(empty($name) || empty($email) || empty($password) || empty($hospital_name) || empty($address) || empty($location)) {
        $error = "All fields are required!";
    }
    else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $error = "Email already exists! Please use a different email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Start transaction to ensure both tables are updated
            $conn->begin_transaction();
            
            try {
                // Insert into users table with role_id = 2 (Hospital)
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, 2)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                $stmt->execute();
                
                // Get the last inserted user ID
                $user_id = $conn->insert_id;
                
                // Insert into hospital table with the new user ID
                $stmt = $conn->prepare("INSERT INTO hospital (UserID, Name, Address, Location, Approval_Status) VALUES (?, ?, ?, ?, 'Pending')");
                $stmt->bind_param("isss", $user_id, $hospital_name, $address, $location);
                $stmt->execute();
                
                // Commit transaction
                $conn->commit();
                $registration_success = true;
                
                // Clear form data
                $name = $email = $password = $confirm_password = '';
                $hospital_name = $address = $location = '';
                
            } catch (Exception $e) {
                // Rollback transaction if there was an error
                $conn->rollback();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Create a Hospital Account</title>
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
                  <span class="d-none d-lg-block">HMS</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create a Hospital Account</h5>
                    <p class="text-center small">Enter hospital details to create account</p>
                  </div>

                  <?php if($registration_success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Registration Successful!</strong> Your hospital has been registered successfully. Please wait for admin approval.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>
                  
                  <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> <?php echo $error; ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>

                  <form action="register_hospital.php" method="POST" class="row g-3 needs-validation" novalidate>
                    <!-- Hospital Information Section -->
                    <div class="mb-3">
                      <h6 class="mb-3 pb-1 border-bottom">Hospital Information</h6>
                      
                      <div class="col-12 mb-3">
                        <label for="hospitalName" class="form-label">Hospital Name</label>
                        <input type="text" name="hospital_name" class="form-control" id="hospitalName" value="<?php echo htmlspecialchars($hospital_name); ?>" required>
                        <div class="invalid-feedback">Please enter hospital name.</div>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($address); ?></textarea>
                        <div class="invalid-feedback">Please enter address.</div>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label for="location" class="form-label">City/Location</label>
                        <input type="text" name="location" class="form-control" id="location" value="<?php echo htmlspecialchars($location); ?>" required>
                        <div class="invalid-feedback">Please enter location.</div>
                      </div>
                    </div>
                    
                    <!-- Contact Person Information Section -->
                    <div class="mb-3">
                      <h6 class="mb-3 pb-1 border-bottom">Contact Person Information</h6>
                      
                      <div class="col-12 mb-3">
                        <label for="yourName" class="form-label">Your Name</label>
                        <input type="text" name="name" class="form-control" id="yourName" value="<?php echo htmlspecialchars($name); ?>" required>
                        <div class="invalid-feedback">Please enter your name.</div>
                      </div>

                      <div class="col-12 mb-3">
                        <label for="yourEmail" class="form-label">Your Email</label>
                        <input type="email" name="email" class="form-control" id="yourEmail" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                      </div>

                      <div class="col-12 mb-3">
                        <label for="yourPassword" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="yourPassword" required>
                        <div class="invalid-feedback">Please enter password.</div>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms" value="true" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="hospital_login.php">Login</a></p>
                      <p class="small mb-0">Registering as a patient? <a href="register.php">Create an account</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                Designed by <a href="#">HMS</a>
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

<?php
// Close database connection
$conn->close();
?>