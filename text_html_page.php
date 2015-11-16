<?php
    // Afficher les erreurs à l'écran
    ini_set('display_errors', 1);
    // Enregistrer les erreurs dans un fichier de log
    ini_set('log_errors', 1);
    // Nom du fichier qui enregistre les logs (attention aux droits à l'écriture)
    error_reporting(E_ALL);

    if (isset($_GET['size'])) {
        $size = $_GET['size'];
    } else {
        $size = 2000;
    }
    $file = $_GET['file'];

    $headerFile = sprintf('%s/header.txt', dirname(__FILE__));
    var_dump($headerFile);
    if (file_exists($headerFile)) {
        $header = file_get_contents($headerFile);
    } else {
        $header = '';
    }

    $errorFile = 'errors/404.eyco';
    if (file_exists($file)) {
        $content = file_get_contents($file);
    } elseif (file_exists($errorFile)) {
        $content = file_get_contents($errorFile);
    } else {
        $content = '';
    }

    preg_match('/^title\. (.*)$/m', $content, $matches);
    $title = isset($matches[1]) ? $title = $matches[1] : sprintf('View file: %s', $file);

    // url
    $content = preg_replace("/(^| )http:\/\/([^ \n]+)($| )/m", ' <a href="http://$2">$2</a> ', $content);

    // title
    $content = preg_replace('/^title\. (.*)$/m', '', $content);
    // h1 à h6
    $content = preg_replace('/^h([0-9]+)\. (.*)$/m', '<h$1>$2</h$1>', $content);
    // p
    $content = preg_replace(['/^p\.$/m', '/^\/p\.$/m'], ['<p>', '</p>'], $content);
    // Supprimer BR en trop
    $content = preg_replace("/(?!<[\/]?.*>)[\n]+(?!<[\/]?.*>)/m", '$1<br />$2', $content);
    //$content = nl2br($content);
    //$content = preg_replace('/^p\. (.*)\/p\.$/m', '<p>$1</p>', $content);

?>

<html>
<head>
    <title><?php print $title; ?></title>
    <style>
        body {
            background-color: #171717;
            color: #999;
            font-size: 1.2em;
        }
        .content {
            text-align: justify;
            /*border: 10px double #171717;*/
            /*border-image: url(decor-dies.png) 30 / 25px round;*/
            border: 10px;
            border-style: double;
            padding: 30px;
            margin-bottom: 50px;
        }
        .content span {
            color: #6C798D;
        }
        header.content {
            background-position: center top;
            padding: 10px;
        }
        div .content {
            background-image: url(debian.png);
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        header pre {
            overflow: hidden;
            margin: 0px;
        }

        nav ul {
            list-style-type: none;
            margin: 0px;
            padding: 5px;
            border-top: 2px dashed;
        }
        nav ul li {
            display: inline;
            padding: 5px;
            border-right: 2px dashed;
        }
        nav ul li:last-child {
            border: none;
        }

        /*nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        nav ul li {
            float: left;
        }

        nav ul li a {
            display: block;
            padding: 5px;
            background-color: #000;
        }*/

        a {
            text-decoration: none;
        }
        a:link, a:visited {
            color: #CFCFCF;
        }
        a:hover {
            color: #616161;
        }
        a:active {
            color: #616161;
        }
    </style>
</head>
<body>
<div class="main">
    <header class="content">
    <?php print $header; ?>
    <?php if ($handle = opendir('/home/marc/Documents/public')): ?>
    <nav>
        <ul>
        <?php while (false !== ($file = readdir($handle))) : ?>
            <?php if (is_file($file) && preg_match('/\.eyco$/', $file)): ?>
                <?php
                    $fileContent = file_get_contents($file);
                    preg_match('/^title\. (.*)$/m', $fileContent, $matches);
                    $name = isset($matches[1]) ? $matches[1] : preg_replace('/(.*)\.eyco$/', '$1', $file);
                ?>
                <li><a href="<?php print $file; ?>"><?php print  $name; ?></a></li>
            <?php endif; ?>
        <?php endwhile; ?>
        </ul>
    </nav>
    <?php closedir($handle); ?>
    </header>
    <?php endif; ?>
    <div class="content">
        <?php
            print $content;
        ?>
    </div>
</div>
</body>
</html>
