@section('partInsCarHelper')


var PartInsCarTableHelper = {
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
        var rowId = DTHelper.getKeyByCell(rowEl, PartInsCarTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartInsCarTableHelper.table, "rowId", rowId, data);

            

            
        DTHelper.updateHtmlCellByKey(PartInsCarTableHelper.table, "rowId", rowId, "insType", data['insType']);
        DTHelper.updateHtmlCellByKey(PartInsCarTableHelper.table, "rowId", rowId, "insNo", data['insNo']);
        DTHelper.updateHtmlCellByKey(PartInsCarTableHelper.table, "rowId", rowId, "company", data['company']);
        DTHelper.updateHtmlCellByKey(PartInsCarTableHelper.table, "rowId", rowId, "insPerson", data['insPerson']);
        DTHelper.updateHtmlCellByKey(PartInsCarTableHelper.table, "rowId", rowId, "benefitPerson", data['benefitPerson']);
  
        
        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartInsCarTableHelper.getColNumber("rowId"));
 
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
    
        $('#partInsCar_table tbody').find('td').each(function() {
            PartInsCarTableHelper.displayRowDetail(oTable, this);
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
            '<div style="padding:2px"><b>ทุนประกัน ความเสียหายต่อรถยนต์: </b>' + data['fundDamage'] + '</div>'+             
            '<div style="padding:2px"><b>ทุนประกัน รถยนต์สูญหายหรือไฟไหม้.: </b>' + data['fundLost'] + '</div>'+    
            '<div style="padding:2px"><b>แนบไฟล์กรมธรรม์: </b>' + fileLink + '</div>'+     
            
       '</div>';
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection

 

            
            
@section('partInsCarJs')

    window.partInsCar_table  = $('#partInsCar_table').dataTable( 
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
                    PartInsCarTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartInsCarTableHelper.setTable(partInsCar_table);
    DTHelper.applySelectable(partInsCar_table, "partInsCar_table");
    
    PartInsCarTableHelper.addDataFromServer(<?php echo json_encode($partInsCar_insCarDatas) ?>);
    
    
    $('#partInsCar_addLink').click(function(e) {
        e.preventDefault();
        popupInsCar_openPopupForAdd( function(rowId, result) { PartInsCarTableHelper.addData(result) } );
    });    

       
    $('#partInsCar_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partInsCar_table, "rowId", key);
        popupInsCar_openPopupForEdit(key, data , function(rowId, result) { PartInsCarTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partInsCar_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partInsCar_table, "rowId", key);
        DTHelper.updateOrderColValue(partInsCar_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partInsCar_table,"edit", false, true); 
         DTHelper.setColumnVisible(partInsCar_table,"delete", false, true); 

        $('#partInsCar_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partInsCarSubmit')
   document.mainForm.partInsCar_insCarDatas.value = $.toJSON(PartInsCarTableHelper.getDataForSubmit()); 
@endsection


            
@section('partInsCarHtml')


<input type='hidden' name='partInsCar_insCarDatas'  />


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
        <a id="partInsCar_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

            
<div class='customTableStyle' > 
<table id='partInsCar_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
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
