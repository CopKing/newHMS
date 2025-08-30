<?php
include 'header.php';
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
    $confirm_password = safe_input($_POST['confirm_password']);
    
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
                
                // Insert into hospital table
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

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Hospital Registration</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Authentication</li>
                <li class="breadcrumb-item active">Register Hospital</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hospital Registration Form</h5>
                        <p class="text-muted">Please fill in all the information below to register your hospital. Your account will be reviewed by an administrator before approval.</p>
                        
                        <?php if($registration_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Registration Successful!</strong> Your hospital has been registered successfully. Please wait for admin approval. You will be notified once your account is approved.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form class="row g-3" method="POST" action="">
                            <!-- Hospital Information Section -->
                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Hospital Information</h5>
                                
                                <div class="col-md-12">
                                    <label for="hospital_name" class="form-label">Hospital Name</label>
                                    <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="<?php echo htmlspecialchars($hospital_name); ?>" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="address" class="form-label">Full Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="location" class="form-label">City/Location</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Contact Person Information Section -->
                            <div class="border rounded p-3 mb-4">
                                <h5 class="mb-3">Contact Person Information</h5>
                                
                                <div class="col-md-12">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <!-- Terms and Submit -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms" value="true" id="acceptTerms" required>
                                    <label class="form-check-label" for="acceptTerms">
                                        I agree and accept the <a href="#">terms and conditions</a> and <a href="#">privacy policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Register Hospital</button>
                            </div>
                            
                            <div class="col-12">
                                <p class="small mb-0">Already have an account? <a href="login.php?type=hospital">Login</a></p>
                                <p class="small mb-0">Registering as a patient? <a href="register.php">Patient Registration</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<?php
// Close database connection
$conn->close();
include 'footer.php';
?>