<?php
include 'header.php';

if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Get statistics for dashboard
$total_users = 0;
$total_hospitals = 0;
$pending_hospitals = 0;
$approved_hospitals = 0;
$total_vaccines = 0;
$available_vaccines = 0;

// Get total users count
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if($result) {
    $row = $result->fetch_assoc();
    $total_users = $row['count'];
}

// Get total hospitals count
$result = $conn->query("SELECT COUNT(*) as count FROM hospital");
if($result) {
    $row = $result->fetch_assoc();
    $total_hospitals = $row['count'];
}

// Get pending hospitals count
$result = $conn->query("SELECT COUNT(*) as count FROM hospital WHERE Approval_Status = 'Pending'");
if($result) {
    $row = $result->fetch_assoc();
    $pending_hospitals = $row['count'];
}

// Get approved hospitals count
$result = $conn->query("SELECT COUNT(*) as count FROM hospital WHERE Approval_Status = 'Approved'");
if($result) {
    $row = $result->fetch_assoc();
    $approved_hospitals = $row['count'];
}

// Get total vaccines count
$result = $conn->query("SELECT COUNT(*) as count FROM vaccines");
if($result) {
    $row = $result->fetch_assoc();
    $total_vaccines = $row['count'];
}

// Get available vaccines count
$result = $conn->query("SELECT COUNT(*) as count FROM vaccines WHERE available = 1");
if($result) {
    $row = $result->fetch_assoc();
    $available_vaccines = $row['count'];
}

