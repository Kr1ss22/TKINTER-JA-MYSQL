<?php
// Lisame konfiguratsiooni, et saada andmebaasi ühendus
include("config.php"); 

// Alustame sessiooni, et kasutada sessioonimuutujaid
session_start();

// Kui sessioon on juba olemas (kasutaja on sisse logitud), suuname admin kausta
if (isset($_SESSION['tuvastamine'])) {
    header('admin/'); // suunab automaatselt admin lehele
    exit(); // lõpetame skripti täitmise
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <!-- Lisame Bootstrapi CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Kohandatud CSS -->
    <style>
       .btn-color{
          background-color: #0e1c36;
          color: #fff;
        }

        .profile-image-pic{
          height: 200px;
          width: 200px;
          object-fit: cover;
        }

        .cardbody-color{
          background-color: #ebf2fa;
        }

        a{
          text-decoration: none;
        }
    </style>  
  </head>
  <body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
      
      <?php
            // Kui POST andmed on olemas (kasutaja vajutas login nuppu)
            if (!empty($_POST['user']) && !empty($_POST['password'])) {
                $login = $_POST['user']; // kasutajanimi
                $str = $_POST['password']; // parool
                
                // Võtame kõik kasutajad andmebaasist (siin võiks kasutada päringut ainult konkreetse kasutaja jaoks)
                $paring = "SELECT * FROM users";
                $saada_paring = mysqli_query($yhendus, $paring);
                $rida = mysqli_fetch_assoc($saada_paring); // võtame esimese rea
                $s = $rida["password"]; // salvestame parooli hash'i
                
                // Kontrollime kasutajanime ja parooli
                if ($login == 'admin' && password_verify($str, $s)) {
                    echo "Tere admin"; // edukas sisselogimine
                    $_SESSION['tuvastamine'] = 'misiganes'; // loome sessiooni muutuja
                    header('Location: admin/'); // suuname admin kausta
                    exit();
                } else {
                    // Vale kasutajanimi või parool
                    echo "Vale kasutajanimi või parool";
                }
            }
      ?>

        <h2 class="text-center text-dark mt-5">Login sisse</h2>
        <div class="card my-5">
          <!-- Login vorm -->
          <form class="card-body cardbody-color p-lg-5" method="post">
            <div class="mb-3">
              <input type="text" class="form-control" name="user" aria-describedby="emailHelp"
                placeholder="Kasutaja">
            </div>
            <div class="mb-3">
              <input type="password" class="form-control" name="password" placeholder="Parool">
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-color px-5 mb-5 w-100">Login</button>
            </div>
            <div class="form-group d-md-flex">
                <div class="w-50">
                    <!-- "Mäleta mind" valik -->
                    <label class="checkbox-wrap checkbox-primary">Mäleta mind
                        <input type="checkbox" checked name="remember">
                        <span class="checkmark"></span>
                    </label>
                </div>
	        </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <!-- Lisame Bootstrapi JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>
