<?php
  session_start();
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $conn = new mysqli('localhost', 'root', 'root', 'persistentlogin');
    $sql = "SELECT uid FROM users WHERE username='$username' AND password='$password' LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
      return;
    }
    $row = $result->fetch_assoc();
    $uid = $row['uid'];
    $sessionId = $uid . $username . time();
    $sessionToken = $uid . $username . time();
    $sql = "UPDATE users SET sessionId='$sessionId', sessionToken='$sessionToken' WHERE uid='$uid';";
    $conn->query($sql);

    createSession($uid, $username, $sessionId, $sessionToken);
    header("Location: index.php");
    exit();
  }

  function createSession($uid, $username, $sessionId, $sessionToken) {
    $_SESSION['uid'] = $uid;
    $_SESSION['username'] = $username;
    $_SESSION['session-id'] = $sessionId;
    $_SESSION['session-token'] = $sessionToken;
    setcookie('uid', $uid, time() + 15768000);
    setcookie('username', $username, time() + 15768000);
    setcookie('session-id', $sessionId, time() + 15768000);
    setcookie('session-token', $sessionToken, time() + 15768000);
  }
?>

<!DOCTYPE html>
<html>
<head>
    <title>persistentlogin</title>
</head>

<body>
  <h1>Login</h1>
  <form action="login.php" method="POST">
      <input type="text" name="username" placeholder="Username" /><br>
      <input type="password" name="password" placeholder="Password" /><br>
      <input type="submit" name="login" value="Log In" />
  </form>
</body>
</html>
