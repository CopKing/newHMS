<?php
include 'header.php';

// Check if user is logged in and is a patient
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Get approved hospitals with their vaccines
$approved_hospitals = [];
$result = $conn->query("SELECT h.Hospital_ID, h.Name, h.Address, h.Location 
                        FROM hospital h 
                        WHERE h.Approval_Status = 'Approved' 
                        ORDER BY h.Name ASC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $hospital_id = $row['Hospital_ID'];
        $approved_hospitals[$hospital_id] = $row;
        
        // Get vaccines for this hospital
        $vaccines_result = $conn->query("SELECT v.id, v.name, v.available 
                                      FROM vaccines v 
                                      WHERE v.available = 1 
                                      ORDER BY v.name ASC");
        $approved_hospitals[$hospital_id]['vaccines'] = [];
        
        if($vaccines_result) {
            while($vaccine = $vaccines_result->fetch_assoc()) {
                $approved_hospitals[$hospital_id]['vaccines'][] = $vaccine;
            }
        }
    }
}

// Get user's vaccine applications
$user_id = $_SESSION['user_id'];
$vaccine_applications = [];
$result = $conn->query("SELECT va.id, va.application_date, va.status, va.appointment_date, 
                               h.Name as hospital_name, v.name as vaccine_name
                        FROM vaccine_applications va
                        JOIN hospital h ON va.hospital_id = h.Hospital_ID
                        JOIN vaccines v ON va.vaccine_id = v.id
                        WHERE va.user_id = $user_id
                        ORDER BY va.application_date DESC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $vaccine_applications[] = $row;
    }
}
// Add this after fetching vaccine applications
$regular_appointments = [];
$result = $conn->query("SELECT a.id, a.appointment_date, a.status, a.reason, a.appointment_type,
                               h.Name as hospital_name
                        FROM appointments a
                        JOIN hospital h ON a.hospital_id = h.Hospital_ID
                        WHERE a.user_id = $user_id
                        ORDER BY a.appointment_date DESC");
if($result) {
    while($row = $result->fetch_assoc()) {
        $regular_appointments[] = $row;
    }
}
?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Patient Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
            <!-- Available Hospitals Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
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
                  <h5 class="card-title">Hospitals <span>| Available</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-hospital"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo count($approved_hospitals); ?></h6>
                      <span class="text-success small pt-1 fw-bold">Approved</span> <span class="text-muted small pt-2 ps-1">hospitals</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Available Hospitals Card -->
            
            <!-- Available Vaccines Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
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
                  <h5 class="card-title">Vaccines <span>| Available</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-capsule"></i>
                    </div>
                    <div class="ps-3">
                      <h6>
                        <?php 
                        $total_vaccines = 0;
                        foreach($approved_hospitals as $hospital) {
                            $total_vaccines += count($hospital['vaccines']);
                        }
                        echo $total_vaccines;
                        ?>
                      </h6>
                      <span class="text-success small pt-1 fw-bold">Available</span> <span class="text-muted small pt-2 ps-1">vaccines</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Available Vaccines Card -->
            
            <!-- My Applications Card -->
            <div class="col-xxl-4 col-xl-12">
              <div class="card info-card customers-card">
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
                  <h5 class="card-title">My Applications <span>| Status</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-file-medical"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo count($vaccine_applications); ?></h6>
                      <span class="text-primary small pt-1 fw-bold">Submitted</span> <span class="text-muted small pt-2 ps-1">applications</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End My Applications Card -->
            
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
                  <h5 class="card-title">Hospital & Vaccine Overview <span>/Statistics</span></h5>
                  <!-- Line Chart -->
                  <div id="reportsChart"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Hospitals',
                          data: [31, 40, 28, 51, 42, 82, 56],
                        }, {
                          name: 'Vaccines',
                          data: [11, 32, 45, 32, 34, 52, 41]
                        }, {
                          name: 'Applications',
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
            
            <!-- Available Hospitals and Vaccines -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
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
                  <h5 class="card-title">Available Hospitals & Vaccines <span>| Apply Now</span></h5>
                  <div class="accordion" id="hospitalAccordion">
                    <?php if(!empty($approved_hospitals)): ?>
                      <?php foreach($approved_hospitals as $hospital): ?>
                        <div class="accordion-item">
                          <h2 class="accordion-header" id="heading<?php echo $hospital['Hospital_ID']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $hospital['Hospital_ID']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $hospital['Hospital_ID']; ?>">
                              <strong><?php echo htmlspecialchars($hospital['Name']); ?></strong>
                              <span class="text-muted"><?php echo htmlspecialchars($hospital['Location']); ?></span>
                            </button>
                          </h2>
                          <div id="collapse<?php echo $hospital['Hospital_ID']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $hospital['Hospital_ID']; ?>" data-bs-parent="#hospitalAccordion">
                            <div class="accordion-body">
                              <div class="row mb-3">
                                <div class="col-md-6">
                                  <p><strong>Address:</strong> <?php echo htmlspecialchars($hospital['Address']); ?></p>
                                </div>
                                <div class="col-md-6">
                                  <p><strong>Available Vaccines:</strong> <?php echo count($hospital['vaccines']); ?></p>
                                </div>
                              </div>
                              
                              <?php if(!empty($hospital['vaccines'])): ?>
                                <div class="table-responsive">
                                  <table class="table table-sm">
                                    <thead>
                                      <tr>
                                        <th>Vaccine Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($hospital['vaccines'] as $vaccine): ?>
                                        <tr>
                                          <td><?php echo htmlspecialchars($vaccine['name']); ?></td>
                                          <td><span class="badge bg-success">Available</span></td>
                                          <td>
                                            <a href="apply_vaccine.php?hospital_id=<?php echo $hospital['Hospital_ID']; ?>&vaccine_id=<?php echo $vaccine['id']; ?>" class="btn btn-sm btn-primary">Apply</a>
                                          </td>
                                        </tr>
                                      <?php endforeach; ?>
                                    </tbody>
                                  </table>
                                </div>
                              <?php else: ?>
                                <p class="text-muted">No vaccines available at this hospital.</p>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="alert alert-info">
                        No approved hospitals found at this time.
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div><!-- End Available Hospitals and Vaccines -->
            
            <!-- My Applications -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
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
                  <h5 class="card-title">My Vaccine Applications <span>| Status</span></h5>
                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Hospital</th>
                        <th scope="col">Vaccine</th>
                        <th scope="col">Application Date</th>
                        <th scope="col">Appointment Date</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(!empty($vaccine_applications)): ?>
                        <?php foreach($vaccine_applications as $index => $application): ?>
                          <tr>
                            <th scope="row"><?php echo $index + 1; ?></th>
                            <td><?php echo htmlspecialchars($application['hospital_name']); ?></td>
                            <td><?php echo htmlspecialchars($application['vaccine_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($application['application_date'])); ?></td>
                            <td>
                              <?php if($application['appointment_date']): ?>
                                <?php echo date('M d, Y', strtotime($application['appointment_date'])); ?>
                              <?php else: ?>
                                <span class="text-muted">Pending</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if($application['status'] == 'Pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                              <?php elseif($application['status'] == 'Approved'): ?>
                                <span class="badge bg-success">Approved</span>
                              <?php elseif($application['status'] == 'Completed'): ?>
                                <span class="badge bg-info">Completed</span>
                              <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center">No vaccine applications found</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End My Applications -->
          </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns -->
        <div class="col-lg-4">
<!-- Quick Actions -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <div class="d-grid gap-2">
            <a href="book_appointment.php" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Book Appointment
            </a>
            <a href="patient_profile.php" class="btn btn-info">
                <i class="bi bi-person"></i> My Profile
            </a>
            <a href="medical_records.php" class="btn btn-success">
                <i class="bi bi-file-medical"></i> Medical Records
            </a>
            <a href="#" class="btn btn-warning">
                <i class="bi bi-chat-dots"></i> Contact Support
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
                <?php if(!empty($vaccine_applications)): ?>
                  <?php foreach(array_slice($vaccine_applications, 0, 3) as $application): ?>
                    <div class="activity-item d-flex">
                      <div class="activite-label"><?php echo date('M d', strtotime($application['application_date'])); ?></div>
                      <i class='bi bi-circle-fill activity-badge text-<?php echo $application['status'] == 'Pending' ? 'warning' : ($application['status'] == 'Approved' ? 'success' : ($application['status'] == 'Completed' ? 'info' : 'danger')); ?> align-self-start'></i>
                      <div class="activity-content">
                        Vaccine application for <a href="#" class="fw-bold text-dark"><?php echo htmlspecialchars($application['vaccine_name']); ?></a> at <a href="#" class="fw-bold text-dark"><?php echo htmlspecialchars($application['hospital_name']); ?></a> is <?php echo strtolower($application['status']); ?>
                      </div>
                    </div><!-- End activity item-->
                  <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if(empty($vaccine_applications)): ?>
                  <div class="activity-item d-flex">
                    <div class="activite-label">Today</div>
                    <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                    <div class="activity-content">
                      Welcome to your patient dashboard
                    </div>
                  </div><!-- End activity item-->
                <?php endif; ?>
              </div>
            </div>
          </div><!-- End Recent Activity -->
          
          <!-- System Statistics -->
          <div class="card">
            <div class="card-body pb-0">
              <h5 class="card-title">Vaccine Distribution <span>| Overview</span></h5>
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
                      name: 'Vaccine Types',
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
                      data: [
                        <?php
                        // Count vaccines by type (this is a simplified example)
                        $vaccine_types = [];
                        foreach($approved_hospitals as $hospital) {
                            foreach($hospital['vaccines'] as $vaccine) {
                            $vaccine_name = $vaccine['name'];
                            if(isset($vaccine_types[$vaccine_name])) {
                                $vaccine_types[$vaccine_name]++;
                            } else {
                                $vaccine_types[$vaccine_name] = 1;
                            }
                            }
                        }
                        
                        foreach($vaccine_types as $name => $count) {
                            echo "{value: $count, name: '" . addslashes($name) . "'},";
                        }
                        ?>
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
              <h5 class="card-title">Health Tips <span>| Today</span></h5>
              <div class="news">
                <div class="post-item clearfix">
                  <img src="../assets/img/news-1.jpg" alt="">
                  <h4><a href="#">Preparing for Your Vaccination</a></h4>
                  <p>Learn what to do before, during, and after your vaccination appointment...</p>
                </div>
                <div class="post-item clearfix">
                  <img src="../assets/img/news-2.jpg" alt="">
                  <h4><a href="#">COVID-19 Vaccine Safety</a></h4>
                  <p>Information about vaccine safety and potential side effects...</p>
                </div>
                <div class="post-item clearfix">
                  <img src="../assets/img/news-3.jpg" alt="">
                  <h4><a href="#">Booster Shot Recommendations</a></h4>
                  <p>Find out when and why you might need a booster shot...</p>
                </div>
              </div><!-- End sidebar recent posts-->
            </div>
          </div><!-- End News & Updates -->
        </div><!-- End Right side columns -->
      </div>
    </section>
  </main><!-- End #main -->
  <?php include 'footer.php'; ?>