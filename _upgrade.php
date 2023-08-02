<?php
/**
 * CWP Media tool
 */

$delete_file = __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'delete.txt';
if (file_exists($delete_file)) {
    $file = file_get_contents($delete_file);

    $fileArray = explode("\n", $file);

    foreach ($fileArray as $file) {
        if (file_exists(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.$file)) {
            unlink(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.$file);
        }
    }
}
