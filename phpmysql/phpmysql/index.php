<?php include("config.php"); ?> <!-- Siin tõmmatakse sisse andmebaasiühenduse seaded -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HKHK spordipäev 2025</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>HKHK spordipäev 2025!</h1>
    <!-- Link admini sisselogimisele -->
    <a href="login.php" class="btn btn-primary">Admin</a>

    <!-- Otsinguvorm -->
    <form action="index.php" method="get" class="py-4">
      <input type="text" name="otsi">
      <select name="cat">
        <option value="fullname">Nimi</option>
        <option value="category">Spordiala</option>
      </select>
      <input type="submit" value="Otsi...">
    </form>

    <!-- Tabel registreeritud osalejate jaoks -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">id</th>
          <th scope="col">firstname</th>
          <th scope="col">email</th>
          <th scope="col">age</th>
          <th scope="col">gender</th>
          <th scope="col">category</th>
          <th scope="col">reg_time</th>
          <!-- <th scope="col">muuda</th>
          <th scope="col">kustuta</th> -->
        </tr>
      </thead>
      <tbody>

        <?php
            // --- LEHEKÜLJENDUS ---

            $uudiseid_lehel = 50; // mitu kirjet ühel lehel näidatakse

            // loeme, mitu kirjet kokku on
            $uudiseid_kokku_paring = "SELECT COUNT('id') FROM sport2025";
            $lehtede_vastus = mysqli_query($yhendus, $uudiseid_kokku_paring);
            $uudiseid_kokku = mysqli_fetch_array($lehtede_vastus);
            $lehti_kokku = $uudiseid_kokku[0]; // mitu kirjet kokku
            $lehti_kokku = ceil($lehti_kokku/$uudiseid_lehel); // mitu lehte kokku

            // infot väljastamiseks (võid eemaldada kui ei soovi näidata)
            echo 'Lehekülgi kokku: '.$lehti_kokku.'<br>';
            echo 'Uudiseid lehel: '.$uudiseid_lehel.'<br>';

            // Kasutaja valitud leht (kui pole valitud, siis esimene leht)
            if (isset($_GET['page'])) {
              $leht = $_GET['page'];
            } else {
              $leht = 1;
            }

            // Millisest kirjest alustatakse
            $start = ($leht-1)*$uudiseid_lehel;

            // --- OTSING ---
            if(isset($_GET['otsi']) && !empty($_GET["otsi"])){
              $s = $_GET['otsi'];   // otsitav sõna
              $cat = $_GET['cat'];  // millise välja järgi otsida
              echo "<tr><td colspan='6'>Otsing: ".$s."</td></tr>";
              $paring = 'SELECT * FROM sport2025 WHERE '.$cat.' LIKE "%'.$s.'%"';
              // NB! See pole turvaline lahendus (võimalik SQL injection)!
            } else {
              // kui otsingut ei ole, siis tavapärane lehekülgede kaupa kuvamine
              $paring = "SELECT * from sport2025 LIMIT $start, $uudiseid_lehel";
            }
            
            // saadame päringu andmebaasi
            $saada_paring = mysqli_query($yhendus, $paring);

            // kuvame iga rea tabelis
            while($rida = mysqli_fetch_assoc($saada_paring)){
                echo "<tr>";
                echo "<td>".$rida['id']."</td>";
                echo "<td>".$rida['fullname']."</td>";
                echo "<td>".$rida['email']."</td>";
                echo "<td>".$rida['age']."</td>";
                echo "<td>".$rida['gender']."</td>";
                echo "<td>".$rida['category']."</td>";
                echo "<td>".$rida['reg_time']."</td>";
                // echo "<td><a class='btn btn-success' href='?muuda&id=".$rida['id']."'>Muuda</a></td>";
                // echo "<td><a class='btn btn-danger' href='?kustuta&id=".$rida['id']."'>Kustuta</a></td>";
                echo "</tr>";
            }

            // --- LEHEKÜLJE LINGID ---
            $eelmine = $leht - 1;
            $jargmine = $leht + 1;

            if ($leht>1) {
              echo "<a class='btn btn-primary m-1' href='?page=$eelmine'>Eelmine</a> ";
            }

            if ($lehti_kokku >= 1) {
              for ($i=1; $i<=$lehti_kokku ; $i++) { 
                if ($i==$leht) {
                  echo "<b><a class='btn btn-primary m-1' href='?page=$i'>$i</a></b> ";
                } else {
                  echo "<a class='btn btn-primary m-1' href='?page=$i'>$i</a> ";
                }
              }
            }

            if ($leht<$lehti_kokku) {
              echo "<a class='btn btn-primary m-1' href='?page=$jargmine'>Järgmine</a> ";
            }
        ?>

      </tbody>
    </table>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
