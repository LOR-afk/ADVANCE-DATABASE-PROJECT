<?php
session_start();

if (empty($_SESSION) || !isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/");
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = '1cashier_db';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getUsers($conn) {
    $query = "SELECT * FROM users WHERE role != 'admin'";
    return $conn->query($query);
}

if (isset($_POST['add_user'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (first_name, last_name, username, password, role) VALUES ('$first_name', '$last_name', '$username', '$password', '$role')";
    if ($conn->query($query)) {
        $new_user = [
            'id' => $conn->insert_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'role' => $role
        ];
        echo json_encode(['success' => true, 'user' => $new_user]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit;
    }
}

if (isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    $query = "UPDATE users SET role = '$new_role' WHERE id = $user_id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'user_id' => $user_id, 'new_role' => ucfirst($new_role)]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit;
    }
}

$users = getUsers($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .dropdown-menu {
            background-color: #2f8d2f; /* Dropdown background color */
        }
        .dropdown-item:hover {
            background-color: #0056b3; /* Dropdown item hover color */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a href="manage-product.php" class="navbar-brand">Your Daily Cravings</a>
        <span class="navbar-text ms-3">Welcome, <?= ucfirst($_SESSION['username']) ?></span>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
 
                   ☰
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="admin_dashboard.php">Dashboard</a></li>
                    <li><a class="dropdown-item" href="manage-product.php">Manage Products</a></li>
                    <li><a class="dropdown-item" href="manage-users.php">Manage Users</a></li>
                    <li><a class="dropdown-item" href="transaction_history.php">Transaction History</a></li>
                    <li><a class="dropdown-item" href="cashier.php">Cashier View</a></li>
                </ul>
            </div>
        </div>
        <a href="../actions/logout.php" class="btn btn-danger ms-3">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="display-6 fw-bold text-center">Manage Users</h1>
    <div class="row">
        <div class="col-md-6">
            <h3>Add New User</h3>
            <form id="addUserForm">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </div>
        <div class="col-md-6">
            <h3>User List</h3>
            <table class="table table-bordered" id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()) { ?>
                        <tr data-user-id="<?= $user['id'] ?>">
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['first_name'] . ' ' . $user['last_name'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td class="user-role"><?= ucfirst($user['role']) ?></td>
                            <td>
                                <form class="change-role-form" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="new_role" class="form-select form-select-sm" required>
                                        <option value="cashier" <?= $user['role'] === 'cashier' ? 'selected' : '' ?>>Cashier</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-warning btn-sm">Change Role</button>
                                </form>
                                <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('addUserForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    formData.append('add_user', true); // Add this to identify the form submission

    fetch('manage-users.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const newUserRow = document.createElement('tr');
            newUserRow.setAttribute('data-user-id', data.user.id);
            newUserRow.innerHTML = `
                <td>${data.user.id}</td>
                <td>${data.user.first_name} ${data.user.last_name}</td>
                <td>${data.user.username}</td>
                <td class="user-role">${data.user.role.charAt(0).toUpperCase() + data.user.role.slice(1)}</td>
                <td>
                    <form class="change-role-form" style="display: inline;">
                        <input type="hidden" name="user_id" value="${data.user.id}">
                        <select name="new_role" class="form-select form-select-sm" required>
                            <option value="cashier">Cashier</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">Change Role</button>
                    </form>
                    <a href="?delete=${data.user.id}" class="btn btn-danger btn-sm">Delete</a>
                </td>
            `;
            document.querySelector('#userTable tbody').appendChild(newUserRow);
            this.reset();
        } else {
            alert('Failed to add user: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => console.error('Error:', error));
});

document.querySelectorAll('.change-role-form').forEach(form => {
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        formData.append('change_role', true); // Add this to identify the form submission

        fetch('manage-users.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const roleCell = this.closest('tr').querySelector('.user-role');
                roleCell.textContent = data.new_role.charAt(0).toUpperCase() + data.new_role.slice(1);
            } else {
                alert('Failed to change role: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
</body>
</html>