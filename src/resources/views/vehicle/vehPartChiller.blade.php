@section('partChillerHelper')


var BaseChillerTableHelper = function( ) {};

BaseChillerTableHelper.prototype = {
    autoRunId: 9991,
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table, tableId) {
        this.table = table;
        this.tableId = tableId;
     },
     
                        
    addData: function() {
        var rowId = this.getAutoRunId();    
        this.table.fnAddData(
                { 
                  "rowId": rowId ,
                  "counterColumn":"",
  
                  "order":"",
                  "orderInput": partChiller_getOrderInput(this.tableId, rowId),                    
                  "operateDate":"",
                  "operateDateInput": partChiller_getOperateDateInput(this.tableId, rowId), 
                  "expDate":"",
                  "expDateInput": partChiller_getExpDateInput(this.tableId, rowId),                   
                  "operateBy":"",
                  "operateByInput": partChiller_getOperateByInput(this.tableId, rowId),
                  "fileDatas":"",
                  "fileLink": "",
                  "edit": '<a class="edit" href="">แก้ไข</a>',
                  "delete": '<a class="delete" href="">ลบ</a>'
                }                
        );
       
        
        $("#"+this.tableId+"-operateDate-" + rowId).datepicker();
        $("#"+this.tableId+"-expDate-" + rowId).datepicker();
       
        DTHelper.updateOrderColValue(this.table, 1);
    },

    updateData: function(rowId, data) {
        //console.log(data);
       
        if (AppUtil.isEmpty(rowId) ) return ;        
        DTHelper.updateDataByKey(this.table, "rowId", rowId, "fileDatas", data['file'] );
        DTHelper.updateHtmlCellByKey(this.table, "rowId", rowId, "fileLink", ImageFileUtil.getImageLink(  data['file']  ) );        

    },
    
    addDataFromServer : function(datas) {
        DTHelper.clearDatas(this.table);     
  
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        for ( var i = 0; i < datas.length; i++) {
            var rowId = this.getAutoRunId();
            
            datas[i]['rowId'] = rowId;
            datas[i]['counterColumn'] = "";
            
            datas[i]['orderInput'] = partChiller_getOrderInput(this.tableId, rowId,  datas[i]['order']);
            datas[i]['operateDateInput'] = partChiller_getOperateDateInput(this.tableId, rowId,  datas[i]['operateDate']);
            datas[i]['expDateInput'] = partChiller_getExpDateInput(this.tableId, rowId,  datas[i]['expDate']);
            datas[i]['operateByInput'] = partChiller_getOperateByInput(this.tableId, rowId,  datas[i]['operateBy']);
            datas[i]['fileLink'] = ImageFileUtil.getImageLink( datas[i]['fileDatas']  );
            
            datas[i]['edit'] = '<a class="edit" href="">แก้ไข</a>';            
            datas[i]['delete'] = '<a class="delete" href="">ลบ</a>';
            
            this.table.fnAddData( datas[i]);
            $("#"+this.tableId+"-operateDate-" + rowId).datepicker();   
            $("#"+this.tableId+"-expDate-" + rowId).datepicker();               
        }
           

        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
                
        for ( var i = 0; i < datas.length; i++) {
            var rowId = datas[i]['rowId'];
             
            datas[i]['order'] = $("#"+this.tableId+"-order-"+rowId).val();
            datas[i]['orderInput'] = "";      
            datas[i]['operateDate'] = $("#"+this.tableId+"-operateDate-"+rowId).val();
            datas[i]['operateDateInput'] = "";                          
            datas[i]['expDate'] = $("#"+this.tableId+"-expDate-"+rowId).val();
            datas[i]['expDateInput'] = "";        
            datas[i]['operateBy'] = $("#"+this.tableId+"-operateBy-"+rowId).val();
            datas[i]['operateByInput'] = "";    

            datas[i]['delete'] = "";
        }
        return datas;
    },
    
    debug: function() {
    }
};


