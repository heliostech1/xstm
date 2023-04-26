@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
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
                              { "mData": "import_date" }, 
                              { "mData": "import_type" },    
                              { "mData": "shop_id" },
                              { "mData": "accountId" },   
                              { "mData": "item_id" },           
                              { "mData": "item_status" },   
                              { "mData": "item_import_type" },                              
                              { "mData": "item_update_time" },   

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
    $("#<?=$fieldNames['to_date']?>").datepicker();
    
    $('#searchBtn').click( function() {
        oTable.fnPageChange('first');
    } );

    $('#clearCriteriaBtn').click(function() { 
        FormHelper.clearValue('searchForm');
        
        $("#<?php echo $fieldNames['date']?>").val("<?php echo $default_date ?>");  
      
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

<div id="pageTitle"><?php echo $sitePageName?></div>
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


<fieldset class='sectionFieldset' style='margin: 0 10px'>
    <legend ><a href='javascript:void(0)' id='filterTitleLink' class='filterTitleLink' >ค้นหา &#9660</a></legend>

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">
        <tr>
            <td>
                      
                <span class="formLabel">วันที่นำเข้า:</span>
                <input class="textInput" type="text" style="width:80px" id="<?php echo $fieldNames['date']?>" value='<?php echo $fieldDatas['date']?>' autocomplete="off" >
                -
                <input class="textInput" type="text" style="width:80px"  id="<?php echo $fieldNames['to_date']?>" value='<?php echo $fieldDatas['to_date']?>' autocomplete="off" >
                &nbsp;

                
                &nbsp;&nbsp;<input type="button" class='formButton' value="ค้นหา" id="searchBtn" />
                &nbsp;<input type="button" class='formButton' value="ล้าง" id="clearCriteriaBtn" />  
                
            </td>
        </tr>                
    </table>

</fieldset>


<div style='height: 10px; font-size: 0px'></div>
<div class='customTableStyle' >
<table id='resultListTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th  width='15'>&nbsp;</th>     
            <th  width='1'></th>
            <th  width='150'>เวลาที่นำเข้า</th>                   
            <th  width='150'>ประเภทการนำเข้า</th>  
     
            <th  width='100'>ร้านค้า</th>   
            <th  width='100'>บัญชี</th>  
            
            <th  width='100'>รหัสข้อมูล</th>   
            <th  width='100'>สถานะข้อมูล</th>       
            <th  width='150'>นำเข้าโดย</th>  
            <th  width='120'>เวลาข้อมูลอัปเดตล่าสุด</th>
            
                           
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>
</div>
<br>



@endsection



