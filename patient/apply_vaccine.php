<?php
include 'header.php';

// Check if user is logged in and is a patient
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Get hospital and vaccine IDs from URL
$hospital_id = isset($_GET['hospital_id']) ? (int)$_GET['hospital_id'] : 0;
$vaccine_id = isset($_GET['vaccine_id']) ? (int)$_GET['vaccine_id'] : 0;

// Validate hospital and vaccine
if($hospital_id <= 0 || $vaccine_id <= 0) {
    header("Location: index.php");
    exit();
}

// Get hospital details
$hospital = null;
$stmt = $conn->prepare("SELECT Hospital_ID, Name, Address, Location FROM hospital WHERE Hospital_ID = ? AND Approval_Status = 'Approved'");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0) {
    $hospital = $result->fetch_assoc();
}
$stmt->close();

// Get vaccine details
$vaccine = null;
$stmt = $conn->prepare("SELECT id, name FROM vaccines WHERE id = ? AND available = 1");
$stmt->bind_param("i", $vaccine_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0) {
    $vaccine = $result->fetch_assoc();
}
$stmt->close();

// Check if hospital and vaccine are valid
if(!$hospital || !$vaccine) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $preferred_date = safe_input($_POST['preferred_date']);
    $preferred_time = safe_input($_POST['preferred_time']);
    $notes = safe_input($_POST['notes']);
    
    // Validate inputs
    if(empty($preferred_date)) {
        $error_message = "Please select a preferred date.";
    } else {
        // Check if user already has a pending application for this vaccine
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id FROM vaccine_applications 
                              WHERE user_id = ? AND vaccine_id = ? AND status = 'Pending'");
        $stmt->bind_param("ii", $user_id, $vaccine_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0) {
            $error_message = "You already have a pending application for this vaccine.";
        } else {
            // Insert application
            $stmt = $conn->prepare("INSERT INTO vaccine_applications 
                                  (user_id, hospital_id, vaccine_id, preferred_date, preferred_time, notes, status, application_date) 
                                  VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            $stmt->bind_param("iissss", $user_id, $hospital_id, $vaccine_id, $preferred_date, $preferred_time, $notes);
            
            if($stmt->execute()) {
                $success_message = "Your vaccine application has been submitted successfully! You will be notified once it's approved.";
            } else {
                $error_message = "Failed to submit application. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Apply for Vaccine</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Apply for Vaccine</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Vaccine Application</h5>
              
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
              
              <div class="mb-4">
                <h6>Hospital Information</h6>
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($hospital['Name']); ?></h5>
                    <p class="card-text">
                      <strong>Address:</strong> <?php echo htmlspecialchars($hospital['Address']); ?><br>
                      <strong>Location:</strong> <?php echo htmlspecialchars($hospital['Location']); ?>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="mb-4">
                <h6>Vaccine Information</h6>
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($vaccine['name']); ?></h5>
                    <p class="card-text">
                      Please make sure you are eligible for this vaccine before applying.
                    </p>
                  </div>
                </div>
              </div>
              
              <form method="POST" action="">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="preferred_date" class="form-label">Preferred Date</label>
                    <input type="date" class="form-control" id="preferred_date" name="preferred_date" min="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label for="preferred_time" class="form-label">Preferred Time</label>
                    <select class="form-select" id="preferred_time" name="preferred_time" required>
                      <option value="">Select a time</option>
                      <option value="09:00">09:00 AM</option>
                      <option value="10:00">10:00 AM</option>
                      <option value="11:00">11:00 AM</option>
                      <option value="12:00">12:00 PM</option>
                      <option value="13:00">01:00 PM</option>
                      <option value="14:00">02:00 PM</option>
                      <option value="15:00">03:00 PM</option>
                      <option value="16:00">04:00 PM</option>
                      <option value="17:00">05:00 PM</option>
                    </select>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="notes" class="form-label">Additional Notes (Optional)</label>
                  <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional information or special requirements..."></textarea>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmInfo" required>
                    <label class="form-check-label" for="confirmInfo">
                      I confirm that the information provided is accurate and I am eligible for this vaccine.
                    </label>
                  </div>
                </div>
                
                <div class="d-flex justify-content-between">
                  <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                  <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?php include 'footer.php'; ?>