// student_lab_crud/insert.php
<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

<h2>Add Student</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Age</label>
        <input type="number" name="age" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Course</label>
        <input type="text" name="course" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Save</button>
    <a href="select.php" class="btn btn-secondary">Back</a>
</form>

<?php
if (isset($_POST['submit'])) {
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $age    = $_POST['age'];
    $course = $_POST['course'];

    $sql = "INSERT INTO students (name, email, age, course) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $name, $email, $age, $course);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success mt-3'>Student added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
    }
}
?>

</body>
</html>
