<?php
session_start();
if (!isset($_SESSION['loggedIn'])) {
  header('Location:../Home/home.php');
  exit();
}
$logged_user_id = $_SESSION['logged_user_id'];
//$logged_user_id = 1;
//Current month bilans
if (isset($_POST['current_month']) || isset($_POST['previous_month']) || isset($_POST['current_year']) || isset($_POST['custom_period'])) {
  if (isset($_POST['current_month'])) {
    $end_date = date("Y-m-d");
    $start_date = date("Y-m-01");
  } else if (isset($_POST['previous_month'])) {

    $year = date("Y");
    $month = date("m") - 1;
    if ($month < 1) {
      $month = 12;
      $year -= 1;
    }
    $day =  date("t", mktime(0, 0, 0, $month, 1, $year));
    $end_date = date("$year-$month-$day");
    $start_date = date("$year-$month-01");
  } else if (isset($_POST['current_year'])) {
    $end_date = date("Y-m-d");
    $start_date = date("Y-01-01");
  } else if (isset($_POST['custom_period'])) {
    $end_date = $_POST['date_end'];
    $start_date = $_POST['date_start'];
  }
  $get_balance = true;

  require_once "../Login/connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  try {
    $db_connection = new mysqli($host, $db_user, $db_password, $db_name);
    if ($db_connection->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      //get sum in incomes categories
      if ($get_incomes_cat_sum = $db_connection->query("SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) AS category_sum FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE incomes.user_id = '$logged_user_id' AND incomes.date_of_income BETWEEN '$start_date' AND '$end_date' GROUP BY incomes_category_assigned_to_users.name ORDER BY category_sum DESC")) {
      } else {
        throw new Exception($db_connection->error);
      }
      //get sum in expenses categories
      if ($get_expenses_cat_sum = $db_connection->query("SELECT expenses_category_assigned_to_users.name, SUM(expenses.amount) AS category_sum FROM expenses_category_assigned_to_users INNER JOIN expenses ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE expenses.user_id = '$logged_user_id' AND expenses.date_of_expense BETWEEN '$start_date' AND '$end_date' GROUP BY expenses_category_assigned_to_users.name ORDER BY category_sum DESC")) {
      } else {
        throw new Exception($db_connection->error);
      }

      //get all incomes 
      if ($get_all_incomes = $db_connection->query("SELECT incomes.amount, incomes.date_of_income, incomes.income_comment , incomes_category_assigned_to_users.name AS category_name  FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE incomes.user_id = '$logged_user_id' AND incomes.date_of_income BETWEEN '$start_date' AND '$end_date' ORDER BY incomes.date_of_income DESC ")) {
      } else {
        throw new Exception($db_connection->error);
      }

      //get all expenses 
      if ($get_all_expenses = $db_connection->query("SELECT expenses.amount, expenses.date_of_expense, expenses.expense_comment , expenses_category_assigned_to_users.name AS expense_category_name, payment_methods_assigned_to_users.name AS payment_method FROM expenses INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id INNER JOIN payment_methods_assigned_to_users ON payment_methods_assigned_to_users.id=expenses.payment_method_assigned_to_user_id WHERE expenses.user_id = '$logged_user_id' AND expenses.date_of_expense BETWEEN '$start_date' AND '$end_date' ORDER BY expenses.date_of_expense DESC ")) {
      } else {
        throw new Exception($db_connection->error);
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
  <link rel="stylesheet" href="./stylebilans.css">
  <!-- serif font - logo -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <!-- sans font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">

  <title>BilansPage</title>
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

    <section class="date-modal">
      <div class="modal fade bd-modal-md" tabindex="-1" role="dialog" aria-labelledby="date-modal" aria-hidden="true">
        <div class="modal-dialog modal-md">
          <form method="post">
            <div class="modal-content px-4 py-2">
              <h3 class="modal-header">Wybierz zakres dat</h3>
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">Data początkowa</span>
                <input type="date" id="date" class="form-control input-place" name="date_start" aria-label="Date">
              </div>
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon2">Data końcowa</span>
                <input type="date" id="date2" class="form-control input-place" name="date_end" aria-label="Date">
              </div>
              <div class="text-center">
                <button type="submit" value="1" name="custom_period" class="btn btn-success"> Akceptuj</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>

    <article>
      <div id="site">

        <div class=" container-fluid menu  ">
          <div class="row">
            <div class="dropdown nav-item d-flex justify-content-md-end period-change">
              <button class="btn btn-secondary dropdown-toggle period-list" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Wybierz Okres
              </button>
              <form method="post">
                <ul class="dropdown-menu">
                  <li><button class="dropdown-item" type="submit" value="1" name="current_month">Bieżący miesiąc</button></li>
                  <li><button class="dropdown-item" value="1" name="previous_month" type="submit">Poprzedni miesiąc</button></li>
                  <li><button class="dropdown-item" type="submit" value="1" name="current_year">Bieżący rok</button></li>
                  <li><a class="dropdown-item" id="modal-item" href="#" data-bs-toggle="modal"
                      data-bs-target=".bd-modal-md">Niestandardowy</a>
                  </li>
                </ul>
              </form>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 col-sm-12 pt-5">
              <div class="row">
                <div class="row">
                  <div class="col-md-6">
                    <div class="bd-example text-panel table">
                      <table class="table table-hover incomes">
                        <thead>
                          <tr>
                            <th colspan="3"> Przychody według kategorii </th>
                          </tr>
                          <tr>
                            <th scope="col"> </th>
                            <th scope="col">Kategoria</th>
                            <th scope="col">Kwota</th>

                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (isset($get_incomes_cat_sum)) {
                            $row_count = 1;
                            $total_incomes_sum = 0;
                            while ($incomes_by_sum = $get_incomes_cat_sum->fetch_assoc()) {
                              echo '<tr>
                              <th scope="row">' . $row_count . '</th>
                              <td>' . $incomes_by_sum['name'] . '</td>
                              <td>' . $incomes_by_sum['category_sum'] . '</td>
                              </tr>';
                              $row_count++;
                              $total_incomes_sum += $incomes_by_sum['category_sum'];
                            }
                          }
                          unset($get_incomes_cat_sum);
                          ?>

                          <tr>
                            <th colspan="2">Suma</th>
                            <td><?php if (isset($total_incomes_sum)) echo $total_incomes_sum; ?> PLN</td>
                          </tr>
                        </tbody>
                      </table>
                      <button class="btn btn-outline-secondary" id="show-incomes"> Pokaż przychody w okresie</button>
                      <div class="">

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="bd-example text-panel table">
                      <table class="table table-hover expenses ">
                        <thead>
                          <tr>
                            <th colspan="3"> Wydatki według kategorii </th>
                          </tr>
                          <tr>
                            <th scope="col"></th>
                            <th scope="col">Kategoria</th>
                            <th scope="col">Kwota</th>

                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (isset($get_expenses_cat_sum)) {
                            $row_count = 1;
                            $expenses_sum_array = array();
                            $expenses_cat_array = array();
                            $total_expenses_sum = 0;
                            while ($expenses_by_sum = $get_expenses_cat_sum->fetch_assoc()) {
                              echo '<tr>
                              <th scope="row">' . $row_count . '</th>
                              <td>' . $expenses_by_sum['name'] . '</td>
                              <td>' . $expenses_by_sum['category_sum'] . '</td>
                              </tr>';
                              $row_count++;
                              $total_expenses_sum += $expenses_by_sum['category_sum'];
                              array_push($expenses_sum_array, $expenses_by_sum['category_sum']);
                              array_push($expenses_cat_array, $expenses_by_sum['name']);
                            }
                          }
                          unset($get_expenses_cat_sum);

                          ?>

                          <tr>
                            <th colspan="2">Suma</th>
                            <td><?php if (isset($total_expenses_sum)) echo $total_expenses_sum; ?> PLN</td>
                          </tr>
                        </tbody>
                      </table>
                      <button class="btn btn-outline-secondary" id="show-expenses"> Pokaż wydatki w okresie</button>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="bd-example text-panel table" id="incomes-details">
                      <table class="table table-hover ">
                        <thead>
                          <tr>
                            <th colspan="6"> Szczegółowy wykaz przychodów </th>
                          </tr>
                          <tr>
                            <th scope="col"> </th>
                            <th scope="col">Data</th>
                            <th scope="col">Kwota</th>
                            <th scope="col">Kategoria</th>
                            <th scope="col">Komentarz</th>
                            <th scope="col"><img src="../img/tools.svg" alt="tools icon"></th>

                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (isset($get_all_incomes)) {
                            $row_count = 1;

                            while ($incomes = $get_all_incomes->fetch_assoc()) {
                              echo '<tr>
                              <th scope="row">' . $row_count . '</th>
                              <td>' . $incomes['date_of_income'] . '</td>
                              <td>' . $incomes['amount'] . '</td>
                              <td>' . $incomes['category_name'] . '</td>
                              <td>' . $incomes['income_comment'] . '</td>
                              <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                              </tr>';
                              $row_count++;
                            }
                          }
                          unset($get_all_incomes);
                          ?>

                        </tbody>
                      </table>

                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="bd-example text-panel table" id="expenses-details">
                      <table class="table table-hover ">
                        <thead>
                          <tr>
                            <th colspan="7"> Szczegółowy wykaz wydatków </th>
                          </tr>
                          <tr>
                            <th scope="col"></th>
                            <th scope="col">Data</th>
                            <th scope="col">Kwota</th>
                            <th scope="col">Sposób płatności</th>
                            <th scope="col">Kategoria</th>
                            <th scope="col">Komentarz</th>
                            <th scope="col"><img src="../img/tools.svg" alt="tools icon"></th>

                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (isset($get_all_expenses)) {
                            $row_count = 1;

                            while ($expenses = $get_all_expenses->fetch_assoc()) {
                              echo '<tr>
                              <th scope="row">' . $row_count . '</th>
                              <td>' . $expenses['date_of_expense'] . '</td>
                              <td>' . $expenses['amount'] . '</td>
                              <td>' . $expenses['payment_method'] . '</td>
                              <td>' . $expenses['expense_category_name'] . '</td>
                              <td>' . $expenses['expense_comment'] . '</td>
                              <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                              </tr>';
                              $row_count++;
                            }
                          }
                          unset($get_all_expenses);
                          ?>


                        </tbody>
                      </table>

                    </div>
                  </div>

                </div>
              </div>

            </div>

            <div class="col-md-4 p-4 pt-5">
              <div class="row text-panel balance-sum ">
                <div class="col ">

                  <h3 id="balance-header">Bilans za wskazany okres wynosi:
                    <?php

                    if (isset($get_balance)) {
                      $summar_balance = $total_incomes_sum - $total_expenses_sum;
                      if ($summar_balance > 0) {
                        echo '<span class="balance-difference">' . $summar_balance .
                          ' PLN</span> </h3>';
                        echo '<p class="balance-feedback" style="color:green">Gratulacje. Świetnie zarządzasz finansami!</p>';
                      } else {
                        echo '<span class="balance-difference" style="color:red">' . $summar_balance .
                          ' PLN</span> </h3>';
                        echo '<p class="balance-feedback" style="color:red">Uważaj! Twoje wydatki przerosły dochody! </p>';
                      }
                    }
                    ?>
                </div>
              </div>
              <div class="row pie-char text-panel">
                <div>
                  <canvas id="myChart"></canvas>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                  const ctx = document.getElementById('myChart');

                  new Chart(ctx, {
                    type: 'pie',
                    data: {
                      labels: [
                        <?php
                        if (isset($get_balance)) {
                          foreach ($expenses_cat_array as $category) {
                            echo "'" . $category . "',";
                          }
                        }
                        ?>
                      ],

                      datasets: [{
                        label: 'Kwota (PLN)',
                        data: [
                          <?php
                          if (isset($get_balance)) {
                            foreach ($expenses_sum_array as $sum) {
                              echo "'" . $sum . "',";
                            }
                          }
                          ?>
                        ],
                        borderWidth: 1
                      }]
                    },
                    options: {
                      title: {
                        display: true,
                        text: "Rozkład wydatków według kategorii"
                      }
                    }
                  });
                </script>

              </div>
            </div>

          </div>

        </div>
      </div>
    </article>


  </main>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="./index.js"></script>
</body>

</html>