@extends('layouts.app')

@include('monitorPlan.planItemPopup')

@section('header')

<script type='text/javascript'>


var ResultTableHelper = {
    autoRunId: 9991,

    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table) {
        this.table = table;
     },
     
     
    addData: function(inputData) {
        var data = {};
        data["rowId"] = this.getAutoRunId();   
        data["reorder"] = "";    
        data["counter"] = "";   
        
        data["monitorTopic"] = inputData["monitorTopic"];
        data["itemName"] = inputData["itemName"];
        data["itemCode"] = inputData["itemCode"];    
        data["dataType"] = inputData["dataType"];
        data["dataTypeDesc"] = AppUtil.getDropdownText( 'planItemPopup_dataType' , inputData["dataType"] );
        data["warnAmount"] = inputData["warnAmount"];
        data["alertAmount"] = inputData["alertAmount"];    
        
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';              

        if ( DTHelper.isExist(this.table,  inputData["monitorTopic"], 'monitorTopic' ) ) {
            alert("ไม่สามารถเพิ่มหัวข้อการซ่อมบำรุงซ้ำ");
            return;
        }

        this.table.fnAddData(data);
        this.table.tableDnDUpdate();
        ResultTableHelper.updateSeqColValue();
    },
    
    addDataFromServer : function(datas) {
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = datas[i];
            row["rowId"] = this.getAutoRunId();   
            row["dataTypeDesc"] =  AppUtil.getDropdownText( 'planItemPopup_dataType' , row["dataType"] );            
            row["reorder"] = "";    
            row["counter"] = "";    
            row["edit"] = '<a class="edit" href="">แก้ไข</a>';  
            row["delete"] = '<a class="delete" href="">ลบ</a>';      
            output.push(row);
        }
           
        this.table.fnAddData(output);
        
        this.table.tableDnDUpdate();
        ResultTableHelper.updateSeqColValue();
    },

    deleteData: function(rowEl) {
        var rowId = DTHelper.getKeyByCell(rowEl, ResultTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        ResultTableHelper.updateSeqColValue();
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;

        
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "monitorTopic", data['monitorTopic']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "itemName", data['itemName']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "itemCode", data['itemCode']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "dataType", data['dataType']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "warnAmount", data['warnAmount']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "alertAmount", data['alertAmount']);
        
       
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "monitorTopic", data['monitorTopic']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "itemName", data['itemName']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "itemCode", data['itemCode']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "dataTypeDesc", AppUtil.getDropdownText( 'planItemPopup_dataType' , data["dataType"]) );        
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "warnAmount", data['warnAmount']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "alertAmount", data['alertAmount']);
        
        ResultTableHelper.updateSeqColValue();
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, ResultTableHelper.getColNumber("rowId"));
 
        var output = [];
        for ( var i = 0; i < idList.length; i++) {
            var key = idList[i];
            var row = DTHelper.getDataByKey(this.table, "rowId", key);       
            output.push(row);
        }
        
        return output;
    },

    updateSeqColValue: function() {
        if ( DTHelper.isDataEmpty(this.table) ) return;
        
        var order = 1;
        
        $(this.table).find('tbody').find('tr').each(function() {
            var idTd = $(this).find('td:eq('+ ResultTableHelper.getColNumber("rowId") + ')');
            var seqTd = $(this).find('td:eq('+ ResultTableHelper.getColNumber("counter") + ')');         
            var idValue = (idTd.length > 0)? idTd.html(): "";
            
            if (seqTd.length > 0 ) {
                seqTd.html(order);
                DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", idValue, "counter", order);
                order++;
            }           
        }); 
    },
    
    getSelected: function() {
       var data = DTHelper.getSelectedDataByKey(this.table, "rowId", ResultTableHelper.getColNumber("rowId"));
       return data;
    },
    

    getColNumber: function(name) {
        var result = DTHelper.getHtmlColNumberByColName(this.table, name);        
        return result-1;
    },
    
    
    debug: function() {

    }
};