var ChillerExamTableHelper = new BaseChillerTableHelper();
var ChillerMapTableHelper = new BaseChillerTableHelper();


function partChiller_getOrderInput(tableId, rowId, val) {
        var inputId = tableId +"-order-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>    
        return "<input class='textInput' type='text' style='width:60px'  id='" + inputId + "' value='" + val +"' />";            
}    
    
function partChiller_getOperateDateInput(tableId, rowId, val) {
        var inputId = tableId +"-operateDate-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>           
        return "<input class='textInput' type='text' style='width:90px'  id='" + inputId + "' value='" + val +"' />";            
}    

function partChiller_getExpDateInput(tableId, rowId, val) {
        var inputId = tableId +"-expDate-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>           
        return "<input class='textInput' type='text' style='width:90px'  id='" + inputId + "' value='" + val +"' />";            
}   


function partChiller_getOperateByInput(tableId, rowId, val) {
        var inputId = tableId +"-operateBy-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>           
        return "<input class='textInput' type='text' style='width:170px'  id='" + inputId + "' value='" + val +"' />";            
}    

@endsection



@section('partChillerJs')

    window.partChiller_examTable  = $('#partChiller_examTable').dataTable( 
            {
                "oLanguage": DTHelper.thaiLang,
                "bPaginate": false,
                "bFilter": false,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo":false,
                "bSort": false,
                "bAutoWidth": false,
                "aoColumns": [    
                        { "mData": "rowId" ,"sClass": "forceHidden", "bSortable": false},                
                        { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false }, 
                        
                        { "mData": "orderInput" },                               
                        { "mData": "operateDateInput" },
                        { "mData": "expDateInput" },
                        { "mData": "operateByInput" },     
                        { "mData": "fileLink" },
                        
                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ]
	             
            }
    );
    
    window.partChiller_mapTable  = $('#partChiller_mapTable').dataTable( 
            {
                "oLanguage": DTHelper.thaiLang,
                "bPaginate": false,
                "bFilter": false,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo":false,
                "bSort": false,
                "bAutoWidth": false,
                "aoColumns": [    
                        { "mData": "rowId" ,"sClass": "forceHidden", "bSortable": false},                
                        { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false }, 
                        
                        { "mData": "orderInput" },                               
                        { "mData": "operateDateInput" },
                        { "mData": "expDateInput" },
                        { "mData": "operateByInput" },     
                        { "mData": "fileLink" },
                        
                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ]
	             
            }
    );
    
    $('#partChiller_examTableAddLink').click(function(e) {
        e.preventDefault();
        ChillerExamTableHelper.addData();
    });
    
    $('#partChiller_mapTableAddLink').click(function(e) {
        e.preventDefault();
        ChillerMapTableHelper.addData();
    });    


       
    $('#partChiller_examTable').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partChiller_examTable, "rowId", key);
        attachFilePopup_openPopupForEdit(key, data, function(rowId, data) { ChillerExamTableHelper.updateData(rowId, data) } );
    } ); 
    
    $('#partChiller_mapTable').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partChiller_mapTable, "rowId", key);
        attachFilePopup_openPopupForEdit(key, data, function(rowId, data) { ChillerMapTableHelper.updateData(rowId, data) } );
    } ); 
    

    $('#partChiller_examTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partChiller_examTable, "rowId", key);
        DTHelper.updateOrderColValue(partChiller_examTable, 1);
    } );
    $('#partChiller_mapTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partChiller_mapTable, "rowId", key);
        DTHelper.updateOrderColValue(partChiller_mapTable, 1);
    } ); 
    
    
    ChillerExamTableHelper.setTable(window.partChiller_examTable, 'partChiller_examTable');
    ChillerExamTableHelper.addDataFromServer(<?php echo json_encode($partChiller_examDatas) ?>);
    
    ChillerMapTableHelper.setTable(window.partChiller_mapTable, 'partChiller_mapTable');
    ChillerMapTableHelper.addDataFromServer(<?php echo json_encode($partChiller_mapDatas) ?>);
    
    <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  

       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partChiller_examTable,"delete", false, true); 
         DTHelper.setColumnVisible(partChiller_mapTable,"delete", false, true); 
         
         DTHelper.setColumnVisible(partChiller_examTable,"edit", false, true); 
         DTHelper.setColumnVisible(partChiller_mapTable,"edit", false, true); 
         
        $('#partChiller_examTableAddLink').hide();
        $('#partChiller_mapTableAddLink').hide();        
     <?php endif; ?>  

        
        
