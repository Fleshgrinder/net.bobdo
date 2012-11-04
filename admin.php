<?php

/**
 * Simple PHP script which generates the index.html file for us.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 */

/**
 * Strips all new lines, tabs or whitespaces from a string.
 *
 * @param string $string
 *   The string to optimize.
 * @return string
 *   The optimized string.
 */
function strip($string) {
  return preg_replace('/[\t\s\n]*(<.*>)[\t\s\n]*/', '$1', $string);
}

/** @var $IP Inclusion path for absolute paths. */
$IP = getcwd() . '/';

/** @var $css */
$css = file_get_contents($IP . 'style.css');

/** @var $mainnav */
$mainnav = '';

/** @var $subnav */
$subnav = '';

/** @var $articles */
$articles = '';

// Generate the main and sub navigation.
foreach (array(
  'About' => NULL,
  'Experiments' => array('Clocks', 'Erotic Guide', 'Ortwin Oberhauser'),
  'Projects' => array('Counter-Strike', 'FHboter', 'foobar2000', 'Georgiev Art', 'Indiana Jones Lego', 'MovLib', 'Richard Fussenegger', 'Verbundlinien', 'WebGL RPG'),
  'Work' => array('Andrea Fussenegger', 'DVD-Forum.at', 'NERVENHAMMER', 'OMICRON', 'Treemotion', 'VIVATIER', 'Wikipedia'),
  'Theses' => array('Bachelor 1', 'Bachelor 2')
) as $mainnav_point => $subnav_array) {
  $id = strtolower($mainnav_point);
  $mainnav .= '<a href="#' . $id . '">' . $mainnav_point . '</a>';
  if (is_array($subnav_array)) {
    $subnav .= '<nav id="' . $id . '" class="subnav">';
    foreach ($subnav_array as $subnav_point) {
      $subid = str_replace(array(' ', '.'), '-', strtolower($subnav_point));
      $subnav .= '<a href="#' . $subid . '">' . $subnav_point . '</a>';
      $article_file = $IP . 'articles/' . $subid . '.php';

      if (!file_exists($article_file)) {
        file_put_contents($article_file, '<?php

/** @var $title */
$title = \'' . $subnav_point . '\';

/** @var $subtitle */
$subtitle = \'\';

/** @var $description */
$description = <<<EOF
EOF;
');
      }

      include $article_file;
      $articles .=
        '<article id="' . $subid . '" class="content">' .
          '<h2 class="title"><span>' . $title . '</span></h2>' .
          '<h3 class="subtitle">' . $subtitle . '</h3>' .
          '<div class="description">' . $description . '</div>' .
        '</article>';
      $css .= '#' . $subid . '{background-image:url(/img/' . $subid . '.' . (
        file_exists($IP . 'img/' . $subid . '.jpg') ? 'jpg' : 'png'
      ) . ')}';
    }
    $subnav .= '</nav>';
  }
}

/** @var $about */
$about = file_get_contents($IP . 'articles/about.html');

/** @var $js */
$js = file_get_contents($IP . 'script.js');

/** @var $content */
$content = <<<EOF
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>bobdo</title>
  <style>$css</style>
</head>
<body>
  <img class="hidden" src="/logo.png" alt="">
  <div id="loading">
    <div id="loading-bar"></div>
    <div id="loading-progress-wrapper"><div id="loading-progress">0%</div></div>
  </div>
  <div id="table">
    <div id="table-row">
      <div id="black" class="table-cell">
        <h1>bobdo</h1>
        <nav id="mainnav-copy"></nav>
      </div>
      <div id="white" class="table-cell">
        <nav id="mainnav">$mainnav</nav>
        <article id="about" class="subnav"><div id="about-content">$about</div></article>
        $subnav
      </div>
    </div>
  </div>
  $articles
  <script>$js</script>
</body>
</html>
EOF;

// Write the content into the index file.
file_put_contents($IP . 'index.html', strip($content));

// Redirect user to new content; we're done!
header('Location: /');