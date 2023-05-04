@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {

	/* Init the table */
    var oTable = DTHelper.createPagingDatatable('resultListTable', '<?php echo $sitePageId ?>',
            {
                "aaSorting": [[3,'asc']],
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
                              { "mData": "province" },
                              
                              { "mData": "odometer" },
                              { "mData": "workCompany" , "bSortable": false},
                              { "mData": "vCareType" , "bSortable": false},
                              { "mData": "containerType" , "bSortable": false},   
                              { "mData": "monitorPlan" , "bSortable": false},  
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
        $("#<?php echo $fieldNames['active']?>").val("<?php echo $defaultActive ?>");          
    }); 
	
    $( "#<?php echo $fieldNames['licensePlate']?>" ).autocomplete({
        source: <?php echo json_encode($licensePlateList) ?>,
        minLength:0
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
    
    $( "#<?php echo $fieldNames['regisDateFrom']?>" ).datepicker();
    $( "#<?php echo $fieldNames['regisDateTo']?>" ).datepicker();
    $( "#<?php echo $fieldNames['taxDueDateFrom']?>" ).datepicker();
    $( "#<?php echo $fieldNames['taxDueDateTo']?>" ).datepicker();
    $( "#<?php echo $fieldNames['gasExpDateFrom']?>" ).datepicker();
    $( "#<?php echo $fieldNames['gasExpDateTo']?>" ).datepicker();
    
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

<h4>ค้นหา</h4>
<fieldset class='sectionFieldset'>
    

        
        
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
                 
                <span class="formLabel">ยี่ห้อรถ:</span>
                <input class="textInput" type="text" style="width:120px" id="<?php echo $fieldNames['brand']?>" value='<?php echo $fieldDatas['brand']?>'>                
                 &nbsp;  

                <span class="formLabel">วันจดทะเบียน:</span>
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['regisDateFrom']?>" value='<?php echo $fieldDatas['regisDateFrom']?>' autocomplete="off" >                
                - 
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['regisDateTo']?>" value='<?php echo $fieldDatas['regisDateTo']?>' autocomplete="off" >                
                &nbsp; 
                
                <span class="formLabel">ผู้ถือกรรมสิทธ์:</span>
                <input class="textInput" type="text" style="width:120px" id="<?php echo $fieldNames['ownerName']?>" value='<?php echo $fieldDatas['ownerName']?>'>                
                 &nbsp;  

            </td>
        </tr>                  
        <tr>
            <td>

                <span class="formLabel">ชนิดตู้สินค้า:</span>
                {!! SiteHelper::dropdown($fieldNames['containerType'], $goodsContainerOpt, $fieldDatas['containerType'], " id='".$fieldNames['containerType']."' class='textInput' style='width:120px' ") !!}
                 &nbsp;  
                 
                <span class="formLabel">ชนิดรถให้บริการ:</span>
                {!! SiteHelper::dropdown($fieldNames['vCareType'], $vCareTypeOpt, $fieldDatas['vCareType'], " id='".$fieldNames['vCareType']."' class='textInput' style='width:120px' ") !!}
                 &nbsp; 
                 
                <span class="formLabel">ชนิดน้ำมันเชื้อเพลิง:</span>
                {!! SiteHelper::dropdown($fieldNames['oilType'], $fuelOilOpt, $fieldDatas['oilType'], " id='".$fieldNames['oilType']."' class='textInput' style='width:120px' ") !!}
                 &nbsp;  
                
                <span class="formLabel">ชนิดแก๊สเชื้อเพลิง:</span>
                {!! SiteHelper::dropdown($fieldNames['gasType'], $fuelGasOpt, $fieldDatas['gasType'], " id='".$fieldNames['gasType']."' class='textInput' style='width:120px' ") !!}
  
                
            </td>
        </tr>     
        <tr>
            <td>
               
        
                
                <span class="formLabel">ชื่อผู้ให้บริการ:</span>
                {!! SiteHelper::dropdown($fieldNames['vehicleCare'], $vehicleCareOpt, $fieldDatas['vehicleCare'], " id='".$fieldNames['vehicleCare']."' class='textInput' style='width:120px' ") !!}
                &nbsp;  
                
                <span class="formLabel">เลขตัวถังรถ:</span>
                <input class="textInput" type="text" style="width:120px" id="<?php echo $fieldNames['bodyNumber']?>" value='<?php echo $fieldDatas['bodyNumber']?>'>                
                 &nbsp;  
                 
                <span class="formLabel">เลขเครื่องยนต์:</span>
                <input class="textInput" type="text" style="width:120px" id="<?php echo $fieldNames['engineNumber']?>" value='<?php echo $fieldDatas['engineNumber']?>'>                
                 &nbsp;  
                 
                <span class="formLabel">ภาษีรถ:</span> <!-- วันครบกำหนดเสียภาษี -->
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['taxDueDateFrom']?>" value='<?php echo $fieldDatas['taxDueDateFrom']?>' autocomplete="off" >                
                - 
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['taxDueDateTo']?>" value='<?php echo $fieldDatas['taxDueDateTo']?>' autocomplete="off" >                
                &nbsp; 
                
            </td>
        </tr>     
        <tr>
            <td>
                               
                <span class="formLabel">วันหมดอายุถังแก๊ส:</span>
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['gasExpDateFrom']?>" value='<?php echo $fieldDatas['gasExpDateFrom']?>' autocomplete="off" >                
                - 
                <input class="textInput" type="text" style="width:90px" id="<?php echo $fieldNames['gasExpDateTo']?>" value='<?php echo $fieldDatas['gasExpDateTo']?>' autocomplete="off" >                
                &nbsp; 
                
                
                <span class="formLabel">สถานะ:</span>
                {!! SiteHelper::dropdown($fieldNames['active'], $activeOpt, $fieldDatas['active'], " id='".$fieldNames['active']."' class='textInput' style='width:120px' ") !!}

                
                
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
            <th  width='100'>รหัสรถ</th>         
            <th  width='100'>ทะเบียนรถ</th>   
            <th  width='100'>จังหวัด</th>
            
            <th  width='100'>เลขไมล์</th>
            <th  width='100'>สังกัด</th>
            <th  width='100'>ชนิดรถให้บริการ</th>
            <th  width='100'>ชนิดตู้สินค้า</th>
            <th  width='100'>แผนซ่อมบำรุง</th>
            <th  width='100'>สถานะ</th>
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>

<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('vehicle/add', ' value="เพิ่ม" id="addBtn"  ') !!}
   {!! SiteHelper::footerBtn('vehicle/edit', ' value="แก้ไข" id="editBtn" '); !!}
   {!! SiteHelper::footerBtn('vehicle/view', ' value="เรียกดู" id="viewBtn"  '); !!}      
   {!! SiteHelper::footerBtn('vehicle/delete', ' value="ลบ" id="deleteBtn" '); !!}
   <div style='clear: both'></div>
</div>


@endsection



