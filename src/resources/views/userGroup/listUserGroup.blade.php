@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('mainTable', '<?php echo $sitePageId ?>',
            {
                //"aaSorting": [[1,'asc']],
                <?php if (!empty($tableDisplayStart)) echo "'iDisplayStart': $tableDisplayStart," ?>
                <?php if (!empty($tableDisplayLength)) echo "'iDisplayLength': $tableDisplayLength," ?>     
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','userGroupId')," 
                 ?>
                "sAjaxSource": "./getDataTable",
                "bSort" : false,
                "sSorting": [],                
                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },                              
                              { "mData": "userGroupId" },
                              { "mData": "name" },
                              { "mData": "color" },                              
                              { "mData": "description" },
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

    DTHelper.applySelectable(oTable, "mainTable");
    
    $('#addBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['userGroupId']: "";
        submitPageData("./add", oTable, id);        
    } );

    $('#editBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./edit", oTable, data['userGroupId']);
        }
    } );

    $('#viewBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./view", oTable, data['userGroupId']);
        }
    } );

    $('#deleteBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        if (AppUtil.checkEmpty(data)) {
             if (confirm("คูณต้องการลบ  '"+data['userGroupId']+"' ?")) {
                 submitPageData("./delete", oTable, data['userGroupId']);
             }
        }
    } );


    $('#editPagePermissionBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['userGroupId']: "";
        submitPageData("./editPagePermission", oTable, id);                  
    } );       
    
    $('#viewPagePermissionBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['userGroupId']: "";
        submitPageData("./viewPagePermission", oTable, id);                
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
<fieldset class='sectionFieldset' style='margin: 0 10px'>
    

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id="searchForm">
        <tr>
            <td>
                <span class="formLabel">รหัส:</span>
                <input class="textInput" type="text" style="width:250px" id="<?php echo $fieldNames['userGroupId']?>" value='<?php echo $fieldDatas['userGroupId']?>'>                
                                 
                &nbsp;&nbsp;<input type="button" class='formButton' value="ค้นหา" id="searchBtn" />
                &nbsp;<input type="button" class='formButton' value="ล้าง" id="clearCriteriaBtn" />  
                
            </td>
        </tr>                
    </table>

</fieldset>
<div style='height: 10px; font-size: 0px'></div>

<table id='mainTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th width='15'>&nbsp;</th>           
            <th width='120'>รหัส</th>
            <th width='200'>ชื่อกลุ่มผู้ใช้</th>
            <th width='150'>สี</th>
            <th width='400'>รายละเอียด</th>            
        </tr>
    </thead>
    <tbody>
    </tbody>    
</table>


<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('userGroup/add', ' value="เพิ่ม" id="addBtn"  ') !!}
   {!! SiteHelper::footerBtn('userGroup/edit', ' value="แก้ไข" id="editBtn" '); !!}

   {!! SiteHelper::footerBtn('userGroup/editPagePermission', ' value="ตั้งค่าสิทธิ์การใช้หน้าต่างๆ" id="editPagePermissionBtn" '); !!}
   {!! SiteHelper::footerBtn('userGroup/viewPagePermission', ' value="เรียกดูสิทธิ์การใช้หน้าต่างๆ" id="viewPagePermissionBtn"  '); !!}
  
   {!! SiteHelper::footerBtn('userGroup/deleteUserGroup', ' value="ลบ" id="deleteBtn" '); !!}
   <div style='clear: both'></div>
</div>


@endsection



