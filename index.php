<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.css" >
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css" >
    <link rel="stylesheet" href="css/jquery-ui.css" >
    <link rel="stylesheet" href="css/jquery.dataTables.min.css" >
    <link rel="stylesheet" href="css/buttons.dataTables.min.css" >
</head>
<body>
<?php
require_once 'config.php';

require (ROOT_PATH . 'lib/define/Conf.php');
require(ROOT_PATH . 'lib/db/PostgreSQLClass.php');
require(ROOT_PATH . 'lib/ssp.class.pg.php' );

$pgSQL = new PostgreSQLClass();
//$conn = $pgSQL->getConnectionAnyDB("10.1.1.3","5432","production","db_pl_ntb_new_system_20180707","user_pl_ntb_new_system_20180707","db@pl_ntb_new_system_20180707") or die(pg_last_error());
$conn = $pgSQL->getConnectionAnyDB("10.1.1.3","5432","production","db_pl_ntb_new_system_20180707","rls_dev","S@igon_D3v") or die(pg_last_error());

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


    $result ='';
    if(isset($_POST["import"]))
    {
        $filename=$_FILES["file"]["tmp_name"];
        if($_FILES["file"]["size"] > 0)
        {
            $file = fopen($filename, "r");


                // Connect to Database
                $pgSQL = new PostgreSQLClass();
                $conn->beginTransaction();
                $sql = "Truncate table db_pl_ntb_new_system_20180707.tmp_qc_result";
                $stmt = $conn->prepare($sql);
                $stmt->execute();


                 $row=1;
                 while ( ($emapData = fgetcsv($file)) !== FALSE )
                 {
                     if($row == 1){ $row++; continue; }
                     $sql = "INSERT into tmp_qc_result(id_f1) values ('$emapData[0]')";
                     $stmt = $conn->prepare($sql);
                     $stmt->execute();
                 }

                $conn->commit();
                $result =  'CSV File has been successfully Imported';

         
            fclose($file);
            //header('Location: index.php');
        }
        else
        {
            $result ='Invalid File:Please Upload CSV File';
        }
    }


    
