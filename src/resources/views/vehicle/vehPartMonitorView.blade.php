@section('partMonitorViewHelper')



var PartMonitorViewTableHelper = {
    autoRunId: 9991,

    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },
    
    setTable: function(table, tableId) {
        this.table = table;
        this.tableId = tableId;
     },
     

    addDataFromServer : function(datas) {
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = datas[i];
            row["rowId"] = this.getAutoRunId();   
            row["counterColumn"] = i+1;
            output.push(row);
        }
           
        this.table.fnAddData(output);

    },

 
    
    debug: function() {
    }
    
};


@endsection



@section('partMonitorViewJs')

    window.partMonitorView_monitorTable = $('#partMonitorView_monitorTable').dataTable( // make it global
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
                              { "mData": "dataTypeDesc"  },
                              { "mData": "warnAmount"  },
                              { "mData": "alertAmount"  },     
                              
                              { "mData": "lastRepairDate"  },
                              { "mData": "lastRepairOdo"  }, 
                              
                              { "mData": "warnAt"  },
                              { "mData": "warnStatus"  },   
                              
                              { "mData": "alertAt"  },
                              { "mData": "alertStatus"  },                               
                ],          
            
            }
    );
        


    PartMonitorViewTableHelper.setTable(window.partMonitorView_monitorTable, 'partMonitorView_monitorTable');
    PartMonitorViewTableHelper.addDataFromServer( <?php echo json_encode($partMonitorView_monitorDatas) ?> );


@endsection


@section('partMonitorViewSubmit')

@endsection



@section('partMonitorViewHtml')

<input type='hidden' name='partMonitorView_monitorDatas'  />

            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ</td>
            <td style=text-align:left><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ</td>
            <td style=text-align:left><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
        <tr>
            <td class="formLabel" style='width:200px' >เลขไมล์</td>
            <td style=text-align:left><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $odometer }}'  autocomplete="off">
        </tr>  
<?php endif; ?>   
        <tr>
            <td class="formLabel"  style='width:200px' >แผนซ่อมบำรุง</td>
            <td> {!! SiteHelper::dropdown("partMonitorView_monitorPlan", $monitorPlanOpt, $partMonitor_monitorPlan, "  id='Two-oneone' class='textInput' style='width:400px'  ") !!} </td> 
        </tr> 
  
    </tbody>
</table>

<br/>

<div class='customTableStyle' > 
<table id='partMonitorView_monitorTable' cellspacing='0' cellpadding='0'  class='display'  >
    <thead>
        <tr class='nodrop' >
           <th  rowspan="2" width='15'>&nbsp;</th>        
           <th  rowspan="2" width='15'>&nbsp;</th>  
           <th  rowspan="2" width='150'>ห้วข้อการซ่อมบำรุง</th> 
           <th  rowspan="2" width='150'>ชื่อ</th> 
     <!--      <th  rowspan="2" width='100'>รหัส</th>    -->   
           <th  rowspan="2" width='100'>ชนิดข้อมูล</th>     
           <th  rowspan="2" width='100'>แจ้ง Warning ทุกๆ</th>  
           <th  rowspan="2"  width='100' style='border-right:1px solid black'>แจ้ง Alert ทุกๆ</th>  

           <th colspan="2" style='text-align:center;border-right:1px solid black'>ข้อมูลซ่อมครั้งล่าสุด</th>      
           <th colspan="2" style='text-align:center;border-right:1px solid black'>แจ้งเตือน Warning</th>      
           <th colspan="2" style='text-align:center'>แจ้งเตือน Alert</th>                  

        </tr>
        <tr>
            <th width='100'>วันที่</th>        
            <th width='100' style='border-right:1px solid black'>เลขไมล์</th>  
          
            <th width='120'  >เมื่อมีค่า</th>        
            <th width='120' style='border-right:1px solid black'>สถานะ</th>  
            
            <th width='120' >เมื่อมีค่า</th>        
            <th width='120'>สถานะ</th>    
            
        </tr> 
        
    </thead>
    <tbody>
    </tbody>
</table>
</div>

        
    
      
    
@endsection
