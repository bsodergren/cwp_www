<?php
require_once(".config.inc.php");

use Symfony\Component\Finder\Finder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

define('TITLE', "View Form");

$form_number = '';
$file_id = '';
$sheet_id = 0;

if(key_exists('form_number',$_REQUEST))
{
    $form_number = $_REQUEST['form_number']; 
}

if(key_exists('file_id',$_REQUEST))
{
    $file_id = $_REQUEST['file_id']; 
}

if(key_exists('sheet_id',$_REQUEST))
{
    $sheet_id = $_REQUEST['sheet_id']; 
}


if(!is_dir($media->xlsx_directory))
{
    XLSXViewer::checkifexist($media);
}

$finder = new Finder();
$finder->files()->in($media->xlsx_directory)->name('*.xlsx')->notName('~*')->sortByName(true);
$found = false;

if (!$finder->hasResults())
{
    XLSXViewer::checkifexist($media);
} 

    $idx=0;
    $params['FORM_LIST_HTML'] = '';

    foreach ($finder as $file)
    {   
         $files[] =  $file->getRealPath();
         $class = "enabled";
         preg_match('/.*_([FM0-9]+).xlsx/',  $file->getRealPath(), $output_array);
         [$text_form,$text_number] = explode("FM",$output_array[1]);

         if($form_number != '')
         {
            if($form_number == $text_number)
            {
                $file_id = $idx;
                $found = true;
            }
        } else {
            $found = true;
        }
         
            if($file_id == $idx){
                $class = "disabled";
                $current_form_number = $text_number;
            }
     

            $page_form_html .= template::GetHTML('/view/form_link', [
                'PAGE_FORM_URL' => __URL_PATH__ . "/view.php?job_id=" . $media->job_id . "&file_id=" . $idx,
                'PAGE_FORM_NUMBER' => "FM ".$text_number,
                'FORM_DISABLED' => $class,
            ]);

        if(0 == $idx % 9 && $idx > 0) {
            $params['FORM_LIST_HTML'] .= template::GetHTML('/view/form_list', ['FORM_LINKS_HTML' => $page_form_html]);
            $page_form_html = '';
        }
        $idx++;
    }


if ($found === false)
{
    XLSXViewer::checkifexist($media);
} 
if($page_form_html != ''){

    $params['FORM_LIST_HTML'] .= template::GetHTML('/view/form_list', ['FORM_LINKS_HTML' => $page_form_html]);
}


    if($file_id != '')
    {


        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($files[$file_id]);
        $sheet_names = $spreadsheet->getSheetNames();
       
        $params['SHEET_LIST_HTML'] = '';
        foreach ($sheet_names as $sheet_index => $sheet_name)
        {   
            if($sheet_name == 'Worksheet')
            {
                continue;
            }

            $class = "enabled";
            if($sheet_id == $sheet_index){
                $class = "disabled";
            }
            
            [$name,$_] = explode(" ",$sheet_name);
            [$sheetName,$former] = explode("_",$name);
            if(!isset($last)) {
                $last = '';
            }
         
            $cellValue = ucwords(strtolower($spreadsheet->getSheet($sheet_index)->getCellByColumnAndRow(2, 8)->getValue()));

            $sheet_form_array[$former][] = template::GetHTML('/view/sheet_link', [
                'PAGE_FORM_URL' => __URL_PATH__ . "/view.php?job_id=" . $media->job_id . "&file_id=" . $file_id."&sheet_id=" . $sheet_index,
                'PAGE_FORM_NUMBER' => $sheetName ." ".$cellValue,
                'SHEET_DISABLED' => $class,
                'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',
                'SHEET_CLASS' => 'bg-success',
            ]);

        }

        foreach ($sheet_form_array as $former => $buttons)
        {
            $button[0] = template::GetHTML('/view/former_button',['FORMER_DESC'=>$former]);
            $buttons = array_merge($button,$buttons);
            $sheet_links_html = implode("\n",$buttons);
            $params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_links_html]);
        }

        $sheet_edit_html = template::GetHTML('/view/sheet_link', [
            'PAGE_FORM_URL' => __URL_PATH__ . "/form.php?job_id=" . $media->job_id . "&form_number=" . $current_form_number,
            'PAGE_FORM_NUMBER' => "Edit Form",
            'SHEET_DISABLED' => "enabled",
            'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',

            'SHEET_CLASS' => 'btn-info',
        ]);


        $params['SHEET_LIST_HTML']  .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_edit_html]);
   
            $writer = IOFactory::createWriter($spreadsheet, 'Html');

            $writer->setSheetIndex($sheet_id);

            
            $custom_css = $writer->generateStyles(true); 

          
            $rep_array = [
                '{border: 1px solid black;}' => '{border: 0px dashed red;}',
                'font-size:11pt;' => '',
                'font-size:11pt' => '',
            ];

            foreach ($rep_array as $find => $replace ){
                $custom_css = str_replace($find,$replace,$custom_css );
            }
            //$custom_css = str_replace('{border: 1px solid black;}','{border: 0px dashed red;}',$custom_css );



//            $custom_css = str_replace('font-size:11pt','',$custom_css );
                    


            $message = $writer->generateSheetData();

            $message = str_replace('column0 style7','column0 style6',$message );
            $lines = explode("\n",$message);
            
            $id = 0;
            $end = false;
            foreach($lines as $k => $text)
            {
                if(str_contains($text,'"row0"')
                || str_contains($text,'"row1"')
                || str_contains($text,'"row2"')
                || str_contains($text,'"row3"')
                )
                {
                    $array_rows[] = $k;
                    $end = true;
                }
                if($end === true)
                {
                    if(str_contains($text,"/tr"))
                    {
                        $array_rows[] = $k;
                        $id++;
                        $end = false;
                    }
                }
                
            }

          //  $lines[1] = str_replace("border='0'","border='0'",$lines[1] );
            $key = array_key_last($array_rows);
            $key = $array_rows[$key] + 1;
            $text_array_head = array_slice($lines,0,$array_rows[0]);
            $text_array_body = array_slice($lines,$key);

            $message = implode("\n",$text_array_head);
            $message .= implode("\n",$text_array_body);

            // dd();
            // $writer->save('php://output');

            //$msg_array = explode("\r",$message);
            //dd($msg_array);


            $params['MESSAGE']= $message;
        }
    

    Header::Display("",["CUSTOM_CSS" => $custom_css]);

    $template->template("view/main", $params);
$template->render();



include_once __LAYOUT_FOOTER__;