?>


    <div class="container-fluid" style="margin-top:1%">
        <h3>Report Zapin</h3>


        <p id="result"><?php echo $result; ?></p>
        <div class="row">
            <form action="" method="post"
                name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                <div>
                    <input type="file" name="file"
                        id="file" accept=".xls,.xlsx">
                    <button type="submit" id="submit" name="import"
                        class="btn-submit">Import</button>
            
                </div>
            
            </form>
        </div>

        <br>

        <div id="root">
            <div class="row">
                <div class="col-lg-2">
                    <span><strong class="title " id="start">Bắt đầu</strong></span>
                    <input id="fromDate" class="form-control datepicker" />
                </div>
                <div class="col-lg-2">
                    <span><strong class="title " id="end">Kết thúc</strong></span>
                    <input id="endDate" class="form-control datepicker" />
                </div>
                <div class="col-lg-2" style="margin-top: auto;">
                    <span><strong class="title " id="end">&nbsp;</strong></span>
                    <button type="button" class="btn btn-primary" id="btnSearch" @click=" SearchReport()"  > 
                     View (F7)
                    </button>
                </div>
            </div>

            <br>
            
            <div class="col-sm-12 col-md-8 col-lg-12">

            <div id="div_dc">
							<table id="dc" class="display" width="100%"></table> 
                    </div>
                    <br>


                    <div id="div_sumary">
							<table id="sumary" class="display" width="100%"></table> 
                    </div>

                    <br>

                    <div id="div_detail">
							  <table id="detail" class="display" width="100%"></table>
					</div>
			</div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.js" ></script>
    <script src="js/jquery-1.12.4.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/datatables.min.js" ></script>
    <script src="js/vue.js"></script>
    <script src="js/axios.js"></script>
    <script src="js/dataTables.buttons.min.js" ></script>
    <script src="js/buttons.html5.min.js" ></script>

    <script src="js/jszip.min.js" ></script>
    <script src="js/pdfmake.min.js" ></script>
    <script src="js/vfs_fonts.js" ></script>
    <script src="js/buttons.html5.min.js" ></script>




    

    
    <script>


    new Vue({
    el: '#root',
    data: {
        filter: {
           startTime:'' ,
           endTime:'' 
        },
    },
    mounted() {

    },
    computed: {},
    methods: {
        SearchReport() {
                    this.filter.startTime = $('#fromDate').val().split("/").reverse().join("/");
                    this.filter.endTime = $('#endDate').val().split("/").reverse().join("/");

                    var dcButton = [{
               				extend: 'excel',
               				 filename: 'Datachecker' ,
                                text: 'Export Datachecker'}];
                                
                    var sumaryButton = [{
               				extend: 'excel',
               				 filename: 'Sumary' ,
               				 text: 'Export Sumary'}];
                     
               		var detailButton = 	 [ {
                     		extend: 'excel',
           				 	filename:'Detail'  ,
           				 	text: 'Export Detail'}];
                       var domjs = 'Bfrtip';
                       var condition = {
                        "startTime":this.filter.startTime,
                        "endTime":this.filter.endTime
                    };

                      /**** Datachecker *****/
                    var dataName = ["id_f1", "managementid", "cancel_status", "step_status","field_name","value_1","value_2","value_verify","user_1","user_2","user_verify"];
                    var columnDefs_dc = [];
                    var aoData_dc = [];

                    columnDefs_dc.push({"title": "STT","targets": 0,
                    			 "render": function(data, type, row, meta) {
	        							return  `<span>${meta.row + 1}</span>`;	
	        						}	
                             });
                    
                    aoData_dc.push({"mData": null, "defaultContent": ""});
                             
                    for (i = 0; i < dataName.length; i++) { 

                        columnDefs_dc.push( {"title": dataName[i].toUpperCase(),"targets": (i+1)} );
                        aoData_dc.push( {"mData":  dataName[i], "defaultContent": ""});

                    }
                    console.log(columnDefs_dc);
                    console.log(aoData_dc);

                    $('#div_dc').html('<table id="dc" class="table table-striped table-bordered table-hover dataTable display" width="100%"></table>');
	                   table = $('#dc').dataTable({
	                         "ajax": {
	                           "url": 'ReportRaw.php?crud=dc',
	                           "type": "POST",
	                           "data": function(d) {
	                             return JSON.stringify(condition);
	                           },
	                           error: function (xhr, error, thrown) {
	                         	  console.log(error);
	                             //location.reload();
	                           },
	                           "dataSrc": "",
	                           "contentType": "application/json; charset=utf-8"
	                         },
	                         select: {
	                           style: 'single'
	                         },
	                         "columnDefs": columnDefs_dc,
	                         "aoColumns": aoData_dc,
	                         "ordering": false,
	                         "bPaginate": true,
                             "pageLength": 5,
                             dom: domjs,
	                         buttons: dcButton,
	                        // "scrollX": true,
	                        // "scrollY": 350,
	                         "initComplete": function(settings, json) {
	                           //$("#search").attr("disabled", false);
	                           
	                           $("#cusloading").hide();
	                         },
	                         "fnDrawCallback": function ( oSettings ) {
	                     	    //$(oSettings.nTHead).hide();
	                       },
	                         "autoWidth": false
                     });



                  
                    /**** Sumary *****/
                    /*
					axios.post('ReportRaw.php?crud=sumary', this.filter )
						.then(function(response){
							if(response.data.error){
                                alert("Error");
							}
							else{
                                console.log(response.data.report);
                                //Sumary
							}
                    });
                    */
                  
                    
                  

                    var columnDefs_sumary = [
                    		 {"title": "STT","targets": 0,
                    			 "render": function(data, type, row, meta) {
	        							return  `<span>${meta.row + 1}</span>`;	
	        						}	
                    		 },
	                   	  	 {"title": "User Working","targets": 1},
	                         {"title": "Step Name","targets": 2},
	                         {"title": "Total Apps","targets": 3},
	                         {"title": "Total Fields","targets": 4}
                    ];
                   var  aoData_sumary = [
                    	{"mData": null, "defaultContent": ""},
                    	{"mData": "user_working", "defaultContent": ""},
                    	{"mData": "step_name", "defaultContent": ""},
                    	{"mData": "total_apps", "defaultContent": ""},
                    	{"mData": "total_fields", "defaultContent": ""}
                    ];

                 


	                $('#div_sumary').html('<table id="sumary" class="table table-striped table-bordered table-hover dataTable display" width="100%"></table>');
	                   table = $('#sumary').dataTable({
	                         "ajax": {
	                           "url": 'ReportRaw.php?crud=sumary',
	                           "type": "POST",
	                           "data": function(d) {
	                             return JSON.stringify(condition);
	                           },
	                           error: function (xhr, error, thrown) {
	                         	  console.log(error);
	                             //location.reload();
	                           },
	                           "dataSrc": "",
	                           "contentType": "application/json; charset=utf-8"
	                         },
	                         select: {
	                           style: 'single'
	                         },
	                         "columnDefs": columnDefs_sumary,
	                         "aoColumns": aoData_sumary,
	                         "ordering": false,
	                         "bPaginate": true,
                             "pageLength": 5,
                             dom: domjs,
	                         buttons: sumaryButton,
	                        // "scrollX": true,
	                        // "scrollY": 350,
	                         "initComplete": function(settings, json) {
	                           //$("#search").attr("disabled", false);
	                           
	                           $("#cusloading").hide();
	                         },
	                         "fnDrawCallback": function ( oSettings ) {
	                     	    //$(oSettings.nTHead).hide();
	                       },
	                         "autoWidth": false
                     });
                  //////////////////////
                  var columnDefs_detail = [
                    		 {"title": "STT","targets": 0,
                    			 "render": function(data, type, row, meta) {
	        							return  `<span>${meta.row + 1}</span>`;	
	        						}	
                    		 },
	                   	  	 {"title": "Manegement ID","targets": 1},
	                         {"title": "User Working","targets": 2},
	                         {"title": "Step Name","targets": 3},
                             {"title": "App ID","targets": 4},
                             {"title": "Field Name","targets": 5},
                             {"title": "Incorrect","targets": 6},
                             {"title": "Correct","targets": 7}
                    ];
                   var  aoData_detail = [
                    	{"mData": null, "defaultContent": ""},
                    	{"mData": "managementid", "defaultContent": ""},
                    	{"mData": "user_working", "defaultContent": ""},
                    	{"mData": "step_name", "defaultContent": ""},
                        {"mData": "app_id", "defaultContent": ""},
                        {"mData": "field_name", "defaultContent": ""},
                        {"mData": "incorrect", "defaultContent": ""},
                        {"mData": "correct", "defaultContent": ""}
                        
                    ];

                    var condition = {
                        "startTime":this.filter.startTime,
                        "endTime":this.filter.endTime
                    };


	                $('#div_detail').html('<table id="detail" class="table table-striped table-bordered table-hover dataTable display" width="100%"></table>');
	                   table = $('#detail').dataTable({
	                         "ajax": {
	                           "url": 'ReportRaw.php?crud=detail',
	                           "type": "POST",
	                           "data": function(d) {
	                             return JSON.stringify(condition);
	                           },
	                           error: function (xhr, error, thrown) {
	                         	  console.log(error);
	                             //location.reload();
	                           },
	                           "dataSrc": "",
	                           "contentType": "application/json; charset=utf-8"
	                         },
	                         select: {
	                           style: 'single'
	                         },
	                         "columnDefs": columnDefs_detail,
	                         "aoColumns": aoData_detail,
	                         "ordering": false,
	                         "bPaginate": true,
                             "pageLength": 5,
                             dom: domjs,
	                         buttons: detailButton,
	                        // "scrollX": true,
	                        // "scrollY": 350,
	                         "initComplete": function(settings, json) {
	                           //$("#search").attr("disabled", false);
	                           
	                           $("#cusloading").hide();
	                         },
	                         "fnDrawCallback": function ( oSettings ) {
	                     	    //$(oSettings.nTHead).hide();
	                       },
	                         "autoWidth": false
	                       });   



        }
    }
    });
    $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' }).datepicker("setDate", new Date());

    </script>

</body>
</html>
