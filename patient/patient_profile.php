<?php
include 'header.php';

// Check if user is logged in and is a patient
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update profile
    if(isset($_POST['update_profile'])) {
        $name = safe_input($_POST['name']);
        $email = safe_input($_POST['email']);
        
        // Validate inputs
        if(empty($name) || empty($email)) {
            $error_message = "Name and email are required fields.";
        } else {
            // Check if email is already used by another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                $error_message = "Email is already in use by another account.";
            } else {
                // Update user profile
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $email, $user_id);
                
                if($stmt->execute()) {
                    $success_message = "Profile updated successfully!";
                    // Update session variables
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                } else {
                    $error_message = "Failed to update profile. Please try again.";
                }
                $stmt->close();
            }
        }
    }
    
    // Change password
    if(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate inputs
        if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All password fields are required.";
        } else if($new_password != $confirm_password) {
            $error_message = "New passwords do not match.";
        } else if(strlen($new_password) < 6) {
            $error_message = "Password must be at least 6 characters long.";
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if(password_verify($current_password, $user['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if($stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Failed to change password. Please try again.";
                }
                $stmt->close();
            } else {
                $error_message = "Current password is incorrect.";
            }
        }
    }
    
    // Delete account
    if(isset($_POST['delete_account'])) {
        // Verify password before deletion
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if(password_verify($password, $user['password'])) {
            // Delete user account
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            
            if($stmt->execute()) {
                // Destroy session and redirect to login
                session_destroy();
                header("Location: ../login.php?account_deleted=1");
                exit();
            } else {
                $error_message = "Failed to delete account. Please try again.";
            }
            $stmt->close();
        } else {
            $error_message = "Password is incorrect. Account deletion cancelled.";
        }
    }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT id, name, email, role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>My Profile</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="../assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                        <h3>Patient</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview" type="button" role="tab" aria-selected="true">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" type="button" role="tab" aria-selected="false">Edit Profile</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password" type="button" role="tab" aria-selected="false">Change Password</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings" type="button" role="tab" aria-selected="false">Settings</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2">
                            <!-- Display success/error messages -->
                            <?php if(!empty($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Profile Overview Tab -->
                            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                                <h5 class="card-title">Profile Details</h5>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['name']); ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Account Type</div>
                                    <div class="col-lg-9 col-md-8">Patient</div>
                                </div>
                            </div>

                            <!-- Edit Profile Tab -->
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
                                <h5 class="card-title">Profile Settings</h5>
                                
                                <form method="POST" action="">
                                    <div class="row mb-3">
                                        <label for="profileName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" type="text" class="form-control" id="profileName" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="profileEmail" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" class="form-control" id="profileEmail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form><!-- End Profile Edit Form -->
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane fade pt-3" id="profile-change-password" role="tabpanel">
                                <h5 class="card-title">Change Password</h5>
                                
                                <form method="POST" action="">
                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="current_password" type="password" class="form-control" id="currentPassword" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="new_password" type="password" class="form-control" id="newPassword" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="confirm_password" type="password" class="form-control" id="renewPassword" required>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Settings Tab -->
                            <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">
                                <h5 class="card-title">Account Settings</h5>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Delete Account</h5>
                                        <p class="card-text">Once you delete your account, there is no going back. Please be certain.</p>
                                        
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                            Delete Account
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Delete Account Modal -->
                                <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                                                <form method="POST" action="">
                                                    <div class="mb-3">
                                                        <label for="deletePassword" class="form-label">Please enter your password to confirm:</label>
                                                        <input type="password" class="form-control" id="deletePassword" name="password" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Bordered Tabs -->
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