<?php
// Alustame sessiooni, et saaks kontrollida, kas admin on sisse logitud
session_start();

// Lisame konfiguratsiooni, et saada andmebaasi ühendus
include("config.php");

// Kontrollime, kas admin on sisse logitud; kui ei, suuname login lehele
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Muutuja teadete kuvamiseks kasutajale
$notice = '';

// Kontrollime, kas vormi on postitatud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Võtame ja puhastame POST andmed
    $fullname = trim($_POST['fullname']); // ees- ja perekonnanimi
    $email = trim($_POST['email']);       // e-post
    $age = (int)$_POST['age'];           // vanus, teisendame int-iks
    $gender = trim($_POST['gender']);     // sugu
    $category = trim($_POST['category']); // spordiala

    // Kontrollime, et kõik väljad on täidetud ja vanus positiivne
    if ($fullname === '' || $email === '' || $age <= 0 || $gender === '' || $category === '') {
        $notice = "Täida kõik väljad korrektselt!";
    } else {
        // Kontrollime, kas sellise nimega kasutaja juba eksisteerib
        $stmt = mysqli_prepare($yhendus, "SELECT COUNT(*) FROM sport2025 WHERE fullname = ?");
        mysqli_stmt_bind_param($stmt, "s", $fullname); // lisame parameetri ettevalmistatud päringule
        mysqli_stmt_execute($stmt); // käivitame päringu
        mysqli_stmt_bind_result($stmt, $count); // sidume tulemuse muutujaga $count
        mysqli_stmt_fetch($stmt); // loeme tulemuse
        mysqli_stmt_close($stmt); // sulgeme päringu

        if ($count > 0) {
            // Kui nimi juba olemas, näitame teadet
            $notice = "Sellise nimega kasutaja on juba olemas!";
        } else {
            // Kui nimi unikaalne, lisame uue kasutaja
            $now = date('Y-m-d H:i:s'); // praegune aeg registreerimiseks
            $stmt = mysqli_prepare($yhendus, "INSERT INTO sport2025 (fullname, email, age, gender, category, reg_time) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssisss", $fullname, $email, $age, $gender, $category, $now);

            // Käivitame lisamise
            if ($stmt->execute()) {
                // Kui edukas, suuname admin index lehele
                header('Location: index.php');
                exit();
            } else {
                // Kui viga, näitame teadet
                $notice = "Tekkis viga kasutajat lisades: " . mysqli_error($yhendus);
            }

            $stmt->close(); // sulgeme päringu
        }
    }
}
?>

<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <title>Admin leht - Lisa kasutaja</title>
  <!-- Lisame Bootstrapi stiilid -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
  <div class="container py-4">
    <h1>Lisa uus kasutaja</h1>

    <!-- Kuvame teadet, kui see olemas -->
    <?php if ($notice): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($notice); ?></div>
    <?php endif; ?>

    <!-- Vorm uue kasutaja lisamiseks -->
    <form method="post">
      <div class="mb-3">
        <label for="fullname" class="form-label">Nimi</label>
        <input type="text" id="fullname" name="fullname" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="age" class="form-label">Vanus</label>
        <input type="number" id="age" name="age" class="form-control" min="1" required>
      </div>

      <div class="mb-3">
        <label for="gender" class="form-label">Sugu</label>
        <select id="gender" name="gender" class="form-select" required>
          <option value="">Vali</option>
          <option value="Mees">Mees</option>
          <option value="Naine">Naine</option>
          <option value="Muu">Muu</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">Spordiala</label>
        <input type="text" id="category" name="category" class="form-control" required>
      </div>

      <!-- Submit nupp -->
      <button type="submit" class="btn btn-primary">Lisa kasutaja</button>
    </form>

    <!-- Link välja logimiseks -->
    <p class="mt-3"><a href="logout.php">Logi välja</a></p>
  </div>

  <!-- Lisame Bootstrapi JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
