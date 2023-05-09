@section('partFuelHelper')


var GasDatasTableHelper = {
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
  
                  "number": tankNumber,
                  "numberInput": partFuel_getNumberInput(this.tableId, rowId, tankNumber),                    
                  "regisDate":"",
                  "regisDateInput": partFuel_getRegisDateInput(this.tableId, rowId),  
                  "expDate":"",
                  "expDateInput": partFuel_getExpDateInput(this.tableId, rowId),
                  "delete": '<a class="delete" href="">ลบ</a>'
                }                
        );
       
        
        $("#"+this.tableId+"-regisDate-" + rowId).datepicker();
        $("#"+this.tableId+"-expDate-" + rowId).datepicker();
       
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
            
            datas[i]['numberInput'] = partFuel_getNumberInput(this.tableId, rowId,  datas[i]['number']), 
            datas[i]['regisDateInput'] = partFuel_getRegisDateInput(this.tableId, rowId,  datas[i]['regisDate']),
            datas[i]['expDateInput'] = partFuel_getExpDateInput(this.tableId, rowId,  datas[i]['expDate']), 

            datas[i]['delete'] = '<a class="delete" href="">ลบ</a>';
            
            this.table.fnAddData( datas[i]);
            $("#"+this.tableId+"-regisDate-" + rowId).datepicker();   
            $("#"+this.tableId+"-expDate-" + rowId).datepicker();               
        }
           

        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
                
        for ( var i = 0; i < datas.length; i++) {
            var rowId = datas[i]['rowId'];
             
            datas[i]['number'] = $("#"+this.tableId+"-number-"+rowId).val();
            datas[i]['numberInput'] = "";      
            datas[i]['regisDate'] = $("#"+this.tableId+"-regisDate-"+rowId).val();
            datas[i]['regisDateInput'] = "";                          
            datas[i]['expDate'] = $("#"+this.tableId+"-expDate-"+rowId).val();
            datas[i]['expDateInput'] = "";                         
            datas[i]['delete'] = "";
        }
        return datas;
    },
    
    debug: function() {
    }
};

function partFuel_getNumberInput(tableId, rowId, val) {
        var inputId = tableId +"-number-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>
        return "<input class='textInput' type='text' style='width:150px'  id='" + inputId + "' value='" + val +"' />";            
}    
    
function partFuel_getRegisDateInput(tableId, rowId, val) {
        var inputId = tableId +"-regisDate-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>        
        return "<input class='textInput' type='text' style='width:90px'  id='" + inputId + "' value='" + val +"' />";            
}    

function partFuel_getExpDateInput(tableId, rowId, val) {
        var inputId = tableId +"-expDate-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";
        <?php if ($pageMode == 'view'): ?>  return val; <?php endif; ?>        
        return "<input class='textInput' type='text' style='width:90px'  id='" + inputId + "' value='" + val +"' />";            
}   

@endsection



@section('partFuelJs')

  
    window.partFuel_gasTankTable  = $('#partFuel_gasTankTable').dataTable( 
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
                        { "mData": "numberInput" },                               
                        { "mData": "regisDateInput" },
                        { "mData": "expDateInput" },
                        { "mData": "delete"}
                  
                ]
	             
            }
    );
    
    $('#partFuel_gasTankTableAddLink').click(function(e) {
        e.preventDefault();
        GasDatasTableHelper.addData("");
    });

    $('#partFuel_gasTankTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(partFuel_gasTankTable, "rowId", key);
        DTHelper.updateOrderColValue(partFuel_gasTankTable, 1);
    } ); 
    
    GasDatasTableHelper.setTable(window.partFuel_gasTankTable, 'partFuel_gasTankTable');
    GasDatasTableHelper.addDataFromServer(<?php echo json_encode($partFuel_gasDatas) ?>);
    
    
    
    <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
       $('#partFuel_certDate').datepicker();
       $('#partFuel_certExpDate').datepicker();       
       
     <?php endif; ?>  

       
     <?php if ($pageMode == 'view'): ?>  
         DTHelper.setColumnVisible(partFuel_gasTankTable,"delete", false, true);   
        $('#partFuel_gasTankTableAddLink').hide();
     <?php endif; ?>  

        
    $('#partFuel_gasTankTableAutoLink').click(function(e) {
        e.preventDefault();
        
        var datas = PartRegisTableHelper.getDataForSubmit();
        var tankNumber = DTHelper.getLastRowCellData(datas, 'gasTankNumber');           
        var numbers = AppUtil.stringToArray(tankNumber);
        
        for (var i=0; i < numbers.length; i++) {
            if (! DTHelper.isExist( partFuel_gasTankTable, numbers[i], 'number' )) {
                GasDatasTableHelper.addData(numbers[i]);
            }
        }
        
    });    

    
