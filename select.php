// student_lab_crud/select.php
<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

<h2>Students</h2>
<a href="insert.php" class="btn btn-success mb-3">Add Student</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['course']}</td>
                <td>
                    <a href='update.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'>No records found</td></tr>";
    }
    ?>
    </tbody>
</table>

</body>
</html>
