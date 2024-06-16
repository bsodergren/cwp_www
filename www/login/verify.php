<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLDisplay;

/*
 * CWP Media tool for load flags
 */

define('__AUTH__', false);

require_once '../.config.inc.php';

if (array_key_exists('token', $_GET)) {
    try {
        $auth->confirmEmail($_GET['selector'], $_GET['token']);

        $msg = 'Email address has been verified';
    } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
        $msg = 'Invalid token';
    } catch (\Delight\Auth\TokenExpiredException $e) {
        $msg = 'Token expired';
    } catch (\Delight\Auth\UserAlreadyExistsException $e) {
        $msg = 'Email address already exists';
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $msg = 'Too many requests';
    }
}

echo Elements::JavaRefresh('/login/login.php', 0, $msg);
