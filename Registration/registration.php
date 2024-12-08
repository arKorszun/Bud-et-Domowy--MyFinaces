<?php
  session_start();
  

  if (isset($_POST['email']))
  {
    // Fully correct validation flag
    $allRight = true;

    //Name validation
    $username = $_POST['username'];
    //Length check 2-20 chars
    if((strlen($username)<2) || (strlen($username)>20))
    {
      $allRight = false;
      $_SESSION['e_username'] = "Imię musi zawierać od 2 do 20 znaków!";
    }
    //Check if all char are alfanumeric
    if (ctype_alnum($username) == false )
    {
      $allRight = false;
      $_SESSION['e_username'] = "Imię może składać sie tylko cyfr i liter(bez polskich znaków)!";
    }

    //email validation
    $email = $_POST['email'];
    $email_safe = filter_var($email, FILTER_SANITIZE_EMAIL);
    if((filter_var($email_safe, FILTER_VALIDATE_EMAIL)== false) || ($email_safe!=$email))
    {
      $allRight = false;
      $_SESSION['e_email'] = "Podaj poprawny adres email!";
    }

    //password validation
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if((strlen($password)<8) || (strlen($password)>20))
    {
      $allRight = false;
      $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
    }
    if($password!=$password2)
    {
      $allRight = false;
      $_SESSION['e_password'] = "Podane hasła nie są identyczne!";
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
   
    require_once "../Login/connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try
    {
      $db_connection = new mysqli($host,$db_user,$db_password,$db_name);
      if ($db_connection->connect_errno!=0)
      {
        throw new Exception(mysqli_connect_errno());
      } else {

        //check if email is free
        $result = $db_connection->query("SELECT id FROM users WHERE email='$email'");
        if (!$result) throw new Exception($db_connection->error);
        $is_there_such_email = $result->num_rows;
        if($is_there_such_email>0)
        {
          $allRight = false;
          $_SESSION['e_email'] = "Istnieje już konto przypisane do podanego adresu email!";
        }

        
      }

      if ($allRight == true)
      {
      // Validation completed - add user to db
      if ($db_connection->query("INSERT INTO users VALUES(NULL,'$username', '$password_hash', '$email')"))
      {
        $_SESSION['registration_complete'] = true;
        // Get new user id and add default category to finance tables
        
        $get_new_user_id = $db_connection->query(
          sprintf("SELECT id FROM users WHERE email='%s'",
          mysqli_real_escape_string($db_connection,$email)));
          
        if ($get_new_user_id)
        {
          //get user id
          $user_id = $get_new_user_id->fetch_assoc();
          $new_user_id = $user_id['id'];
          
          //copy default category of incomes/expenses and payment methods for users
          $nameI_query = $db_connection->query("SELECT name FROM incomes_category_default");
          while ($category_name = $nameI_query->fetch_assoc())
            {
              $name = $category_name['name'];
              $db_connection->query("INSERT INTO incomes_category_assigned_to_users VALUES(NULL, '$new_user_id','$name') ");
            } 

          $nameE_query = $db_connection->query("SELECT name FROM expenses_category_default");
          while ($category_name = $nameE_query->fetch_assoc())
            {
               $name = $category_name['name'];
              $db_connection->query("INSERT INTO expenses_category_assigned_to_users VALUES(NULL, '$new_user_id','$name') ");
            } 

          $nameP_query = $db_connection->query("SELECT name FROM payment_methods_default");
          while ($payment_name = $nameP_query->fetch_assoc())
            {
              $name = $payment_name['name'];
              $db_connection->query("INSERT INTO payment_methods_assigned_to_users VALUES(NULL, '$new_user_id','$name') ");
            } 
  
        } else{
          throw new Exception($db_connection->error);
        }


        header('Location: ../Login/login.php');
      } else{
         throw new Exception($db_connection->error);
      }      
      }

      $db_connection->close();
    }
    catch(Exception $error)
    {
      echo '<span style="color:red;">Błąd serwera! Spróbuj ponownie później!</span>';
      echo'<br/>Informacja developerska: '.$error;      
    }    
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="./styleRegistration.css">
  <!-- serif font - logo -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <!-- sans font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">

  <title>RegistrationPage</title>
</head>

<body>
  <main>

    <nav class="navbar navbar-expand " aria-label="Second navbar example">
      <div class="container-fluid main-navbar align-items-end ">
        <div class="navbar-brand col-8 px-5 align-bottom">
          <a class="navbar-brand" id="logo" href="../Home/home.php"><img class="coin" src="../img/piggy-bank.svg" alt="coin icon">
            MyFinances</a>
        </div>

      </div>
    </nav>

    <article>

      <div>
        <div id="site">
          <div class="container text-panel">
            <div class="row ">
              <div class="col registration text-center ">
                <h1 class="h4 mt-2 fw-normal">Rejestracja</h1>
              </div>
            </div>
            <form method="post">
              <div class="row justify-content-center ">
                <div class="col-9 pt-4">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><img src="../img/person.svg"
                        alt="person icon"></span>
                    <input type="text" class="form-control input-place" placeholder="Imie" aria-label="Imie"
                      name="username">                   
                  </div>
                </div>                
              </div>
              <?php
                    if(isset($_SESSION['e_username']))
                    {
                      echo '<div class="col errors text-center ">'.'<p style="color:red">'.$_SESSION['e_username'].'</p>'.'</div>';
                      unset($_SESSION['e_username']);
                    }
                    ?>
              <div class="row justify-content-center">
                <div class="col-9">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon2"><img src="../img/envelope-at.svg"
                        alt="envelope icon"></span>
                    <input type="email" class="form-control input-place" placeholder="Email" aria-label="Email"
                      name="email">
                  </div>
                </div>
              </div>
              <?php
                    if(isset($_SESSION['e_email']))
                    {
                      echo '<div class="col errors text-center ">'.'<p style="color:red">'.$_SESSION['e_email'].'</p>'.'</div>';
                      unset($_SESSION['e_email']);
                    }
                    ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon3"><img src="../img/lock.svg" alt="lock icon"></span>
                    <input type="password" class="form-control input-place" placeholder="Hasło" aria-label="Hasło"
                      name="password">
                  </div>
                </div>
              </div>
              <?php
                    if(isset($_SESSION['e_password']))
                    {
                      echo '<div class="col errors text-center ">'.'<p style="color:red">'.$_SESSION['e_password'].'</p>'.'</div>';
                      unset($_SESSION['e_password']);
                    }
                    ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon4"><img src="../img/lock.svg" alt="lock icon"></span>
                    <input type="password" class="form-control input-place" placeholder="Powtórz hasło"
                      aria-label="Powtórz hasło" name="password2">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col pb-3 text-center">
                  <button type="submit" class="btn btn-primary"
                    style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: 5rem; --bs-btn-font-size: 1.2rem;">Zarejestruj</button>

                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </article>


  </main>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="./index.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

</body>

</html>