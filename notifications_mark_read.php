<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
try {
  if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
  $uid = (int)$_SESSION['user_id'];
  // Only if table exists
  $has = $conn->query("SHOW TABLES LIKE 'notifications'");
  if (!$has || $has->num_rows === 0) { echo json_encode(['success'=>true,'updated'=>0]); exit; }
  $notifId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if ($notifId > 0) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $notifId, $uid);
  } else {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND (is_read = 0 OR is_read IS NULL)");
    $stmt->bind_param('i', $uid);
  }
  $stmt->execute();
  $count = $stmt->affected_rows;
  $stmt->close();
  echo json_encode(['success'=>true,'updated'=>$count]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
