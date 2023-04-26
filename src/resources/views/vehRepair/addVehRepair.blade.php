@extends('layouts.app')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

var TopicTableHelper = {

    autoRunId: 9991,
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },
    
    setTable: function(table) {
        this.table = table;
    },
          
    addData: function(datas) {
        for (var i=0; i< datas.length; i++ ) {
            var topicName = datas[i]['name'];
            
            if (!DTHelper.isExist(this.table, topicName, 'name' )) {
                this.table.fnAddData(
                        { 
                          "counterColumn":"", 
                          "name": topicName,                   
                          "delete": '<a class="delete" href="">ลบ</a>'
                        }
               );
                     
            }
                     
        }
        DTHelper.updateOrderColValue(this.table, 0);
    },
    
    addDataFromServer : function(datas) {
        DTHelper.clearDatas(this.table);     
        datas = AppUtil.stringToArray(datas);
      
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = {};
            row['counterColumn'] = "";  
            row['name'] =  datas[i] ;      
            row['delete'] = '<a class="delete" href="">ลบ</a>';
            output.push(row);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 0);
    },
    
    getDataForSubmit: function() {
        var datas = this.table.fnGetData();
        var output = [];
        
        for ( var i = 0; i < datas.length; i++) {
            var name = datas[i]['name'];
            if (AppUtil.isNotEmpty(name)) {
                output.push( name );
            }           
        }

        return AppUtil.arrayToString(output);
    },
    
    debug: function() { }
 };



$(document).ready(function() {

    
    window.fixItemTable = new MySimpleDataListTable('fixItemTable', '<?php echo $pageMode ?>');

    window.topicTable = $('#topicTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "sDom": 'lrtip',
                "bPaginate": false,
                "bFilter": true,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
         
                "aoColumns": [ 
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "name" },                               
                              { "mData": "delete" },
                ],          
            
            }
        );
    
    TopicTableHelper.setTable(topicTable);
    TopicTableHelper.addDataFromServer(  "{{ $monitorTopicDatas }}" );     
    
    $("#popupTopic").dialog({
        width:600,
        height:400,
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                var datas =  DTHelper.getSelections(popupTopicTable);                 
                TopicTableHelper.addData(datas);                 
                $("#popupTopic").dialog('close');
                 
            },                 
            "ยกเลิก": function() {
                $("#popupTopic").dialog('close');
            }
        }      
    });
    
    
    window.popupTopicTable = $('#popupTopicTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "sDom": 'lrtip',
                "bPaginate": false,
                "bFilter": true,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
         
                "aoColumns": [ 
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "checkboxColumn", "sClass": "cellCheckbox", "bSortable": false },                               
                              { "mData": "name" },                               
                ],          
            
            }
     );
    
    popupTopicTable.fnAddData( <?php echo $allMonitorTopicDatas  ?>);
    DTHelper.applyCheckboxStyleSelectable(popupTopicTable, "popupTopicTable");
    


    $('#saveBtn').click(function() { 
        setTimeout(function() {
            document.mainForm.fixItemDatas.value = fixItemTable.getDataForSubmit();            
            document.mainForm.fileDatas.value = uploader.getDataStringForSubmit();
            document.mainForm.monitorTopicDatas.value = TopicTableHelper.getDataForSubmit();              
            document.mainForm.submit();
        }, 300); 
    }); 
  
    window.uploader = new BatchUploader( {
        uploaderName: "uploader",
        containerId: "uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    
    fixItemTable.addDataFromServer( "<?php echo $fixItemDatas ?>" );     
    uploader.addDataStringFromServer(  "{{ $fileDatas }}" ); 

    $('#fixItemTableAddLink').click(function(e) {
        e.preventDefault();
        fixItemTable.addData();
    });

    $('#topicTableAddLink').click(function(e) {
        e.preventDefault();        
        DTHelper.clearSelections(popupTopicTable);
        $("#popupTopic").dialog('open');          
    });
    
    $('#topicTable').on('click', 'a.delete', function (e) {
        e.preventDefault();

        var key = DTHelper.getKeyByCell(this, 1);
        DTHelper.deleteRowByKey(topicTable, "name", key);
        DTHelper.updateOrderColValue(topicTable, 0);
    } ); 


    $('#fixStartDateInput').datepicker();    
    $('#fixEndDateInput').datepicker();    
    
    $('#vehicleIdInput').select2();
    
    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    
     $('#fixItemTableAddLink').hide();
     $('#topicTableAddLink').hide();
      
    //DTHelper.setColumnVisible(fixItemTable,"delete", false, true);         
    DTHelper.setColumnVisible(topicTable,"delete", false, true);      
 
   
    
    
    <?php endif; ?>
        
        
} );