// Get recent hospital registrations
$recent_hospitals = [];
$result = $conn->query("SELECT h.Hospital_ID, h.Name, h.Approval_Status, u.name as user_name, u.email 
                        FROM hospital h 
                        LEFT JOIN users u ON h.UserID = u.id 
                        ORDER BY h.Hospital_ID DESC LIMIT 5");
if($result) {
    while($row = $result->fetch_assoc()) {
        $recent_hospitals[] = $row;
    }
}

// Get recent user registrations
$recent_users = [];
$result = $conn->query("SELECT id, name, email, role_id FROM users ORDER BY id DESC LIMIT 5");
if($result) {
    while($row = $result->fetch_assoc()) {
        $recent_users[] = $row;
    }
}

// Get all appointments with user and hospital details
$appointments = [];
$result = $conn->query("SELECT a.id, a.appointment_date, a.appointment_type, a.reason, a.status, a.created_at,
                               u.name as patient_name, u.email as patient_email,
                               h.Name as hospital_name, h.Location as hospital_location
                        FROM appointments a
                        JOIN users u ON a.user_id = u.id
                        JOIN hospital h ON a.hospital_id = h.Hospital_ID
                        ORDER BY a.created_at DESC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Get all vaccine applications with user and hospital details
$vaccine_applications = [];
$result = $conn->query("SELECT va.id, va.application_date, va.status, va.appointment_date,
                               u.name as patient_name, u.email as patient_email,
                               h.Name as hospital_name, h.Location as hospital_location,
                               v.name as vaccine_name
                        FROM vaccine_applications va
                        JOIN users u ON va.user_id = u.id
                        JOIN hospital h ON va.hospital_id = h.Hospital_ID
                        JOIN vaccines v ON va.vaccine_id = v.id
                        ORDER BY va.application_date DESC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $vaccine_applications[] = $row;
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
?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Admin Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
            <!-- Total Users Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="users.php">View All Users</a></li>
                    <li><a class="dropdown-item" href="users.create.php">Add New User</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Total Users <span>| System</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $total_users; ?></h6>
                      <span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">accounts</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Users Card -->
            
            <!-- Hospitals Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="hospital_approvals.php">Manage Hospitals</a></li>
                    <li><a class="dropdown-item" href="#">View All Hospitals</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Hospitals <span>| Registered</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-hospital"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $total_hospitals; ?></h6>
                      <span class="text-success small pt-1 fw-bold"><?php echo $approved_hospitals; ?></span> <span class="text-muted small pt-2 ps-1">approved</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Hospitals Card -->
            
            <!-- Pending Approvals Card -->
            <div class="col-xxl-4 col-xl-12">
              <div class="card info-card customers-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="hospital_approvals.php">Review Pending</a></li>
                    <li><a class="dropdown-item" href="#">View All</a></li>
                  </ul>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Pending Approvals <span>| Hospitals</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $pending_hospitals; ?></h6>
                      <span class="text-danger small pt-1 fw-bold">Awaiting</span> <span class="text-muted small pt-2 ps-1">approval</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Pending Approvals Card -->
            
            <!-- Vaccines Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="vaccines.php">Manage Vaccines</a></li>
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
                      <h6><?php echo $available_vaccines; ?>/<?php echo $total_vaccines; ?></h6>
                      <span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">vaccines</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Vaccines Card -->
            
            <!-- Total Appointments Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
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
            <div class="col-xxl-4 col-xl-12">
              <div class="card info-card customers-card">
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
                  <h5 class="card-title">System Overview <span>/Statistics</span></h5>
                  <!-- Line Chart -->
                  <div id="reportsChart"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Users',
                          data: [31, 40, 28, 51, 42, 82, 56],
                        }, {
                          name: 'Hospitals',
                          data: [11, 32, 45, 32, 34, 52, 41]
                        }, {
                          name: 'Vaccines',
                          data: [15, 11, 32, 18, 9, 24, 11]
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
                  <h5 class="card-title">Recent Requests <span>| All Types</span></h5>
                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Patient</th>
                        <th scope="col">Type/Service</th>
                        <th scope="col">Hospital</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
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
                              <?php echo htmlspecialchars($request['data']['hospital_name']); ?>
                              <br><small class="text-muted"><?php echo htmlspecialchars($request['data']['hospital_location']); ?></small>
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
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center">No requests found</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent Requests -->
            
            <!-- Recent Hospital Registrations -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="hospital_approvals.php">View All</a></li>
                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Recent Hospital Registrations <span>| Today</span></h5>
                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Hospital Name</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Email</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(!empty($recent_hospitals)): ?>
                        <?php foreach($recent_hospitals as $hospital): ?>
                          <tr>
                            <th scope="row"><a href="#"><?php echo $hospital['Hospital_ID']; ?></a></th>
                            <td><?php echo htmlspecialchars($hospital['Name']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['email']); ?></td>
                            <td>
                              <?php if($hospital['Approval_Status'] == 'Approved'): ?>
                                <span class="badge bg-success">Approved</span>
                              <?php elseif($hospital['Approval_Status'] == 'Pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                              <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <a href="hospital_approvals.php" class="btn btn-sm btn-outline-primary">Manage</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center">No hospital registrations found</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent Hospital Registrations -->
            
            <!-- Recent User Registrations -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Actions</h6>
                    </li>
                    <li><a class="dropdown-item" href="users.php">View All</a></li>
                    <li><a class="dropdown-item" href="users.create.php">Add New User</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Recent User Registrations <span>| Today</span></h5>
                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(!empty($recent_users)): ?>
                        <?php foreach($recent_users as $user): ?>
                          <tr>
                            <th scope="row"><?php echo $user['id']; ?></th>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                              <?php 
                              $roleNames = [
                                1 => 'Admin',
                                2 => 'Hospital',
                                3 => 'Patient'
                              ];
                              echo $roleNames[$user['role_id']] ?? 'Unknown';
                              ?>
                            </td>
                            <td>
                              <a href="users.php" class="btn btn-sm btn-outline-primary">Manage</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="5" class="text-center">No user registrations found</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent User Registrations -->
          </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns -->
        <div class="col-lg-4">
          <!-- Quick Actions -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Quick Actions</h5>
              <div class="d-grid gap-2">
                <a href="users.create.php" class="btn btn-primary">
                  <i class="bi bi-person-plus"></i> Add New User
                </a>
                <a href="hospital_approvals.php" class="btn btn-info">
                  <i class="bi bi-building"></i> Review Hospital Approvals
                </a>
                <a href="vaccines.php" class="btn btn-success">
                  <i class="bi bi-capsule"></i> Manage Vaccines
                </a>
                <a href="users.php" class="btn btn-warning">
                  <i class="bi bi-people"></i> Manage All Users
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
                
                <?php if(!empty($recent_hospitals)): ?>
                  <?php foreach($recent_hospitals as $hospital): ?>
                    <div class="activity-item d-flex">
                      <div class="activite-label">Recently</div>
                      <i class='bi bi-circle-fill activity-badge text-<?php echo $hospital['Approval_Status'] == 'Approved' ? 'success' : ($hospital['Approval_Status'] == 'Pending' ? 'warning' : 'danger'); ?> align-self-start'></i>
                      <div class="activity-content">
                        Hospital <a href="#" class="fw-bold text-dark"><?php echo htmlspecialchars($hospital['Name']); ?></a> <?php echo strtolower($hospital['Approval_Status']); ?>
                      </div>
                    </div><!-- End activity item-->
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div><!-- End Recent Activity -->
          
          <!-- System Statistics -->
          <div class="card">
            <div class="card-body pb-0">
              <h5 class="card-title">System Statistics <span>| Overview</span></h5>
              <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#trafficChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '5%',
                      left: 'center'
                    },
                    series: [{
                      name: 'System Distribution',
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
                      data: [{
                          value: <?php echo $total_users - $approved_hospitals - 1; ?>,
                          name: 'Patients'
                        },
                        {
                          value: <?php echo $approved_hospitals; ?>,
                          name: 'Hospitals'
                        },
                        {
                          value: 1,
                          name: 'Administrators'
                        },
                        {
                          value: <?php echo count($appointments); ?>,
                          name: 'Appointments'
                        },
                        {
                          value: <?php echo count($vaccine_applications); ?>,
                          name: 'Vaccine Apps'
                        }
                      ]
                    }]
                  });
                });
              </script>
            </div>
          </div><!-- End System Statistics -->
          
          <!-- News & Updates Traffic -->
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
            <div class="card-body pb-0">
              <h5 class="card-title">System Announcements <span>| Today</span></h5>
              <div class="news">
                <div class="post-item clearfix">
                  <img src="../assets/img/news-1.jpg" alt="">
                  <h4><a href="#">New Hospital Registration System</a></h4>
                  <p>Hospitals can now register online through our new registration portal...</p>
                </div>
                <div class="post-item clearfix">
                  <img src="../assets/img/news-2.jpg" alt="">
                  <h4><a href="#">Vaccine Management System Updated</a></h4>
                  <p>The vaccine management system has been updated with new features...</p>
                </div>
                <div class="post-item clearfix">
                  <img src="../assets/img/news-3.jpg" alt="">
                  <h4><a href="#">Security Enhancements</a></h4>
                  <p>New security measures have been implemented to protect user data...</p>
                </div>
              </div><!-- End sidebar recent posts-->
            </div>
          </div><!-- End News & Updates -->
        </div><!-- End Right side columns -->
      </div>
    </section>
  </main><!-- End #main -->
  <?php include 'footer.php'; ?>