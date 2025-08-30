<?php
include 'header.php';

// Check if user is logged in and is a hospital
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Get hospital ID - try different session variables
$hospital_id = 0;
if(isset($_SESSION['Hospital_ID'])) {
    $hospital_id = $_SESSION['Hospital_ID'];
} elseif(isset($_SESSION['hospital_id'])) {
    $hospital_id = $_SESSION['hospital_id'];
} else {
    // Try to get hospital ID from database using user ID
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT Hospital_ID FROM hospital WHERE UserID = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result && $row = $result->fetch_assoc()) {
        $hospital_id = $row['Hospital_ID'];
        $_SESSION['hospital_id'] = $hospital_id; // Store in session for future use
    }
}

$success_message = '';
$error_message = '';

// Initialize hospital and user data
$hospital = [];
$user = [];

// Get hospital details
if($hospital_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM hospital WHERE Hospital_ID = ?");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hospital = $result->fetch_assoc();
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update hospital details
    if(isset($_POST['update_hospital'])) {
        $name = safe_input($_POST['name']);
        $address = safe_input($_POST['address']);
        $location = safe_input($_POST['location']);
        $contact = safe_input($_POST['contact']);

        $stmt = $conn->prepare("UPDATE hospital SET Name = ?, Address = ?, Location = ?, Contact = ? WHERE Hospital_ID = ?");
        $stmt->bind_param("ssssi", $name, $address, $location, $contact, $hospital_id);

        if($stmt->execute()) {
            $success_message = "Hospital details updated successfully!";
            // Refresh hospital data
            $stmt = $conn->prepare("SELECT * FROM hospital WHERE Hospital_ID = ?");
            $stmt->bind_param("i", $hospital_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $hospital = $result->fetch_assoc();
        } else {
            $error_message = "Failed to update hospital details. Please try again.";
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
            $user_data = $result->fetch_assoc();
            
            if(password_verify($current_password, $user_data['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if($stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Failed to change password. Please try again.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        }
    }
    
    // Update services
    if(isset($_POST['update_services'])) {
        $rt_pcr = isset($_POST['rt_pcr']) ? 1 : 0;
        $rapid_antigen = isset($_POST['rapid_antigen']) ? 1 : 0;
        $antibody = isset($_POST['antibody']) ? 1 : 0;
        $home_collection = isset($_POST['home_collection']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE hospital SET rt_pcr = ?, rapid_antigen = ?, antibody = ?, home_collection = ? WHERE Hospital_ID = ?");
        $stmt->bind_param("iiiii", $rt_pcr, $rapid_antigen, $antibody, $home_collection, $hospital_id);
        
        if($stmt->execute()) {
            $success_message = "Services updated successfully!";
            // Refresh hospital data
            $stmt = $conn->prepare("SELECT * FROM hospital WHERE Hospital_ID = ?");
            $stmt->bind_param("i", $hospital_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $hospital = $result->fetch_assoc();
        } else {
            $error_message = "Failed to update services. Please try again.";
        }
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Hospital Profile</h1>
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
                        <img src="../assets/img/hospital-profile.jpg" alt="Profile" class="rounded-circle">
                        <h2><?php echo isset($hospital['Name']) ? htmlspecialchars($hospital['Name']) : 'Hospital Name'; ?></h2>
                        <h3>Hospital</h3>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
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
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-services" type="button" role="tab" aria-selected="false">Services</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-approval" type="button" role="tab" aria-selected="false">Approval Status</button>
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
                                <h5 class="card-title">Hospital Details</h5>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Hospital Name</div>
                                    <div class="col-lg-9 col-md-8"><?php echo isset($hospital['Name']) ? htmlspecialchars($hospital['Name']) : 'N/A'; ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Hospital ID</div>
                                    <div class="col-lg-9 col-md-8"><?php echo isset($hospital['Hospital_ID']) ? htmlspecialchars($hospital['Hospital_ID']) : 'N/A'; ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Address</div>
                                    <div class="col-lg-9 col-md-8"><?php echo isset($hospital['Address']) ? htmlspecialchars($hospital['Address']) : 'N/A'; ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Location</div>
                                    <div class="col-lg-9 col-md-8"><?php echo isset($hospital['Location']) ? htmlspecialchars($hospital['Location']) : 'N/A'; ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Contact</div>
                                    <div class="col-lg-9 col-md-8"><?php echo isset($hospital['Contact']) ? htmlspecialchars($hospital['Contact']) : 'N/A'; ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Approval Status</div>
                                    <div class="col-lg-9 col-md-8">
                                        <?php 
                                        if(isset($hospital['Approval_Status'])) {
                                            $status = $hospital['Approval_Status'];
                                            $statusClass = '';
                                            if($status == 'Approved') {
                                                $statusClass = 'bg-success';
                                            } elseif($status == 'Pending') {
                                                $statusClass = 'bg-warning';
                                            } else {
                                                $statusClass = 'bg-danger';
                                            }
                                            echo '<span class="badge ' . $statusClass . '">' . $status . '</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Unknown</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Approved By</div>
                                    <div class="col-lg-9 col-md-8">
                                        <?php 
                                        if(isset($hospital['Approved_By_Admin_UserID']) && $hospital['Approved_By_Admin_UserID']) {
                                            $admin_id = $hospital['Approved_By_Admin_UserID'];
                                            $admin_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                                            $admin_stmt->bind_param("i", $admin_id);
                                            $admin_stmt->execute();
                                            $admin_result = $admin_stmt->get_result();
                                            if($admin_result && $admin_result->num_rows > 0) {
                                                $admin = $admin_result->fetch_assoc();
                                                echo htmlspecialchars($admin['name']);
                                            } else {
                                                echo "Unknown";
                                            }
                                        } else {
                                            echo "Not yet approved";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Profile Tab -->
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
                                <h5 class="card-title">Hospital Settings</h5>
                                
                                <form method="POST" action="">
                                    <div class="row mb-3">
                                        <label for="hospitalName" class="col-md-4 col-lg-3 col-form-label">Hospital Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" type="text" class="form-control" id="hospitalName" value="<?php echo isset($hospital['Name']) ? htmlspecialchars($hospital['Name']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="hospitalAddress" class="col-md-4 col-lg-3 col-form-label">Address</label>
                                        <div class="col-md-8 col-lg-9">
                                            <textarea name="address" class="form-control" id="hospitalAddress" rows="3" required><?php echo isset($hospital['Address']) ? htmlspecialchars($hospital['Address']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="hospitalLocation" class="col-md-4 col-lg-3 col-form-label">Location</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="location" type="text" class="form-control" id="hospitalLocation" value="<?php echo isset($hospital['Location']) ? htmlspecialchars($hospital['Location']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="hospitalContact" class="col-md-4 col-lg-3 col-form-label">Contact</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="contact" type="text" class="form-control" id="hospitalContact" value="<?php echo isset($hospital['Contact']) ? htmlspecialchars($hospital['Contact']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="update_hospital" class="btn btn-primary">Save Changes</button>
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

                            <!-- Services Tab -->
                            <div class="tab-pane fade pt-3" id="profile-services" role="tabpanel">
                                <h5 class="card-title">Hospital Services</h5>
                                
                                <form method="POST" action="">
                                    <div class="row mb-3">
                                        <div class="col-md-8 offset-md-2">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="rt_pcr" id="rt_pcr" <?php echo (isset($hospital['rt_pcr']) && $hospital['rt_pcr']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="rt_pcr">
                                                    RT-PCR Testing
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="rapid_antigen" id="rapid_antigen" <?php echo (isset($hospital['rapid_antigen']) && $hospital['rapid_antigen']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="rapid_antigen">
                                                    Rapid Antigen Test
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="antibody" id="antibody" <?php echo (isset($hospital['antibody']) && $hospital['antibody']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="antibody">
                                                    Antibody Testing
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="home_collection" id="home_collection" <?php echo (isset($hospital['home_collection']) && $hospital['home_collection']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="home_collection">
                                                    Home Sample Collection
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="update_services" class="btn btn-primary">Update Services</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Approval Status Tab -->
                            <div class="tab-pane fade pt-3" id="profile-approval" role="tabpanel">
                                <h5 class="card-title">Approval Status</h5>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Current Status</div>
                                    <div class="col-lg-9 col-md-8">
                                        <?php 
                                        if(isset($hospital['Approval_Status'])) {
                                            $status = $hospital['Approval_Status'];
                                            $statusClass = '';
                                            $statusMessage = '';
                                            
                                            if($status == 'Approved') {
                                                $statusClass = 'bg-success';
                                                $statusMessage = 'Your hospital has been approved and is active in the system.';
                                            } elseif($status == 'Pending') {
                                                $statusClass = 'bg-warning';
                                                $statusMessage = 'Your hospital registration is pending approval by the administrator. You will be notified once approved.';
                                            } else {
                                                $statusClass = 'bg-danger';
                                                $statusMessage = 'Your hospital registration has been rejected. Please contact the administrator for more information.';
                                            }
                                            echo '<span class="badge ' . $statusClass . '">' . $status . '</span>';
                                            echo '<div class="alert alert-' . ($status == 'Approved' ? 'success' : ($status == 'Pending' ? 'warning' : 'danger')) . ' mt-2">' . $statusMessage . '</div>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Unknown</span>';
                                            echo '<div class="alert alert-secondary mt-2">Approval status information not available.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Approved By</div>
                                    <div class="col-lg-9 col-md-8">
                                        <?php 
                                        if(isset($hospital['Approved_By_Admin_UserID']) && $hospital['Approved_By_Admin_UserID']) {
                                            $admin_id = $hospital['Approved_By_Admin_UserID'];
                                            $admin_stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
                                            $admin_stmt->bind_param("i", $admin_id);
                                            $admin_stmt->execute();
                                            $admin_result = $admin_stmt->get_result();
                                            if($admin_result && $admin_result->num_rows > 0) {
                                                $admin = $admin_result->fetch_assoc();
                                                echo "<strong>" . htmlspecialchars($admin['name']) . "</strong><br>";
                                                echo "<small>" . htmlspecialchars($admin['email']) . "</small>";
                                            }
                                        } else {
                                            echo "Not yet approved";
                                        }
                                        ?>
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