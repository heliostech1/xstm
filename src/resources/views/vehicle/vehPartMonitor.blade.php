@section('partMonitorHelper')



var PartMonitorTableHelper = {
    autoRunId: 9991,
    allValueDatas : null,
    allPlanDatas: null,
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },
    
    setTable: function(table, tableId) {
        this.table = table;
        this.tableId = tableId;
     },
     
    setInitDatas: function(allValueDatas, allPlanDatas) {
        this.allValueDatas = allValueDatas;
        this.allPlanDatas = allPlanDatas;
     },
     
    getPlanDatasById: function(planId) {
        if (AppUtil.isNotEmpty(planId) && AppUtil.isNotEmpty(this.allPlanDatas) ) {
        
           for (var i = 0; i < this.allPlanDatas.length; i++) {
               if (this.allPlanDatas[i].mongoId == planId) {
                   return this.allPlanDatas[i].detailDatas;
               }
           }
        }
    
        return [];
    },
    
    getValueDatasByTopic: function(monitorTopic) {
        if (AppUtil.isNotEmpty(monitorTopic) && AppUtil.isNotEmpty(this.allValueDatas) ) {
        
           for (var i = 0; i < this.allValueDatas.length; i++) {
               if (this.allValueDatas[i].monitorTopic == monitorTopic) {
                   return this.allValueDatas[i];
               }
           }
        }
    
        return [];
    },
    
    
    updateDatasByPlan : function() {
        DTHelper.clearDatas( this.table); 
    
        var planId = $( "#partMonitor_monitorPlan" ).val();
        var planDatas = this.getPlanDatasById(planId);

        for ( var i = 0; i < planDatas.length; i++) {
            var rowId = this.getAutoRunId();  

            var valueData = this.getValueDatasByTopic(planDatas[i].monitorTopic);
            var warnAt = (valueData.warnAt)? valueData.warnAt: "";
            var alertAt =  (valueData.alertAt)? valueData.alertAt: "";
            
            this.table.fnAddData(
                { 
                   "rowId": rowId,
                   "counterColumn":"",
                   "monitorTopic":  planDatas[i].monitorTopic,
                   "itemName": planDatas[i].itemName,
                   "itemCode": planDatas[i].itemCode,
                   "dataTypeDesc": planDatas[i].dataTypeDesc,

                   "warnAmount": planDatas[i].warnAmount,
                   "alertAmount": planDatas[i].alertAmount,
                   "warnAt": warnAt,
                   "alertAt": alertAt,
                  
                   "warnAtInput": partMonitor_getWarnAtInput(this.tableId, rowId, warnAt),  
                   "alertAtInput": partMonitor_getAlertAtInput(this.tableId, rowId, alertAt), 
                }                
            );
            
            if (planDatas[i].dataType == "date") {
                $("#"+this.tableId+"-warnAt-" + rowId).datepicker();
                $("#"+this.tableId+"-alertAt-" + rowId).datepicker();
            }
            
        }
           

        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        var datas = this.table.fnGetData();
        var output = [];
        
        for ( var i = 0; i < datas.length; i++) {
            var rowId = datas[i]['rowId'];
            
            var dataRow = {
               "monitorTopic": datas[i]['monitorTopic'],
               "warnAt": $("#"+this.tableId+"-warnAt-"+rowId).val(),
               "alertAt": $("#"+this.tableId+"-alertAt-"+rowId).val(),
            }
            
            output.push(dataRow);
        }

        
        return output;
    },
    
 
    
    debug: function() {
    }
    
};

function partMonitor_getWarnAtInput(tableId, rowId, val) {
        var inputId = tableId +"-warnAt-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>
        return "<input class='textInput' type='text' style='width:120px'  id='" + inputId + "' value='" + val +"' />";            
}  

function partMonitor_getAlertAtInput(tableId, rowId, val) {
        var inputId = tableId +"-alertAt-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>
        return "<input class='textInput' type='text' style='width:120px'  id='" + inputId + "' value='" + val +"' />";            
}  

@endsection



@section('partMonitorJs')

    window.partMonitor_monitorTable = $('#partMonitor_monitorTable').dataTable( // make it global
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
                              { "mData": "rowId" , "sClass": "forceHidden"},                
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  

                              { "mData": "monitorTopic"}, 
                              { "mData": "itemName"},                              
                      //        { "mData": "itemCode"  },         
                              { "mData": "dataTypeDesc"  },
                              { "mData": "warnAmount"  },
                              { "mData": "alertAmount"  },     
                              
                              { "mData": "warnAtInput"  },
                              { "mData": "alertAtInput"  },                               
                ],          
            
            }
    );
        


    PartMonitorTableHelper.setTable(window.partMonitor_monitorTable, 'partMonitor_monitorTable');
    PartMonitorTableHelper.setInitDatas( <?php echo json_encode($partMonitor_monitorDatas) ?>, <?php echo $monitorPlanDatas ?>);
    
    
    PartMonitorTableHelper.updateDatasByPlan();

    $( "#partMonitor_monitorPlan" ).change(function() {
       PartMonitorTableHelper.updateDatasByPlan();
    });

        
        
@endsection


@section('partMonitorSubmit')
   
   document.mainForm.partMonitor_monitorDatas.value = $.toJSON(PartMonitorTableHelper.getDataForSubmit());  
     
   
@endsection



@section('partMonitorHtml')

<input type='hidden' name='partMonitor_monitorDatas'  />

            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ</td>
            <td style='text-align:left'><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ</td>
            <td style='text-align:left'><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   
        <tr>
            <td class="formLabel"  style='width:200px' >แผนซ่อมบำรุง</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partMonitor_monitorPlan", $monitorPlanOpt, $partMonitor_monitorPlan, "  id='partMonitor_monitorPlan' class='textInput' style='width:400px'   ") !!} </td> 
        </tr> 
  
        
        <tr>
            <td class="formLabel" style="padding-top: 10px">ข้อมูลซ่อมบำรุง</td>
            <td>

                 <div class='customTableStyle' > 
                 <table id='partMonitor_monitorTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'  >
                     <thead>
                     <tr class='nodrop' >
                        <th  width='15'>&nbsp;</th>        
                        <th  width='15'>&nbsp;</th>  
                        <th  width='200'>ห้วข้อการซ่อมบำรุง</th> 
                        <th  width='180'>ชื่อ</th> 
                  <!--      <th  width='100'>รหัส</th>    -->   
                        <th  width='100'>ชนิดข้อมูล</th>     
                        <th  width='100'>แจ้ง Warning ทุกๆ</th>  
                        <th  width='100'>แจ้ง Alert ทุกๆ</th>  

                        <th  width='150'>เริ่มแจ้ง Warning เมื่อค่าถึง</th>  
                        <th  width='150'>เริ่มแจ้ง Alert เมื่อค่าถึง</th>  
                        
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
