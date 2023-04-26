@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {
   

    var oTable = $('#resultListTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "bPaginate": false,
                "bFilter": false,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo":false,
                "bSort": false,
                "bAutoWidth": false,
                
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','setttingId')," 
                 ?>
                        
                        
                "aoColumns": [
                              { "mData": "columnCounter", "sClass": "cellCounter", "bSortable": false },             
                              { "mData": "settingId", "sClass": "forceHidden"},   
                              { "mData": "category" },
                              { "mData": "name" },                              
                              { "mData": "value" },                              
                              { "mData": "unit" },                                            
                ],

            }
     );
        
    DTHelper.applySelectable(oTable, "resultListTable");
    oTable.fnAddData(<?php echo $tableDatas ?>);


    $('#addBtn').click( function() {
        var data = DTHelper.getSelected(oTable);
        id = (data)? data['settingId']: "";
        submitPageData("./add", oTable, id);        
    } );

    $('#editBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./edit", oTable, data['settingId']);
        }
    } );

    $('#viewBtn').click( function() {
        if (DTHelper.checkSingleSelected(oTable)) {
            var data = DTHelper.getSelected(oTable);
            submitPageData("./view", oTable, data['settingId']);
        }
    } );

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
            <th  width='1'>รหัส(ซ่อน)</th>  
            <th  width='150'>ประเภท</th>
            <th  width='250'>ชื่อ</th>            
            <th  width='250'>ค่า</th>            
            <th  width='150'>หน่วย</th>
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>
</div>

<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('appSetting/edit', ' value="แก้ไข" id="editBtn" '); !!}
   {!! SiteHelper::footerBtn('appSetting/view', ' value="เรียกดู" id="viewBtn"  '); !!}

   <div style='clear: both'></div>
</div>


@endsection



