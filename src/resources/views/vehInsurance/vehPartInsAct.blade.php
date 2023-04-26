@section('partInsActHelper')


var PartInsActTableHelper = {
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
        var rowId = DTHelper.getKeyByCell(rowEl, PartInsActTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartInsActTableHelper.table, "rowId", rowId, data);


            
        DTHelper.updateHtmlCellByKey(PartInsActTableHelper.table, "rowId", rowId, "company", data['company']);
        DTHelper.updateHtmlCellByKey(PartInsActTableHelper.table, "rowId", rowId, "insNo", data['insNo']);
        DTHelper.updateHtmlCellByKey(PartInsActTableHelper.table, "rowId", rowId, "insPerson", data['insPerson']);
        DTHelper.updateHtmlCellByKey(PartInsActTableHelper.table, "rowId", rowId, "address", data['address']);
        DTHelper.updateHtmlCellByKey(PartInsActTableHelper.table, "rowId", rowId, "agreeDate", data['agreeDate']);
               

        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartInsActTableHelper.getColNumber("rowId"));
 
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
        var oTable = this.table;
        if (!oTable) return;
    
        $('#partInsAct_table tbody').find('td').each(function() {
            PartInsActTableHelper.displayRowDetail(oTable, this);
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
            '<div style="padding:2px"><b>จำนวนเบี้ยประกัน: </b>' + data['amount'] + '</div>'+    

            '<div style="padding:5px 2px 2px 2px"><b><u>รายการรถยนต์ที่เอาประกันภัย</u></b></div>'+               
            '<div style="padding:2px"><b>รหัส: </b>' + data['carCode'] + '</div>'+   
            '<div style="padding:2px"><b>ชื่อรถยนต์/รุ่น : </b>' + data['carName'] + '</div>'+   
            '<div style="padding:2px"><b>เลขทะเบียน: </b>' + data['carLicensePlate'] + '</div>'+ 
            '<div style="padding:2px"><b>ภาพตารางกรมธรรม์: </b>' + fileLink + '</div>'+             
       '</div>';
            
       
        html += '<div style="float:left; width:400px ">'+
            '<div style="padding:2px"><b>ระยะเวลาประกันภัย: เริ่มต้น </b>' + data['insStartDate'] + ' <b> ถึง </b>' +  data['insEndDate']  + '</div>'+ 
                        
            '<div style="padding:5px 2px 2px 2px"><b>&nbsp;</b></div>'+               
            '<div style="padding:2px"><b>เลขตัวถัง: </b>' + data['carBodyNumber'] + '</div>'+   
            '<div style="padding:2px"><b>แบบตัวถัง: </b>' + data['carBodyType'] + '</div>'+   
            '<div style="padding:2px"><b>จำนวนที่นั่ง/ขนาด/น้ำหนัก: </b>' + data['carSize'] + '</div>'+    
            
       '</div>';
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection

 
            
@section('partInsActJs')


            
    window.partInsAct_table  = $('#partInsAct_table').dataTable( 
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
                        
                        { "mData": "company" },                               
                        { "mData": "insNo" },
                        { "mData": "insPerson" },
                        { "mData": "address" },     
                        { "mData": "agreeDate" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartInsActTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartInsActTableHelper.setTable(partInsAct_table);
    DTHelper.applySelectable(partInsAct_table, "partInsAct_table");
    
    PartInsActTableHelper.addDataFromServer(<?php echo json_encode($partInsAct_insActDatas) ?>);
    
    
    $('#partInsAct_addLink').click(function(e) {
        e.preventDefault();
        popupInsAct_openPopupForAdd( function(rowId, result) { PartInsActTableHelper.addData(result) } );
    });    

       
    $('#partInsAct_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partInsAct_table, "rowId", key);
        popupInsAct_openPopupForEdit(key, data , function(rowId, result) { PartInsActTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partInsAct_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partInsAct_table, "rowId", key);
        DTHelper.updateOrderColValue(partInsAct_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partInsAct_table,"edit", false, true); 
         DTHelper.setColumnVisible(partInsAct_table,"delete", false, true); 

        $('#partInsAct_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partInsActSubmit')
   document.mainForm.partInsAct_insActDatas.value = $.toJSON(PartInsActTableHelper.getDataForSubmit()); 
@endsection


            
@section('partInsActHtml')


<input type='hidden' name='partInsAct_insActDatas'  />


<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
    </tbody>
</table>


<?php endif; ?>   

            
<div style='padding:0px 20px 1px 0px'>
     <div style='float: right; text-align: right;' >
        <a id="partInsAct_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

<div class='customTableStyle' > 
<table id='partInsAct_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay'   >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='120'>บริษัทประกัน</th>
        <th  width='120'>กรมธรรม์ประกันภัยเลขที่</th>
        <th  width='120'>ชื่อผู้เอาประกันภัย</th>
        <th  width='400'>ที่อยู่</th>
        <th  width='100'>วันที่ทำพรบ</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
