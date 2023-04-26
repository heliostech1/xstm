@extends('layouts.app')

@include('userManual.manualItemPopup')

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
        data["itemType"] = inputData["itemType"];
        data["itemName"] = inputData["itemName"];

        data["file"] = inputData["file"];
        data["fileLink"] = ImageFileUtil.getImageLink( inputData["file"] );
        
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';              

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
            row["fileLink"] = ImageFileUtil.getImageLink( row["file"] );              
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
        
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "itemType", data['itemType']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "itemName", data['itemName']);
        DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", rowId, "file", data['file']);
        
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "itemType", data['itemType']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "itemName", data['itemName']);
        DTHelper.updateHtmlCellByKey(ResultTableHelper.table, "rowId", rowId, "fileLink", ImageFileUtil.getImageLink( data["file"]) );        
        
 
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
            var itemType = $(this).find('td:eq('+ ResultTableHelper.getColNumber("itemType") + ')');            
            var idValue = (idTd.length > 0)? idTd.html(): "";
           // console.log(itemType);
           
            var isTopic = (itemType.length > 0 && itemType.html() == "กลุ่ม")? true: false;
            
            if (isTopic) {
                order = 1;
                $(this).css("background-color", "#cfe5ff"); //  $(this).css("font-weight", "bold");
            }
            else {
                $(this).css("background-color", "#fff");
            }
            
            
            if (seqTd.length > 0 ) {
                if (!isTopic) {
                    seqTd.html(order);
                    DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", idValue, "counter", order);
                    order++;
                }
                else {
                    seqTd.html("");
                    DTHelper.updateDataByKey(ResultTableHelper.table, "rowId", idValue, "counter", "");
                }

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
                              { "mData": "itemType"  }, 
                              { "mData": "itemName"}, 
                              { "mData": "fileLink"},                                     
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
        manualItemPopup_openPopupForAdd();
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
        manualItemPopup_openPopupForEdit(key, data);
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
    
    
          
    @yield('manualItemPopupJs')    

    manualItemPopupSubmitCallback = function(rowId, data) {
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

<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


 <?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display: none"></div>



<form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >


 {{ csrf_field() }}

 
<input type='hidden' name='mongoId' value='any'  />
<input type='hidden' name='valueDatas'  />
<input type='hidden' name='fileDatas'  />

        
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
            <th  width='100'>ประเภท</th>  
            <th  width='300'>ชื่อกลุ่ม/ชื่อรายการ</th> 
            <th  width='300'>คู่มือ</th>                
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
    
    <?php if ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง"  id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>

@yield('manualItemPopupHtml')


@endsection



