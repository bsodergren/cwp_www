<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="description" content="<?php if(  defined('APP_DESCRIPTION') ){ ?><?php echo APP_DESCRIPTION; ?><?php } ?>">
        <meta name="author" content="<?php if(  defined('APP_OWNER') ){ ?><?php echo APP_OWNER; ?><?php } ?>">
        <link rel="icon" type="image/png" href="<?php echo __URL_HOME__; ?>/assets/themes/application/images/favicon.png?590900">

        <title><?php if( defined('APP_NAME') ){ ?><?php echo APP_NAME; ?><?php } ?> | <?php if( defined('TITLE') ){ ?><?php echo TITLE; ?><?php } ?></title>

        <?php require $this->checkTemplate("index/../../common/header/bootstrap_5");?>


        <?php if( !empty($custom_css) ){ ?>

        <?php require $this->checkTemplate("index/../../common/header/css");?>

        <?php } ?>


        <?php if( !empty($custom_js) ){ ?>

        <?php require $this->checkTemplate('index/../../common/header/'.$custom_js);?>

        <?php } ?>

    </head>

    <body>
        <?php if( $UseNavbar ){ ?>

        <?php require $this->checkTemplate('index/../../common/header/'.$navbarTemplate);?>

        <?php } ?>

        <main role="main" class="container mt-1">

            <?php if( !empty($return_msg) ){ ?>

            <?php require $this->checkTemplate("index/../../common/header/return_msg");?>

            <?php } ?>