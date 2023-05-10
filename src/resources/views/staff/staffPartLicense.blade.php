@section('partLicenseHelper')



var PartLicenseTableHelper = {
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
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';              

        data["licenseTypeDesc"] = AppUtil.getDropdownText('popupLicense_licenseType', data['licenseType'] );    
        data["fileLink"] = ImageFileUtil.getImageLink(  data['fileDatas']  );    
        
        this.table.fnAddData(data);
        DTHelper.updateOrderColValue(this.table, 1);
        this.displayAllRowDetail();        
    },
    
    addDataFromServer : function(datas) {
        //console.log(datas);
        
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var data = datas[i];
            data["rowId"] = this.getAutoRunId();   
            data["counterColumn"] = "";     
            data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
            data["delete"] = '<a class="delete" href="">ลบ</a>';    
            
            data["licenseTypeDesc"] = AppUtil.getDropdownText('popupLicense_licenseType', data['licenseType'] );    
            data["fileLink"] = ImageFileUtil.getImageLink(  data['fileDatas']  );    
        
            output.push(data);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 1);
        this.displayAllRowDetail();
    },

    deleteData: function(rowEl) {
        var rowId = DTHelper.getKeyByCell(rowEl, PartLicenseTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartLicenseTableHelper.table, "rowId", rowId, data);

            
        DTHelper.updateHtmlCellByKey(PartLicenseTableHelper.table, "rowId", rowId, "licenseTypeDesc", AppUtil.getDropdownText(data['licenseType']) );
        DTHelper.updateHtmlCellByKey(PartLicenseTableHelper.table, "rowId", rowId, "issueNo", data['issueNo']);
        DTHelper.updateHtmlCellByKey(PartLicenseTableHelper.table, "rowId", rowId, "issueDate", data['issueDate']);
        DTHelper.updateHtmlCellByKey(PartLicenseTableHelper.table, "rowId", rowId, "expDate", data['expDate']);
        DTHelper.updateHtmlCellByKey(PartLicenseTableHelper.table, "rowId", rowId, "fileLink", ImageFileUtil.getImageLink(  data['fileDatas']  ) );
  
        
        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartLicenseTableHelper.getColNumber("rowId"));
 
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



@section('partLicenseJs')

   window.partLicense_table  = $('#partLicense_table').dataTable( 
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
            
                        { "mData": "licenseTypeDesc" },                               
                        { "mData": "issueNo" },
                        { "mData": "issueDate" },
                        { "mData": "expDate" },     
                        { "mData": "fileLink" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    //PartLicenseTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartLicenseTableHelper.setTable(partLicense_table);
    DTHelper.applySelectable(partLicense_table, "partLicense_table");
    
    PartLicenseTableHelper.addDataFromServer(<?php echo json_encode($partLicense_mainDatas) ?>);
    
    
    $('#partLicense_addLink').click(function(e) {
        e.preventDefault();
        popupLicense_openPopupForAdd( function(rowId, result) { PartLicenseTableHelper.addData(result) } );
    });    
       
    $('#partLicense_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partLicense_table, "rowId", key);
        popupLicense_openPopupForEdit(key, data , function(rowId, result) { PartLicenseTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partLicense_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partLicense_table, "rowId", key);
        DTHelper.updateOrderColValue(partLicense_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partLicense_table,"edit", false, true); 
         DTHelper.setColumnVisible(partLicense_table,"delete", false, true); 

        $('#partLicense_addLink').hide();
   
     <?php endif; ?>  
        


@endsection


@section('partLicenseSubmit')
   
   document.mainForm.partLicense_mainDatas.value = $.toJSON(PartLicenseTableHelper.getDataForSubmit()); 
   
@endsection



@section('partLicenseHtml')


<input type='hidden' name='partLicense_mainDatas'  />
            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >รหัสพนักงาน:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $staffCode }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >ชื่อ นามสกุล:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $staffName }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   

    </tbody>
</table>

    
<div style='padding:0px 20px 1px 0px'>
     <div style='float: right; text-align: right;' >
        <a id="partLicense_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

            

            
<div class='customTableStyle' > 
<table id='partLicense_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='100'>ประเภทใบขับขี่</th>
        <th  width='100'>ฉบับที่</th>
        <th  width='100'>วันอนุญาต</th>
        <th  width='100'>วันหมดอายุ</th>
        <th  width='100'>ภาพใบขับขี่</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>



@endsection
