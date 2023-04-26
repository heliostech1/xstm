
@section('partTimeTableHelper')

var TimeTableHelper = {
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
        if (!AppUtil.isIdExist("timeTable")) return;
        
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
        if (!AppUtil.isIdExist("timeTable")) return "";
        
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

@section('partTimeTableJs')

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
    
    
            window.timeTable = $('#timeTable').dataTable( 
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
                              { "mData": "time" , "sClass": "timeTable_timeCell"},                              
                              { "mData": "delete" }
                ],

           
                "fnDrawCallback": function () {      
                    <?php if ($pageMode == "add" || $pageMode == "edit"): ?>

                    
                    $('#timeTable tbody td.timeTable_timeCell').editable(function(value, settings) { 
                        TimeTableHelper.updateInnerData(this, "time", value);
                        return(value);
                    }, { 
                        type:'timeInput', onblur : "submit", placeholder: "", width: "98%"
                    });    

                    <?php endif ?>
                }	             
            }
    );
    
    TimeTableHelper.setTable(window.timeTable);
    TimeTableHelper.addDataFromServer("<?php echo $alarmTimeForCheckDate?>");
    
    $('#addTimeLink').click(function(e) {
        e.preventDefault();
        TimeTableHelper.addData();
    });


    $('#timeTable').on('click', 'a.delete', function (e) {
        e.preventDefault();
        
        var key = DTHelper.getKeyByCell(this, 0);
        DTHelper.deleteRowByKey(timeTable, "row_id", key);
        DTHelper.updateOrderColValue(timeTable, 1);
    } ); 
    
    
    <?php if ($pageMode == 'edit'): ?>


    <?php elseif ($pageMode == 'view'): ?>
    
    
        DTHelper.setColumnVisible(timeTable,"delete", false, true);
        $('#addTimeLink').hide();
        
    <?php endif; ?>
    

@endsection


@section('partTimeTableHtml')

              <div style='padding:0px 20px 1px 0px'>
                     <div style='float: left; width: 700px; text-align: right;' id='addTimeCont'>
                        <a id="addTimeLink" href="javascript:void(0);" >เพิ่มข้อมูล</a> 
                     </div>
                     <div style='clear: both'></div>
                </div>

            
                <table id='timeTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:700px' >
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
