@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {
    AppUtil.setFilterTitleToogleForm("filterTitleLink","searchForm");


            
            
    var oTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
              //  "aaSorting": [[3,'asc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     

                "sAjaxSource": "./getDataTable",
                "bSort" : false,
                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },     
                              { "mData": "shop_id" },    
                              { "mData": "accountId" }, 
                              { "mData": "check_date" },                                 
                              { "mData": "new_count" },   
                              { "mData": "update_count" },   
                              { "mData": "result" },   
                              { "mData": "last_item_create_time" },                                 
                              { "mData": "last_item_update_time" },   
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
                              

                 
                              
<div style='height: 10px; font-size: 0px'></div>
<div class='customTableStyle' >
<table id='resultListTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th  width='15'>&nbsp;</th>        
            <th  width='100'>ร้านค้า</th>
            <th  width='100'>บัญชี</th>
            <th  width='120'>เวลาที่ตรวจสอบ</th>            
            <th width='90' >จำนวนเพิ่มใหม่</th>            
            <th width='90' >จำนวนอัปเดต</th>  
            <th width='120' >ผลการนำเข้า</th>  
            <th width='120' >เวลาข้อมูลสร้างล่าสุด</th>               
            <th width='120' >เวลาข้อมูลอัปเดตล่าสุด</th>                
        </tr>     

    </thead>
    <tbody>
    </tbody>    
</table>
</div>
<br>




@endsection



