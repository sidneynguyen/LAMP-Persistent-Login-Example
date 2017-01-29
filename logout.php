<?php
session_start();
$sessionId = $_SESSION['session-id'];
$conn = new mysqli('localhost', 'root', 'root', 'persistentlogin');
$sql = "UPDATE users SET sessionId=NULL, sessionToken=NULL WHERE sessionId='$sessionId';";
$conn->query($sql);
destroySession();

function destroySession() {
  session_destroy();
  setcookie("session-id", "", time() - 3600);
  setcookie("session-token", "", time() - 3600);
}
header("Location: index.php");
exit();
?>
