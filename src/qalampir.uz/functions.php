<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
$log = __DIR__ . "/../data/log.txt";

/* FUNCTIONS */

/**
 * @param $value
 */
function debug($value)
{
    /*echo '<pre>';
    var_dump($value);
    echo '</pre>';*/
    echo $value . PHP_EOL;
}

/**
 * @param $url
 * @param $name
 * @param $extensions
 * @return int|mixed
 */
function download_file($url, $name, $extensions)
{
    try {
        $path = checkFolder($extensions);
        $file_path = fopen($path . $name, 'w');
        $client = new Client();
        $response = $client->get($url, ['save_to' => $file_path]);
        return $response->getStatusCode();

    } catch (Exception $error){
        return $error->getCode();
    }

}


function executeTime($sTime, $eTime) {
    echo "\n\n----------\n";
    echo (float)($eTime - $sTime);
    echo "\n\n";
}


function checkFolder($path) {
    $date = date('Y-m');
    if (!is_dir("$path/$date")) {
        $mkdir = mkdir("$path/$date");
        if (!$mkdir) throw new Exception("new folder created error!");
        return "$path/$date/";
    }
    else return "$path/$date/";
}

function makeKeywords($text) {
    $text = str_replace(['.', ',', '!', '?'], "", $text);
    $array = explode(' ', $text);
    return implode(", ", $array);
}
function slugify($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}