<?php
namespace bzscrap;

require_once __DIR__ . '/functions.php';

$bugzilla_query = 'https://bugzilla.mozilla.org/'
                . 'buglist.cgi?j_top=OR&f1=flagtypes.name&o1=equals'
                . '&classification=Client%20Software&query_format=advanced'
                . '&f2=flagtypes.name&v1=review%3F&v2=needinfo%3F'
                . '&product=Mozilla%20Localizations';

// cache in a local cache folder if possible
$csv = cacheUrl($bugzilla_query . '&ctype=csv');

ob_start();
foreach (getBugsFromCSV($csv) as $bug_number => $bug_title) {
    echo '<ul>';
    if (!empty($bug_number)) {
        echo '<li><a href="https://bugzilla.mozilla.org/show_bug.cgi?id='
             . $bug_number
             . '">'
             . $bug_number
             . ': '
             . $bug_title
             . '</a></li>';
    }
    echo '</ul>';
}

$content = ob_get_contents();
ob_end_clean();

?>
<!doctype html>
<html>
<head>
    <title>Show bug results</title>
    <meta charset="utf-8"></head>
<body>

<p>Results for <a href="<?=$bugzilla_query?>">this query</a>.</p>

<?=$content;?>
</body>
</html>

