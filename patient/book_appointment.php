<?php
include 'header.php';

// Check if user is logged in and is a patient
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Get approved hospitals
$hospitals = [];
$result = $conn->query("SELECT Hospital_ID, Name, Location FROM hospital WHERE Approval_Status = 'Approved' ORDER BY Name ASC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

$success_message = '';
$error_message = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_id = (int)$_POST['hospital_id'];
    $appointment_date = safe_input($_POST['appointment_date']);
    $appointment_time = safe_input($_POST['appointment_time']);
    $appointment_type = safe_input($_POST['appointment_type']);
    $reason = safe_input($_POST['reason']);
    
    // Validate inputs
    if(empty($hospital_id) || empty($appointment_date) || empty($appointment_time) || empty($appointment_type)) {
        $error_message = "All fields are required.";
    } else {
        // Check if the selected date is not in the past
        if(strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
            $error_message = "Appointment date cannot be in the past.";
        } else {
            // Insert appointment
            $user_id = $_SESSION['user_id'];
            $status = 'Pending';
            $stmt = $conn->prepare("INSERT INTO appointments 
                                  (user_id, hospital_id, appointment_date, appointment_time, appointment_type, reason, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("iisssss", $user_id, $hospital_id, $appointment_date, $appointment_time, $appointment_type, $reason, $status);
            
            if($stmt->execute()) {
                $success_message = "Your appointment has been booked successfully! You will be notified once it's confirmed.";
            } else {
                $error_message = "Failed to book appointment. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Book Appointment</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Book Appointment</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Book an Appointment</h5>
              
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
              
              <form method="POST" action="">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="hospital_id" class="form-label">Select Hospital</label>
                    <select class="form-select" id="hospital_id" name="hospital_id" required>
                      <option value="">Choose a hospital</option>
                      <?php foreach($hospitals as $hospital): ?>
                        <option value="<?php echo $hospital['Hospital_ID']; ?>"><?php echo htmlspecialchars($hospital['Name'] . ', ' . $hospital['Location']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="appointment_date" class="form-label">Preferred Date</label>
                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label for="appointment_time" class="form-label">Preferred Time</label>
                    <select class="form-select" id="appointment_time" name="appointment_time" required>
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
                  <label for="appointment_type" class="form-label">Appointment Type</label>
                  <select class="form-select" id="appointment_type" name="appointment_type" required>
                    <option value="">Select appointment type</option>
                    <option value="General Consultation">General Consultation</option>
                    <option value="Vaccination">Vaccination</option>
                    <option value="Follow-up">Follow-up</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Specialist">Specialist Consultation</option>
                  </select>
                </div>
                
                <div class="mb-3">
                  <label for="reason" class="form-label">Reason for Visit</label>
                  <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please describe the reason for your appointment..." required></textarea>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmInfo" required>
                    <label class="form-check-label" for="confirmInfo">
                      I confirm that the information provided is accurate.
                    </label>
                  </div>
                </div>
                
                <div class="d-flex justify-content-between">
                  <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                  <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?php include 'footer.php'; ?>