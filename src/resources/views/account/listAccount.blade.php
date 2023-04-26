@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('accountListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[1,'asc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','accountId')," 
                 ?>
                "sAjaxSource": "./getDataTable",

                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },                              
                              { "mData": "accountId" },
                              { "mData": "description" },
                              { "mData": "contactName" },
                              { "mData": "contactPhone" },
                              { "mData": "contactEmail" },
                              { "mData": "active" }
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

    DTHelper.applySelectable(oTable, "accountListTable");
    
    $('#addBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['accountId']: "";
        submitPageData("./add", oTable, id);        
    } );

    $('#editBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./edit", oTable, data['accountId']);
        }
    } );

    $('#viewBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./view", oTable, data['accountId']);
        }
    } );

    $('#deleteBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        if (AppUtil.checkEmpty(data)) {
             if (confirm("คูณต้องการลบบัญชี  '"+data['accountId']+"' ?")) {
                 submitPageData("./delete", oTable, data['accountId']);
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

<h4 >ค้นหา</h4>
<fieldset class='sectionFieldset'>
    

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">
        <tr>
            <td>
                <span class="formLabel">รหัส:</span>
                <input class="textInput" type="text" style="width:250px" id="<?php echo $fieldNames['accountId']?>" value='<?php echo $fieldDatas['accountId']?>'>                
                                 
                &nbsp;&nbsp;<input type="button" class='formButton' value="ค้นหา" id="searchBtn" />
                &nbsp;<input type="button" class='formButton' value="ล้าง" id="clearCriteriaBtn" />  
                
            </td>
        </tr>                
    </table>

</fieldset>
<div style='height: 10px; font-size: 0px'></div>

<table id='accountListTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th width='15'>&nbsp;</th>           
            <th width='100'>บัญชี</th>
            <th width='180'>รายละเอียด</th>
            <th width='120'>ชื่อที่ติดต่อได้</th>
            <th width='120'>โทรศัพท์ที่ติดต่อได้</th>
            <th width='120'>อีเมล์ที่ติดต่อได้</th>
            <th width='80'>สถานะ</th>
        </tr>
    </thead>
    <tbody>
    </tbody>    
</table>
<br>

<div class='footerBtnCont'>
   <?php if ($isSysadminAccount): ?>
   {!! SiteHelper::footerBtn('account/add', ' value="เพิ่ม" id="addBtn"  ') !!}
   <?php endif; ?>
   {!! SiteHelper::footerBtn('account/edit', ' value="แก้ไข" id="editBtn" '); !!}
   {!! SiteHelper::footerBtn('account/view', ' value="เรียกดู" id="viewBtn"  '); !!}
   
   <?php if ($isSysadminAccount): ?>   
   {!! SiteHelper::footerBtnRight('account/delete', ' value="ลบ" id="deleteBtn" '); !!}
   <div style='clear: both'></div>
   <?php endif; ?>   
</div>


@endsection



