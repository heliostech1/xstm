@section('partOwnerHelper')


var PartOwnerTableHelper = {
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
        var rowId = DTHelper.getKeyByCell(rowEl, PartOwnerTableHelper.getColNumber("rowId")); 
        
        DTHelper.deleteRowByKey(this.table, "rowId", rowId);
        DTHelper.updateOrderColValue(this.table, 1);
    },
    
    updateData: function(rowId, data) {
        if (AppUtil.isEmpty(data) ) return ;
        
        data["rowId"] = rowId;
        data["counterColumn"] = "";         
        data["edit"] = '<a class="edit" href="">แก้ไข</a>';  
        data["delete"] = '<a class="delete" href="">ลบ</a>';     
        
        DTHelper.updateRowByKey(PartOwnerTableHelper.table, "rowId", rowId, data);

            
        DTHelper.updateHtmlCellByKey(PartOwnerTableHelper.table, "rowId", rowId, "ownerDate", data['ownerDate']);
        DTHelper.updateHtmlCellByKey(PartOwnerTableHelper.table, "rowId", rowId, "ownerName", data['ownerName']);
        DTHelper.updateHtmlCellByKey(PartOwnerTableHelper.table, "rowId", rowId, "ownerBirthDate", data['ownerBirthDate']);
        DTHelper.updateHtmlCellByKey(PartOwnerTableHelper.table, "rowId", rowId, "ownerAddress", data['ownerAddress']);
        DTHelper.updateHtmlCellByKey(PartOwnerTableHelper.table, "rowId", rowId, "ownerPhone", data['ownerPhone']);
               

        DTHelper.updateOrderColValue(this.table, 1);  
        this.displayAllRowDetail();        
    },
    
    getDataForSubmit: function() {
        var idList = DTHelper.getHtmlDataAtCol(this.table, PartOwnerTableHelper.getColNumber("rowId"));
 
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
    
        $('#partOwner_table tbody').find('td').each(function() {
            PartOwnerTableHelper.displayRowDetail(oTable, this);
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
            '<div style="padding:2px"><b>ผู้ครอบครอง: </b>' + data['holderName'] + '</div>'+    
            '<div style="padding:2px"><b>เลขที่บัตร: </b>' + data['cardNumber'] + '</div>'+   
            '<div style="padding:2px"><b>วันเกิด: </b>' + data['holderBirthDate'] + '</div>'+   
            '<div style="padding:2px"><b>สัญชาติ: </b>' + data['holderNation'] + '</div>'+   

       '</div>';
            
       
        html += '<div style="float:left; width:400px ">'+
            '<div style="padding:2px"><b>ที่อยู่: </b>' + data['holderAddress'] + '</div>'+             
            '<div style="padding:2px"><b>โทร.: </b>' + data['holderPhone'] + '</div>'+    
            '<div style="padding:2px"><b>สัญญาเช่าซื้อเลขที่: </b>' + data['leaseContractNumber'] + '</div>'+   
            '<div style="padding:2px"><b>ภาพข้อมูลการครอบครองรถ: </b>' + fileLink + '</div>'+     
            
       '</div>';
       
        html += '<div style="clear:both"></div>';

                   
        oTable.fnOpen( nTr, html, 'cellAdditionalDetail' );

    },
    
    debug: function() {

    }
};



@endsection

 
            
@section('partOwnerJs')

    window.partOwner_table  = $('#partOwner_table').dataTable( 
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
                        
                        { "mData": "ownerDate" },                               
                        { "mData": "ownerName" },
                        { "mData": "ownerBirthDate" },
                        { "mData": "ownerAddress" },     
                        { "mData": "ownerPhone" },

                        { "mData": "edit" },                          
                        { "mData": "delete"}
                  
                ],
                
                "fnDrawCallback": function () { 
                    PartOwnerTableHelper.displayAllRowDetail();
                }         
            }
    );
    
    PartOwnerTableHelper.setTable(partOwner_table);
    DTHelper.applySelectable(partOwner_table, "partOwner_table");
    
    PartOwnerTableHelper.addDataFromServer(<?php echo json_encode($partOwner_ownerDatas) ?>);
    
    
    $('#partOwner_addLink').click(function(e) {
        e.preventDefault();
        popupOwner_openPopupForAdd( function(rowId, result) { PartOwnerTableHelper.addData(result) } );
    });    

       
    $('#partOwner_table').on('click', 'a.edit', function (e) {
        e.preventDefault();
        var key = DTHelper.getKeyByCell(this, 0);
        var data = DTHelper.getDataByKey(partOwner_table, "rowId", key);
        popupOwner_openPopupForEdit(key, data , function(rowId, result) { PartOwnerTableHelper.updateData(rowId, result) });
    } ); 
    
    $('#partOwner_table').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partOwner_table, "rowId", key);
        DTHelper.updateOrderColValue(partOwner_table, 1);
    } );    
    
     <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
   
       
     <?php endif; ?>  
       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partOwner_table,"edit", false, true); 
         DTHelper.setColumnVisible(partOwner_table,"delete", false, true); 

        $('#partOwner_addLink').hide();
   
     <?php endif; ?>  
        
        
@endsection


@section('partOwnerSubmit')
   document.mainForm.partOwner_ownerDatas.value = $.toJSON(PartOwnerTableHelper.getDataForSubmit()); 
@endsection


            
@section('partOwnerHtml')


<input type='hidden' name='partOwner_ownerDatas'  />


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
        <?php if ($pageMode == 'view'): ?>  
        <tr>
            <td class="formLabel"  >อายุรถ (ปี):</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $ageYear }}'  autocomplete="off">
        </tr>  
        <?php endif; ?>          
    </tbody>
</table>


<?php endif; ?>   

            
<div style='padding:0px 20px 1px 0px'>
     <div style='float: right; text-align: right;' >
        <a id="partOwner_addLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
     </div>
     <div style='clear: both'></div>
</div>

            
<div class='customTableStyle' > 
<table id='partOwner_table' cellspacing='0' cellpadding='0' class='tableInnerDisplay's  >
    <thead>
    <tr class='nodrop' >
        <th  width='20' >&nbsp;</th>
        <th  width='20' ></th>     
        
        <th  width='90'>วันที่ครอบครองรถ</th>
        <th  width='170'>ผู้ถือกรรมสิทธ์</th>
        <th  width='100'>วันเกิด</th>
        <th  width='400'>ที่อยู่</th>
        <th  width='100'>โทร.</th>  
        
        
        <th  width='70'>แก้ไข</th>
        <th  width='70'>ลบ</th>        
    </tr
    </thead>
    <tbody>
    </tbody>
</table>
</div>


            
@endsection
