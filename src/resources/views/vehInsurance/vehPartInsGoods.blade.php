@section('partInsGoodsHelper')


var PartInsGoodsTableHelper = {
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
        var rowId = DTHelper.getKeyByCell(rowEl, PartInsGoodsTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartInsGoodsTableHelper.table, "rowId", rowId, data);

            

            
        DTHelper.updateHtmlCellByKey(PartInsGoodsTableHelper.table, "rowId", rowId, "insType", data['insType']);
        DTHelper.updateHtmlCellByKey(PartInsGoodsTableHelper.table, "rowId", rowId, "insNo", data['insNo']);
        DTHelper.updateHtmlCellByKey(PartInsGoodsTableHelper.table, "rowId", rowId, "company", data['company']);
        DTHelper.updateHtmlCellByKey(PartInsGoodsTableHelper.table, "rowId", rowId, "insPerson", data['insPerson']);
        DTHelper.updateHtmlCellByKey(PartInsGoodsTableHelper.table, "rowId", rowId, "benefitPerson", data['benefitPerson']);
  
        
        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartInsGoodsTableHelper.getColNumber("rowId"));
 
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
    
        $('#partInsGoods_table tbody').find('td').each(function() {
            PartInsGoodsTableHelper.displayRowDetail(oTable, this);
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
            '<div style="padding:2px"><b>วันทำสัญญาประกันภัย: </b>' + data['agreeDate'] + '</div>'+    
            '<div style="padding:2px"><b>วันทำกรมธรรม์ประกันภัย: </b>' + data['issueDate'] + '</div>'+   
            '<div style="padding:2px"><b>ระยะเวลาประกันภัย: เริ่มต้น </b>' + data['insStartDate'] + ' <b> สิ้นสุด </b>' +  data['insEndDate']  + '</div>'+  
            '<div style="padding:2px"><b>เบี้ยประกัน: </b>' + data['amount'] + '</div>'+   

       '</div>';
            
       
        html += '<div style="float:left; width:400px ">'+
            '<div style="padding:2px"><b>ทุนประกัน: </b>' + data['fund'] + '</div>'+                
            '<div style="padding:2px"><b>แนบไฟล์กรมธรรม์: </b>' + fileLink + '</div>'+     
            
       '</div>';
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection

 

            
            
@section('partInsGoodsJs')

    window.partInsGoods_table  = $('#partInsGoods_table').dataTable( 
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
                        
                        { "mData": "insType" },                               
                        { "mData": "insNo" },
                        { "mData": "company" },
                        { "mData": "insPerson" },     
                        { "mData": "benefitPerson" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartInsGoodsTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartInsGoodsTableHelper.setTable(partInsGoods_table);
    DTHelper.applySelectable(partInsGoods_table, "partInsGoods_table");
    
    PartInsGoodsTableHelper.addDataFromServer(<?php echo json_encode($partInsGoods_insGoodsDatas) ?>);
    
    
    $('#partInsGoods_addLink').click(function(e) {
        e.preventDefault();
        popupInsGoods_openPopupForAdd( function(rowId, result) { PartInsGoodsTableHelper.addData(result) } );
    });    

       
    $('#partInsGoods_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partInsGoods_table, "rowId", key);
        popupInsGoods_openPopupForEdit(key, data , function(rowId, result) { PartInsGoodsTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partInsGoods_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partInsGoods_table, "rowId", key);
        DTHelper.updateOrderColValue(partInsGoods_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partInsGoods_table,"edit", false, true); 
         DTHelper.setColumnVisible(partInsGoods_table,"delete", false, true); 

        $('#partInsGoods_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partInsGoodsSubmit')
   document.mainForm.partInsGoods_insGoodsDatas.value = $.toJSON(PartInsGoodsTableHelper.getDataForSubmit()); 
@endsection


            
@section('partInsGoodsHtml')


<input type='hidden' name='partInsGoods_insGoodsDatas'  />


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
        <a id="partInsGoods_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

            
<div class='customTableStyle' > 
<table id='partInsGoods_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='120'>ประเภทประกัน</th>
        <th  width='120'>กรมธรรม์ประกันภัยเลขที่</th>
        <th  width='150'>ชื่อบริษัทประกันภัย</th>
        <th  width='150'>ผู้เอาประกันภัย</th>
        <th  width='150'>ผู้รับผลประโยชน์</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
