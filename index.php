<?php
session_start();

function loadData() {
    $raw = file_get_contents(__DIR__ . '/profile.json');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = [];
    }
    if (!isset($data['interests']) || !is_array($data['interests'])) {
        $data['interests'] = [];
    }
    return $data;
}

function saveData($data) {
    file_put_contents(__DIR__ . '/profile.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$data = loadData();
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $interests = $data['interests'];
    $lowered = array_map('strtolower', $interests);

    if ($action === 'add') {
        $newInterest = trim($_POST['new_interest'] ?? '');
        if ($newInterest === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } elseif (in_array(strtolower($newInterest), $lowered, true)) {
            $_SESSION['message'] = 'Tento zájem už existuje.';
            $_SESSION['messageType'] = 'error';
        } else {
            $data['interests'][] = $newInterest;
            saveData($data);
            $_SESSION['message'] = 'Zájem byl úspěšně přidán.';
            $_SESSION['messageType'] = 'success';
        }
    } elseif ($action === 'delete') {
        if (isset($_POST['index'])) {
            $index = (int) $_POST['index'];
            if (isset($data['interests'][$index])) {
                unset($data['interests'][$index]);
                $data['interests'] = array_values($data['interests']);
                saveData($data);
                $_SESSION['message'] = 'Zájem byl odstraněn.';
                $_SESSION['messageType'] = 'success';
            } else {
                $_SESSION['message'] = 'Zájem nebyl nalezen.';
                $_SESSION['messageType'] = 'error';
            }
        }
    } elseif ($action === 'edit') {
        $index = isset($_POST['index']) ? (int) $_POST['index'] : null;
        $edited = trim($_POST['edited_interest'] ?? '');
        if ($edited === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } elseif ($index === null || !isset($data['interests'][$index])) {
            $_SESSION['message'] = 'Zájem nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        } else {
            $loweredEdited = strtolower($edited);
            $duplicate = false;
            foreach ($data['interests'] as $i => $item) {
                if ($i === $index) {
                    continue;
                }
                if (strtolower($item) === $loweredEdited) {
                    $duplicate = true;
                    break;
                }
            }
            if ($duplicate) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $data['interests'][$index] = $edited;
                $data['interests'] = array_values($data['interests']);
                saveData($data);
                $_SESSION['message'] = 'Zájem byl upraven.';
                $_SESSION['messageType'] = 'success';
            }
        }
    }
    header('Location: index.php');
    exit;
}

$name = htmlspecialchars($data['name'] ?? 'Neznámý');
$description = htmlspecialchars($data['description'] ?? 'IT student.');
$skills = is_array($data['skills'] ?? null) ? $data['skills'] : [];
$interests = is_array($data['interests'] ?? null) ? $data['interests'] : [];
$projects = is_array($data['projects'] ?? null) ? $data['projects'] : [];
$github = htmlspecialchars($data['github'] ?? '#');
$email = htmlspecialchars($data['email'] ?? 'example@example.com');
$editIndex = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
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
          <?php foreach ($interests as $i => $interest): ?>
            <li>
              <?php echo htmlspecialchars($interest); ?>
              <form method="POST" style="display:inline; margin-left:.4rem;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="index" value="<?php echo $i; ?>">
                <button type="submit" class="small-btn">Smazat</button>
              </form>
              <a href="index.php?edit=<?php echo $i; ?>" class="small-btn">Upravit</a>
            </li>
            <?php if ($editIndex === $i): ?>
              <li>
                <form method="POST" class="edit-form">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="index" value="<?php echo $i; ?>">
                  <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($interest); ?>" required>
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

    <section class="card">
      <h3>Projekty</h3>
      <?php if (count($projects) > 0): ?>
        <?php foreach ($projects as $project): ?>
          <article class="project">
            <h4><?php echo htmlspecialchars($project['title'] ?? 'Bez názvu'); ?></h4>
            <p><?php echo htmlspecialchars($project['description'] ?? 'Popis chybí'); ?></p>
            <?php if (!empty($project['link'])): ?>
              <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank">Odkaz</a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Žádné projekty k zobrazení.</p>
      <?php endif; ?>
    </section>

    <section class="card contact">
      <h3>Kontakt</h3>
      <p>GitHub: <a href="<?php echo $github; ?>" target="_blank"><?php echo $github; ?></a></p>
      <p>Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
    </section>

    <footer class="footer">PHP verze IT profilu • Data z profile.json</footer>
  </div>
</body>
</html>
