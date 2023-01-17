<?php
require_once(".config.inc.php");

define('TITLE', "Media Job editor");
define('NO_NAV',true);

include_once __LAYOUT_HEADER__;

$media = new media();
$template = new TEmplate();

$deleted_id = 0;

$job_id=$_REQUEST['job_id'];
$form_number=$_REQUEST['form_number'];

$media->job_id = $job_id;

function toArray($obj) {
    $vars = get_object_vars ( $obj );
    $array = array ();
    foreach ( $vars as $key => $value ) {
        $array [ltrim ( $key, '_' )] = $value;
    }
    return $array;
}


$var = $media->get_drop_form_data($form_number,["SORT_FORMER"=>1,"SORT_LETTER"=>1]);

$display = new HTMLDisplay();

foreach($var as $obj){
    $form_row[] =  toArray($obj);

   
}

 $rows_html='';
foreach ($form_row as $idx => $row) 
{

	$params=[];
	$params['FORM_NUMBER'] =  $row['form_number'];
	$params['ROW_LETTER'] =  $row['form_letter'];
	$params['ROW_ID'] =  $row['id'];
	$params['ROW_DESC'] = $row['market']." ".$row['pub']." ".$row['ship'];
	$params['PCS_COUNT'] = $row['count'];
		
		if ( $row["former"] == "Back" ) {$params['CHECK_BACK'] = "checked"; }
		if ( $row["former"] == "Front" ) {$params['CHECK_FRONT'] = "checked"; }

		$params['FT_VALUE'] = $row['face_trim'];
	
		$rows_html .= $template->return("form_edit/row",$params);
}

$body_html = $template->return("form_edit/table_body",['TABLE_ROWS' => $rows_html]);


$html_array = [
	'JOB_ID' => $job_id,
	'FORM_NUMBER' => $form_number,
	'TABLE_BODY' => $body_html,
	'TABLE_HEADER' => $template->return("form_edit/table_header")
];


echo $template->return("form_edit/main", $html_array);


include_once __LAYOUT_FOOTER__;

?>