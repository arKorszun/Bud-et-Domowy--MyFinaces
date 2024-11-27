<?php
session_start();
if (!isset($_SESSION['loggedIn'])) {
  header('Location:../Home/home.php');
  exit();
}

$logged_user_id = $_SESSION['logged_user_id'];


if ((isset($_POST['expense_amount'])) && (isset($_POST['expense_date'])) && (isset($_POST['expense_category']))) {

  //Validation flag
  $allRight = true;

  //Amount validation
  $expense_amount = $_POST['expense_amount'];
  //check if its numeric or empty
  if ($expense_amount == "") {
    $allRight = false;
    $_SESSION['e_expense_amount'] = "Kwota nie może byc pusta!";
  }
  if (!is_numeric($expense_amount)) {
    $allRight = false;
    $_SESSION['e_expense_amount'] = "Kwota musi byc liczbą!";
  }
  //date validation 
  $expense_date = $_POST['expense_date']; //YYYY-MM-DD
  $today = date("Y-m-d");
  if ($expense_date > $today) {
    $allRight = false;
    $_SESSION['e_expense_date'] = "Data wydatku nie może być późniejsza niż dzień dzisiejszy!";
  }
  if ($expense_date == "") {
    $allRight = false;
    $_SESSION['e_expense_date'] = "Wybierz datę wydatku!";
  }
  // payment method validation
  $payment_method = $_POST['payment_method'];
  if ($payment_method == "Sposób płatności") {
    $allRight = false;
    $_SESSION['e_payment_method'] = "Wybierz metodę płatności!";
  }

  // category validation
  $expense_category = $_POST['expense_category'];
  if ($expense_category == "Kategoria wydatku") {
    $allRight = false;
    $_SESSION['e_expense_category'] = "Wybierz kategorię wydatku!";
  }

  $expense_comment = $_POST['expense_comment'];
  $expense_comment = htmlentities($expense_comment, ENT_QUOTES, "UTF-8");

  // rememebering form data in case of error
  //$_SESSION['temp_expense_amount'] = $expense_amount;
  //$_SESSION['temp_expense_date'] = $expense_date;
  //$_SESSION['temp_expense_category'] = $expense_category;
  //$_SESSION['temp_payment_method'] = $payment_method;
  //if(isset($_POST['expense_comment'])){
  // $_SESSION['temp_expense_comment'] = $expense_comment;
  //}



  require_once "../Login/connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  try {
    $db_connection = new mysqli($host, $db_user, $db_password, $db_name);
    if ($db_connection->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      if ($allRight == true) {
        if ($get_expense_id = $db_connection->query("SELECT id FROM expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.user_id = '$logged_user_id' AND expenses_category_assigned_to_users.name = '$expense_category'")) {
          $user_expense_data = $get_expense_id->fetch_assoc();
          $expense_category_id = $user_expense_data['id'];

          if ($get_payment_id = $db_connection->query("SELECT id FROM payment_methods_assigned_to_users WHERE payment_methods_assigned_to_users.user_id = '$logged_user_id' AND payment_methods_assigned_to_users.name = '$payment_method'")) {
            $user_payment_data = $get_payment_id->fetch_assoc();
            $payment_method_id = $user_payment_data['id'];

            $add_expense_query = $db_connection->query("INSERT INTO expenses VALUES(NULL,'$logged_user_id','$expense_category_id','$payment_method_id','$expense_amount','$expense_date','$expense_comment')");
            if ($add_expense_query) {
              $_SESSION['new_expense_added'] = true;
            } else {
              throw new Exception($db_connection->error);
            }
          } else {
            throw new Exception($db_connection->error);
          }
        } else {
          throw new Exception($db_connection->error);
        }
      }
    }
  } catch (Exception $error) {
    echo '<span style="color:red;">Błąd serwera! Spróbuj ponownie później!</span>';
    echo '<br/>Informacja developerska: ' . $error;
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
  <link rel="stylesheet" href="./styleExpense.css">
  <!-- serif font - logo -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <!-- sans font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">

  <title>AddExpensePage</title>
</head>

<body>
  <main>

    <nav class="navbar navbar-expand-md " aria-label="navbar">
      <div class="container-fluid main-navbar align-items-start ">
        <div class="navbar-brand col-4 px-5 d-flex">
          <a class="navbar-brand" id="logo" href="./addExpense.php"><img class="coin" src="../img/piggy-bank.svg" alt="coin icon">
            MyFinances</a>
          <button class="navbar-toggler" id="menubtn" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <div class="col-8 ">

          <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="d-grid gap-2 d-flex navigation ">
              <li class="nav-item">
                <a role="button" href="../Main/main.php" class="btn btn-outline-secondary home px-3 "><img src="../img/house-fill.svg"
                    alt="house icon">Strona Główna</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../AddIncome/addIncome.php" class="btn btn-outline-secondary px-3"><img src="../img/coin.svg" alt="coin icon">Dodaj
                  Przychód</a>
              </li>
              <li class="nav-item">
                <a role="button" href="./addExpense.php" class="btn btn-outline-secondary px-3"><img src="../img/cart-plus.svg"
                    alt="cart icon">Dodaj Wydatek</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../Bilans/bilans.php" class="btn btn-outline-secondary px-3"><img src="../img/clipboard-data.svg"
                    alt="clipbord icon">Przeglądaj Bilans</a>
              </li>
              <li class="nav-item">
                <a role="button" class="btn btn-outline-secondary px-3"><img src="../img/tools.svg"
                    alt="tools icon">Ustawienia</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../Login/logOut.php" class="btn btn-outline-secondary logout px-3"><img src="../img/box-arrow-right.svg"
                    alt="logout icon">Wyloguj</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <article>


      <div>
        <div id="site">
          <div class="container text-panel">
            <div class="row ">
              <div class="col window-header text-center ">
                <h1 class="h4 mt-2 fw-normal">Wprowadź Dane Wydatku</h1>
              </div>
            </div>
            <?php
            if (isset($_SESSION['new_expense_added'])) {
              echo '<div class="col text-center mt-3">' . '<p style="color:green">' . "Dodano wydatek do bazy!" . '</p>' . '</div>';
              unset($_SESSION['new_expense_added']);
            }
            ?>
            <form method="post">
              <div class="row justify-content-center ">
                <div class="col-9 pt-4">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="amount"><img src="../img/coin.svg" alt="coin icon"></span>
                    <input type="text" class="form-control input-place" placeholder="Kwota" aria-label="Kwota"
                      name="expense_amount">
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_expense_amount'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_expense_amount'] . '</p>' . '</div>';
                unset($_SESSION['e_expense_amount']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="expense-date"><img src="../img/calendar-event.svg"
                        alt="calendar icon"></span>
                    <input type="date" id="date" class="form-control input-place" aria-label="date"
                      name="expense_date">
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_expense_date'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_expense_date'] . '</p>' . '</div>';
                unset($_SESSION['e_expense_date']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3 flex-nowrap">
                    <div class="input-group-prepend ">
                      <label class="input-group-text" for="payment-method"><img src="../img/wallet2.svg"
                          alt="wallet icon"></label>
                    </div>
                    <select class="custom-select " id="payment-method" name="payment_method">
                      <option selected>Sposób płatności</option>
                      <option value="Karta płatnicza">Karta płatnicza</option>
                      <option value="Gotówka">Gotówka</option>
                      <option value="Karta kredytowa">Karta kredytowa</option>
                    </select>
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_payment_method'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_payment_method'] . '</p>' . '</div>';
                unset($_SESSION['e_payment_method']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3 flex-nowrap">
                    <div class="input-group-prepend ">
                      <label class="input-group-text  " for="expense-category"><img src="../img/book-half.svg"
                          alt="book icon"></label>
                    </div>
                    <select class="custom-select " id="expense-category" name="expense_category">
                      <option selected>Kategoria wydatku</option>
                      <option value="Jedzenie">Jedzenie</option>
                      <option value="Paliwo">Paliwo</option>
                      <option value="Komunikacja miejska">Komunikacja miejska</option>
                      <option value="Taxi">Taxi</option>
                      <option value="Rozrywka">Rozrywka</option>
                      <option value="Zdrowie">Zdrowie</option>
                      <option value="Ubrania">Ubrania</option>
                      <option value="Art.higieniczne">Art.higieniczne</option>
                      <option value="Dzieci">Dzieci</option>
                      <option value="Wypoczynek">Wypoczynek</option>
                      <option value="Podróże">Podróże</option>
                      <option value="Oszczędności">Oszczędności</option>
                      <option value="Emerytura">Emerytura</option>
                      <option value="Spłata długów">Spłata długów</option>
                      <option value="Prezenty">Prezenty</option>
                      <option value="Inne">Inne</option>
                    </select>
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_expense_category'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_expense_category'] . '</p>' . '</div>';
                unset($_SESSION['e_expense_category']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3">
                    <span class="input-group-text " id="coment"><img src="../img/pencil.svg" alt="pencil icon"
                        alt="pencil icon"></span>
                    <input type="text" class="form-control input-place" placeholder="Komentarz (opcjonalnie)"
                      aria-label="coment" name="expense_comment">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-3"></div>
                <div class="col-3 pb-3 text-center">
                  <button type="submit" class="btn btn-primary"
                    style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: 2rem; --bs-btn-font-size: 1.2rem;">Dodaj
                  </button>

                </div>
                <div class="col-3 pb-3 text-center">
                  <button type="button" class="btn btn-primary" id="cancelbtn" href="./addExpense.php"
                    style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: 2rem; --bs-btn-font-size: 1.2rem;">Anuluj
                  </button>

                </div>
            </form>
            <div class="col-3"></div>
          </div>
        </div>
      </div>
      </div>

    </article>


  </main>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

</body>

</html>