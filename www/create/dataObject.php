<?php

use CWP\Core\Media;

require_once '../.config.inc.php';

$connect = new PDO(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);

if (isset($_REQUEST['query'])) {
    if (isset($_REQUEST['table'])) {


        $condition = preg_replace('/[^A-Za-z0-9\- ]/', '', $_REQUEST["query"]);
        $table =  "job_".$_REQUEST["table"];

        $query = "SELECT name FROM  ".$table."
            WHERE name LIKE '%".$condition."%'
            ORDER BY id DESC
            LIMIT 10
        ";


        $result = $connect->query($query);

        $replace_string = '<b>'.$condition.'</b>';

        foreach($result as $row) {
            $data[] = array(
                'post_title'		=>	str_ireplace($condition, $replace_string, $row["name"])

            );
        }

        echo json_encode($data);



        //string text retreived from the textbox
        // $condition = preg_replace('/[^A-Za-z0-9\- ]/', '', $_REQUEST["query"]);
        //

        // $result = Media::$connection->query('SELECT name FROM '.$table.' WHERE name like ?', $condition.'%');


        // if ($result->getRowCount() > 0) {
        //     foreach($result as $row) {
        //         $data[] = array(
        //             //'post_title'		=>	str_ireplace($condition, $replace_string, $row->name)
        //             'post_title'		=>	$row->name,
        //         );
        //     }
        // }
    }
}
$post_data = json_decode(file_get_contents('php://input'), true);

if(isset($post_data['search_query'])) {



    $query = "
	SELECT search_id FROM recent_search
	WHERE search_query = :search_query AND
    search_table = :search_table
	";

    $statement = $connect->prepare($query);

    $statement->bindValue(':search_query', $post_data['search_query']);
    $statement->bindValue(':search_table', $post_data['table']);

    try {
        $statement->execute();
    } catch (PDOException $e) {
        $statement->debugDumpParams();
        echo $e->getMessage();
    }

    if($statement->rowCount() == 0) {

        $query = "INSERT INTO recent_search (search_query,search_table) VALUES (:search_query, :search_table)";


        $statement = $connect->prepare($query);

        $statement->bindValue(':search_query', $post_data['search_query']);
        $statement->bindValue(':search_table', $post_data['table']);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            $statement->debugDumpParams();

            echo $e->getMessage();
        }
    }

    $output = array(
        'success'	=>	true
    );

    echo json_encode($output);

}

if(isset($post_data['action'])) {
    if($post_data['action'] == 'fetch') {
        //$table = $post_data['table'];
        $table =  "job_".$post_data["table"];
        $query = "SELECT name FROM ".$table; // WHERE search_table = '".$table."' ORDER BY search_id DESC LIMIT 10";

        $result = $connect->query($query);

        $data = array();

        foreach($result as $row) {

            $data[] = array(
            //    'id'				=>	$row['search_id'],
                'search_query'		=>	$row["name"]
            );
        }

        echo json_encode($data);
    }

}
