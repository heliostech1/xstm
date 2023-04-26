@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    window.resultTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[2,'desc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','mongoId')," 
                 ?>
                "sAjaxSource": "./getDataTable",

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

                    <?php foreach ($fieldNames as $name): ?>
                    aoData.push( { "name": "<?php echo $name?>", "value": $('#<?php echo $name?>').val() } );
                    <?php endforeach; ?>
                    
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

    $("#<?=$fieldNames['date']?>").datepicker();
    $("#<?=$fieldNames['toDate']?>").datepicker();
    
    $('#searchBtn').click( function() {
        resultTable.fnPageChange('first');
    } );

    $('#clearCriteriaBtn').click(function() { 
        FormHelper.clearValue('searchForm');
    }); 
	

} );


function submitPageData(target, oTable, rowId, newWindow) {
    var form = document.hiddenCriteriaForm;
    var targetBlank = (newWindow === true)?  "_blank": "_self";
    form.setAttribute("target", targetBlank);     

    <?php foreach ($fieldNames as $name): ?>
        form.<?php echo $name?>.value = $('#<?php echo $name?>').val();  
    <?php endforeach; ?>
               
    form.<?php echo $fieldPrefix?>_tableDisplayStart.value = oTable.fnSettings()._iDisplayStart;
    form.<?php echo $fieldPrefix?>_tableDisplayLength.value = oTable.fnSettings()._iDisplayLength;
    form.<?php echo $fieldPrefix?>_tableSelectedId.value = rowId;
    form.action = target;
    form.submit();
}

function updateAckBy(mongoId) {
        
        $.ajax({
            type: "POST",
            url: "./updateAckBy",
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

<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">

 <?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display: none"></div>

<form name='hiddenCriteriaForm' method='post' style='display:none;'> 
 {{ csrf_field() }}
<?php foreach ($fieldNames as $name): ?>
    <input type='hidden' name='<?php echo $name?>' />
<?php endforeach; ?>
    
<input type='hidden' name='<?php echo $fieldPrefix?>_tableDisplayStart' />
<input type='hidden' name='<?php echo $fieldPrefix?>_tableDisplayLength' />
<input type='hidden' name='<?php echo $fieldPrefix?>_tableSelectedId' />
</form>

<h4 >ค้นหา</h4>
<fieldset class='sectionFieldset'>
    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">
        <tr>
            <td>

                <span class="formLabel">วันที่:</span>
                <input class="textInput" type="text" style="width:80px" id="<?php echo $fieldNames['date']?>" value='<?php echo $fieldDatas['date']?>'>
                -
                <input class="textInput" type="text" style="width:80px"  id="<?php echo $fieldNames['toDate']?>" value='<?php echo $fieldDatas['toDate']?>'>
                &nbsp;
       
                <span class="formLabel">รับทราบแล้ว:</span>
                {!! SiteHelper::dropdown($fieldNames['ackBy'], $yesNoOpt, $fieldDatas['ackBy'], " id='".$fieldNames['ackBy']."' class='textInput'") !!}
                
                
                
                &nbsp;&nbsp;<input type="button" class='formButton' value="ค้นหา" id="searchBtn" />
                &nbsp;<input type="button" class='formButton' value="ล้าง" id="clearCriteriaBtn" />  
                
            </td>
        </tr>                
    </table>

</fieldset>


<div style='height: 10px; font-size: 0px'></div>

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

<br>



@endsection



