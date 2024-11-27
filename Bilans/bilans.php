<?php
session_start();
if (!isset($_SESSION['loggedIn'])) {
  header('Location:../Home/home.php');
  exit();
}
//$logged_user_id = $_SESSION['logged_user_id'];
$logged_user_id = 1;
//Current month bilans
if (isset($_POST['current_month']))
{
  $end_date = date("Y-m-d");
  $start_date = date("Y-m-01");
  
  require_once "../Login/connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  try {
    $db_connection = new mysqli($host, $db_user, $db_password, $db_name);
    if ($db_connection->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      
        if ($get_incomes_cat_sum = $db_connection->query("SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) AS category_sum FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE incomes.user_id = '$logged_user_id' AND incomes.date_of_income BETWEEN '$start_date' AND '$end_date' GROUP BY incomes_category_assigned_to_users.name ORDER BY category_sum DESC")) 
        {
          
          
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
          <a class="navbar-brand" id="logo" href="../Home/home.php"><img class="coin" src="../img/piggy-bank.svg"
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
                <a role="button" href="../Home/home.php" class="btn btn-outline-secondary logout px-3"><img
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
          <div class="modal-content px-4 py-2">
            <h3 class="modal-header">Wybierz zakres dat</h3>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Data początkowa</span>
              <input type="date" id="date" class="form-control input-place" aria-label="Date"
                aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon2">Data końcowa</span>
              <input type="date" id="date2" class="form-control input-place" aria-label="Date"
                aria-describedby="basic-addon2">
            </div>
            <div class="text-center">
              <a href="#" role="button" class="btn btn-success"> Akceptuj</a>
            </div>
          </div>
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
                  <li><a class="dropdown-item" href="#">Poprzedni miesiąc</a></li>
                  <li><a class="dropdown-item" href="#">Bieżący rok</a></li>
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
                          if(isset($get_incomes_cat_sum))
                          {
                            $row_count = 1;
                            $total_sum = 0;
                            while($incomes_by_sum = $get_incomes_cat_sum->fetch_assoc())
                            {
                              echo '<tr>
                              <th scope="row">'.$row_count.'</th>
                              <td>'.$incomes_by_sum['name'].'</td>
                              <td>'.$incomes_by_sum['category_sum'].'</td>
                              </tr>';
                              $row_count++;
                              $total_sum+=$incomes_by_sum['category_sum'];
                            }
                          }                         
                          unset($get_incomes_cat_sum);
                          ?>

                          <tr>
                            <th colspan="2">Suma</th>
                            <td><?php if(isset($total_sum)) echo $total_sum; ?> PLN</td>
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
                          <tr>
                            <th scope="row">1</th>
                            <td>Ubrania</td>
                            <td>200</td>

                          </tr>
                          <tr>
                            <th scope="row">2</th>
                            <td>Jedzenie</td>
                            <td>500</td>

                          </tr>
                          <tr>
                            <th scope="row">3</th>
                            <td>Na złotą jesień, czyli emeryturę</td>
                            <td>300</td>

                          </tr>
                          <tr>
                            <th scope="row">4</th>
                            <td>Inne</td>
                            <td>1000</td>

                          </tr>
                          <tr>
                            <th colspan="2">Suma </th>
                            <td>2000</td>
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
                          <tr>
                            <th scope="row">1</th>
                            <td>2024-10-15</td>
                            <td>5000</td>
                            <td>Wynagrodzenie</td>
                            <td> </td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">2</th>
                            <td>2024-10-15</td>
                            <td>500</td>
                            <td>Odsetki bankowe</td>
                            <td>Lokata</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">3</th>
                            <td>2024-10-15</td>
                            <td>200</td>
                            <td>Sprzedaż na allegro</td>
                            <td>Sprzedaż bluzy</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">4</th>
                            <td>2024-10-15</td>
                            <td>1000</td>
                            <td>Inne</td>
                            <td>Prezent</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>
                          </tr>

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
                          <tr>
                            <th scope="row">1</th>
                            <td>2024-10-15</td>
                            <td>200</td>
                            <td>Gotówka</td>
                            <td>Ubrania</td>
                            <td>Spodnie</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">2</th>
                            <td>2024-10-15</td>
                            <td>500</td>
                            <td>Karta Kredytowa</td>
                            <td>Jedzenie</td>
                            <td>Obiad na mieście</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">3</th>
                            <td>2024-10-15</td>
                            <td>300</td>
                            <td>Gotówka</td>
                            <td>Na złotą jesień, czyli emeryturę</td>
                            <td></td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>
                          <tr>
                            <th scope="row">4</th>
                            <td>2024-10-15</td>
                            <td>1000</td>
                            <td>Gotówka</td>
                            <td>Inne</td>
                            <td>Rower</td>
                            <td><img src="../img/pencil.svg" alt="pencil icon"> <img src="../img/trash.svg"
                                alt="trash icon"> </td>

                          </tr>

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

                  <h3 id="balance-header">Bilans za wskazany okres wynosi: <span class="balance-difference">4700
                      PLN</span> </h3>
                  <p class="balance-feedback">Gratulacje. Świetnie zarządzasz finansami!</p>
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
                      labels: ['Ubrania', 'Jedzenie', 'Emerytura', 'Inne', ],
                      datasets: [{
                        label: 'Kategoria wydatku',
                        data: [200, 500, 300, 1000],
                        borderWidth: 1
                      }]
                    },
                    options: {}
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