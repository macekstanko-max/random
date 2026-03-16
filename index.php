<?php
session_start();

$pdo = new PDO('sqlite:' . __DIR__ . '/profile.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE IF NOT EXISTS interests (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE)');

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

$profile = [];
$raw = @file_get_contents(__DIR__ . '/profile.json');
if ($raw !== false) {
    $profile = json_decode($raw, true) ?: [];
}
$name = htmlspecialchars($profile['name'] ?? 'Jan Novák');
$description = htmlspecialchars($profile['description'] ?? 'IT student.');
$skills = is_array($profile['skills'] ?? null) ? $profile['skills'] : [];
$projects = is_array($profile['projects'] ?? null) ? $profile['projects'] : [];
$github = htmlspecialchars($profile['github'] ?? '#');
$email = htmlspecialchars($profile['email'] ?? 'example@example.com');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $interest = trim($_POST['new_interest'] ?? '');
        if ($interest === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            $check = $pdo->prepare('SELECT id FROM interests WHERE LOWER(name) = LOWER(?) LIMIT 1');
            $check->execute([$interest]);
            if ($check->fetch()) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $insert = $pdo->prepare('INSERT INTO interests (name) VALUES (?)');
                $insert->execute([$interest]);
                $_SESSION['message'] = 'Zájem byl přidán.';
                $_SESSION['messageType'] = 'success';
            }
        }
    } elseif ($action === 'delete') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $stmt = $pdo->prepare('DELETE FROM interests WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Zájem byl odstraněn.';
        $_SESSION['messageType'] = 'success';
    } elseif ($action === 'edit') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $edited = trim($_POST['edited_interest'] ?? '');
        if ($edited === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            $check = $pdo->prepare('SELECT id FROM interests WHERE LOWER(name) = LOWER(?) AND id != ? LIMIT 1');
            $check->execute([$edited, $id]);
            if ($check->fetch()) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $update = $pdo->prepare('UPDATE interests SET name = ? WHERE id = ?');
                $update->execute([$edited, $id]);
                $_SESSION['message'] = 'Zájem byl upraven.';
                $_SESSION['messageType'] = 'success';
            }
        }
    }
    header('Location: index.php');
    exit;
}

$interestsStmt = $pdo->query('SELECT id, name FROM interests ORDER BY id');
$interests = $interestsStmt->fetchAll(PDO::FETCH_ASSOC);
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
?>
<!doctype html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IT Profil | <?php echo $name; ?></title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="page">
    <header class="hero">
      <div>
        <p class="eyebrow">Osobní IT profil</p>
        <h1><?php echo $name; ?></h1>
        <p class="subtitle"><?php echo $description; ?></p>
      </div>
      <div class="avatar">👨‍💻</div>
    </header>

    <section class="card intro">
      <h2>O mně</h2>
      <p><?php echo $description; ?></p>
    </section>

    <section class="card">
      <h3>Skills</h3>
      <ul>
        <?php foreach ($skills as $skill): ?>
          <li><?php echo htmlspecialchars($skill); ?></li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section class="card">
      <h3>Zájmy</h3>
      <?php if (!empty($message)): ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>

      <?php if (count($interests) > 0): ?>
        <ul>
          <?php foreach ($interests as $interest): ?>
            <li>
              <?php echo htmlspecialchars($interest['name']); ?>
              <form method="POST" style="display:inline; margin-left:.4rem;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo (int)$interest['id']; ?>">
                <button type="submit" class="small-btn">Smazat</button>
              </form>
              <a href="index.php?edit=<?php echo (int)$interest['id']; ?>" class="small-btn">Upravit</a>
            </li>
            <?php if ($editId === (int)$interest['id']): ?>
              <li>
                <form method="POST" class="edit-form">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?php echo (int)$interest['id']; ?>">
                  <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($interest['name']); ?>" required>
                  <button type="submit" class="small-btn">Uložit</button>
                  <a href="index.php" class="small-btn">Zrušit</a>
                </form>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné zájmy k zobrazení.</p>
      <?php endif; ?>

      <form method="POST" class="interest-form">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
          <input type="text" name="new_interest" required placeholder="Nový zájem">
          <button type="submit">Přidat zájem</button>
        </div>
      </form>
    </section>

    <section class="card contact">
      <h3>Kontakt</h3>
      <p>GitHub: <a href="<?php echo $github; ?>" target="_blank"><?php echo $github; ?></a></p>
      <p>Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
    </section>

    <footer class="footer">PHP verze IT profilu • Data v SQLite</footer>
  </div>
</body>
</html>
