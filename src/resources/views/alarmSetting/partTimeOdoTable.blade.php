
@section('partTimeOdoTableHelper')

var TimeOdoTableHelper = {
    autoRunId: 9991,
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table) {
        this.table = table;
     },
     
    addData: function() {
        this.table.fnAddData(
                { 
                  "row_id": this.getAutoRunId(),
                  "counter":"",
                  "time":"",    
                  "delete": '<a class="delete" href="">ลบข้อมูล</a>'
                }
       );
        DTHelper.updateOrderColValue(this.table, 1);
    },

    addDataFromServer : function(datas) {
        if (!AppUtil.isIdExist("timeOdoTable")) return;
        
        DTHelper.clearDatas(this.table);     
        datas = AppUtil.stringToArray(datas);
      
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = {};
 
            row['counter'] = "";
            row['row_id'] = this.getAutoRunId();       
            row['time'] = datas[i];                   
            row['delete'] = '<a class="delete" href="">ลบข้อมูล</a>';
            output.push(row);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 1);
    },

    getDataForSubmit: function() {
        if (!AppUtil.isIdExist("timeOdoTable")) return "";
        
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
        var output = [];
        
        for ( var i = 0; i < datas.length; i++) {
            var time = datas[i]['time'];
            
            if (AppUtil.isNotEmpty(time)) {
                output.push( datas[i]['time'] );
            }           
        }

        return AppUtil.arrayToString(output);
    },

    updateInnerData: function(cell, indexOfUpdate, updateValue) {
        var keyValue = DTHelper.getKeyByCell(cell);
        DTHelper.updateDataByKey(this.table, "row_id", keyValue, indexOfUpdate, updateValue);
        
    },
    
    debug: function() {
    }
};



@endsection

@section('partTimeOdoTableJs')

    $.mask.definitions['h']='[0-2]';
    $.mask.definitions['m']='[0-5]';
          
    
    $.editable.addInputType('timeInput', {
        element : function(settings, original) {
            var input = $('<input type="text" id="timeInput_" class="textInput" style="width:98%;">'); 
            input.mask("h9:m9");            
            $(this).append(input);
            return(input);
        }            
    });
    
    
     window.timeOdoTable = $('#timeOdoTable').dataTable( 
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
                              { "mData": "row_id", "sClass": "forceHidden"}, 
                              { "mData": "counter", "sClass": "cellCounter", "bSortable": false },
                              { "mData": "time" , "sClass": "timeOdoTable_timeCell"},                              
                              { "mData": "delete" }
                ],

           
                "fnDrawCallback": function () {      
                    <?php if ($pageMode == "add" || $pageMode == "edit"): ?>

                    
                    $('#timeOdoTable tbody td.timeOdoTable_timeCell').editable(function(value, settings) { 
                        TimeOdoTableHelper.updateInnerData(this, "time", value);
                        return(value);
                    }, { 
                        type:'timeInput', onblur : "submit", placeholder: "", width: "98%"
                    });    

                    <?php endif ?>
                }	             
            }
    );
    
    TimeOdoTableHelper.setTable(window.timeOdoTable);
    TimeOdoTableHelper.addDataFromServer("<?php echo $alarmTimeForCheckOdo?>");
    
    $('#addTimeOdoLink').click(function(e) {
        e.preventDefault();
        TimeOdoTableHelper.addData();
    });


    $('#timeOdoTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(timeOdoTable, "row_id", key);
        DTHelper.updateOrderColValue(timeOdoTable, 1);
    } ); 
    
    
    <?php if ($pageMode == 'edit'): ?>


    <?php elseif ($pageMode == 'view'): ?>
    
    
        DTHelper.setColumnVisible(timeOdoTable,"delete", false, true);
        $('#addTimeOdoLink').hide();
        
    <?php endif; ?>
    

@endsection


@section('partTimeOdoTableHtml')

              <div style='padding:0px 20px 1px 0px'>
                     <div style='float: left; width: 700px; text-align: right;' id='addTimeOdoCont'>
                        <a id="addTimeOdoLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                     </div>
                     <div style='clear: both'></div>
                </div>

              
                <table id='timeOdoTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:700px' >
                    <thead>
                    <tr class='nodrop' >
                        <th  width='20' >&nbsp;</th>
                        <th  width='20' ></th>     
                        <th  width='300'>เวลา</th>
                        <th  width='100'>ลบ</th>
                    </tr
                    </thead>
                    <tbody>
                    </tbody>
                </table>
          

@endsection
