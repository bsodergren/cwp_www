<?php
require '../.config.inc.php';
define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

?>

<H1 align="center">Updating Media Source files</H1>

<?php

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;

$rendererName    = 'Unified';

// the Diff class options
$differOptions   = [
    // show how many neighbor lines
    // Differ::CONTEXT_ALL can be used to show the whole file
    'context'          => 3,
    // ignore case difference
    'ignoreCase'       => false,
    // ignore whitespace difference
    'ignoreWhitespace' => false,
];

// the renderer class options
$rendererOptions = [
    // how detailed the rendered HTML in-line diff is? (none, line, word, char)
    'detailLevel'         => 'line',
    // renderer language: eng, cht, chs, jpn, ...
    // or an array which has the same keys with a language file
    'language'            => 'eng',
    // show line numbers in HTML renderers
    'lineNumbers'         => true,
    // show a separator between different diff hunks in HTML renderers
    'separateBlock'       => true,
    // show the (table) header
    'showHeader'          => true,
    // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
    // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
    'spacesToNbsp'        => false,
    // HTML renderer tab width (negative = do not convert into spaces)
    'tabSize'             => 4,
    // this option is currently only for the Combined renderer.
    // it determines whether a replace-type block should be merged or not
    // depending on the content changed ratio, which values between 0 and 1.
    'mergeThreshold'      => 0.8,
    // this option is currently only for the Unified and the Context renderers.
    // RendererConstant::CLI_COLOR_AUTO = colorize the output if possible (default)
    // RendererConstant::CLI_COLOR_ENABLE = force to colorize the output
    // RendererConstant::CLI_COLOR_DISABLE = force not to colorize the output
    'cliColorization'     => RendererConstant::CLI_COLOR_AUTO,
    // this option is currently only for the Json renderer.
    // internally, ops (tags) are all int type but this is not good for human reading.
    // set this to "true" to convert them into string form before outputting.
    'outputTagAsString'   => false,
    // this option is currently only for the Json renderer.
    // it controls how the output JSON is formatted.
    // see available options on https://www.php.net/manual/en/function.json-encode.php
    'jsonEncodeFlags'     => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
    // this option is currently effective when the "detailLevel" is "word"
    // characters listed in this array can be used to make diff segments into a whole
    // for example, making "<del>good</del>-<del>looking</del>" into "<del>good-looking</del>"
    // this should bring better readability but set this to empty array if you do not want it
    'wordGlues'           => [' ', '-'],
    // change this value to a string as the returned diff if the two input strings are identical
    'resultForIdenticals' => null,
    // extra HTML classes added to the DOM of the diff container
    'wrapperClasses'      => ['diff-wrapper'],
];

