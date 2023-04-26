@extends('layouts.app')

@section('header')


<script type='text/javascript'>
$(document).ready(function() {


    var oTable = DTHelper.createPagingDatatable('historyTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[0,'desc']],
                "sAjaxSource": "./getSiteUsageHistoryDataTable",

                "aoColumns": [
                              { "mData": "usageTime" },
                              { "mData": "usageBy" },
                              { "mData": "ip" },
                              { "mData": "userAgent" },
                              { "mData": "usageType" },
                              { "mData": "description" }
                ],
                                
                "fnServerData": function ( sSource, aoData, fnCallback ) {

                    aoData.push( { "name": "criteria_id", "value":  $('#criteriaId').val() } );
                    aoData.push( { "name": "criteria_date", "value": $('#criteriaDate').val() } );
                    aoData.push( { "name": "criteria_to_date", "value": $('#criteriaToDate').val() } );
                    aoData.push( { "name": "criteria_type", "value": $('#criteriaType').val() } );

                    
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

    
    DTHelper.applySelectable(oTable, "historyTable");

    $("#criteriaDate").datepicker({
        changeMonth: true,
        changeYear: true
    });

    $("#criteriaToDate").datepicker({
        changeMonth: true,
        changeYear: true
    });

    $('#searchBtn').click( function() {
        oTable.fnPageChange('first');
    } );

    $('#clearCriteriaBtn').click(function() { 
        $('#criteriaDate').val("");
        $('#criteriaToDate').val("");
        $('#criteriaId').val("");
        $('#criteriaType').val("");
    });         

} );

</script>


@endsection

@section('content')

<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display:none"></div>

<h4>ค้นหา</h4>
<fieldset class='sectionFieldset' style='margin: 0 10px 10px 10px'>
    
    <table cellspacing="0" border="0" cellpadding="0" class="formTable">
        <tr><td>
            <span class="formLabel">วันที่:</span>
            <input class="textInput" type="text" style='width:80px' id="criteriaDate" autocomplete="off"> -
            <input class="textInput" type="text" style='width:80px' id="criteriaToDate" autocomplete="off">
            &nbsp;
            <span class="formLabel">รหัสผู้ใช้:</span>
            <input class="textInput" type="text" size='11' id="criteriaId" value="<?php echo $criteria_id?>" >
            &nbsp;
            <span class="formLabel">การใช้งาน:</span> 
            <input id='criteriaType' class="textInput" type="text" size='15' > 
            
            &nbsp;&nbsp;<input type="button" class='formButton' value="ค้นหา" id="searchBtn"/>
            &nbsp;<input type="button" class='formButton' value="ล้าง" id="clearCriteriaBtn"/>
        </td></tr>      
    </table>
</fieldset>

<table id='historyTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th width='130' >เวลา</th>
            <th width='80' >ผู้ทำการ<br>ใช้งาน</th>
            <th width='70' >หมายเลข ip</th>
            <th width='150' >บราวเซอร์ที่ใช้</th>
            <th width='150'>การใช้งาน</th>            
            <th width='180' >รายละเอียด</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<br />


<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>
    <div style='clear: both'></div>
</div>

@endsection