@endsection


@section('partChillerSubmit')
   document.mainForm.partChiller_examDatas.value = $.toJSON(ChillerExamTableHelper.getDataForSubmit());  
   document.mainForm.partChiller_mapDatas.value = $.toJSON(ChillerMapTableHelper.getDataForSubmit());     
@endsection



@section('partChillerHtml') 
            
<input type='hidden' name='partChiller_examDatas'  />
<input type='hidden' name='partChiller_mapDatas'  />


<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

            
            
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   

        <tr>
            <td class="formLabel" style='width:200px' >ยี่ห้อเครื่องทำความเย็น</td>
            <td style='text-align:left' ><input id=Two-oneone class="textInput" type="text" style="width:400px" value='{{ $partChiller_brand }}' name="partChiller_brand">
            </td>
        </tr>           
        <tr>
            <td class="formLabel">โมเดลเครื่องทำความเย็น</td>
            <td style='text-align:left'><input id=Two-oneone class="textInput" type="text" style="width:400px" value='{{ $partChiller_model }}' name="partChiller_model">
            </td>
        </tr>         
        <tr>
            <td class="formLabel">ชนิดสารทำความเย็น:</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partChiller_refrigerant", $refrigerantOpt, $partChiller_refrigerant, "  class='textInput' style='width:400px' id=Two-oneone  ") !!} </td> 
        </tr>   
        <tr>
            <td class="formLabel">ช่วงอุณหภูมิทำความเย็น:</td>
            <td style='text-align:left'><input id=Two-oneone class="textInput" type="text" style="width:400px" value='{{ $partChiller_temperature }}' name="partChiller_temperature">
            </td>
        </tr>          

        <tr>
            <td class="formLabel" style="padding-top: 10px">ข้อมูลการสอบเทียบ <br/>เครื่องทำความเย็น:</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 900px; text-align: right;' >
                         <a id="partChiller_examTableAddLink" href="javascript:void(0);" >เพิ่ม</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partChiller_examTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:900px' >
                     <thead>
                     <tr class='nodrop' >
                         <th  width='20' >&nbsp;</th>
                         <th  width='20' ></th>     
                         <th  width='90'>ครั้งที่</th>
                         <th  width='120'>วันที่สอบเทียบ</th>
                         <th  width='120'>วันหมดอายุ</th>
                         <th  width='180'>ชื่อผู้สอบเทียบ</th>
                         <th  width='90'>เอกสาร</th>  
                         <th  width='70'>แก้ไข</th>
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
            <td class="formLabel" style="padding-top: 10px">ข้อมูลการทำ mapping <br/>เครื่องทำความเย็น:</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 900px; text-align: right;' >
                         <a id="partChiller_mapTableAddLink" href="javascript:void(0);" >เพิ่ม</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partChiller_mapTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:900px' >
                     <thead>
                     <tr class='nodrop' >
                         <th  width='20' >&nbsp;</th>
                         <th  width='20' ></th>     
                         <th  width='90'>ครั้งที่</th>
                         <th  width='120'>วันที่สอบเทียบ</th>
                         <th  width='120'>วันหมดอายุ</th>
                         <th  width='180'>ชื่อผู้สอบเทียบ</th>
                         <th  width='90'>เอกสาร</th>  
                         <th  width='70'>แก้ไข</th>
                         <th  width='70'>ลบ</th>
                     </tr
                     </thead>
                     <tbody>
                     </tbody>
                 </table>
                 </div>

             </td> 
        </tr>  
        
        
    </tbody>
</table>

@endsection