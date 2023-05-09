@section('partCareHelper')


var PartCareBaseDriverTableHelper = function( ) {};

PartCareBaseDriverTableHelper.prototype = {

    setTable: function(table) {
        this.table = table;
     },
     
                        
    addData: function(datas) {
        for (var i = 0; i < datas.length; i++) {
           var data = datas[i];

            //console.log(data);
            if (!DTHelper.isExist(this.table, data['mongoId'], 'staffId') ) {
                this.table.fnAddData(
                        { 
                          "staffId": data['mongoId'],
                          "counterColumn":"", 
                          "staffName": data['staffName'], 
                          "phone": data['phone'], 
                          "workCompanyDesc": data['workCompany'],
                          "workCompanyId": data['workCompanyId'],
                          "delete": '<a class="delete" href="">ลบ</a>'
                        }
                );
            }
        }

        DTHelper.updateOrderColValue(this.table, 1);
    },

    addDataFromServer : function(datas) {
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        for ( var i = 0; i < datas.length; i++) {
            datas[i]['counterColumn'] = "";
            datas[i]['delete'] = '<a class="delete" href="">ลบ</a>';
        }
           
        this.table.fnAddData(datas);
        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
                
        for ( var i = 0; i < datas.length; i++) {
            datas[i]['delete'] = "";
        }

        
        return datas;
    },
    
 
    
    debug: function() {
    }
    
};


var PartCareDriverTableHelper = new PartCareBaseDriverTableHelper();
var PartCareWorkerTableHelper = new PartCareBaseDriverTableHelper();


@endsection



@section('partCareJs')

    window.partCare_driverTable = $('#partCare_driverTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "sDom": 'lrtip',
                "bPaginate": false,
                "bFilter": true,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
         
                "aoColumns": [
                              { "mData": "staffId" , "sClass": "forceHidden"},   
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "staffName" },                               
                              { "mData": "phone" },
                              { "mData": "workCompanyDesc" },
                              { "mData": "delete" },
                ],          
            
            }
        );
        
    window.partCare_workerTable = $('#partCare_workerTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "sDom": 'lrtip',
                "bPaginate": false,
                "bFilter": true,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
         
                "aoColumns": [
                              { "mData": "staffId" , "sClass": "forceHidden"},   
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },                                
                              { "mData": "staffName" },                               
                              { "mData": "phone" },
                              { "mData": "workCompanyDesc" },
                              { "mData": "delete" },
                ],          
            
            }
        );

    $('#partCare_driverTableAddLink').click(function(e) {
        e.preventDefault();
        popupCare_openPopupForAdd( function(data) { PartCareDriverTableHelper.addData(data) } );
    });
    
    $('#partCare_workerTableAddLink').click(function(e) {
        e.preventDefault();
        popupCare_openPopupForAdd( function(data) { PartCareWorkerTableHelper.addData(data) } );
    });    


    $('#partCare_driverTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partCare_driverTable, "staffId", key);
        DTHelper.updateOrderColValue(partCare_driverTable, 1);
    } );
    
    $('#partCare_workerTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partCare_workerTable, "staffId", key);
        DTHelper.updateOrderColValue(partCare_workerTable, 1);
    } ); 
    

    PartCareDriverTableHelper.setTable(window.partCare_driverTable);
    PartCareDriverTableHelper.addDataFromServer(<?php echo json_encode($partCare_driverDatas) ?>);
    
    PartCareWorkerTableHelper.setTable(window.partCare_workerTable);
    PartCareWorkerTableHelper.addDataFromServer(<?php echo json_encode($partCare_workerDatas) ?>);
    

     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partCare_driverTable,"delete", false, true); 
         DTHelper.setColumnVisible(partCare_workerTable,"delete", false, true); 
         
        $('#partCare_driverTableAddLink').hide();
        $('#partCare_workerTableAddLink').hide();        
     <?php endif; ?> 
        
        
@endsection


@section('partCareSubmit')
   
   document.mainForm.partCare_driverDatas.value = $.toJSON(PartCareDriverTableHelper.getDataForSubmit());  
   document.mainForm.partCare_workerDatas.value = $.toJSON(PartCareWorkerTableHelper.getDataForSubmit());     
   
@endsection



@section('partCareHtml')

<input type='hidden' name='partCare_driverDatas'  />
<input type='hidden' name='partCare_workerDatas'  />
            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ</td>
            <td><input class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   
        <tr>
            <td class="formLabel"  style='width:200px' >ชื่อผู้ให้บริการ</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partCare_vehicleCare", $vehicleCareOpt, $partCare_vehicleCare, "  class='textInput' style='width:400px' id=Two-oneone  ") !!} </td> 
        </tr> 
        <tr>
            <td class="formLabel">ชนิดของรถ</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partCare_vCareType", $vCareTypeOpt, $partCare_vCareType, "  class='textInput' style='width:400px'  id=Two-oneone ") !!} </td> 
        </tr> 
        
        
        <tr>
            <td class="formLabel" style="padding-top: 10px">ผู้ชับขี่ประจำรถ</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 800px; text-align: right;' >
                         <a id="partCare_driverTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partCare_driverTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:800px' >
                     <thead>
                     <tr class='nodrop' >
                        <th  width='15'>&nbsp;</th>   
                        <th  width='15'>&nbsp;</th>       
                        <th  width='100'>ชื่อ นามสกุล</th>   
                        <th  width='100'>เบอร์โทร</th>
                        <th  width='100'>สังกัด</th>
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
            <td class="formLabel" style="padding-top: 10px">แรงงานประจำรถ</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 800px; text-align: right;' >
                         <a id="partCare_workerTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partCare_workerTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:800px' >
                     <thead>
                     <tr class='nodrop' >
                        <th  width='15'>&nbsp;</th>   
                        <th  width='15'>&nbsp;</th>       
                        <th  width='100'>ชื่อ นามสกุล</th>   
                        <th  width='100'>เบอร์โทร</th>
                        <th  width='100'>สังกัด</th>
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
