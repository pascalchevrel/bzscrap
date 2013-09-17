<?php
namespace bzscrap;

function getBugsFromCSV($csv, $full = false)
{
    $shortBugs = $fullBugs = $temp = [];

    if (($handle = fopen($csv, 'r')) !== false) {
        while (($data = fgetcsv($handle, 300, ',')) !== false) {
            if ($data[0] == 'bug_id') {
                $fields = $data;
                continue;
            }

            foreach ($fields as $key => $field) {
                $temp[$field] = $data[$key];
            }

            $fullBugs[] = $temp;
            $shortBugs[$temp['bug_id']] = $temp['short_desc'];
        }
        fclose($handle);
    }

    return ($full) ? $fullBugs : $shortBugs;
}

function cacheUrl($url, $time = 120)
{
    $cache_dir  = __DIR__ . '/cache/';
    $cache_file = $cache_dir . sha1($url) . '.cache';

    if (is_file($cache_file)) {

        $age = $_SERVER['REQUEST_TIME'] - filemtime($cache_file);

        if ($age < $time) {

            return $cache_file;
        }
    }

    // Only fetch external data if we can write to Cache folder
    if (is_dir($cache_dir)) {
        $file = file_get_contents($url);
        file_put_contents($cache_file, $file);

        return $cache_file;
    }

    // No caching possible, return $url
    return $url;
}


function jsonOutput(array $data, $jsonp = false)
{
    $json = json_encode($data);
    $mime = 'application/json';

    if ($jsonp && is_string($jsonp)) {
        $mime = 'application/javascript';
        $json = $jsonp . '(' . $json . ')';
    }

    ob_start();
    header("access-control-allow-origin: *");
    header("Content-type: {$mime}; charset=UTF-8");
    echo $json;
    $json = ob_get_contents();
    ob_end_clean();

    return $json;
}
