<?php
include 'header.php';

// Check if user is logged in and is a patient
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's medical records
$medical_records = [];
$stmt = $conn->prepare("SELECT id, record_type, record_date, doctor_name, diagnosis, treatment, notes 
                        FROM medical_records 
                        WHERE user_id = ? 
                        ORDER BY record_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result) {
    while($row = $result->fetch_assoc()) {
        $medical_records[] = $row;
    }
}
$stmt->close();
?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Medical Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Medical Records</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Your Medical History</h5>
              
              <?php if(empty($medical_records)): ?>
                <div class="alert alert-info">
                  No medical records found. Your medical records will appear here once they are added by healthcare providers.
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($medical_records as $record): ?>
                        <tr>
                          <td><?php echo date('M d, Y', strtotime($record['record_date'])); ?></td>
                          <td><?php echo htmlspecialchars($record['record_type']); ?></td>
                          <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                          <td><?php echo htmlspecialchars(substr($record['diagnosis'], 0, 50)) . (strlen($record['diagnosis']) > 50 ? '...' : ''); ?></td>
                          <td><?php echo htmlspecialchars(substr($record['treatment'], 0, 50)) . (strlen($record['treatment']) > 50 ? '...' : ''); ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewRecordModal<?php echo $record['id']; ?>">
                              View Details
                            </button>
                          </td>
                        </tr>
                        
                        <!-- View Record Modal -->
                        <div class="modal fade" id="viewRecordModal<?php echo $record['id']; ?>" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="viewRecordModalLabel">Medical Record Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="row mb-3">
                                  <div class="col-md-6">
                                    <p><strong>Record Type:</strong> <?php echo htmlspecialchars($record['record_type']); ?></p>
                                    <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($record['record_date'])); ?></p>
                                    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($record['doctor_name']); ?></p>
                                  </div>
                                </div>
                                <div class="mb-3">
                                  <p><strong>Diagnosis:</strong></p>
                                  <p><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></p>
                                </div>
                                <div class="mb-3">
                                  <p><strong>Treatment:</strong></p>
                                  <p><?php echo nl2br(htmlspecialchars($record['treatment'])); ?></p>
                                </div>
                                <?php if(!empty($record['notes'])): ?>
                                  <div class="mb-3">
                                    <p><strong>Additional Notes:</strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($record['notes'])); ?></p>
                                  </div>
                                <?php endif; ?>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?php include 'footer.php'; ?>