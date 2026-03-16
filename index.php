<?php
$raw = file_get_contents(__DIR__ . '/profile.json');
$data = json_decode($raw, true);
if ($data === null) {
    $data = [];
}
$name = isset($data['name']) ? htmlspecialchars($data['name']) : 'Neznámý';
$description = isset($data['description']) ? htmlspecialchars($data['description']) : 'IT student.';
$skills = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : [];
$interests = isset($data['interests']) && is_array($data['interests']) ? $data['interests'] : [];
$projects = isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : [];
$github = isset($data['github']) ? htmlspecialchars($data['github']) : '#';
$email = isset($data['email']) ? htmlspecialchars($data['email']) : 'mailto:example@example.com';
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
      <?php if (count($interests) > 0): ?>
        <ul>
          <?php foreach ($interests as $interest): ?>
            <li><?php echo htmlspecialchars($interest); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné zájmy k zobrazení.</p>
      <?php endif; ?>
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
