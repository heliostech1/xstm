@section('partRegisHelper')


var PartRegisTableHelper = {
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
            row["edit"] = '<a class="edit" href="">แก้ไข</a>';  
            row["delete"] = '<a class="delete" href="">ลบ</a>';      
            output.push(row);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 1);
        this.displayAllRowDetail();
    },

    deleteData: function(rowEl) {
        var rowId = DTHelper.getKeyByCell(rowEl, PartRegisTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartRegisTableHelper.table, "rowId", rowId, data);

                        
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "regisDate", data['regisDate']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "regisNumber", data['regisNumber']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "province", data['province']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "vehicleType", data['vehicleType']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "vehicleRegisType", data['vehicleRegisType']);
        
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "look", data['look']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "brand", data['brand']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "design", data['design']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "model", data['model']);
        DTHelper.updateHtmlCellByKey(PartRegisTableHelper.table, "rowId", rowId, "color", data['color']);        

        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartRegisTableHelper.getColNumber("rowId"));
 
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
    
    getSelected: function() {
       var data = DTHelper.getSelectedDataByKey(this.table, "rowId", PartRegisTableHelper.getColNumber("rowId"));
       return data;
    },
    

    getColNumber: function(name) {
        var result = DTHelper.getHtmlColNumberByColName(this.table, name);        
        return result-1;
    },
    
    displayAllRowDetail: function() {
        var oTable = this.table;
        if (!oTable) return;
    
        $('#partRegis_table tbody').find('td').each(function() {
            PartRegisTableHelper.displayRowDetail(oTable, this);
        }); 
    },
   
            
            
    displayRowDetail: function(oTable, tdEl) {
        if (AppUtil.isEmpty(tdEl) || AppUtil.isEmpty(oTable)) return;
        
        var nTr = $(tdEl).parents('tr')[0];
        var data = oTable.fnGetData( nTr );   
        //console.log(data);
        
        if (AppUtil.isEmpty(data)) return;
        
        var fileLink = ImageFileUtil.getImageLink(  data['fileDatas'] );
        
                        
        var html = '<div style="float:left; width:400px ">'+
            '<div style="padding:2px"><b>เลขตัวรถ: </b>' + data['bodyNumber'] + '</div>'+    
            '<div style="padding:2px"><b>อยู่ที่: </b>' + data['address'] + '</div>'+   
            '<div style="padding:2px"><b>ยี่ห้อเครื่องยนต์: </b>' + data['engineBrand'] + '</div>'+   
            '<div style="padding:2px"><b>เลขเครื่องยนต์: </b>' + data['engineNumber'] + '</div>'+   
            '<div style="padding:2px"><b>เชื้อเพลิง: </b>' + AppUtil.getDropdownText('popupRegis_fuel',  data['fuel'] ) + '</div>'+   
            
            '<div style="padding:2px"><b>เลขถังแก๊ส: </b>' + data['gasTankNumber'] + '</div>'+    
            '<div style="padding:2px"><b>จำนวน (สูบ): </b>' + data['loop'] + '</div>'+   
            '<div style="padding:2px"><b>ซีซี: </b>' + data['cc'] + '</div>'+     
            
       '</div>';
            
        html += '<div style="float:left; ">'+
            '<div style="padding:2px"><b>แรงม้า: </b>' + data['horsePower'] + '</div>'+   
            '<div style="padding:2px"><b>จำนวนเพลา/ล้อ/ยาง: </b>' + data['wheel'] + '</div>'+          
            
            '<div style="padding:2px"><b>น้ำหนักรถ (กก.): </b>' + data['carWeight'] + '</div>'+    
            '<div style="padding:2px"><b>น้ำหนักบรรทุก/น้ำหนักลงเพลา (กก.): </b>' + data['loadWeight'] + '</div>'+   
            '<div style="padding:2px"><b>น้ำหนักรวม: </b>' + data['totalWeight'] + '</div>'+   
            '<div style="padding:2px"><b>ที่นั่ง: </b>' + data['seat'] + '</div>'+   
            '<div style="padding:2px"><b>ภาพข้อมูลจดทะเบียน: </b>' + fileLink+ '</div>'+    
            
       '</div>';
       
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection



@section('partRegisJs')

    window.partRegis_table  = $('#partRegis_table').dataTable( 
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
                        
                        { "mData": "regisDate" },                               
                        { "mData": "regisNumber" },
                        { "mData": "province" },
                        { "mData": "vehicleType" },     
                        { "mData": "vehicleRegisType" },
                        
                        { "mData": "look" },                               
                        { "mData": "brand" },
                        { "mData": "design" },
                        { "mData": "model" },     
                        { "mData": "color" },
                        
                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartRegisTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartRegisTableHelper.setTable(partRegis_table);
    DTHelper.applySelectable(partRegis_table, "partRegis_table");
    
    PartRegisTableHelper.addDataFromServer(<?php echo json_encode($partRegis_regisDatas) ?>);
    
    
    $('#partRegis_addLink').click(function(e) {
        e.preventDefault();
        popupRegis_openPopupForAdd( function(rowId, result) { PartRegisTableHelper.addData(result) } );
    });    

       
    $('#partRegis_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partRegis_table, "rowId", key);
        popupRegis_openPopupForEdit(key, data , function(rowId, result) { PartRegisTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partRegis_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partRegis_table, "rowId", key);
        DTHelper.updateOrderColValue(partRegis_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partRegis_table,"edit", false, true); 
         DTHelper.setColumnVisible(partRegis_table,"delete", false, true); 

        $('#partRegis_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partRegisSubmit')
   document.mainForm.partRegis_regisDatas.value = $.toJSON(PartRegisTableHelper.getDataForSubmit()); 
@endsection




            
@section('partRegisHtml')


<input type='hidden' name='partRegis_regisDatas'  />


<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

        <tr>
            <td  class="formLabel" style='width:500px;'>รหัสรถ</td>
            <td ><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:500px;'>ทะเบียนรถ</td>
            <td ><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
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
        <a id="partRegis_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

<div class='customTableStyle' > 
<table id='partRegis_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        <th  width='90'>วันจดทะเบียน</th>
        <th  width='100'>เลขทะเบียน</th>
        <th  width='100'>จังหวัด</th>
        <th  width='100'>ประเภทรถ</th>
        <th  width='100'>รย.</th>  
        
        <th  width='100'>ลักษณะ</th>
        <th  width='100'>ยี่ห้อรถ</th>
        <th  width='100'>แบบ</th>
        <th  width='100'>รุ่น</th>      
        <th  width='100'>สี</th>    

        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
