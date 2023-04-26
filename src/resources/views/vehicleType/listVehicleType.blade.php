@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[2,'asc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','mongoId')," 
                 ?>
                "sAjaxSource": "./getDataTable",

                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "mongoId", "sClass": "forceHidden"},  
                         //     { "mData": "createdAt" },                               
                              { "mData": "name" },
                              { "mData": "active" },
                              { "mData": "vcDailyCheckProfileId" },
                              { "mData": "vcLiquidProfileId" }                              
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

    $("#<?=$fieldNames['date']?>").datepicker();
    $("#<?=$fieldNames['toDate']?>").datepicker();
        
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
             if (confirm("คูณต้องการลบข้อมูล '"+data['name']+"' ?")) {
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
<fieldset class='sectionFieldset'>
    

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">
        <tr>
            <td>
<!--
                <span class="formLabel">วันที่สร้าง:</span>
                <input class="textInput" type="text" style="width:80px" id="<?php echo $fieldNames['date']?>" value='<?php echo $fieldDatas['date']?>'>
                -
                <input class="textInput" type="text" style="width:80px"  id="<?php echo $fieldNames['toDate']?>" value='<?php echo $fieldDatas['toDate']?>'>
                &nbsp;
           -->  

                <span class="formLabel">ชื่อ:</span>
                <input class="textInput" type="text" style="width:250px" id="<?php echo $fieldNames['name']?>" value='<?php echo $fieldDatas['name']?>'>                
                 &nbsp;                          
             
                 
                <span class="formLabel">สถานะ:</span>
                {!! SiteHelper::dropdown($fieldNames['active'], $activeOpt, $fieldDatas['active'], " id='".$fieldNames['active']."' class='textInput' style='width:150px' ") !!}
                
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
            <th  width='1'>รหัส(ซ่อน)</th>  
       
            <th  width='200'>ชื่อ</th>           
            <th  width='100'>สถานะ</th>
            
            <th  width='100'>แผนตรวจเช็คประจำวัน</th>
            <th  width='100'>แผนเช็คของเหลว</th>            
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>
<br>

<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('vehicleType/add', ' value="เพิ่ม" id="addBtn"  ') !!}
   {!! SiteHelper::footerBtn('vehicleType/edit', ' value="แก้ไข" id="editBtn" '); !!}
   {!! SiteHelper::footerBtn('vehicleType/view', ' value="เรียกดู" id="viewBtn"  '); !!}
   {!! SiteHelper::footerBtn('vehicleType/delete', ' value="ลบ" id="deleteBtn" '); !!}
   <div style='clear: both'></div>
</div>


@endsection



