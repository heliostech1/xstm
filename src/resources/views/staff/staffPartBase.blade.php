

@section('partBaseHelper')



var ReateTableHelper = {
    autoRunId: 9991,
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table, tableId) {
        this.table = table;
        this.tableId = tableId;
     },

    addData: function(tankNumber) {
        var rowId = this.getAutoRunId();    
        this.table.fnAddData(
                { 
                  "rowId": rowId ,
                  "counterColumn":"",
  
                  "name": "",
                  "nameInput": partBase_getNameInput(this.tableId, rowId),                    
                  "detail":"",
                  "detailInput": partBase_getDetailInput(this.tableId, rowId),  
                  "phone":"",
                  "phoneInput": partBase_getPhoneInput(this.tableId, rowId),
                  "address":"",
                  "addressInput": partBase_getAddressInput(this.tableId, rowId),                  
                  "delete": '<a class="delete" href="">ลบ</a>'
                }                
        );
       
       
        DTHelper.updateOrderColValue(this.table, 1);
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
            
            datas[i]['nameInput'] = partBase_getNameInput(this.tableId, rowId,  datas[i]['name']), 
            datas[i]['detailInput'] = partBase_getDetailInput(this.tableId, rowId,  datas[i]['detail']),
            datas[i]['phoneInput'] = partBase_getPhoneInput(this.tableId, rowId,  datas[i]['phone']), 
            datas[i]['addressInput'] = partBase_getAddressInput(this.tableId, rowId,  datas[i]['address']), 
            
            datas[i]['delete'] = '<a class="delete" href="">ลบ</a>';
            
            this.table.fnAddData( datas[i]);              
        }
           

        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
                
        for ( var i = 0; i < datas.length; i++) {
            var rowId = datas[i]['rowId'];
             
            datas[i]['name'] = $("#"+this.tableId+"-name-"+rowId).val();
            datas[i]['nameInput'] = "";      
            datas[i]['detail'] = $("#"+this.tableId+"-detail-"+rowId).val();
            datas[i]['detailInput'] = "";                          
            datas[i]['phone'] = $("#"+this.tableId+"-phone-"+rowId).val();
            datas[i]['phoneInput'] = "";              
            datas[i]['address'] = $("#"+this.tableId+"-address-"+rowId).val();
            datas[i]['addressInput'] = "";             
            datas[i]['delete'] = "";
        }
        return datas;
    },
    
    debug: function() {
    }
};

function partBase_getNameInput(tableId, rowId, val) {
        var inputId = tableId +"-name-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>
        return "<input class='textInput' type='text' style='width:150px'  id='" + inputId + "' value='" + val +"' />";            
}    
    
function partBase_getDetailInput(tableId, rowId, val) {
        var inputId = tableId +"-detail-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>        
        return "<input class='textInput' type='text' style='width:100px'  id='" + inputId + "' value='" + val +"' />";            
}    

function partBase_getPhoneInput(tableId, rowId, val) {
        var inputId = tableId +"-phone-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>        
        return "<input class='textInput' type='text' style='width:100px'  id='" + inputId + "' value='" + val +"' />";            
}   

function partBase_getAddressInput(tableId, rowId, val) {
        var inputId = tableId +"-address-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>        
        return "<textarea class='textInput' type='text' style='width:270px'  id='" + inputId + "' >" + val +"</textarea>";            
}   


@endsection



@section('partBaseJs')

  
    window.partBase_relateTable  = $('#partBase_relateTable').dataTable( 
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
                        { "mData": "nameInput" },                               
                        { "mData": "detailInput" },
                        { "mData": "phoneInput" },
                        { "mData": "addressInput" },                        
                        { "mData": "delete"}
                  
                ]
	             
            }
    );
    
    $('#partBase_relateTableAddLink').click(function(e) {
        e.preventDefault();
        ReateTableHelper.addData("");
    });

    $('#partBase_relateTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partBase_relateTable, "rowId", key);
        DTHelper.updateOrderColValue(partBase_relateTable, 1);
    } ); 
    
    ReateTableHelper.setTable(window.partBase_relateTable, 'partBase_relateTable');
    ReateTableHelper.addDataFromServer(<?php echo json_encode($partBase_relateDatas) ?>);
    
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partBase_relateTable,"delete", false, true);   
        $('#partBase_relateTableAddLink').hide();
     <?php endif; ?>  

        
    window.partBase_uploader = new BatchUploader( {
        uploaderName: "partBase_uploader", containerId: "partBase_fileContainer",
        enableInfo: true,  mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    
    partBase_uploader.addDataStringFromServer(<?php echo $partBase_fileDatas?>);  
    
    
@endsection


@section('partBaseSubmit')

   document.mainForm.partBase_relateDatas.value = $.toJSON(ReateTableHelper.getDataForSubmit());  
   document.mainForm.partBase_fileDatas.value = $.toJSON(partBase_uploader.getDataStringForSubmit());     
            
@endsection




@section('partBaseHtml')

<input type='hidden' name='partBase_relateDatas'  />
<input type='hidden' name='partBase_fileDatas'  />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>


        <tr>
            <td class="formLabel" style='width:200px'  >รหัสพนักงาน:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $staffCode }}' name="staffCode" autocomplete="off">
        </tr>               
        <tr>
            <td class="formLabel">ชื่อ นามสกุล:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $staffName }}' name="staffName"></td>
        </tr>      
        <tr>
            <td class="formLabel">ที่อยู่:</td>
            <td><textarea class="textAreaInput" type="text" style="width:400px"  name="partBase_address">{{ $partBase_address }}</textarea></td>
        </tr>    
        <tr>
            <td class="formLabel">เบอร์โทร:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partBase_phone }}' name="partBase_phone"></td>
        </tr>           
        <tr>
            <td class="formLabel" style="padding-top: 10px">ข้อมูลผู้ติดต่อฉุกเฉิน:</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 900px; text-align: right;' >
                         <a id="partBase_relateTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partBase_relateTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay' style='width:900px' >
                     <thead>
                     <tr class='nodrop' >
                         <th  width='20' >&nbsp;</th>
                         <th  width='20' ></th>     
                         <th  width='160'>ชื่อ</th>
                         <th  width='100'>ความสัมพันธ์</th>
                         <th  width='100'>เบอร์โทร</th>
                         <th  width='280'>ที่อยู่</th>                         
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
            <td class="formLabel">สังกัด:</td>
            <td> {!! SiteHelper::dropdown("partBase_workCompany", $workCompanyOpt, $partBase_workCompany, "  id='partBase_workCompany'  class='textInput' style='width:400px'  ") !!} </td> 
        </tr>           
        <tr>
            <td class="formLabel">ชนิดพนักงาน:</td>
            <td> {!! SiteHelper::dropdown("partBase_staffType", $staffTypeOpt, $partBase_staffType, "  id='partBase_staffType'  class='textInput' style='width:400px'  ") !!} </td> 
        </tr>           
        <!--
        <tr>
            <td class="formLabel">ทะเบียนรถที่ประจำ:</td>
            <td> { !! SiteHelper::dropdown("partBase_vehicleId", $vehicleOpt, $partBase_vehicleId, "  id='partBase_vehicleId' class='textInput' style='width:400px'  ") !!  } </td> 
        </tr>   
        -->
        <tr>
            <td class="formLabel">รูปถ่ายพนักงาน:</td>
            <td><div id='partBase_fileContainer' style='padding:0px'></div></td>            
        </tr>  
       
    </tbody>
</table>



@endsection


