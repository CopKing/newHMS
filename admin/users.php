<?php
include 'header.php';
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new user
    if (isset($_POST['add_user'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role_id = (int)$_POST['role_id'];
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        
        if (!empty($name) && !empty($email) && !empty($role_id)) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, role_id, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $name, $email, $role_id, $password);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Update user role
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $role_id = (int)$_POST['role_id'];
        $stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $role_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch users from database
$result = $conn->query("SELECT id, name, email, role_id FROM users ORDER BY name ASC");
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>User Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Tables</li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Users</h5>
                        
                        <!-- Add User Form -->
                        <form method="POST" action="" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <input type="text" name="name" placeholder="Full Name" required class="form-control">
                            </div>
                            <div class="col-md-3">
                                <input type="email" name="email" placeholder="Email Address" required class="form-control">
                            </div>
                            <div class="col-md-2">
                                <select name="role_id" required class="form-select">
                                    <option value="">Select Role</option>
                                    <option value="1">Admin</option>
                                    <option value="2">Hospital</option>
                                    <option value="3">Patient</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="password" name="password" placeholder="Password" required class="form-control">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_user" class="btn btn-success w-100">Add User</button>
                            </div>
                        </form>
                        
                        <!-- Users Table -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <?php 
                                                $roleNames = [
                                                    1 => 'Admin',
                                                    2 => 'Hospital',
                                                    3 => 'Patient',
                                                ];
                                                echo $roleNames[$row['role_id']] ?? 'Unknown';
                                                ?>
                                            </td>
                                            <td>
                                                <!-- Update Role Form -->
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                    <select name="role_id" class="form-select form-select-sm d-inline" style="width: auto;">
                                                        <option value="1" <?= $row['role_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                                                        <option value="2" <?= $row['role_id'] == 2 ? 'selected' : '' ?>>Hospital</option>
                                                        <option value="3" <?= $row['role_id'] == 3 ? 'selected' : '' ?>>Patient</option>
                                                    </select>
                                                    <button type="submit" name="update_role" class="btn btn-primary btn-sm">Update</button>
                                                </form>
                                                
                                                <!-- Delete Form -->
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No users found. Add your first user above!</td>
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
?>