@endsection


@section('partFuelSubmit')
   document.mainForm.partFuel_gasDatas.value = $.toJSON(GasDatasTableHelper.getDataForSubmit());  
@endsection



@section('partFuelHtml') 
            
<input type='hidden' name='partFuel_gasDatas'  />
       
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
        <tr >
            <td class="formLabel" style='width:200px' >ชนิดน้ำมันเชื้อเพลิง</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partFuel_oilType", $fuelOilOpt, $partFuel_oilType, "  class='textInput' style='width:400px; ' id=Two-oneoneFuel ") !!} </td> 
        </tr> 
        <tr>
            <td class="formLabel">ปริมาตรความจุถังน้ำมัน(ลิตร)</td>
            <td style='text-align:left'><input id= Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_oilTankSize }}' name="partFuel_oilTankSize">
            </td>
        </tr>   
        <tr>
            <td class="formLabel">ชนิดแก๊สเชื้อเพลิง</td>
            <td style='text-align:left'> {!! SiteHelper::dropdown("partFuel_gasType", $fuelGasOpt, $partFuel_gasType, "  class='textInput' style='width:400px' id=Two-oneoneFuel  ") !!} </td> 
        </tr>   
        <tr>
            <td class="formLabel">จำนวนถังแก๊ส</td>
            <td style='text-align:left'><input id=Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_gasCount }}' name="partFuel_gasCount">
            </td>
        </tr>          
        <tr>
            <td class="formLabel">ปริมาตรความจุถังแก๊สรวม</td>
            <td style='text-align:left'><input id=Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_gasTotalSize }}' name="partFuel_gasTotalSize">
            </td>
        </tr>     
        
        <tr>
            <td class="formLabel" style="padding-top: 10px">ข้อมูลถังแก๊ส</td>
            <td>
                 <div style='padding:0px 20px 1px 0px'>
                      <div style='float: left; width: 600px; text-align: right;' >
                          
                         <a id="partFuel_gasTankTableAutoLink" href="javascript:void(0);" >เติมอัตโนมัติ</a>  
                         &nbsp;
                         <a id="partFuel_gasTankTableAddLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                      </div>
                      <div style='clear: both'></div>
                 </div>

                 <div class='customTableStyle' > 
                 <table id='partFuel_gasTankTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:600px' >
                     <thead>
                     <tr class='nodrop' >
                         <th  width='20' >&nbsp;</th>
                         <th  width='20' ></th>     
                         <th  width='170'>หมายเลขถัง</th>
                         <th  width='120'>วันจดทะเบียน</th>
                         <th  width='120'>วันหมดอายุ</th>
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
            <td class="formLabel" style="padding-top: 10px"><b>ข้อมูลใบรับรองวิศวกร</b></td>
            <td></td>
        </tr>          
        <tr>
            <td class="formLabel">ผู้รับรอง</td>
            <td style='text-align:left'><input id=Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_certBy }}' name="partFuel_certBy">
            </td>
        </tr>  
        <tr>
            <td class="formLabel">วันที่ทำใบรับรอง</td>
            <td style='text-align:left'><input id=Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_certDate }}' name="partFuel_certDate" id="partFuel_certDate"  autocomplete="off" >
            </td>
        </tr> 
        <tr>
            <td class="formLabel">วันหมดอายุ</td>
            <td style='text-align:left'><input id=Two-oneoneFuel class="textInput" type="text" style="width:400px" value='{{ $partFuel_certExpDate }}' name="partFuel_certExpDate" id="partFuel_certExpDate"  autocomplete="off" >
            </td>
        </tr>     
        <tr>
            <td class="formLabel">ภาพข้อมูลใบรับรอง</td>
            <td><div id='partFuel_fileContainer' style='padding:0px'></div></td>            
        </tr>         
    </tbody>
</table>

@endsection
