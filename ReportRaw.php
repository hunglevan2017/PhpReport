<?php
require_once 'config.php';
require (ROOT_PATH . 'lib/define/Conf.php');
require(ROOT_PATH . 'lib/db/PostgreSQLClass.php');
require(ROOT_PATH . 'lib/ssp.class.pg.php' );

$pgSQL = new PostgreSQLClass();
//$conn = $pgSQL->getConnectionAnyDB("10.1.1.3","5432","production","db_s18001_c006_dc_system_20181109","user_s18001_c006_dc_system_20181109","db@s18001_c006_dc_system_20181109") or die(pg_last_error());
$conn = $pgSQL->getConnectionAnyDB("10.1.1.3","5432","production","db_pl_ntb_new_system_20180707","rls_dev","S@igon_D3v") or die(pg_last_error());

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$out = array('error' => false);

if(isset($_GET['crud'])){
	$crud = $_GET['crud'];
}



if($crud == 'dc'){

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); //convert JSON into array
  
    try {
        // Connect to Database
        $pgSQL = new PostgreSQLClass();
        $conn->beginTransaction();
  
        $sql = "select * from sp_report_qc_get_datachecker_multi()";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $out['error'] = false;
        $out['report'] = $stmt->fetchAll();
  
        unset($stmt);
        $conn->commit();
  
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
  }

if($crud == 'sumary'){

  $inputJSON = file_get_contents('php://input');
  $input = json_decode($inputJSON, TRUE); //convert JSON into array

  try {
      // Connect to Database
      $pgSQL = new PostgreSQLClass();
      $conn->beginTransaction();

       $sql = "select * from sp_report_qc_typos_total('";
       $sql = $sql.$input['startTime']."','";
       $sql = $sql.$input['endTime']."')";


      $stmt = $conn->prepare($sql);
      $stmt->execute();
      
      $out['error'] = false;
      $out['report'] = $stmt->fetchAll();

      unset($stmt);
      $conn->commit();

  } catch (Exception $exc) {
      echo $exc->getTraceAsString();
  }
}


if($crud == 'detail'){

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); //convert JSON into array
  
    try {
        // Connect to Database
        $pgSQL = new PostgreSQLClass();
        $conn->beginTransaction();
  
        $sql = "select * from sp_report_qc_typos_detail('";
        $sql = $sql.$input['startTime']."','";
        $sql = $sql.$input['endTime']."')";
  
        $stmt = $conn->prepare($sql);
        $stmt->execute();


        $out['error'] = false;
        $out['report'] = $stmt->fetchAll();

        unset($stmt);
        $conn->commit();
  
  
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
  }

header("Content-type: application/json");
echo json_encode($out['report']);
die();


?>