</script>

@endsection

@section('content')


<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php endif; ?>

 {{ csrf_field() }}
        
<input type='hidden' name='mongoId' value='<?php echo $mongoId?>' />
<input type='hidden' name='fixItemDatas'  />
<input type='hidden' name='fileDatas'  />
<input type='hidden' name='monitorTopicDatas'  />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>


    
                              

<?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
        <tr>
            <td class="formLabel" style='width:200px'>ทะเบียนรถ:</td>
            <td>{!! SiteHelper::dropdown('vehicleId', $vehicleOpt, $vehicleId, "class='textInput' id='vehicleIdInput' style='width:400px' ") !!}
            </td>
        </tr>  
<?php endif; ?>   
        
<?php if ($pageMode == 'view'): ?>  
        <tr>
             <td class="formLabel"  style='width:200px' >รหัสรถ:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $vehicleId }}' name="vehicleId"></td>
        </tr>         
        <tr>
            <td class="formLabel">ทะเบียนรถ:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $licensePlate }}' name="$icensePlate"></td>          
        </tr>             
        <tr>
             <td class="formLabel"  >ครั้งที่:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $times }}' name="times"></td>
        </tr>   
<?php endif; ?>  
         
        <tr>
             <td class="formLabel">วันที่ซ่อมบำรุง:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $fixStartDate }}' name="fixStartDate" id="fixStartDateInput" autocomplete="off" ></td>
        </tr> 
        <tr>
             <td class="formLabel">วันที่ซ่อมเสร็จ:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $fixEndDate }}' name="fixEndDate" id="fixEndDateInput" autocomplete="off" ></td>
        </tr>   
        <tr>
             <td class="formLabel">หัวข้อการซ่อมบำรุง:</td>
             <td>
                <div style='padding:0px 20px 1px 0px'>
                     <div style='float: left; width: 400px; text-align: right;' >
                        <a id="topicTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                     </div>
                     <div style='clear: both'></div>
                </div>

                <div class='customTableStyle' > 
                <table id='topicTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:400px' >
                    <thead>
                    <tr class='nodrop' >
                        <th  width='20' ></th>     
                        <th  width='350'>ชื่อ</th>
                        <th  width='70'>ลบ</th>
                    </tr
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>

             </td>
        </tr>         

        
        <tr>
             <td class="formLabel">รายการซ่อม:</td>
             <td>
                <div style='padding:0px 20px 1px 0px'>
                     <div style='float: left; width: 400px; text-align: right;' >
                        <a id="fixItemTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                     </div>
                     <div style='clear: both'></div>
                </div>

                <div class='customTableStyle' > 
                <table id='fixItemTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:400px' >
                    <thead>
                    <tr class='nodrop' >
                        <th  width='20' >&nbsp;</th>
                        <th  width='20' ></th>     
                        <th  width='350'>ข้อมูล</th>
                        <th  width='70'>ลบ</th>
                    </tr
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>

             </td>
        </tr> 
        <tr>
             <td class="formLabel">เลขไมล์:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $odometer }}' name="odometer"></td>
        </tr> 
        
        <tr>
             <td class="formLabel">ค่าใช้จ่ายในการซ่อม:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $cost }}' name="cost"></td>
        </tr> 
        <tr>
             <td class="formLabel">การรับประกัน:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $guaranty }}' name="guaranty" ></td>
        </tr>        
        <tr>
             <td class="formLabel">ชนิดการซ่อม:</td>
            <td>{!! SiteHelper::dropdown('repairGroup', $repairGroupOpt, $repairGroup, "class='textInput' id='repairGroupInput' style='width:400px' ") !!}
            </td>
        </tr> 
        <tr>
            <td class="formLabel">แนบเอกสาร:</td>
            <td><div id='uploaderCont' style='padding:0px'></div></td>            
        </tr> 
                        
    </tbody>
</table>


</form>


  

<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'add'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
    
    <?php elseif ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>



<div id="popupTopic" style='padding:2px 0px; display:none;' title="หัวข้อการซ่อมบำรุง"> <!--  -->

<div style='height: 10px; font-size: 0px'></div>

<table id='popupTopicTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th  width='15'>&nbsp;</th>  
            <th  width='15'>&nbsp;</th> 
            <th  width='200'>ชื่อ</th>   
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>
    
<br>


</div> 


@endsection




   


