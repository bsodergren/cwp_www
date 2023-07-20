<?php
// {major}.{minor}.{patch}.{env:BUILD_NUMBER}'

require_once '../.config.inc.php';
use coderofsalvation\BrowserStream;
use Symfony\Component\Finder\Finder;

define('TITLE', 'Test Page');
//$template = new Template();

require __LAYOUT_HEADER__;

BrowserStream::put('loading');

for ($i = 0; $i < 10; $i++) {
    BrowserStream::put('.');
    sleep(1);
}

die();
?>
<pre><?php ?></pre>
<pre><?php echo utils::fracToFloat('7-3/4')?></pre>
<?php

die();
$table = 'flag_style';
$key_column = 'id';
$res = $explorer->table($table)->fetchall();

//$res = $explorer->table($table)->select("definedName,setting_value")->where("setting_cat = ?", 'server')->fetchall();
echo '<pre>';
echo '$update_data = ['.PHP_EOL;
echo "\t".'"'.$table.'" => ['.PHP_EOL;
echo "\t"."\t".'"'.$key_column.'" => ['.PHP_EOL;
foreach ($res as $k => $data) {
    // echo "<br>" . PHP_EOL;
    foreach ($data as $k => $v) {
        // if($k == "id") {
        //     continue;
        // }
        if ($k == $key_column) {
            echo "\t"."\t"."\t"."'".$v."'  => [".PHP_EOL;
            continue;
        }
        echo "\t"."\t"."\t"."\t".'"'.$k.'"=>"'.addslashes($v).'",'.PHP_EOL;
    }
    echo "\t"."\t"."\t".'],'.PHP_EOL;
}
echo "\t"."\t"."],
\t],
];";
echo '</pre>';
dd($res);

die();

$data =
"ENTREPRENEUR,PFS,8 x 10 3/4,  1/4 ,  3/16,  3/16
COSMOPOLITAN,PFS,7 15/16 x 11 1/8,  5/16,,
FORTUNE,PFS,8 1/8 x 10 3/4,  1/8 ,  3/16,  3/16
FORBES,PFS,8 1/4 x 10 3/4,,  3/16,  3/16
SPORTS ILLUSTRATED,PFS,8 1/4 x 10 3/4,,  3/16,  3/16
ELLE,PFS,8 1/4 x 11 1/8,,,
MEN'S HEALTH,PFS,8 1/4 x 11 1/8,,,
MEN'S JOURNAL,PFS,8 1/4 x 11 1/8,,,
WOMEN'S HEALTH,PFS,8 3/16 X  11 1/8,  1/16,,
ALL RECIPES,PFM,8 1/2 x 11 1/8,  1/8 ,,
COUNTRY LIVING,PFM,8 1/2 x 11 1/8,  1/8 ,,
ELLE DÉCOR,PFM,8 1/2 x 11 1/8,  1/8 ,,
ESQUIRE,PFM,8 7/16 x 11 1/8,  3/16,,
FOOD & WINE,PFM,8 1/2 x 11 1/8,  1/8 ,,
HOUSE BEAUTIFUL,PFM,8 1/2 x 11 1/8,  1/8 ,,
TRAVEL + LEISURE,PFM,8 1/2 x 11 1/8,  1/8 ,,
FOOD NETWORK,PFL,8 3/4 x 11 1/8,  1/2 ,,
HGTV MAGAZINE,PFL,8 3/4 x 11 1/8,  1/2 ,,
TOWN & COUNTRY,PFL,9 x 11 1/8,  1/8 ,,
ELLE,PFL,9 1/8 x 11 1/8,  1/8 ,,
HARPER'S BAZAAR,PFL,9 1/8 x 11 1/8,  1/8 ,,
BETTER HOMES & GARDENS,PFL,9 1/4 X 11 1/8,,,
REAL SIMPLE,PFL,9 1/4 x 11 1/8,,,
BUSINESS WEEK,SHS,8 3/8 x 10 3/4,,,  3/8
PEOPLE,SHS,8 3/8 x 10 3/4,,,  3/8
SMITHSONIAN,SHS,8 3/8 x 10 3/4,,,  3/8
THE WEEK,SHS,8 3/8 x 10 3/4,,,  3/8
TIME,SHS,8 3/8 x 10 3/4,,,  3/8 ";

$array = explode("\n", $data);

foreach ($array as $line) {
    [$pub,$bind,$trim,$face,$foot,$head] = explode(',', $line);

    $trim = str_replace('  ', ' ', $trim);
    $trim = str_replace(' ', '-', $trim);
    $trim = str_ireplace('-x-', ' x ', $trim);

    $bind = strtolower($bind);

    $head = trim($head);
    $foot = trim($foot);
    $face = trim($face);

    if ($face == '') {
        $face = '0';
    }
    if ($foot == '') {
        $foot = '0';
    }
    if ($head == '') {
        $head = '0';
    }
    $pub = MediaXLSX::CleanPublication($pub);
    $res = null;
    $insert = null;
    $count = null;
    $res = $explorer->table('pub_trim')->select('id')->where('pub_name = ?  AND bind = ? ', $pub, $bind)->fetch();
    $pub_array[] = [
        'pub_name' =>  $pub,
        'bind' => $bind,
        'head_trim' => trim($head),
        'foot_trim' => trim($foot),
        'face_trim' => trim($face),
        'delivered_size' => $trim, ];
    if ($res === null) {
        $dataarray = [
            'pub_name' =>  $pub,
            'bind' => $bind,
            'head_trim' => trim($head),
            'foot_trim' => trim($foot),
            'face_trim' => trim($face),
            'delivered_size' => $trim, ];
        $insert = $explorer->table('pub_trim')->insert($dataarray);
    } else {
        $update = [
            'head_trim' => $head,
            'foot_trim' => $foot,
            'face_trim' => $face,
            'delivered_size' => $trim,
        ];
        $count = $explorer->table('pub_trim')->where('id', $res->id)->update($update);
    }
}

dump($pub_array);
?>