<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaMailer;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

require_once '.config.inc.php';

if (array_key_exists('token', $_GET)) {
    try {
        $auth->confirmEmail($_GET['selector'], $_GET['token']);

        echo 'Email address has been verified';

        echo HTMLDisplay::JavaRefresh('/test.php', 10);
        exit;
    } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
        exit('Invalid token');
    } catch (\Delight\Auth\TokenExpiredException $e) {
        exit('Token expired');
    } catch (\Delight\Auth\UserAlreadyExistsException $e) {
        exit('Email address already exists');
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        exit('Too many requests');
    }
}

$_POST['email'] = 'bjorn.sodergren@gmail.com';
$_POST['name'] = 'bjorn sodergren';
$_POST['password'] = 'q1w2e3r4';
$_POST['username'] = 'bjorn';

// exit;

$mail = new MediaMailer();

$mail->recpt($_POST['email'], $_POST['name']);     // Add a recipient

// Attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

// Content

try {
    $userId = $auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) use ($mail) {
        $mail->subject('Verification email');
        $html = 'Send '.$selector.' and '.$token.' to the user (e.g. via email)';
        $html .= '  For emails, consider using the mail(...) function, Symfony Mailer, Swiftmailer, PHPMailer, etc.';
        $html .= '  For SMS, consider using a third-party service and a compatible SDK';
        $html .= ' <a href="'.__URL_HOME__.'/test.php?selector='.urlencode($selector).
        '&token='.urlencode($token).'">click here</a>';

        $mail->Body($html);
        $mail->mail();
    });

    echo 'We have signed up a new user with the ID '.$userId;
    exit;
} catch (\Delight\Auth\InvalidEmailException $e) {
    exit('Invalid email address');
} catch (\Delight\Auth\InvalidPasswordException $e) {
    exit('Invalid password');
} catch (\Delight\Auth\UserAlreadyExistsException $e) {
    echo 'User already exists';
} catch (\Delight\Auth\TooManyRequestsException $e) {
    exit('Too many requests');
}

try {
    $auth->login($_POST['email'], $_POST['password']);

    echo 'User is logged in';
} catch (\Delight\Auth\InvalidEmailException $e) {
    exit('Wrong email address');
} catch (\Delight\Auth\InvalidPasswordException $e) {
    exit('Wrong password');
} catch (\Delight\Auth\EmailNotVerifiedException $e) {
    exit('Email not verified');
} catch (\Delight\Auth\TooManyRequestsException $e) {
    exit('Too many requests');
}

exit;

$adapter = new LocalFilesystemAdapter(
    // Determine root directory
    __PROJECT_ROOT__
);

$appKey = 'm2xqkk0ojabhluo';
$appSecret = 'fcy77exrlrh03g1';

$client = new Client(__DROPBOX_AUTH_TOKEN__);
$adapter = new DropboxAdapter($client);
$filesystem = new Filesystem($adapter);
$path = '.';
try {
    $listing = $filesystem->listContents($path, 0);
    /** @var \League\Flysystem\StorageAttributes $item */
    foreach ($listing as $item) {
        $path = $item->path();
        if ($item instanceof FileAttributes) {
            echo $path.'<br>';
            // handle the file
        } elseif ($item instanceof DirectoryAttributes) {
            // handle the directory
            echo $path.'<br>';
        }
    }
} catch (FilesystemException $exception) {
    dd($exception);
    // handle the error
}
