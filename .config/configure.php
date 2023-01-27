<?php

function debug(...$var)
{
    echo "<pre>" . var_export($var, 1) . "</pre>";
}

define('__COMPOSER_DIR__',  '../library/vendor');
set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_DIR__);
require __COMPOSER_DIR__ . '/autoload.php';


use Noodlehaus\Config;
use Noodlehaus\Writer\ini;
use Nette\Utils\FileSystem;


$config_file = $_SERVER['DOCUMENT_ROOT']."/.config/config.ini";
if (!file_exists($config_file))
{
    $config_default = "[application]\nname=cwp";
    FileSystem::write($config_file, $config_default);
}

$conf = new Config($config_file);

// create our form object and use Bulma as our form wrapper
$form = new Formr\Formr('bulma');

// make all fields required
$form->required = '*';

// check if the form has been submitted
if ($form->submitted())
{
    // get our form values and assign them to a variable
    $data = $form->validate('root_dir,bin_dir,web_root,url_root,db_dir');

    // show a success message if no errors
    if($form->ok()) {

        foreach($data as $key => $value){
            $conf->set('server.'.$key, $value);
        }
        $config = $conf->all();
        $conf->toFile($config_file);
        FileSystem::rename(realpath($_SERVER['SCRIPT_FILENAME']),realpath($_SERVER['SCRIPT_FILENAME']).".ran");
        $form->redirect($data['url_root']);

    } else {
        $form->success_message = "Thank you, {$data['root_dir']}!";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
<body class="container">
    <?php $form->messages();
    $form->open('','',$_SERVER['SCRIPT_NAME'],'','class="bar"');
    $form->text('root_dir', 'Root/Install Directory',  FileSystem::normalizePath($_SERVER['DOCUMENT_ROOT'] . "/../.."));
    $form->text('bin_dir', 'local bin Directory', FileSystem::normalizePath($_SERVER['DOCUMENT_ROOT'] . "/../../.bin"));
    $form->text('db_dir', 'SQLite DB Directory', FileSystem::normalizePath($_SERVER['DOCUMENT_ROOT'] . "/../../.database"));
    $form->text('web_root', 'Web root', $_SERVER['DOCUMENT_ROOT']);
    $form->text('url_root', 'URL Home', '/');    

    $form->submit_button();
    $form->close();
    ?>
</body>
</html>