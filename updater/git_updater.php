<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>

<HEAD>
	<TITLE>Test for Manuel Lemos' PHP Git client class</TITLE>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>    
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

 
</HEAD>

<BODY>
	<H1 align="center">Test for Manuel Lemos' PHP Git client class</H1>
	
			<div class="progress">
    <div id="theBar" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"
        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
        
    </div>
</div>

<script>
    var i = 100;

    var counterBack = setInterval(function () {
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
		require('git_http.php');
		require('git_client.php');

		$base_dir = __DIR__ . "\\..\\";
		set_time_limit(0);
		$git = new git_client_class;

		/* Connection timeout */
		$git->timeout = 20;

		/* Data transfer timeout */
		$git->data_timeout = 60;

		/* Output debugging information about the progress of the connection */
		$git->debug = 0;

		/* Output debugging information about the HTTP requests */
		$git->http_debug = 0;

		/* Format dubug output to display with HTML pages */
		$git->html_debug = 0;

		$repository = 'https://github.com/bsodergren/cwp_www.git';
		$module = '';
		//    $log_file = 'composer.json';

		echo '<li><h2>Validating the Git repository</h2>', "\n", '<p>Repository: ', $repository, '</p>', "\n", '<p>Module: ', $module, '</p>', "\n";
		flush();
		$arguments = array(
			'Repository' => $repository,
			'Module' => $module,
		);
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
					$git->error = 'it was returned an unexpected Git repository validation error: ' . $git->error;
					break;
			}
		}
		if (strlen($git->error) == 0) {
			echo '<li><h2>Connecting to the Git server</h2>', "\n", '<p>Repository: ', $repository, '</p>', "\n";
			flush();
			$arguments = array(
				'Repository' => $repository
			);
		}
		if (
			strlen($git->error) == 0
			&& $git->Connect($arguments)
		) {
			echo '<li><h2>Checking out files from the repository ' . $repository . '</h2>', "\n";
			flush();
			$arguments = array(
				'Module' => $module,

			);
			if ($git->Checkout($arguments)) {
				$arguments = array(
					'GetFileData' => true,
					'GetFileModes' => false,
					'hash' => true
				);

				$it = new RecursiveDirectoryIterator($base_dir, RecursiveDirectoryIterator::SKIP_DOTS);
				$dfiles = new RecursiveIteratorIterator(
					$it,
					RecursiveIteratorIterator::CHILD_FIRST
				);
				foreach ($dfiles as $d_file) {
					if ($d_file->isDir()) {
						$dirname = str_replacE($base_dir . "\\", "", $d_file->getPathname());

						if (is_dir($d_file->getRealPath())) {

							if (
								$dirname == 'updater' ||
								$dirname == '.database'
							) {
							} else {

								//echo '<pre>', HtmlSpecialChars($dirname), '</pre>';
								rmdir($d_file->getRealPath());
							}
						}
					} else {
						if (file_exists($d_file->getRealPath())) {
							$filename = basename($d_file->getRealPath());
							if (
								$filename == "git_updater.php" ||
								$filename == "git_http.php" ||
								$filename == "git_client.php" ||
								$filename == "cwp_sqlite.db"
							) {
							} else {

								//echo '<pre>', HtmlSpecialChars($filename), '</pre>';
								unlink($d_file->getRealPath());
							}
						}
					}
				}

				for ($files = 0;; ++$files) {
					if (
						!$git->GetNextFile($arguments, $file, $no_more_files)
						|| $no_more_files
					)
						break;
					if (!is_dir($base_dir . "\\" . dirname($file['File']))) {
						mkdir($base_dir . "\\" . dirname($file['File']), 0777, 1);
					}
					file_put_contents($base_dir . "\\" . $file['File'], $file['Data']);

					//$file['Data'] = '';
				//	echo 'Updating file ', HtmlSpecialChars($file['File']), "<br>\n";
				//	flush();
				}
				echo '<pre>Total of ' . $files . ' files</pre>', "\n";
				flush();
				echo '<script>';
				echo "setTimeout(function () { window.location.href = '/index.php'; }, 6000);";
				echo "</script>";
				flush();


			}

			$git->Disconnect();
		}
		if (strlen($git->error))
			echo '<H2 align="center">Error: ', HtmlSpecialChars($git->error), '</H2>', "\n";
		?>
	</UL>
	<HR>

</BODY>

</HTML>