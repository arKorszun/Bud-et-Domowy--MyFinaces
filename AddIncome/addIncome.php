<?php
session_start();
if (!isset($_SESSION['loggedIn'])) {
  header('Location:../Home/home.php');
  exit();
}

$logged_user_id = $_SESSION['logged_user_id'];

if ((isset($_POST['income_amount'])) && (isset($_POST['income_date'])) && (isset($_POST['income_category']))) {

  //Validation flag
  $allRight = true;

  //Amount validation
  $income_amount = $_POST['income_amount'];
  //check if its numeric or empty
  if ($income_amount == "") {
    $allRight = false;
    $_SESSION['e_income_amount'] = "Kwota nie może byc pusta!";
  }
  if (!is_numeric($income_amount)) {
    $allRight = false;
    $_SESSION['e_income_amount'] = "Kwota musi byc liczbą!";
  }
  //date validation 
  $income_date = $_POST['income_date']; //YYYY-MM-DD
  $today = date("Y-m-d");
  if ($income_date > $today) {
    $allRight = false;
    $_SESSION['e_income_date'] = "Data przychodu nie może być późniejsza niż dzień dzisiejszy!";
  }
  if ($income_date == "") {
    $allRight = false;
    $_SESSION['e_income_date'] = "Wybierz datę przychodu!";
  }
  // category validation
  $income_category = $_POST['income_category'];
  if ($income_category == "Wybierz przychód") {
    $allRight = false;
    $_SESSION['e_income_category'] = "Wybierz kategorię przychodu!";
  }
  $income_comment = $_POST['income_comment'];
  $income_comment = htmlentities($income_comment, ENT_QUOTES, "UTF-8");


  require_once "../Login/connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  try {
    $db_connection = new mysqli($host, $db_user, $db_password, $db_name);
    if ($db_connection->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      if ($allRight == true) {
        if ($get_income_id = $db_connection->query("SELECT id FROM incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.user_id = '$logged_user_id' AND incomes_category_assigned_to_users.name = '$income_category'")) {
          $user_data = $get_income_id->fetch_assoc();
          $income_category_id = $user_data['id'];

          $add_income_query = $db_connection->query("INSERT INTO incomes VALUES(NULL,'$logged_user_id','$income_category_id','$income_amount','$income_date','$income_comment')");
          if ($add_income_query) {
            $_SESSION['new_income_added'] = true;
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
  <link rel="stylesheet" href="./styleIncome.css">
  <!-- serif font - logo -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <!-- sans font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">

  <title>AddIncomePage</title>
</head>

<body>
  <main>

    <nav class="navbar navbar-expand-md " aria-label="navbar">
      <div class="container-fluid main-navbar align-items-start ">
        <div class="navbar-brand col-4 px-5 d-flex">
          <a class="navbar-brand" id="logo" href="../Main/main.php"><img class="coin" src="../img/piggy-bank.svg"
              alt="coin icon">
            MyFinances</a>
          <button class="navbar-toggler" id="menubtn" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <div class="col-8 ">

          <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="d-grid gap-2 d-flex navigation ">
              <li class="nav-item">
                <a role="button" href="../Main/main.php" class="btn btn-outline-secondary home px-3 "><img
                    src="../img/house-fill.svg" alt="house icon">Strona Główna</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../AddIncome/addIncome.php" class="btn btn-outline-secondary px-3"><img
                    src="../img/coin.svg" alt="coin icon">Dodaj
                  Przychód</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../AddExpense/addExpense.php" class="btn btn-outline-secondary px-3"><img
                    src="../img/cart-plus.svg" alt="cart icon">Dodaj Wydatek</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../Bilans/bilans.php" class="btn btn-outline-secondary px-3"><img
                    src="../img/clipboard-data.svg" alt="clipbord icon">Przeglądaj Bilans</a>
              </li>
              <li class="nav-item">
                <a role="button" class="btn btn-outline-secondary px-3"><img src="../img/tools.svg"
                    alt="tools icon">Ustawienia</a>
              </li>
              <li class="nav-item">
                <a role="button" href="../Login/logOut.php" class="btn btn-outline-secondary logout px-3"><img
                    src="../img/box-arrow-right.svg" alt="logout icon">Wyloguj</a>
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
              <div class="col registration text-center ">
                <h1 class="h4 mt-2 fw-normal">Wprowadź Dane Przychodu</h1>
              </div>
            </div>
            <?php
            if (isset($_SESSION['new_income_added'])) {
              echo '<div class="col text-center mt-3">' . '<p style="color:green">' . "Dodano przychód do bazy!" . '</p>' . '</div>';
              unset($_SESSION['new_income_added']);
            }
            ?>
            <form method="post">
              <div class="row justify-content-center ">
                <div class="col-9 pt-4">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="amount"><img src="../img/coin.svg" alt="coin icon"></span>
                    <input type="text" class="form-control input-place" placeholder="Kwota" aria-label="Kwota"
                      name="income_amount">
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_income_amount'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_income_amount'] . '</p>' . '</div>';
                unset($_SESSION['e_income_amount']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="income-date"><img src="../img/calendar-event.svg"
                        alt="calendar icon"></span>
                    <input type="date" id="date" class="form-control input-place" aria-label="date" name="income_date">
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_income_date'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_income_date'] . '</p>' . '</div>';
                unset($_SESSION['e_income_date']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3 flex-nowrap">
                    <div class="input-group-prepend">
                      <label class="input-group-text" for="income-category"><img src="../img/book-half.svg"
                          alt="book icon"></label>
                    </div>
                    <select class="custom-select " id="income-category" name="income_category">
                      <option selected>Wybierz przychód</option>
                      <option value="Wynagrodzenie">Wynagrodzenie</option>
                      <option value="Odsetki bankowe">Odsetki bankowe</option>
                      <option value="Sprzedaż na Allegro">Sprzedaż na Allegro</option>
                      <option value="Inne">Inne</option>
                    </select>
                  </div>
                </div>
              </div>
              <?php
              if (isset($_SESSION['e_income_category'])) {
                echo '<div class="col errors text-center ">' . '<p style="color:red">' . $_SESSION['e_income_category'] . '</p>' . '</div>';
                unset($_SESSION['e_income_category']);
              }
              ?>
              <div class="row justify-content-center">
                <div class="col-9 ">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="coment"><img src="../img/pencil.svg" alt="pencil icon"></span>
                    <input type="text" class="form-control input-place" placeholder="Komentarz (opcjonalnie)"
                      aria-label="Komentarz" name="income_comment">
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
                  <button role="button" class="btn btn-primary" id="cancelbtn" href="./addIncome.php"
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