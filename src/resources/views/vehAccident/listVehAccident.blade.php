@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[5,'asc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','mongoId')," 
                 ?>
                "sAjaxSource": "./getDataTable",

                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "mongoId" , "sClass": "forceHidden"},   
                              { "mData": "vehicleId" , "sClass": "forceHidden"},                               
                              { "mData": "licensePlate" },
                              { "mData": "times" },
                              
                              { "mData": "accDate" },
                              { "mData": "accPlace" },                               
                              { "mData": "driverName" },
                              { "mData": "fixStartDate" },
                              { "mData": "fixEndDate" },            
                              
                              { "mData": "cost" },
                              { "mData": "fileDatas" },
                              
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

    DTHelper.applySelectable(oTable, "resultListTable");

    //$("#< ?=$fieldNames['cri_date']?>").datepicker();
    //$("#< ?=$fieldNames['cri_to_date']?>").datepicker();
        
    $('#addBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['mongoId']: "";
        submitPageData("./add", oTable, id);        
    } );

    $('#editBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./edit", oTable, data['mongoId']);
        }
    } );

    $('#viewBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./view", oTable, data['mongoId']);
        }
    } );

    $('#deleteBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        if (AppUtil.checkEmpty(data)) {
             if (confirm("คูณต้องการลบข้อมูล '"+data['licensePlate']+"' ?")) {
                 submitPageData("./delete", oTable, data['mongoId']);
             }
        }
    } );

    
    
    $('#searchBtn').click( function() {
        oTable.fnPageChange('first');
    } );

    $('#clearCriteriaBtn').click(function() { 
        FormHelper.clearValue('searchForm');
    }); 
	
    $( "#<?php echo $fieldNames['licensePlate']?>" ).autocomplete({
        source: <?php echo json_encode($licensePlateList) ?>,
        minLength:0
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
    
    $( "#<?php echo $fieldNames['accDateFrom']?>" ).datepicker();
    $( "#<?php echo $fieldNames['accDateTo']?>" ).datepicker();
    
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
<fieldset class='sectionFieldset' >
    

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">

        <tr>
            <td>
                <!--
                <span class="formLabel">รหัสรถ:</span>
                <input class="textInput" type="text" style="width:120px" id="< ?php echo $fieldNames['vehicleId']?>" value='< ?php echo $fieldDatas['vehicleId']?>'>                
                 &nbsp;  
                 -->

                <span class="formLabel">ทะเบียนรถ:</span>
                <input class="textInput" type="text" style="width:120px" id="<?php echo $fieldNames['licensePlate']?>" value='<?php echo $fieldDatas['licensePlate']?>'>                
                 &nbsp;  

                <span class="formLabel">วันที่เกิดเหตุ:</span>
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['accDateFrom']?>" value='<?php echo $fieldDatas['accDateFrom']?>' autocomplete="off" >                
                - 
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['accDateTo']?>" value='<?php echo $fieldDatas['accDateTo']?>' autocomplete="off" >                
                
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
            <th  width='15'>&nbsp;</th>  
            <th  width='90'>รหัสรถ</th>         
            <th  width='90'>ทะเบียนรถ</th>   
            <th  width='60'>ครั้งที่</th>
            
            <th  width='90'>วันที่เกิดเหตุ</th>
            <th  width='150'>สถานที่เกิดเหตุ</th>
            <th  width='120'>ชื่อผู้ขับขี่</th>
            <th  width='90'>วันที่เข้าซ่อม</th>
            <th  width='90'>วันที่ซ่อมเสร็จ</th>
            
            <th  width='90'>ค่าใช้จ่าย</th>
            <th  width='90'>เอกสาร</th>
            
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>

<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('vehAccident/add', ' value="เพิ่ม" id="addBtn"  ') !!}
   {!! SiteHelper::footerBtn('vehAccident/edit', ' value="แก้ไข" id="editBtn" '); !!}
   {!! SiteHelper::footerBtn('vehAccident/view', ' value="เรียกดู" id="viewBtn"  '); !!}      
   {!! SiteHelper::footerBtn('vehAccident/delete', ' value="ลบ" id="deleteBtn" '); !!}
   <div style='clear: both'></div>
</div>


@endsection