$(document).ready(function() {


    window.resultTable = $('#resultTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "bPaginate": false,
                "bFilter": false,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo":false,
                //"aaSorting": [],
                "bSort": false,
                "bAutoWidth": false,

                "aoColumns": [
                              { "mData": "rowId" , "sClass": "forceHidden"},   // , "sClass": "forceHidden"
                              { "mData": "reorder"  , "sClass": "cellCounter cellDragable", 
                                "mRender": function ( data, type, full ) {
                                   return '&nbsp;&#8693;';                                  
                                }
                              },     
                              { "mData": "counter", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "monitorTopic"}, 
                              { "mData": "itemName"},
                              
                              { "mData": "itemCode"  },         
                              { "mData": "dataTypeDesc"  },
                              { "mData": "warnAmount"  },
                              { "mData": "alertAmount"  },                           
                              { "mData": "edit"},     
                              
                              { "mData": "delete"},                                   
                               
                ],
                "fnRowCallback": function( nRow, aData, iDisplayIndex ) {


                }   
            }
     );
 

    ResultTableHelper.setTable(window.resultTable);
    ResultTableHelper.addDataFromServer(<?=$valueDatas?>);
    

    $('#resultTable').tableDnD({
        onDragStart: function(table, row, e) {
        },
        onDragClass: "row_dragged",
        onDrop: function(table, row, e) {
            DTHelper.synchronizeOrder(resultTable, "rowId", 0);
            ResultTableHelper.updateSeqColValue();
        },
        dragHandle: "cellDragable"
    });
    
    DTHelper.applySelectable(resultTable, "resultTable");
    
    $('#addNewItemLink').click(function(e) {
        e.preventDefault();
        planItemPopup_openPopupForAdd();
       // stepPopup_openPopupForAdd();
    });
    
    $('#resultTable').on('click', 'a.delete', function (e) {
        e.preventDefault();        
       // if (confirm("Delete item?")) {
            ResultTableHelper.deleteData(this);
           
       // }
    } ); 
    
    $('#resultTable').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, ResultTableHelper.getColNumber("rowId"));
        var data = DTHelper.getDataByKey(resultTable, "rowId", key);
        planItemPopup_openPopupForEdit(key, data);
    } ); 

    
    <?php if ($pageMode == 'edit'): ?>

   // FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    DTHelper.setColumnVisible(resultTable,"reorder", false, true);   
    DTHelper.setColumnVisible(resultTable,"edit", false, true);      
    DTHelper.setColumnVisible(resultTable,"delete", false, true);    
    $('#addNewItemCont').hide();
    
    <?php endif; ?>
        

    $('#saveBtn').click(function() { 
        setTimeout(function() {
            document.mainForm.valueDatas.value = $.toJSON(ResultTableHelper.getDataForSubmit());
            // document.mainForm.fileDatas.value = $.toJSON(attachFileUploader.getDataForSubmit());                  
           // console.log(document.mainForm.valueDatas.value );
            document.mainForm.submit();
        }, 300);
    }); 
    
    
    window.attachFileUploader = new BatchUploader( {
        uploaderName: "attachFile",
        containerId: "attachFileContainer",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    
    attachFileUploader.addDataFromServer(<?php echo $fileDatas?>);
          
          
    @yield('planItemPopupJs')    

    
    planItemPopupSubmitCallback = function(rowId, data) {
       if (AppUtil.isEmpty(rowId)) {
           ResultTableHelper.addData(data);
       }
       else {
           ResultTableHelper.updateData(rowId, data);
       }
       
    }    
    
} );




</script>


@endsection

@section('content')

<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


 <?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display: none"></div>


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php endif; ?>

 {{ csrf_field() }}

 
<input type='hidden' name='mongoId' value='{{ $mongoId }}'  />
<input type='hidden' name='valueDatas'  />
<input type='hidden' name='fileDatas'  />

        
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

         
        <tr>
            <td class="formLabel">ชื่อ:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $name }}' name="name"></td>
        </tr>  
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  
        <tr>
            <td class="formLabel">สถานะ:</td>
            <td>{!! SiteHelper::dropdown('active', $activeOpt, $active, "class='textInput' style='width:400px' ") !!}
            </td>
        </tr>
            
<?php endif; ?>   
       
        
    </tbody>
</table>


        
        
<div style='height: 10px; font-size: 0px'></div>

<div style='padding:0px 20px 1px 20px'>

    
     <div style='float: right; width: 400px; text-align: right;' id='addNewItemCont'>
        <a id="addNewItemLink" href="javascript:void(0);" >เพิ่มข้อมูล</a>
        
     </div>
     <div style='clear: both'></div>
</div>


<div class='customTableStyle' >
<table id='resultTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr class='nodrop' >
            <th  width='10'>&nbsp;</th>  
            <th  width='20'>&nbsp;</th>  
            <th  width='20'>&nbsp;</th>  
            <th  width='200'>ห้วข้อการซ่อมบำรุง</th> 
            <th  width='200'>ชื่อ</th> 
            
            <th  width='120'>รหัส</th>       
            <th  width='150'>ชนิดข้อมูลที่ใช้ตรวจสอบ</th>     
            <th  width='150'>แจ้ง Warning ทุกๆ</th>  
            <th  width='150'>แจ้ง Alert ทุกๆ</th>  
            <th  width='80'>แก้ไข</th>   
            
            <th  width='80'>ลบ</th>      
        </tr>  
    
    </thead>
    <tbody>
    </tbody>    
</table>
</div>
<br>

</form>
        
        
<div class='footerBtnCont'>
    
    <?php if ($pageMode == 'add'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
    
    <?php elseif ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="ตกลง"  id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>

@yield('planItemPopupHtml')


@endsection



