
var MySimpleDataListTable = function(tableId, mode) {
    this.init(tableId, mode);
};

MySimpleDataListTable.prototype = {

    autoRunId: 9991,
    mode : 'add',
    
    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

     
    addData: function() {
        var rowId = this.getAutoRunId();
        this.table.fnAddData(
                { 
                  "rowId": rowId,
                  "counter":"", 
                  "detailInput": this.getDetailInput(this.tableId, rowId),                   
                  "delete": '<a class="delete" href="">ลบ</a>'
                }
       );
        DTHelper.updateOrderColValue(this.table, 1);
    },

    addDataFromServer : function(datas) {
        DTHelper.clearDatas(this.table);     
        datas = AppUtil.stringToArray(datas);
      
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var rowId = this.getAutoRunId();
            var row = {};
 
            row['counter'] = "";
            row['rowId'] = rowId;       
            row['detailInput'] = this.getDetailInput(this.tableId, rowId,  datas[i]);      
            row['delete'] = '<a class="delete" href="">ลบ</a>';
            output.push(row);
        }
           
        this.table.fnAddData(output);
        DTHelper.updateOrderColValue(this.table, 1);
    },

    clearDatas: function() {
       this.table.fnClearTable();    
    },
    
    getDataForSubmit: function() {
        var rawDatas = this.table.fnGetData();
        var datas  = AppUtil.cloneObject(rawDatas); // clone
        var output = [];
        
        for ( var i = 0; i < datas.length; i++) {
            var rowId = datas[i]['rowId'];
            var detail = $("#"+this.tableId+"-detail-"+rowId).val();

            if (AppUtil.isNotEmpty(detail)) {
                output.push( detail );
            }           
        }

        return AppUtil.arrayToString(output);
    },

    updateInnerData: function(cell, indexOfUpdate, updateValue) {
        var keyValue = DTHelper.getKeyByCell(cell);
        DTHelper.updateDataByKey(this.table, "rowId", keyValue, indexOfUpdate, updateValue);
        
    },
    
    
    getDetailInput: function(tableId, rowId, val) {
        var inputId = tableId +"-detail-" + rowId;
        val = AppUtil.isNotEmpty(val)? val:"";  
        if (this.mode == "view") return val;
        return "<input class='textInput' type='text' style='width:300px'  id='" + inputId + "' value='" + val +"' />";            
    },


    init: function(tableId, mode) {
        this.mode = mode;
        
        var mDataDelete = { "mData": "delete"};
        if (mode == "view") {
            mDataDelete = { "mData": "delete", "sClass": "forceHidden"};
        }
        
        
        var dataTable = $('#' + tableId).dataTable( 
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
                                  { "mData": "rowId", "sClass": "forceHidden"}, 
                                  { "mData": "counter", "sClass": "cellCounter", "bSortable": false },
                                  { "mData": "detailInput"},                              
                                  mDataDelete,
                    ]
            }
        );
      
        this.table = dataTable;
        this.tableId = tableId;
            
            
        $('#' + tableId).on('click', 'a.delete', function (e) {
            e.preventDefault();

            var key = DTHelper.getKeyByCell(this, 0);
            DTHelper.deleteRowByKey(dataTable, "rowId", key);
            DTHelper.updateOrderColValue(dataTable, 1);
        } ); 

 
        
        //return dataTable;
    },
            
            
    debug: function() {
    }
    
};


