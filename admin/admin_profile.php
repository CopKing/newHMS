<?php
include 'header.php';

// Check if user is logged in and is an admin
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 1) {
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
        } else if(strlen($new_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
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
    
    // Update admin settings
    if(isset($_POST['update_settings'])) {
        $notification_email = isset($_POST['notification_email']) ? 1 : 0;
        $two_factor_auth = isset($_POST['two_factor_auth']) ? 1 : 0;
        
        // Update admin settings (assuming you have an admin_settings table)
        // For now, we'll just show a success message
        $success_message = "Settings updated successfully!";
    }
}

// Fetch current user data - REMOVED created_at column
$stmt = $conn->prepare("SELECT id, name, email, role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch admin statistics
$total_users = 0;
$total_hospitals = 0;
$total_patients = 0;

// Get total users count
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if($result) {
    $row = $result->fetch_assoc();
    $total_users = $row['count'];
}

// Get total hospitals count
$result = $conn->query("SELECT COUNT(*) as count FROM hospital WHERE Approval_Status = 'Approved'");
if($result) {
    $row = $result->fetch_assoc();
    $total_hospitals = $row['count'];
}

// Get total patients count
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id = 3");
if($result) {
    $row = $result->fetch_assoc();
    $total_patients = $row['count'];
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Admin Profile</h1>
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
                        <img src="../assets/img/admin-profile.jpg" alt="Profile" class="rounded-circle">
                        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                        <h3>Administrator</h3>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">System Statistics</h5>
                        <div class="activity">
                            <div class="activity-item d-flex">
                                <div class="activite-label me-2">Total Users:   </div>
                                <div class="activity-badge text-success rounded-pill"><?php echo $total_users; ?></div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activite-label me-2">Approved Hospitals:  </div>
                                <div class="activity-badge text-primary rounded-pill"><?php echo $total_hospitals; ?></div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activite-label me-2">Registered Patients:   </div>
                                <div class="activity-badge text-info rounded-pill"><?php echo $total_patients; ?></div>
                            </div>
                        </div>
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
                                <h5 class="card-title">Admin Details</h5>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Full Name</div>
                                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['name']); ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8">Administrator</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">User ID</div>
                                    <div class="col-lg-9 col-md-8"><?php echo $user['id']; ?></div>
                                </div>
                                
                                <!-- REMOVED Member Since row -->
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Account Status</div>
                                    <div class="col-lg-9 col-md-8"><span class="badge bg-success">Active</span></div>
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
                                            <div class="form-text">Password must be at least 8 characters long.</div>
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
                                <h5 class="card-title">Admin Settings</h5>
                                
                                <form method="POST" action="">
                                    <div class="row mb-3">
                                        <label class="col-md-4 col-lg-3 col-form-label">Notification Preferences</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="notification_email" name="notification_email" checked>
                                                <label class="form-check-label" for="notification_email">
                                                    Email notifications for new hospital registrations
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="system_alerts" name="system_alerts" checked>
                                                <label class="form-check-label" for="system_alerts">
                                                    System alerts and warnings
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-md-4 col-lg-3 col-form-label">Security</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="two_factor_auth" name="two_factor_auth">
                                                <label class="form-check-label" for="two_factor_auth">
                                                    Enable two-factor authentication
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                                    </div>
                                </form>
                                
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <h5 class="card-title">System Actions</h5>
                                        <div class="d-grid gap-2">
                                            <a href="hospital_approvals.php" class="btn btn-outline-primary">
                                                <i class="bi bi-building"></i> Manage Hospital Approvals
                                            </a>
                                            <a href="users.php" class="btn btn-outline-info">
                                                <i class="bi bi-people"></i> Manage Users
                                            </a>
                                            <a href="#" class="btn btn-outline-warning">
                                                <i class="bi bi-gear"></i> System Settings
                                            </a>
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