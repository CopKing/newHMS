<?php
include 'header.php';
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve hospital
    if (isset($_POST['approve_hospital'])) {
        $hospital_id = (int)$_POST['hospital_id'];
        $admin_user_id = $_SESSION['user_id']; // Assuming admin ID is stored in session
        $stmt = $conn->prepare("UPDATE hospital SET Approval_Status = 'Approved', Approved_By_Admin_UserId = ? WHERE Hospital_ID = ?");
        $stmt->bind_param("ii", $admin_user_id, $hospital_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Reject hospital
    if (isset($_POST['reject_hospital'])) {
        $hospital_id = (int)$_POST['hospital_id'];
        $stmt = $conn->prepare("UPDATE hospital SET Approval_Status = 'Rejected' WHERE Hospital_ID = ?");
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Delete hospital
    if (isset($_POST['delete_hospital'])) {
        $hospital_id = (int)$_POST['hospital_id'];
        $stmt = $conn->prepare("DELETE FROM hospital WHERE Hospital_ID = ?");
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch hospitals from database with corrected table name
$result = $conn->query("SELECT h.Hospital_ID, h.Name, h.Address, h.Location, h.Approval_Status, 
                               u.name as user_name, u.email as user_email, 
                               a.name as admin_name
                        FROM hospital h
                        LEFT JOIN users u ON h.UserID = u.id
                        LEFT JOIN users a ON h.Approved_By_Admin_UserId = a.id
                        ORDER BY 
                            CASE h.Approval_Status 
                                WHEN 'Pending' THEN 1 
                                WHEN 'Approved' THEN 2 
                                WHEN 'Rejected' THEN 3 
                            END, 
                            h.Hospital_ID DESC");
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Hospital Approvals</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Management</li>
                <li class="breadcrumb-item active">Hospital Approvals</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hospital Registration Requests</h5>
                        <p>Review and approve hospital registration requests. Pending requests require your approval.</p>
                        
                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs mb-4" id="hospitalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending Approval</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Approved</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">Rejected</button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="hospitalTabsContent">
                            <!-- Pending Hospitals Tab -->
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Hospital Name</th>
                                                <th>Location</th>
                                                <th>Registered By</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $pendingFound = false;
                                            if ($result->num_rows > 0):
                                                $result->data_seek(0); // Reset result pointer
                                                while($row = $result->fetch_assoc()): 
                                                    if ($row['Approval_Status'] == 'Pending'):
                                                        $pendingFound = true;
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Name']) ?></td>
                                                    <td><?= htmlspecialchars($row['Location']) ?></td>
                                                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['user_email']) ?></td>
                                                    <td>
                                                        <!-- Approve Form -->
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="hospital_id" value="<?= $row['Hospital_ID'] ?>">
                                                            <button type="submit" name="approve_hospital" class="btn btn-success btn-sm">Approve</button>
                                                        </form>
                                                        
                                                        <!-- Reject Form -->
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $row['Hospital_ID'] ?>">Reject</button>
                                                        
                                                        <!-- Delete Form -->
                                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this hospital request?');">
                                                            <input type="hidden" name="hospital_id" value="<?= $row['Hospital_ID'] ?>">
                                                            <button type="submit" name="delete_hospital" class="btn btn-outline-danger btn-sm">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Rejection Reason Modal -->
                                                <div class="modal fade" id="rejectModal<?= $row['Hospital_ID'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel">Reject Hospital Registration</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="hospital_id" value="<?= $row['Hospital_ID'] ?>">
                                                                    <div class="mb-3">
                                                                        <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" name="reject_hospital" class="btn btn-danger">Reject Registration</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                    endif;
                                                endwhile;
                                            endif;
                                            
                                            if (!$pendingFound):
                                            ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No pending hospital requests</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Approved Hospitals Tab -->
                            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Hospital Name</th>
                                                <th>Location</th>
                                                <th>Registered By</th>
                                                <th>Approved By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $approvedFound = false;
                                            if ($result->num_rows > 0):
                                                $result->data_seek(0); // Reset result pointer
                                                while($row = $result->fetch_assoc()): 
                                                    if ($row['Approval_Status'] == 'Approved'):
                                                        $approvedFound = true;
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Name']) ?></td>
                                                    <td><?= htmlspecialchars($row['Location']) ?></td>
                                                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['admin_name']) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['Hospital_ID'] ?>">View Details</button>
                                                        
                                                        <!-- Delete Form -->
                                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this hospital?');">
                                                            <input type="hidden" name="hospital_id" value="<?= $row['Hospital_ID'] ?>">
                                                            <button type="submit" name="delete_hospital" class="btn btn-outline-danger btn-sm">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                
                                                <!-- View Details Modal -->
                                                <div class="modal fade" id="viewModal<?= $row['Hospital_ID'] ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="viewModalLabel"><?= htmlspecialchars($row['Name']) ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Location:</strong> <?= htmlspecialchars($row['Location']) ?></p>
                                                                        <p><strong>Registered By:</strong> <?= htmlspecialchars($row['user_name']) ?></p>
                                                                        <p><strong>Email:</strong> <?= htmlspecialchars($row['user_email']) ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Status:</strong> <span class="badge bg-success">Approved</span></p>
                                                                        <p><strong>Approved By:</strong> <?= htmlspecialchars($row['admin_name']) ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <p><strong>Address:</strong></p>
                                                                    <p><?= nl2br(htmlspecialchars($row['Address'])) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                    endif;
                                                endwhile;
                                            endif;
                                            
                                            if (!$approvedFound):
                                            ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No approved hospitals</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Rejected Hospitals Tab -->
                            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Hospital Name</th>
                                                <th>Location</th>
                                                <th>Registered By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rejectedFound = false;
                                            if ($result->num_rows > 0):
                                                $result->data_seek(0); // Reset result pointer
                                                while($row = $result->fetch_assoc()): 
                                                    if ($row['Approval_Status'] == 'Rejected'):
                                                        $rejectedFound = true;
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Name']) ?></td>
                                                    <td><?= htmlspecialchars($row['Location']) ?></td>
                                                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                                                    <td>
                                                        <!-- Delete Form -->
                                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this hospital request?');">
                                                            <input type="hidden" name="hospital_id" value="<?= $row['Hospital_ID'] ?>">
                                                            <button type="submit" name="delete_hospital" class="btn btn-outline-danger btn-sm">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php 
                                                    endif;
                                                endwhile;
                                            endif;
                                            
                                            if (!$rejectedFound):
                                            ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No rejected hospital requests</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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