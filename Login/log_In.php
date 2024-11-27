<?php
session_start();

if (!isset($_POST['email']) || !isset($_POST['password'])) {
  header('Location: login.php');
  exit();
}

require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try {
  $db_connection = new mysqli($host, $db_user, $db_password, $db_name);
  if ($db_connection->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    $email = $_POST['email'];
    $password = $_POST['password'];
    //email sanitization 
    $email = htmlentities($email, ENT_QUOTES, "UTF-8");
    $email_safe = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = $email_safe;

    //sql iniection prevention
    $query_result = $db_connection->query(
      sprintf(
        "SELECT * FROM users WHERE email='%s'",
        mysqli_real_escape_string($db_connection, $email)
      )
    );

    if (!$query_result) throw new Exception($db_connection->error);
    else {
      $is_there_such_user = $query_result->num_rows;
      if ($is_there_such_user > 0) {
        $user_data = $query_result->fetch_assoc();
        if (password_verify($password, $user_data['password'])) {
          $_SESSION['logged_user_id'] = $user_data['id'];
          $_SESSION['logged_user_name'] = $user_data['username'];
          $_SESSION['loggedIn'] = true;

          unset($_SESSION['login_error']);
          $query_result->close();
          $_SESSION['freshly_logged'] = true;
          header('Location: ../Main/main.php');
        } else {
          $_SESSION['login_error'] = '<span style="color:red" >Nieprawidłowy login lub hasło!</span>';
          header('Location:login.php');
        }
      } else {
        $_SESSION['login_error'] = '<span style="color:red" >Nieprawidłowy login lub hasło!</span>';
        header('Location:login.php');
      }
    }

    $db_connection->close();
  }
} catch (Exception $error) {
  echo '<span style="color:red;">Bład serwera, wróć później</span>';
  echo '<br/>Informacja developerska ' . $error;
}

$email = $_POST['email'];
$password = $_POST['password'];
