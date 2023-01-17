<?php
require_once(".config.inc.php");
define('TITLE', "Media Job editor");
$template = new Template();


$job_id=$_REQUEST['job_id'];

$job  = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
$media = new Media($job);

if(key_exists("actSubmit",$_REQUEST))
{
	if($_REQUEST['actSubmit'] == "confirm")
	{

		$media->delete_zip();
		$media->delete_xlsx();   		
		$media->delete_job();

	}

	echo   JavaRefresh(__URL_PATH__."/index.php");
	exit;
}

include_once __LAYOUT_HEADER__;
$form_url = __URL_PATH__."/delete_job.php";



?>

<main role="main" class="container">
<table>
<tr>
    <td>
 <?php
						
		$form = new Formr\Formr();
		$hidden = [	"job_id" => $job_id ];				
		$form->open("",'',$form_url ,'post','',$hidden);
		echo HTMLDisplay::output("Are you sure you want to delete this job <br>");
		$form->input_submit('actSubmit','',"Go Back",'','class="button"');

		$form->input_submit('actSubmit','',"confirm",'','class="button"');

		$form->close();
 
 ?>
</td>
</tr>
</table>

</main>

<?php
include_once __LAYOUT_FOOTER__;
?>