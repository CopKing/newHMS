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

// Handle approval/rejection of requests
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['request_id']) && isset($_POST['request_type'])) {
    $request_id = (int)$_POST['request_id'];
    $request_type = $_POST['request_type'];
    $action = $_POST['action'];
    
    if($request_type == 'appointment') {
        $new_status = ($action == 'approve') ? 'confirmed' : 'cancel';
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND hospital_id = ?");
        $stmt->bind_param("sii", $new_status, $request_id, $hospital_id);
    } else {
        $new_status = ($action == 'approve') ? 'Approved' : 'Rejected';
        $stmt = $conn->prepare("UPDATE vaccine_applications SET status = ? WHERE id = ? AND hospital_id = ?");
        $stmt->bind_param("sii", $new_status, $request_id, $hospital_id);
    }
    
    if($stmt && $stmt->execute()) {
        $success_message = "Request " . (($action == 'approve') ? 'approved' : 'rejected') . " successfully!";
    } else {
        $error_message = "Failed to update request. Please try again.";
    }
}

// Get appointments for this hospital
$appointments = [];
if($hospital_id > 0) {
    $stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.appointment_type, a.reason, a.status, a.created_at,
                                   u.name as patient_name, u.email as patient_email
                            FROM appointments a
                            JOIN users u ON a.user_id = u.id
                            WHERE a.hospital_id = ?
                            ORDER BY a.created_at DESC");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Get vaccine applications for this hospital
$vaccine_applications = [];
if($hospital_id > 0) {
    $stmt = $conn->prepare("SELECT va.id, va.application_date, va.status, va.appointment_date,
                                   u.name as patient_name, u.email as patient_email,
                                   v.name as vaccine_name
                            FROM vaccine_applications va
                            JOIN users u ON va.user_id = u.id
                            JOIN vaccines v ON va.vaccine_id = v.id
                            WHERE va.hospital_id = ?
                            ORDER BY va.application_date DESC");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $vaccine_applications[] = $row;
    }
}

// Get hospital details
$hospital = [];
if($hospital_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM hospital WHERE Hospital_ID = ?");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hospital = $result->fetch_assoc();
}

// Get available vaccines for this hospital
$vaccines = [];
if($hospital_id > 0) {
    $stmt = $conn->prepare("SELECT v.id, v.name, v.available 
                           FROM vaccines v 
                           WHERE v.available = 1 
                           ORDER BY v.name ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $vaccines[] = $row;
    }
}

// Combine and sort all requests by date
$all_requests = [];

// Add appointments
foreach($appointments as $appointment) {
    $all_requests[] = [
        'type' => 'appointment',
        'data' => $appointment,
        'date' => $appointment['created_at']
    ];
}

// Add vaccine applications
foreach($vaccine_applications as $application) {
    $all_requests[] = [
        'type' => 'vaccine',
        'data' => $application,
        'date' => $application['application_date']
    ];
}

// Sort by date (newest first)
usort($all_requests, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Count pending requests
$pending_count = 0;
foreach($all_requests as $request) {
    $status = strtolower($request['data']['status']);
    if($status == 'pending') {
        $pending_count++;
    }
}
?>
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Hospital Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Hospital Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
            <!-- Total Appointments Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">View All Appointments</a></li>
                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Appointments <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo count($appointments); ?></h6>
                      <span class="text-success small pt-1 fw-bold">Regular</span> <span class="text-muted small pt-2 ps-1">appointments</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Appointments Card -->
            
            <!-- Total Vaccine Applications Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">View All Applications</a></li>
                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Applications <span>| Vaccine</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-file-medical"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo count($vaccine_applications); ?></h6>
                      <span class="text-success small pt-1 fw-bold">Vaccine</span> <span class="text-muted small pt-2 ps-1">applications</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Vaccine Applications Card -->
            
            <!-- Pending Requests Card -->
            <div class="col-xxl-4 col-xl-12">
              <div class="card info-card customers-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">View Pending</a></li>
                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Pending Requests <span>| Awaiting Action</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $pending_count; ?></h6>
                      <span class="text-warning small pt-1 fw-bold">Pending</span> <span class="text-muted small pt-2 ps-1">requests</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Pending Requests Card -->
            
            <!-- Available Vaccines Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Manage Vaccines</a></li>
                    <li><a class="dropdown-item" href="#">View All</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Vaccines <span>| Available</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-capsule"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo count($vaccines); ?></h6>
                      <span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">vaccines</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Available Vaccines Card -->
            
            <!-- Reports -->
            <div class="col-12">
              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Hospital Overview <span>/Statistics</span></h5>
                  <!-- Line Chart -->
                  <div id="reportsChart"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Appointments',
                           [<?php echo count($appointments); ?>, <?php echo count($appointments)*0.8; ?>, <?php echo count($appointments)*0.9; ?>, <?php echo count($appointments); ?>, <?php echo count($appointments)*1.1; ?>, <?php echo count($appointments)*1.2; ?>, <?php echo count($appointments)*0.95; ?>],
                        }, {
                          name: 'Vaccine Applications',
                           [<?php echo count($vaccine_applications); ?>, <?php echo count($vaccine_applications)*0.7; ?>, <?php echo count($vaccine_applications)*0.85; ?>, <?php echo count($vaccine_applications)*0.9; ?>, <?php echo count($vaccine_applications)*1.05; ?>, <?php echo count($vaccine_applications)*1.15; ?>, <?php echo count($vaccine_applications); ?>]
                        }, {
                          name: 'Pending Requests',
                          data: [<?php echo $pending_count; ?>, <?php echo $pending_count*1.2; ?>, <?php echo $pending_count*0.8; ?>, <?php echo $pending_count*1.1; ?>, <?php echo $pending_count*0.9; ?>, <?php echo $pending_count*1.3; ?>, <?php echo $pending_count*0.7; ?>]
                        }],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1', '#2eca6a', '#ff771d'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'datetime',
                          categories: ["2023-01-01T00:00:00.000Z", "2023-02-01T00:00:00.000Z", "2023-03-01T00:00:00.000Z", "2023-04-01T00:00:00.000Z", "2023-05-01T00:00:00.000Z", "2023-06-01T00:00:00.000Z", "2023-07-01T00:00:00.000Z"]
                        },
                        tooltip: {
                          x: {
                            format: 'MM/yy'
                          },
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->
                </div>
              </div>
            </div><!-- End Reports -->
            
            <!-- Recent Requests -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">View All Requests</a></li>
                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Recent Requests <span>| For Your Hospital</span></h5>
                  
                  <!-- Display success/error messages -->
                  <?php if(isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <?php echo $success_message; ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>
                  
                  <?php if(isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?php echo $error_message; ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>
                  
                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Patient</th>
                        <th scope="col">Type/Service</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(!empty($all_requests)): ?>
                        <?php foreach(array_slice($all_requests, 0, 10) as $index => $request): ?>
                          <tr>
                            <th scope="row"><?php echo $index + 1; ?></th>
                            <td>
                              <strong><?php echo htmlspecialchars($request['data']['patient_name']); ?></strong>
                              <br><small class="text-muted"><?php echo htmlspecialchars($request['data']['patient_email']); ?></small>
                            </td>
                            <td>
                              <?php 
                              if($request['type'] == 'appointment') {
                                  echo "Appointment: " . htmlspecialchars($request['data']['appointment_type']);
                                  echo "<br><small>" . htmlspecialchars($request['data']['reason']) . "</small>";
                              } else {
                                  echo "Vaccine: " . htmlspecialchars($request['data']['vaccine_name']);
                              }
                              ?>
                            </td>
                            <td>
                              <?php 
                              if($request['type'] == 'appointment') {
                                  echo date('M d, Y', strtotime($request['data']['appointment_date']));
                              } else {
                                  echo date('M d, Y', strtotime($request['data']['application_date']));
                              }
                              ?>
                            </td>
                            <td>
                              <?php 
                              $status = $request['data']['status'];
                              if(strtolower($status) == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                              <?php elseif(strtolower($status) == 'confirmed' || strtolower($status) == 'approved'): ?>
                                <span class="badge bg-success">Confirmed</span>
                              <?php elseif(strtolower($status) == 'completed'): ?>
                                <span class="badge bg-info">Completed</span>
                              <?php elseif(strtolower($status) == 'cancel' || strtolower($status) == 'rejected'): ?>
                                <span class="badge bg-danger">Cancelled</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if(strtolower($request['data']['status']) == 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                  <input type="hidden" name="request_id" value="<?php echo $request['data']['id']; ?>">
                                  <input type="hidden" name="request_type" value="<?php echo $request['type']; ?>">
                                  <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                    <i class="bi bi-check"></i> Approve
                                  </button>
                                  <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
                                    <i class="bi bi-x"></i> Reject
                                  </button>
                                </form>
                              <?php else: ?>
                                <span class="text-muted">Processed</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center">No requests found for your hospital</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent Requests -->
            
            <!-- Available Vaccines -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Manage Vaccines</a></li>
                    <li><a class="dropdown-item" href="#">Add New Vaccine</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Available Vaccines <span>| For Your Hospital</span></h5>
                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">Vaccine Name</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(!empty($vaccines)): ?>
                        <?php foreach($vaccines as $vaccine): ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($vaccine['name']); ?></strong></td>
                            <td>
                              <?php if($vaccine['available']): ?>
                                <span class="badge bg-success">Available</span>
                              <?php else: ?>
                                <span class="badge bg-secondary">Unavailable</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="2" class="text-center">No vaccines available</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Available Vaccines -->
          </div>
        </div><!-- End Left side columns -->
        
        <!-- Right side columns -->
        <div class="col-lg-4">
          <!-- Quick Actions -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Quick Actions</h5>
              <div class="d-grid gap-2">
                <a href="manage_appointments.php" class="btn btn-primary">
                  <i class="bi bi-calendar-check"></i> Manage Appointments
                </a>
                <a href="manage_vaccine_applications.php" class="btn btn-info">
                  <i class="bi bi-file-medical"></i> Manage Applications
                </a>
                <a href="hospital_profile.php" class="btn btn-success">
                  <i class="bi bi-hospital"></i> Hospital Profile
                </a>
                <a href="staff_management.php" class="btn btn-warning">
                  <i class="bi bi-people"></i> Staff Management
                </a>
              </div>
            </div>
          </div><!-- End Quick Actions -->
          
          <!-- Recent Activity -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>
            <div class="card-body">
              <h5 class="card-title">Recent Activity <span>| Today</span></h5>
              <div class="activity">
                <?php if(!empty($all_requests)): ?>
                  <?php foreach(array_slice($all_requests, 0, 3) as $request): ?>
                    <div class="activity-item d-flex">
                      <div class="activite-label">Just now</div>
                      <i class='bi bi-circle-fill activity-badge text-<?php 
                      $status = $request['data']['status'];
                      echo strtolower($status) == 'pending' ? 'warning' : (strtolower($status) == 'confirmed' || strtolower($status) == 'approved' ? 'success' : (strtolower($status) == 'completed' ? 'info' : 'danger')); 
                      ?> align-self-start'></i>
                      <div class="activity-content">
                        <?php 
                        if($request['type'] == 'appointment') {
                            echo "Appointment for <a href='#' class='fw-bold text-dark'>" . htmlspecialchars($request['data']['appointment_type']) . "</a> submitted by <a href='#' class='fw-bold text-dark'>" . htmlspecialchars($request['data']['patient_name']) . "</a>";
                        } else {
                            echo "Vaccine application for <a href='#' class='fw-bold text-dark'>" . htmlspecialchars($request['data']['vaccine_name']) . "</a> submitted by <a href='#' class='fw-bold text-dark'>" . htmlspecialchars($request['data']['patient_name']) . "</a>";
                        }
                        ?>
                      </div>
                    </div><!-- End activity item-->
                  <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if(isset($hospital['Name'])): ?>
                  <div class="activity-item d-flex">
                    <div class="activite-label">Hospital</div>
                    <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                    <div class="activity-content">
                      Hospital: <a href="#" class="fw-bold text-dark"><?php echo htmlspecialchars($hospital['Name']); ?></a>
                    </div>
                  </div><!-- End activity item-->
                <?php endif; ?>
              </div>
            </div>
          </div><!-- End Recent Activity -->
          
          <!-- Hospital Statistics -->
          <div class="card">
            <div class="card-body pb-0">
              <h5 class="card-title">Request Distribution <span>| Your Hospital</span></h5>
              <div id="hospitalChart" style="min-height: 400px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#hospitalChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '5%',
                      left: 'center'
                    },
                    series: [{
                      name: 'Request Types',
                      type: 'pie',
                      radius: ['40%', '70%'],
                      avoidLabelOverlap: false,
                      label: {
                        show: false,
                        position: 'center'
                      },
                      emphasis: {
                        label: {
                          show: true,
                          fontSize: '18',
                          fontWeight: 'bold'
                        }
                      },
                      labelLine: {
                        show: false
                      },
                       [
                        {value: <?php echo count($appointments); ?>, name: 'Appointments'},
                        {value: <?php echo count($vaccine_applications); ?>, name: 'Vaccine Applications'},
                        {value: <?php echo $pending_count; ?>, name: 'Pending Requests'}
                      ]
                    }]
                  });
                });
              </script>
            </div>
          </div><!-- End Hospital Statistics -->
          
          <!-- Hospital Information -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Hospital Information</h5>
              <?php if(!empty($hospital)): ?>
                <div class="row">
                  <div class="col-12">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($hospital['Name'] ?? 'N/A'); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($hospital['Location'] ?? 'N/A'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($hospital['Address'] ?? 'N/A'); ?></p>
                    <p><strong>Status:</strong> 
                      <?php 
                      $status = $hospital['Approval_Status'] ?? 'Unknown';
                      $statusClass = '';
                      if($status == 'Approved') {
                          $statusClass = 'bg-success';
                      } elseif($status == 'Pending') {
                          $statusClass = 'bg-warning';
                      } else {
                          $statusClass = 'bg-danger';
                      }
                      echo '<span class="badge ' . $statusClass . '">' . $status . '</span>';
                      ?>
                    </p>
                  </div>
                </div>
              <?php else: ?>
                <p class="text-muted">Hospital information not available.</p>
              <?php endif; ?>
            </div>
          </div><!-- End Hospital Information -->
        </div><!-- End Right side columns -->
      </div>
    </section>
  </main><!-- End #main -->
  <?php include 'footer.php'; ?>