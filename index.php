<?php
  session_start();

  // Check if id exists in cookie
  if (!isset($_COOKIE['session-id'])) {
    destroySession();
    header("Location: login.php");
    exit();
  }

  // Check if token exists in cookie
  if (!isset($_COOKIE['session-token'])) {
    destroySession();
    header("Location: login.php");
    exit();
  }

  // Check if id and token exists in session
  if (!isset($_SESSION['session-token']) || !isset($_SESSION['session-token'])) {
    // Attempt to restore session
    session_destroy();
    $conn = new mysqli('localhost', 'root', 'root', 'persistentlogin');
    $sessionId = $_COOKIE['session-id'];
    $sessionToken = $_COOKIE['session-token'];
    $sql = "SELECT uid, username FROM users WHERE sessionId='$sessionId' AND sessionToken='$sessionToken' LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
      destroySession();
      header("Location: login.php");
      exit();
    }
    $row = $result->fetch_assoc();
    $uid = $row['uid'];
    $username = $row['username'];

    // Create a new session
    $sessionId = $uid . $username . time();
    $sessionToken = $uid . $username . time();
    $sql = "UPDATE users SET sessionId='$sessionId', sessionToken='$sessionToken' WHERE uid='$uid';";
    $conn->query($sql);

    createSession($uid, $username, $sessionId, $sessionToken);
    echo '<h1>Relogged in</h1>';
  } else {
    // Check if id in session matches with id in cookie
    if ($_SESSION['session-id'] !== $_COOKIE['session-id']) {
      destroySession();
      header("Location: login.php");
      exit();
    }

    // Check if token in session matches with token in cookie
    if ($_SESSION['session-token'] !== $_COOKIE['session-token']) {
      destroySession();
      header("Location: login.php");
      exit();
    }

    // Check if uid and username are set
    if (!isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
      destroySession();
      header("Location: login.php");
      exit();
    }

    // Validate current session
    $conn = new mysqli('localhost', 'root', 'root', 'persistentlogin');
    $sessionId = $_SESSION['session-id'];
    $sessionToken = $_SESSION['session-token'];
    $sql = "SELECT uid, username FROM users WHERE sessionId='$sessionId' AND sessionToken='$sessionToken' LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
      destroySession();
      header("Location: login.php");
      exit();
    }
    $row = $result->fetch_assoc();
    $uid = $row['uid'];
    $username = $row['username'];

    if ($uid !== $_SESSION['uid'] || $username !== $_SESSION['username']) {
      destroySession();
      header("Location: login.php");
      exit();
    }

    echo '<h1>Already logged in</h1>';
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

  function destroySession() {
    session_destroy();
    setcookie("session-id", "", time() - 3600);
    setcookie("session-token", "", time() - 3600);
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>persistenlogin</title>
  </head>

  <body>
    <a href="login.php">Log In</a><br>
    <a href="logout.php">Log Out</a><br>
    <a href="destroysession.php">Destroy Session</a>
  </body>
</html>