if ($form->submitted()) {
    if (array_key_exists('update', $_POST)) {
        ?>

<div class="progress">
	<div id="theBar" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"
		aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
	</div>
</div>

<script>
	var i = 100;

	var counterBack = setInterval(function() {
		i--;
		if (i > 0) {
			document.getElementById("theBar").style.width = i + 1 + "%";
			document.getElementById("theBar").innerHTML = i + 1 + "%";
		} else {
			clearTimeout(counterBack);
		}

	}, 600);
</script>
<HR>
<UL>
	<?php

            $base_dir      = realpath(__DIR__.'\\..\\');
        set_time_limit(0);
        $git               = new git_client_class();

        // Connection timeout
        $git->timeout      = 20;

        // Data transfer timeout
        $git->data_timeout = 60;

        // Output debugging information about the progress of the connection
        $git->debug        = 0;

        // Output debugging information about the HTTP requests
        $git->http_debug   = 0;

        // Format dubug output to display with HTML pages
        $git->html_debug   = 0;

        $repository        = 'https://github.com/bsodergren/cwp_www.git';
        $module            = '';
        //    $log_file = 'composer.json';

        echo '<li><h2>Validating the Git repository</h2>', "\n", '<p>Repository: ', $repository, '</p>', "\n", '<p>Module: ', $module, '</p>', "\n";
        flush();
        $arguments         = [
            'Repository' => $repository,
            'Module'     => $module,
        ];
        if ($git->Validate($arguments, $error_code)) {
            switch ($error_code) {
                case GIT_REPOSITORY_ERROR_NO_ERROR:
                    break;

                case GIT_REPOSITORY_ERROR_INVALID_SERVER_ADDRESS:
                    $git->error = 'It was specified an invalid Git server address';

                    break;

                case GIT_REPOSITORY_ERROR_CANNOT_CONNECT:
                    $git->error = 'Could not connect to the Git server';

                    break;

                case GIT_REPOSITORY_ERROR_INVALID_AUTHENTICATION:
                    $git->error = 'It was specified an invalid user or an incorrect password';

                    break;

                case GIT_REPOSITORY_ERROR_COMMUNICATION_FAILURE:
                    $git->error = 'There was a problem communicating with the Git server';

                    break;

                case GIT_REPOSITORY_ERROR_CANNOT_CHECKOUT:
                    $git->error = 'It was not possible to checkout the specified module from the Git server';

                    break;

                case GIT_REPOSITORY_ERROR_CANNOT_FIND_HEAD:
                    $git->error = 'The repository seems to be empty.';

                    break;

                default:
                    $git->error = 'it was returned an unexpected Git repository validation error: '.$git->error;

                    break;
            }
        }
        if (0 == strlen($git->error)) {
            echo '<li><h2>Connecting to the Git server</h2>', "\n", '<p>Repository: ', $repository, '</p>', "\n";
            flush();
            $arguments = [
                'Repository' => $repository,
            ];
        }
        if (
            0 == strlen($git->error)
            && $git->Connect($arguments)
        ) {
            echo '<li><h2>Checking out files from the repository '.$repository.'</h2>', "\n";
            flush();
            $arguments = [
                'Module' => $module,
            ];
            if ($git->Checkout($arguments)) {
                $arguments = [
                    'GetFileData'  => true,
                    'GetFileModes' => false,
                ];

                for ($files = 0;; ++$files) {
                    if (
                        !$git->GetNextFile($arguments, $file, $no_more_files)
                        || $no_more_files
                    ) {
                        break;
                    }

                    $update_file = false;
                    if (file_exists($base_dir.'\\'.$file['File'])) {
                        $original_file = file_get_contents($base_dir.'\\'.$file['File']);
                        $result        = DiffHelper::calculate(
                            $original_file,
                            $file['Data'],
                            $rendererName,
                            $differOptions,
                            $rendererOptions
                        );
                        if (null != $result) {
                            $update_file = true;
                        }
                    } else {
                        $update_file = true;
                    }

                    if (true == $update_file) {
                        if (!is_dir($base_dir.'\\'.dirname($file['File']))) {
                            mkdir($base_dir.'\\'.dirname($file['File']), 0777, 1);
                        }
                        if (!MediaSettings::isTrue('__SHOW_TRACY__')) {
                            //	file_put_contents($base_dir . "\\" . $file['File'], $file['Data']);
                        }
                        echo 'Updating file ', htmlspecialchars($file['File']), "<br>\n";
                        flush();
                        $update_file = false;
                    }
                }
                echo '<pre>Total of '.$files.' files</pre>', "\n";
                flush();

                if (!MediaSettings::isTrue('__SHOW_TRACY__')) {
                    echo '<script> ';
                    echo "setTimeout(function () { window.location.href = '/index.php'; }, 3000);";
                    echo '</script>';
                }
                flush();
            }

            $git->Disconnect();
        }
        if (strlen($git->error)) {
            echo '<H2 align="center">Error: ', htmlspecialchars($git->error), '</H2>', "\n";
        }

        ?>


</UL>

<HR>


<?php
    }
} else {
    ?>
Are you sure you want to update?
<?php
    $form->open('MyForm');
    $form->input_submit('update', '', 'Update');
    $form->input_submit('cancel', '', 'Cancel');
    $form->close();
}
?>
</BODY>

</HTML>