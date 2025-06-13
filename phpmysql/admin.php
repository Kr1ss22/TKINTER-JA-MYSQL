<?php
session_start();
include("config.php");

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $age = (int)$_POST['age'];
    $gender = trim($_POST['gender']);
    $category = trim($_POST['category']);

    if ($fullname === '' || $email === '' || $age <= 0 || $gender === '' || $category === '') {
        $notice = "Täida kõik väljad korrektselt!";
    } else {
        $stmt = mysqli_prepare($yhendus, "SELECT COUNT(*) FROM sport2025 WHERE fullname = ?");
        mysqli_stmt_bind_param($stmt, "s", $fullname);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $notice = "Sellise nimega kasutaja on juba olemas!";
        } else {
            $now = date('Y-m-d H:i:s');
            $stmt = mysqli_prepare($yhendus, "INSERT INTO sport2025 (fullname, email, age, gender, category, reg_time) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssisss", $fullname, $email, $age, $gender, $category, $now);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            } else {
                $notice = "Tekkis viga kasutajat lisades: " . mysqli_error($yhendus);
            }

            $stmt->close();
        }
    }
}
?>

<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <title>Admin leht - Lisa kasutaja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
  <div class="container py-4">
    <h1>Lisa uus kasutaja</h1>
    <?php if ($notice): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($notice); ?></div>
    <?php endif; ?>

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

      <button type="submit" class="btn btn-primary">Lisa kasutaja</button>
    </form>

    <p class="mt-3"><a href="logout.php">Logi välja</a></p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
