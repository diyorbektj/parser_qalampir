<?php
date_default_timezone_set('Asia/Dushanbe');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 0);
set_time_limit(0);

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../functions/simple_dom.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/functions.php';

use Cocur\Slugify\Slugify;

/* DBAL Doctrine */
$db = DatabaseService::db();

$slugify = new Slugify();

$base_url = "https://qalampir.uz";

$log = __DIR__ . '/data/log.txt';


$client = new GuzzleHttp\Client(['base_uri' => "$base_url/"]);

$response = $client->request('GET', "uz/news/category/texnologiya");
$response = (string) $response->getBody()->getContents();
$html = str_get_html($response);
$section = $html->find('div[class=small_boxes]', 0);
$contents = $section->find('a[class=ss_item item flex_row]');

for ($i = (count($contents) - 1); $i >= 0; $i--) {
    try{
    $title = $contents[$i]->find('div[class=title]', 0)->plaintext;

        $link = $contents[$i]->href;
        $images = $contents[$i]->find('img.lazy', 0)->getAttribute('data-src');


        $childRequest = $client->request('GET', $link);
        $childResponse = (string) $childRequest->getBody()->getContents();
        $childHtml = str_get_html($childResponse);

        $childContent = $childHtml->find('div[class=richtextbox]', 0)->innertext;


        $download_image = download_file($images, $slugify->slugify(substr($title, 0, 15)) . ".jpg", $full_path);
        if ($download_image !== 200) logger("$download_image| The image could not be downloaded - $image");

        $db->insert('post', [
            'title' => $title,
            'desc' => $childContent,
            'autor' => 'admin',
            'image' =>  $slugify->slugify(substr($title, 0, 15)) . ".jpg",
            'date' => date('Y-m-d H:i:s'),
        ]);

        file_put_contents($log, date('H:i d-m-Y')." | $title" . PHP_EOL, FILE_APPEND);

        echo $title . "<br />";
    } catch(Exception $e) {
        file_put_contents($log, date('H:i d-m-Y')." | ". $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo "An error occurred while downloading the file";
    }
}
