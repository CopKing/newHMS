<?php
include 'header.php';
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new vaccine
    if (isset($_POST['add_vaccine'])) {
        $vaccine_name = trim($_POST['vaccine_name']);
        if (!empty($vaccine_name)) {
            $stmt = $conn->prepare("INSERT INTO vaccines (name, available) VALUES (?, 1)");
            $stmt->bind_param("s", $vaccine_name);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Delete vaccine
    if (isset($_POST['delete_vaccine'])) {
        $vaccine_id = (int)$_POST['vaccine_id'];
        $stmt = $conn->prepare("DELETE FROM vaccines WHERE id = ?");
        $stmt->bind_param("i", $vaccine_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Toggle availability
    if (isset($_POST['toggle_available'])) {
        $vaccine_id = (int)$_POST['vaccine_id'];
        $stmt = $conn->prepare("UPDATE vaccines SET available = NOT available WHERE id = ?");
        $stmt->bind_param("i", $vaccine_id);
        $stmt->execute();
        $stmt->close();
    }
}
// Fetch vaccines from database
$result = $conn->query("SELECT id, name, available FROM vaccines ORDER BY name ASC");
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>COVID-19 Vaccine Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Vaccine Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Vaccines</h5>
                        
                        <!-- Add Vaccine Form -->
                        <form method="POST" action="" class="row g-3">
                            <div class="col-auto">
                                <input type="text" name="vaccine_name" placeholder="Enter vaccine name" required class="form-control">
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="add_vaccine" class="btn btn-success">Add Vaccine</button>
                            </div>
                        </form>
                        
                        <!-- Vaccines Table -->
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Vaccine Name</th>
                                    <th>Availability</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $row['available'] ? 'success' : 'danger' ?>">
                                                    <?= $row['available'] ? 'Available' : 'Unavailable' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Toggle Availability Form -->
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="vaccine_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" name="toggle_available" class="btn btn-<?= $row['available'] ? 'warning' : 'primary' ?> btn-sm">
                                                        <?= $row['available'] ? 'Mark Unavailable' : 'Mark Available' ?>
                                                    </button>
                                                </form>
                                                
                                                <!-- Delete Form -->
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vaccine?');">
                                                    <input type="hidden" name="vaccine_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" name="delete_vaccine" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No vaccines found. Add your first vaccine above!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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