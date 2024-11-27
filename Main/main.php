<?php
session_start();
// redirection to home.php when no user is logged In
if(!isset($_SESSION['loggedIn']))
{
  header('Location:../Home/home.php');
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="./stylemain.css">
  <!-- serif font - logo -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <!-- sans font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">

  <title>HomePage</title>
</head>

<body>
  <main>

    <nav class="navbar navbar-expand-md " aria-label="navbar">
      <div class="container-fluid main-navbar align-items-start ">
        <div class="navbar-brand col-4 px-5 d-flex">
          <a class="navbar-brand" id="logo" href="../Home/home.html"><img class="coin" src="../img/piggy-bank.svg" alt="coin icon">
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

      <div id="site">
        <div class=" container menu  ">
          <div class=" row justify-content-center ">
            <div class="col ">
              <div class="loging-confirmation text-center ">
                <?php 
                if(isset($_SESSION['freshly_logged'])){
                echo '<p class="log">Logowanie zakończone sukcesem!</p>';
               }
               else{
                echo '<p class="log"></p>';
               }
                unset($_SESSION['freshly_logged']);
                ?>

              </div>
            </div>
          </div>
          <div class=" row justify-content-center ">
            <div class="col ">

              <div class="text-panel ">
                <div class="container">
                  <div class="row">
                    <div class="col-9">
                      <h2 class="hello">
                        <?php echo "Witaj ".$_SESSION['logged_user_name']."!" ?>
                      </h2>
                      <p>W celu nawigacji użyj menu znajdującego się powyżej
                      </p>
                    </div>
                    <div class="col-3 pt-4 index">
                      <img height="40" src="../img/arrow-up.svg" alt="arrow up">
                    </div>
                  </div>
                </div>
              </div>
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