@section('partClaimHelper')


var PartClaimTableHelper = {
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
        var rowId = DTHelper.getKeyByCell(rowEl, PartClaimTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartClaimTableHelper.table, "rowId", rowId, data);


        DTHelper.updateHtmlCellByKey(PartClaimTableHelper.table, "rowId", rowId, "times", data['times']);
        DTHelper.updateHtmlCellByKey(PartClaimTableHelper.table, "rowId", rowId, "claimDate", data['claimDate']);
        DTHelper.updateHtmlCellByKey(PartClaimTableHelper.table, "rowId", rowId, "claimType", data['claimType']);
        DTHelper.updateHtmlCellByKey(PartClaimTableHelper.table, "rowId", rowId, "insNo", data['insNo']);
        DTHelper.updateHtmlCellByKey(PartClaimTableHelper.table, "rowId", rowId, "claimNo", data['claimNo']);
               

        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartClaimTableHelper.getColNumber("rowId"));
 
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
    
        $('#partClaim_table tbody').find('td').each(function() {
            PartClaimTableHelper.displayRowDetail(oTable, this);
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
            '<div style="padding:2px"><b>วันเวลาที่เกิดเหตุ: </b>' + data['actDate'] + " " + data['actTime'] +  '</div>'+    
            '<div style="padding:2px"><b>ชื่อผู้ขับขี่ขณะเกิดเหตุ: </b>' + data['actDriver'] + '</div>'+   
            '<div style="padding:2px"><b>วันที่เข้าซ่อม: </b>' + data['fixStartDate'] + '</div>'+   
            '<div style="padding:2px"><b>วันที่ซ่อมเสร็จ: </b>' + data['fixEndDate'] + '</div>'+   

       '</div>';
            
       
        html += '<div style="float:left; width:400px ">'+
            '<div style="padding:2px"><b>ค่าใช้จ่ายในการเคลม: </b>' + data['fixCost'] + '</div>'+             
            '<div style="padding:2px"><b>บันทึกข้อมูล: </b>' + data['detail'] + '</div>'+    
            '<div style="padding:2px"><b>แนบเอกสาร: </b>' + fileLink + '</div>'+   

            
       '</div>';
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection

 
            
@section('partClaimJs')


            
    window.partClaim_table  = $('#partClaim_table').dataTable( 
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
                        
                        { "mData": "times" },                               
                        { "mData": "claimDate" },
                        { "mData": "claimType" },
                        { "mData": "insNo" },     
                        { "mData": "claimNo" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartClaimTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartClaimTableHelper.setTable(partClaim_table);
    DTHelper.applySelectable(partClaim_table, "partClaim_table");
    
    PartClaimTableHelper.addDataFromServer(<?php echo json_encode($partClaim_claimDatas) ?>);
    
    
    $('#partClaim_addLink').click(function(e) {
        e.preventDefault();
        popupClaim_openPopupForAdd( function(rowId, result) { PartClaimTableHelper.addData(result) } );
    });    

       
    $('#partClaim_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partClaim_table, "rowId", key);
        popupClaim_openPopupForEdit(key, data , function(rowId, result) { PartClaimTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partClaim_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partClaim_table, "rowId", key);
        DTHelper.updateOrderColValue(partClaim_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partClaim_table,"edit", false, true); 
         DTHelper.setColumnVisible(partClaim_table,"delete", false, true); 

        $('#partClaim_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partClaimSubmit')
   document.mainForm.partClaim_claimDatas.value = $.toJSON(PartClaimTableHelper.getDataForSubmit()); 
@endsection


            
@section('partClaimHtml')


<input type='hidden' name='partClaim_claimDatas'  />


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
        <a id="partClaim_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

<div class='customTableStyle' > 
<table id='partClaim_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='100'>ครั้งที่</th>
        <th  width='100'>วันที่</th>
        <th  width='120'>ชนิดการเคลม</th>
        <th  width='120'>เลขที่กรมธรรม์</th>
        <th  width='120'>หมายเลขการเคลม</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
