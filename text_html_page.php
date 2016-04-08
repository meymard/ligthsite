<?php
    // Afficher les erreurs à l'écran
    ini_set('display_errors', 1);
    // Enregistrer les erreurs dans un fichier de log
    ini_set('log_errors', 1);
    // Nom du fichier qui enregistre les logs (attention aux droits à l'écriture)
    error_reporting(E_ALL);

    $base = (isset($_SERVER['BASE']) && $_SERVER['BASE']) ? $_SERVER['BASE'] : '/';

    if (isset($_GET['size'])) {
        $size = $_GET['size'];
    } else {
        $size = 2000;
    }
    $file = $_GET['file'] ?: '.';
    $isGoodFile = preg_match('/\.eyco$/', $file);

    $headerFile = sprintf('%s/header.txt', dirname(__FILE__));
    if (file_exists($headerFile)) {
        $header = file_get_contents($headerFile);
    } else {
        $header = '';
    }

    $errorFile = 'errors/404.eyco';
    $contents = [];
    if ($isGoodFile && file_exists($file)) {
        $contents[] = file_get_contents($file);
    } elseif (file_exists($file) && is_dir($file)) {
        $files = scandir($file);
        foreach ($files as $f) {
            $fFullName = sprintf('%s/%s/%s', dirname(__FILE__), $file, $f);
            if (is_file($fFullName) && preg_match('/\.eyco$/', $f)) {
                $contents[] = file_get_contents($fFullName);
            }
        }
    } elseif (file_exists($errorFile)) {
        $contents[] = file_get_contents($errorFile);
    } else {
        $contents[] = '';
    }

    foreach ($contents as &$content) {
        preg_match('/^title\. (.*)$/m', $content, $matches);
        $title = isset($matches[1]) ? $title = $matches[1] : sprintf('View file: %s', $file);

        // url
        $content = preg_replace("/(^| )http:\/\/([^ \n]+)($| )/m", ' <a href="http://$2">$2</a> ', $content);
        $content = str_replace('{homepage}', '<a href="/">page d\'accueil</a>', $content);

        // title
        $content = preg_replace('/^title\. (.*)$/m', '', $content);
        // h1 à h6
        $content = preg_replace('/^h([0-9]+)\. (.*)$/m', '<h$1>$2</h$1>', $content);
        // hr
        $content = preg_replace('/^hr\.$/m', '<hr />', $content);
        // ul
        $content = preg_replace(['/^ul\.$/m', '/^\/ul\.$/m'], ['<ul>', '</ul>'], $content);
        $content = preg_replace('/^li\. (.*)$/m', '<li>$1</li>', $content);
        // p
        $content = preg_replace(['/^p\.$/m', '/^\/p\.$/m'], ['<p>', '</p>'], $content);
        // pre
        $content = preg_replace(['/^pre\.$/m', '/^\/pre\.$/m'], ['<pre>', '</pre>'], $content);
        // br
        $content = preg_replace("/(?!<[\/]?.*>)[\n]+(?!<[\/]?.*>)/m", '$1<br />$2', $content);

        $content = preg_replace('/<br \/>(<p>|<\/p>)/m', '$1', $content);
        $content = preg_replace('/(<p>|<\/p>)<br \/>/m', '$1', $content);

        $content = preg_replace("/(>[\n ]*)<br \/>([\n ]*<)/m", '$1$2', $content);
        $content = preg_replace(["/(>[\n ]*)<br \/>/m", "/<br \/>([\n ]*<)/m"], '$1', $content);
    }

?>

<html>
<head>
    <title><?php print $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="<?php print $base; ?>/base.css" type="text/css"/>
    <link rel="shortcut icon" type="image/png" href="<?php print $base; ?>/logo_me_noir.png"/>
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

</head>
<body>
<div class="main">
    <header class="content">
    <?php if (file_exists(dirname(__FILE__))): ?>
    <nav>
        <fieldset>
            <legend>Menu</legend>
            <ul id="menu">
            <li><a href="<?php print $base; ?>/"<?php print ($file == '.') ? ' class="current"' : ''; ?>>Accueil</a></li>
            <?php $files = scandir(dirname(__FILE__)); ?>
            <?php foreach ($files as $f) : ?>
                <?php if (is_dir($f) && strpos($f, '.') !== 0 && $f != 'errors'): ?>
                    <?php
                        $matches = [];
                        $fileContent = file_get_contents($f);
                        $name = $f;
                    ?>
                    <li><a href="<?php print $base; ?>/<?php print $f; ?>"<?php print ($file == $f) ? ' class="current"' : ''; ?>><?php print  $name; ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </fieldset>
    </nav>
    <?php endif; ?>
    <div id="header">
        <?php print $header; ?>
    </div>
    </header>
    <div class="content">
        <?php foreach ($contents as &$content): ?>
        <article>
            <?php
                print $content;
            ?>
        </article>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
