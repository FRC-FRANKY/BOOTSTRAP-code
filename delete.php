// student_lab_crud/delete.php
<?php
include 'db_connect.php';

$id = $_GET['id'];

$sql = "DELETE FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: select.php");
} else {
    echo "Error deleting record: " . $stmt->error;
}
?>
