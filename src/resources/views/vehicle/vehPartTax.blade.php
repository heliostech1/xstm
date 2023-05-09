@section('partTaxHelper')


var PartTaxTableHelper = {
    autoRunId: 9991,

    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table) {
        this.table = table;
     },
     
    addData: function(inputData) {
        //console.log(inputData);
        
        var data = inputData;
        data["rowId"] = this.getAutoRunId();    
        data["counterColumn"] = "";   
        data["fileLink"] =  ImageFileUtil.getImageLink(  data['fileDatas'] );
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';              

        this.table.fnAddData(data);
        DTHelper.updateOrderColValue(this.table, 1);
        this.displayAllRowDetail();        
    },
    
    addDataFromServer : function(datas) {
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = datas[i];
            row["rowId"] = this.getAutoRunId();   
            row["counterColumn"] = "";     
            row["fileLink"] =  ImageFileUtil.getImageLink(  row['fileDatas'] );            
            row["edit"] = '<a class="edit" href="">แก้ไข</a>';  
            row["delete"] = '<a class="delete" href="">ลบ</a>';      
            output.push(row);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 1);
        this.displayAllRowDetail();
    },

    deleteData: function(rowEl) {
        var rowId = DTHelper.getKeyByCell(rowEl, PartTaxTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        var fileLink = ImageFileUtil.getImageLink(  data['fileDatas'] );
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        data["fileLink"] = fileLink;
        
        DTHelper.updateRowByKey(PartTaxTableHelper.table, "rowId", rowId, data);
      
        var fileLink = ImageFileUtil.getImageLink(  data['fileDatas'] );
            
        DTHelper.updateHtmlCellByKey(PartTaxTableHelper.table, "rowId", rowId, "taxDate", data['taxDate']);
        DTHelper.updateHtmlCellByKey(PartTaxTableHelper.table, "rowId", rowId, "dueDate", data['dueDate']);
        DTHelper.updateHtmlCellByKey(PartTaxTableHelper.table, "rowId", rowId, "taxAmount", data['taxAmount']);
        DTHelper.updateHtmlCellByKey(PartTaxTableHelper.table, "rowId", rowId, "extraAmount", data['extraAmount']);
        DTHelper.updateHtmlCellByKey(PartTaxTableHelper.table, "rowId", rowId, "fileDatas", fileLink);
               

        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartTaxTableHelper.getColNumber("rowId"));
 
        var output = [];
        for ( var i = 0; i < idList.length; i++) {
            var key = idList[i];
            var row = DTHelper.getDataByKey(this.table, "rowId", key); 
            if (AppUtil.isNotEmpty(row)) {  // ไม่เอา cellDetail
                output.push(row);
            }
            
        }
        
        return output;
    },
    

    getColNumber: function(name) {
        var result = DTHelper.getHtmlColNumberByColName(this.table, name);        
        return result-1;
    },
    
    displayAllRowDetail: function() {
    },
            
    displayRowDetail: function(oTable, tdEl) {
    },
    
    debug: function() {

    }
};



@endsection


            
@section('partTaxJs')

    window.partTax_table  = $('#partTax_table').dataTable( 
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
                        
                        { "mData": "taxDate" },                               
                        { "mData": "dueDate" },
                        { "mData": "taxAmount" },
                        { "mData": "extraAmount" },     
                        { "mData": "fileLink" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartTaxTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartTaxTableHelper.setTable(partTax_table);
    DTHelper.applySelectable(partTax_table, "partTax_table");
    
    PartTaxTableHelper.addDataFromServer(<?php echo json_encode($partTax_taxDatas) ?>);
    
    
    $('#partTax_addLink').click(function(e) {
        e.preventDefault();
        popupTax_openPopupForAdd( function(rowId, result) { PartTaxTableHelper.addData(result) } );
    });    

       
    $('#partTax_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partTax_table, "rowId", key);
        popupTax_openPopupForEdit(key, data , function(rowId, result) { PartTaxTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partTax_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partTax_table, "rowId", key);
        DTHelper.updateOrderColValue(partTax_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partTax_table,"edit", false, true); 
         DTHelper.setColumnVisible(partTax_table,"delete", false, true); 

        $('#partTax_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partTaxSubmit')
   document.mainForm.partTax_taxDatas.value = $.toJSON(PartTaxTableHelper.getDataForSubmit()); 
@endsection


            
@section('partTaxHtml')


<input type='hidden' name='partTax_taxDatas'  />


<?php if ($pageMode == 'view' && $ageYear > 7): ?>  
<div style="padding:0px 0px 10px 10px; color:red">อายุรถเกิน 7 ปีขึ้น ต้องตรวจสภาพรถก่อนต่อทะเบียน</div>
<?php endif; ?>       
        
                
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

        <tr>
            <td class="formLabel" style='width:400px' >รหัสรถ</td>
            <td><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ</td>
            <td><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
        <?php if ($pageMode == 'view'): ?>  
        <tr>
            <td class="formLabel"  >อายุรถ (ปี)</td>
            <td><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $ageYear }}'  autocomplete="off">
        </tr>  
        <?php endif; ?>          
    </tbody>
</table>


<?php endif; ?>   

            
<div style='padding:0px 20px 1px 0px'>
     <div style='float: right; text-align: right;' >
        <a id="partTax_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>


            
<div class='customTableStyle' > 
<table id='partTax_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='120'>วันเสียภาษี</th>
        <th  width='120'>วันครบกำหนดเสียภาษี</th>
        <th  width='120'>ค่าภาษี</th>
        <th  width='120'>เงินเพิ่ม</th>
        <th  width='120'>ภาพรายการเสียภาษี</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
