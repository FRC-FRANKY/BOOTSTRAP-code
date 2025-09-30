<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Error: No student ID provided.");
}

$id = $_GET['id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Error: Student not found.");
}

// Handle update
if (isset($_POST['submit'])) {
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $course = $_POST['course'];

    $sql = "UPDATE students SET name=?, email=?, course=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $course, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Student updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

<h2>Edit Student</h2>
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
    </div>
    <div class="mb-3">
        <label>Age</label>
        <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($student['age']); ?>" required>
    </div>
    <div class="mb-3">
        <label>Course</label>
        <input type="text" name="course" class="form-control" value="<?php echo htmlspecialchars($student['course']); ?>" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Update</button>
    <a href="select.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
