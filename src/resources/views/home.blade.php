@extends('layouts.app')


@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    window.resultTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[2,'desc']],
                "sAjaxSource": "./alarmLog/getDataTableForDashboard",
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>    
                    
                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "mongoId", "sClass": "forceHidden"},  
                              { "mData": "alarmDate" },       
                              { "mData": "licensePlate" },                                                                                          
                              { "mData": "monitorTopic" },  
                              
                              { "mData": "alarmType" },  
                              { "mData": "message" }, 
                              { "mData": "ackBy" },

                ],
                                          
                "fnServerData": function ( sSource, aoData, fnCallback ) {

                    $.ajax( {
                        "dataType": 'json', 
                        "type": "POST", 
                        "url": sSource, 
                        "data": aoData, 
                        "success": function (json) { 
                            DTHelper.handleSuccess('datatableMessage',json);
                            fnCallback(json);
                        },
                        "error": function (xhr, error, thrown) {
                            DTHelper.handleError('datatableMessage', xhr, error);
                        }
                    } );
                }

            }
     );

     DTHelper.applySelectable(resultTable, "resultListTable");

} );


function updateAckBy(mongoId) {
        
        $.ajax({
            type: "POST",
            url: "./alarmLog/updateAckBy",
            data: {  "mongoId": mongoId },                
            dataType: "json",
           
            beforeSend: function() {
                showProcessingAck(mongoId);
            },
            success: function (json) { 
                //console.log(json);
                hideProcessingAck(mongoId);
                updateAckByResult(mongoId, json);
            },
            error: function (xhr, error, thrown) {
                hideProcessingAck(mongoId);                
                DTHelper.handleErrorAlert( xhr, error);
            }   
        });     
    
}

function showProcessingAck(mongoId) {
    $("#ackButton_" + mongoId).val("กำลังอัปเดต..");    
}

function hideProcessingAck(mongoId) {
    $("#ackButton_" + mongoId).val("รับทราบ");   
}

function updateAckByResult(mongoId, json) {
    if (AppUtil.isNotEmpty(json.error)) {
       alert(json.error);
       return;
    }   
    
    if (AppUtil.isNotEmpty(json.ackBy)) {
       $("#ackContainer_" + mongoId).html(json.ackBy);  
    }   
}


</script>


@endsection




@section('content')

<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">

<?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
 <div id='datatableMessage' class='infoMessage' style="display: none"></div>


<div class='sectionTitleMiddle'>รายการแจ้งเตือน</div>

<table id='resultListTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th  width='15'>&nbsp;</th>     
            <th  width='1'></th>
            <th  width='120'>วันที่แจ้งเตือน</th>            
            <th  width='100'>ทะเบียนรถ</th> 
             <th  width='120'>หัวข้อซ่อมบำรุง</th>
            <th  width='90'>ชนิดการเตือน</th>             
            <th  width='200'>ข้อความ</th>
            <th  width='120'>รับทราบโดย</th>
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>



@endsection





