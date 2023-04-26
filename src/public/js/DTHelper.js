/*
 *  DataTable Helper
 */

var DTHelper = {
    
    tablePrefixId:  "bsisDatatable",
    
    getDisplayStart: function(oTable) {        
        var oSettings = oTable.fnSettings();
        return oSettings._iDisplayStart ;        
    },
    
    defineCustomSort : function() {
        jQuery.fn.dataTableExt.oSort['empty-last-asc']  = function(x,y) {
            if ( y == "")return -1;
            return ((x < y) ? -1 : ((x > y) ?  1 : 0));
        };
        
        jQuery.fn.dataTableExt.oSort['empty-last-desc'] = function(x,y) {
            return ((x < y) ?  1 : ((x > y) ? -1 : 0));
        };
    },
    
    createPagingDatatable: function( tableId, pageId, option ) {
        
        option.oLanguage = DTHelper.thaiLang;
        option.aLengthMenu = DTHelper.getLengthMenu();
        option.bStateSave = true;                
        option.fnStateSave = DTHelper.getFnStateSave( pageId );
        option.fnStateLoad = DTHelper.getFnStateLoad( pageId );                  
        option.sPaginationType = "full_numbers";
        option.bProcessing = true;
        option.bAutoWidth = false;            
        option.iTabIndex = -1;
        
        if (!option.hasOwnProperty('sDom') ) {
            option.sDom = 'lrtip';
        }
        
        if (!option.hasOwnProperty('bServerSide') ) {
            option.bServerSide = true;
        }
        
        var oTable =  $('#' + tableId).dataTable(option);
        
        //==================================
        $('#' + tableId + "_down").click( function() {
            DTHelper.selectDown(oTable);            
        } );
        
        $('#' + tableId + "_down").keydown(function(e) { 
            if (e.keyCode == 13) DTHelper.selectDown(oTable);  // enter
            if (e.keyCode == 37 || e.keyCode == 39 )  $('#' + tableId + "_up").focus(); // arrow left right  
        });
        
        $('#' + tableId + "_up").click( function() {
            DTHelper.selectUp(oTable);
        } );
        
        $('#' + tableId + "_up").keydown(function(e) { 
            if (e.keyCode == 13) DTHelper.selectUp(oTable);  
            if (e.keyCode == 37 || e.keyCode == 39 )  $('#' + tableId + "_down").focus(); // arrow left right              
        });
        
        return oTable;
    },
    
        
    thaiLang : {
        "sLengthMenu" : "แสดง _MENU_ รายการ/หน้า",
        "sZeroRecords" : "ไม่พบรายการใดๆ",
        "sInfo" : "แสดงรายการที่ _START_ -  _END_ จากทั้งหมด _TOTAL_ ",
        "sInfoEmpty" : "ไม่พบรายการ",
        "sInfoFiltered" : "(ถูกกรองจากทั้งหมด _MAX_ รายการ)",
        "sProcessing": "กำลังโหลด...",        
        "oPaginate" : {
            "sFirst" : "&lt;&lt;",
            "sLast" : "&gt;&gt;",
            "sNext" : "&gt;",
            "sPrevious" : "&lt;"
        }
    },   
    
    getLengthMenu: function() {
        return [10, 15, 20, 25, 30, 40, 50, 100];        
    },
        
    getFnStateSave: function(pageId) {             
        return function (oSettings, oData) {
            var uniqueId =  DTHelper.tablePrefixId + "_" + pageId + "_" + oSettings.sInstance;
            AppUtil.setStorageObject( uniqueId, oData);
        };
    },
    
    getFnStateLoad: function(pageId) {
        return function ( oSettings ) {
            var uniqueId =  DTHelper.tablePrefixId + "_" + pageId + "_" + oSettings.sInstance;    
            return AppUtil.getStorageObject(uniqueId);
        };
    },
    
    handleJsonNull : function(json) {
        // กรณี json เป็น null สาเหตุเกิดจาก session expire , reload
        // ใหม่เพิ่อไปหน้า login ()
        // จริงๆมันจะไปเรียก redirect('auth/login', 'refresh'); ที่
        // MY_Controller แล้ว แต่ไม่ work กับ ajax
        if (!json) {
            location.reload(true);
            return true;
        }
        return false;
    },
    
    handleSuccess : function(id, json) {
        var cnt = "#" + id;
        
        this.handleJsonNull(json);
        
        if (AppUtil.isNotEmpty(json.message)) {
            this.styleInfoMessage(id, json.message);
            $(cnt).show().html(json.message);
        }
        else {
            $(cnt).hide().html("");
        }
    },
    
    /**
     * error can be "timeout", "error", "abort", and "parsererror" ,for more
     * detail search "jquery ajax"
     */
    handleError : function(id, xhr, error, desc) {
        
        // check เพราะ
        // http://stackoverflow.com/questions/699941/handle-ajax-error-when-a-user-clicks-refresh
        if (xhr.readyState == 0 || xhr.status == 0) { return; // it's not
        // really an
        // error
        }
        
        if (!desc) {
            desc = "มีความผิดพลาดในการดึงข้อมูล อาจเกิดจากขาดการเชื่อมต่อ กรุณาเรียกใหม่อีกครั้ง (" + error + ")";
        }
        
        if (id == null) {
            alert(desc);
        }
        else {
            var cnt = "#" + id;
            this.styleInfoMessage(id, desc);
            $(cnt).show().html(desc);
        }
    },
    
    styleInfoMessage : function(id, message) {
        if (AppUtil.isEmpty(message)) return;

        if (AppUtil.hasSubString(message, "success")) { // if ($(message).hasClass('success')) {
            $('#'+id).addClass('infoSuccess');
        }
        else {
            $('#'+id).removeClass('infoSuccess');
        }
    },
    
    handleErrorAlert : function(xhr, error, desc) {
        this.handleError(null, xhr, error, desc);
    },
    
    applySelectable : function(oTable, id, isMulti, callback) {
        var selector = "#" + id + " tbody";
        
        //console.log("APPLY SELECTABLE: " + id);
        $(selector).click(function(event) {
            
            //  console.log("APPLY SELECTABLE CLICKED: " + id);
              
            var trNode = $(event.target.parentNode);
            var childTd = trNode.children('td').eq(0);

            //console.log(trNode.html());
             
            if (childTd.hasClass('cellAdditionalDetail') || childTd.hasClass('groupCell') || childTd.hasClass('noSelect'))  return;
            
            if (isMulti == true && event.ctrlKey) { // กรณ๊เป็นแบบเลือกได้หลายแถว
                if (trNode.hasClass('row_selected')) {
                    trNode.removeClass('row_selected');
                }
                else {
                    trNode.addClass('row_selected');
                }
            }
            else if (isMulti == true && event.shiftKey) { // กรณ๊เป็นแบบเลือกได้หลายแถว
                DTHelper.selectRange(oTable, trNode);
            }
            else {
                DTHelper.selectRow(oTable, trNode, true);
            }

            if (callback) {
                callback();
            }
        });
    },
    
    applyMultiSelectable : function(oTable, id) {
        this.applySelectable(oTable, id, true);
    },
    
    applyCheckboxStyleSelectable : function(oTable, id, callback) {
        var selector = "#" + id + " tbody";
        
        $(selector).click(function(event) {
            var trNode = $(event.target.parentNode);
            
            if (trNode.hasClass('row_selected')) {
                trNode.removeClass('row_selected');
            }
            else {
                trNode.addClass('row_selected');
            }

            if (callback) {
                callback();
            }
        });
    },
        
    selectRow : function(oTable, tr, isClear) {
        if (isClear !== false) {
            DTHelper.clearSelections(oTable);
        }
        
        $(tr).addClass('row_selected');
    },
    
    selectTopRow : function(oTable) {
        if (DTHelper.countDatas(oTable) > 0) {
            var tr = $(oTable).find('tbody').find('tr')[0];
            DTHelper.selectRow(oTable, tr);
        }    
    },
    
    selectDown : function(oTable) {
        var aTrs = oTable.fnGetNodes();
        var length = aTrs.length;
        
        for ( var i = 0; i < length; i++) {
            if ($(aTrs[i]).hasClass('row_selected') && i != length -1) {
                DTHelper.selectRow(oTable, aTrs[i+1]);
                return;
            }
        }
        
        if (DTHelper.countDatas(oTable) > 0) DTHelper.selectRow(oTable, aTrs[0]);  
    },
    
    selectUp : function(oTable) {
        var aTrs = oTable.fnGetNodes();
        var length = aTrs.length;
        
        for ( var i = 0; i < length; i++) {
            if ($(aTrs[i]).hasClass('row_selected') && i != 0 ) {
                DTHelper.selectRow(oTable, aTrs[i-1]);
                return;
            }
        }
        
        if (DTHelper.countDatas(oTable) > 0) DTHelper.selectRow(oTable, aTrs[length-1]);  
    },
        
    getSelectedRowNumber : function(oTable) {
        var aTrs = oTable.fnGetNodes();
        var length = aTrs.length;
        
        for ( var i = 0; i < length; i++) {
            if ($(aTrs[i]).hasClass('row_selected') ) {
                return i;
            }
        }
        
        return "not found"; 
    },
    
    selectRowByKey : function(oTable, indexOfKey, key) {
        var data;
        $(oTable).find('tbody').find('tr').each(function() {
            data = oTable.fnGetData(this);
            if (data && data[indexOfKey] == key) {
                DTHelper.selectRow(oTable, this);
            }
        });
    },
    
    deselectRow : function(oTable, tr) {
        $(tr).removeClass('row_selected');
    },
    
    selectRange : function(oTable, tr) {
       
        DTHelper.selectRow(oTable, tr, false);
        
        var selectedIndex = [], index = 0;
        
        $(oTable).find('tbody').find('tr').each(function() {
            if ($(this).hasClass('row_selected')) {
                selectedIndex.push(index);
            }
            index++;
        });
   
        if (selectedIndex.length > 1) {
            var first = selectedIndex[0], last = selectedIndex[selectedIndex.length-1];
            index = 0;
            $(oTable).find('tbody').find('tr').each(function() {
                if (index > first && index < last) {
                    DTHelper.selectRow(oTable, this, false);
                }
                index++;
            }); 
        }
       
    },
    
    /* Get the rows which are currently selected */
    getSelections : function(oTableLocal) {
        var aReturn = new Array();
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            if ($(aTrs[i]).hasClass('row_selected')) {
                aReturn.push(oTableLocal.fnGetData(aTrs[i]));
            }
        }
        return aReturn;
    },
    
    clearSelections : function(oTableLocal) {
        $(oTableLocal.fnSettings().aoData).each(function() {
            $(this.nTr).removeClass('row_selected');
        });
    },    
    
    /*
     * Get a row which are currently selected return data array of selected row
     */
    getSelected : function(oTableLocal) {
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            if ($(aTrs[i]).hasClass('row_selected')) {
                /* Get the data array for this row */
                return oTableLocal.fnGetData(aTrs[i]);
            }
        }
        return null;
    },
    
    countSelections : function(oTableLocal) {
        var aTrs = oTableLocal.fnGetNodes();
        var count = 0;
        for ( var i = 0; i < aTrs.length; i++) {
            if ($(aTrs[i]).hasClass('row_selected')) {
                count++;
            }
        }
        return count;
    },
    
    countDatas : function(oTableLocal) {
        return oTableLocal.fnGetData().length;
    },
    
    countRows: function(oTableLocal) {
        return oTableLocal.fnGetNodes().length;
    },

    checkSelected : function(oTableLocal) {
        if (this.countSelections(oTableLocal) <= 0) {
            alert("กรุณาเลือกรายการข้อมูล");
            return false;
        }
        return true;
    },
    
    checkSingleSelected : function(oTableLocal) {
        if (this.countSelections(oTableLocal) <= 0) {
            alert("กรุณาเลือกรายการข้อมูล");
            return false;
        }
        else if (this.countSelections(oTableLocal) != 1) {
            alert("กรุณาเลือกรายการข้อมูลเพียง 1 รายการ");
            return false;
        }
        return true;
    },
    
    deleteSelections : function(oTableLocal) {
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            if ($(aTrs[i]).hasClass('row_selected')) {
                oTableLocal.fnDeleteRow(aTrs[i]);
            }
        }
    },
    
    // Select row once at first draw
    getSelectRowCallback : function(selectedId, indexOfId) {
        if (indexOfId == null)
            indexOfId = 0;
        
        var rowCallback = function(nRow, aData, iDisplayIndex,
                iDisplayIndexFull) {
            if (selectedId && aData[indexOfId] == selectedId) {
                $(nRow).addClass('row_selected');
                selectedId = null;
            }
            return nRow;
        };
        
        return rowCallback;
    },
    
    dataStore: {},

    // Saved selected row on refresh
    getSelectRowCallbackOnRefresh : function(tableId, selectedId, indexOfId) {
        if (indexOfId == null) {
            indexOfId = 0;            
        }
        
        DTHelper.dataStore[tableId] = {};        
        
        $("#" + tableId + " tbody").click(function(event) {
            var oTable = $("#"+ tableId).dataTable();
            var datas = oTable.fnGetData(event.target.parentNode);
            var store =  DTHelper.dataStore[tableId];
            
            if (datas != null) {
                store.saveSelectedKey = datas[indexOfId];
                store.saveSelectedStart = oTable.fnSettings()._iDisplayStart;
            }
            
            //console.debug("SAVE:" + store.saveSelectedKey+ "|" + store.saveSelectedStart );  
        });
        
        
        var rowCallback = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {    
            var store =  DTHelper.dataStore[tableId];
            //console.debug("CHECK :" + store.saveSelectedKey + "|" + store.saveSelectedStart );          
            
            if (selectedId && aData[indexOfId] == selectedId) { // กรณีเปิดหน้ามาครั้งแรก
                store.saveSelectedKey = selectedId;
                store.saveSelectedStart = 0;
                selectedId = null;
            }
            

            if (store.saveSelectedKey && aData[indexOfId] == store.saveSelectedKey) {      
                var iDisplayStart = $("#"+ tableId).dataTable().fnSettings()._iDisplayStart;
                
                if (store.saveSelectedStart != iDisplayStart) { // กรณีเปลี่ยนหน้าให้ล้างข้อมูลเดิม
                    store.saveSelectedKey = null;
                }
                else {
                    $(nRow).addClass('row_selected');
                }
            }
            
            return nRow;
        };
        
        return rowCallback;        
    },
    
    /**
     *  ได้ข้อมูลที่เก็บในตัวแปรจริง  แต่ลำดับแถวข้อมูลจะเป็นตามที่แสดงที่หน้าจอ
     *  
     *  ใช้ tableDnD ย้าย row แล้วเรียก fnGetData( rowEl ) จะได้แถวผิด !!!
     */
    getDataAtCol : function(oTableLocal, index) {
        var retData = [], data;
        // var aTrs = oTableLocal.fnGetNodes();
        
        $(oTableLocal).find('tbody').find('tr').each(function() {
       
            data = oTableLocal.fnGetData(this);
            
            if (data && data[index]) {
                retData.push(data[index]);
            }
        });
        
        return retData;
    },
    
    getHtmlDataAtCol : function(oTable, htmlIndex) {
        if (htmlIndex == null) {
            htmlIndex = 0;
        }
        
        var datas = [], htmlData;
        
        $(oTable).find('tbody').find('tr').each(function() {    
            htmlData = $(this).find("td:eq(" + htmlIndex + ")").html();     
            if (htmlData) {
                datas.push(htmlData);
            }
        });
        
        return datas;
    },
    
    getSelectionDataAtCol : function(oTableLocal, index) {
        var retData = [], data;
        // var aTrs = oTableLocal.fnGetNodes();
        
        $(oTableLocal).find('tbody').find('tr').each(function() {
            if ($(this).hasClass('row_selected')) {
                data = oTableLocal.fnGetData(this);
                if (data && data[index]) {
                    retData.push(data[index]);
                }
            }
        });
        
        return retData;
    },
    
    // =====================================================================
    
    getKeyByCell : function(cell, positionKeyInTd) {
        if (AppUtil.isEmpty(positionKeyInTd)) {
            positionKeyInTd = "0";
        }
        var selector = "td:eq(" + positionKeyInTd + ")";        
        var nRow = $(cell).parents('tr')[0];
        var key = $(nRow).find(selector).html();
        return key;
    },
    
    getCellByKey: function (oTable, keyCol, keyValue, returnCol) {
        var data = oTable.fnGetData();
        if (data.length == 0) {
            return;
        }

        keyCol = (!AppUtil.isNumber(keyCol))? this.getHtmlColNumberByColName(oTable, keyCol): keyCol;
        returnCol = (!AppUtil.isNumber(returnCol))? this.getHtmlColNumberByColName(oTable, returnCol): returnCol;

        if (!AppUtil.isNumber(keyCol)) return;
        
        var foundTd, theTd;
        $(oTable).find('tbody').find('tr').each(function() {
            theTd = $(this).find('td:nth-child('+ keyCol + ')');
            
            if (theTd.length > 0 && theTd.html() == keyValue) {
                if (AppUtil.isNumber(returnCol)) {
                    foundTd = $(this).find('td:nth-child('+ returnCol + ')');
                }
                else {
                    foundTd = theTd;
                }
                
            }
        });
        
        return foundTd;
    },
    
    getDataByKey : function(oTableLocal, indexOfKey, key) {
        var retData = null, data;
        $(oTableLocal).find('tbody').find('tr').each(function() {
            data = oTableLocal.fnGetData(this);
            if (data && data[indexOfKey] == key) {
                retData = data;
            }
        });
        
        return retData;
    },
    
    /** อัปเดต เฉพาะจุด (cell) */
    updateDataByKey : function(oTable, indexOfKey, keyValue, indexOfUpdate, newData) {
        /*
        $(oTableLocal).find('tbody').find('tr').each(function() {
            data = oTableLocal.fnGetData(this); // this = tr node
            if (data[indexOfKey] == key) {
                oTableLocal.fnUpdate(newData, this);
            }
        });
        */
        
        var aoData = oTable.fnSettings().aoData;       
       
        for ( var i = 0; i < aoData.length; i++) {
            if ( aoData[i]._aData[indexOfKey] == keyValue) {
                oTable.fnSettings().aoData[i]._aData[indexOfUpdate] = newData;
            }
        }
    },
    
    /** อัปเดตทั้งแถว */
    updateRowByKey  : function(oTableLocal, indexOfKey, keyValue, newData) {
        
        $(oTableLocal).find('tbody').find('tr').each(function() {
            data = oTableLocal.fnGetData(this); // this = tr node
            if (data && data[indexOfKey] == keyValue) {                
                oTableLocal.fnUpdate(newData, this);
                return;
            }
        });
    },
    
    deleteRowByKey : function(oTableLocal, indexOfKey, key) {
        $(oTableLocal).find('tbody').find('tr').each(function() {
            data = oTableLocal.fnGetData(this); // this = tr node
            if (data && data[indexOfKey] == key) {
                oTableLocal.fnDeleteRow(this);
            }
        });
    },
    
    deleteAll: function(oTableLocal) {
        oTableLocal.fnClearTable();
    },
    
    clearDatas: function(oTableLocal) {
        oTableLocal.fnClearTable();
    },
    
    // =====================================================================
    
    sumWeight : function(oTableLocal, index1, index2) {
        var sum = (AppUtil.isEmpty(index2))? this.sumNumber(oTableLocal, index1): 
        	this.sumNumberByMultiple2Column(oTableLocal, index1, index2);
        
        return AppUtil.numFormatFloat(sum, 3, true);
    },
    
    sumPrice : function(oTableLocal, index) {
        var sum = this.sumNumber(oTableLocal, index);
        return AppUtil.numFormatFloat(sum, 2, true);
    },
    
    sumNumber: function(oTableLocal, index) {
        var sum = 0.0;
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            datas = oTableLocal.fnGetData(aTrs[i]);
            data = datas[index];
            if (data != null && data != "") {
                sum = sum + AppUtil.numParseFloat(data, 0);
            }
        }
        return sum;
    },
    
    sumNumberByMultiple2Column: function(oTableLocal, index1, index2) {
        var sum = 0.0;
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            datas = oTableLocal.fnGetData(aTrs[i]);
            data1 = datas[index1];
            data2 = datas[index2];
            if (data1 != null && data1 != ""&& data2 != null && data2 != "") {
                sum = sum + ( AppUtil.numParseFloat(data1) * AppUtil.numParseFloat(data2) );
            }
        }
        return sum;
    },
    
    addIfNotExist : function(oTableLocal, datas, indexToCompare) {
        
        if (datas.length) {
            for ( var i = 0; i < datas.length; i++) {
                if (!this.isExist(oTableLocal, datas[i][indexToCompare],
                        indexToCompare)) {
                    oTableLocal.fnAddData(datas[i]);
                }
            }
        }
        
    },
    
    isExist : function(oTableLocal, data, indexToCompare) {
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            datas = oTableLocal.fnGetData(aTrs[i]);
            if (datas[indexToCompare] && datas[indexToCompare] == data) { return true; }
        }
        return false;
    },
    
    isExistTwo : function(oTableLocal, data1, indexToCompare1, data2, indexToCompare2) {
        var aTrs = oTableLocal.fnGetNodes();
        
        for ( var i = 0; i < aTrs.length; i++) {
            datas = oTableLocal.fnGetData(aTrs[i]);
            if (datas[indexToCompare1] && datas[indexToCompare1] == data1 &&
                datas[indexToCompare2] && datas[indexToCompare2] == data2   
            ) { return true; }
        }
        return false;
    },
    
    isEmpty : function(oTableLocal) {
        var data = oTableLocal.fnGetData();
        if (AppUtil.isEmpty(data) || data.length <= 0) {
            return true;
        }
        return false;
    },

    
    /**
     *  ทำลำดับของข้อมูลที่อยู่ใน datatable ให้ตรงกับที่แสดงจริง
     *  ระวังเรื่อง index ที่ใช้ต้อง unique
     *  ( FIXED AND TESTING ถ้าใช้กับ  dataTables version 1.9.0 จะทำให้ลำดับผิด
     *    - index คือชื่อ index ที่อยู่ใน data เช่น "name" (ต้อง unique)
     *    - htmlIndex คือเลขลำดับ column ที่แสดงข้อมูล index เช่น 0
     *  
     */
    synchronizeOrder : function(oTable, index, htmlIndex) {
        if (index == null) {
            index = 0;
        }

        var ids = this.getHtmlDataAtCol(oTable, htmlIndex);   
        //var ids = this.getDataAtCol(oTable, index);
        
        var aoData = oTable.fnSettings().aoData;       
        var newAoData = [];
       
        for ( var i = 0; i < ids.length; i++) {
            for ( var j = 0; j < aoData.length; j++) {
                if (ids[i] == aoData[j]._aData[index]) {
                    newAoData[i] = aoData[j];
                }
            }
        }
       
        //console.debug(ids);
        //console.debug(aoData);
        //console.debug(newAoData);
        
        oTable.fnSettings().aoData = newAoData;
    },

    doRefresh : function(oTable, showProcessing) {
        oSettings = oTable.fnSettings();
        
        if (oSettings.bDrawing === true) { return; }
        
        if (showProcessing) {
            oSettings.oApi._fnDraw(oSettings); 
        }
        else {
            oSettings.oFeatures.bProcessing = false;
            oSettings.oApi._fnDraw(oSettings);
            oSettings.oFeatures.bProcessing = true;
        }
        // oSettings.oApi._fnCalculateEnd(oSettings);       
    },
    
    showProcessing : function(oTable) {
        this.displayProcessing(oTable, true);
    },
    
    hideProcessing : function(oTable) {
        this.displayProcessing(oTable, false);
    },    
    
    displayProcessing : function(oTable, bShow) { // copy from
        // _fnProcessingDisplay in
        // jquery.datatable.js
        oSettings = oTable.fnSettings();
        
        if (oSettings.oFeatures.bProcessing) {
            var an = oSettings.aanFeatures.r;
            for ( var i = 0, iLen = an.length; i < iLen; i++) {
                an[i].style.visibility = bShow ? "visible" : "hidden";
            }
        }        
    },
    
    isProcessing : function(tableId) {
        var processingDiv = tableId + "_processing";
        
        if ($("#" + processingDiv).css('visibility') == "visible") {
            return true;
        }
        return false;
    },
    
    updateOrderColValue : function(oTable, columnNumber, startIndex) {
        var data = oTable.fnGetData();
        if (data.length == 0) {
            return;
        }
        startIndex = (AppUtil.isNotEmpty(startIndex))? startIndex: 1;
        
        columnNumber = columnNumber +1;
        var order = startIndex, theTd;
        $(oTable).find('tbody').find('tr').each(function() {
            theTd = $(this).find('td:nth-child('+ columnNumber + ')');
            if (theTd.length > 0) {
                theTd.html(order);
                order++;
            }           
        });
    },

    updateHtmlCellByKey: function (oTable, keyCol, keyValue, updateCol, updateVal) {
        var data = oTable.fnGetData();
        if (data.length == 0) {
            return;
        }

        keyCol = (!AppUtil.isNumber(keyCol))? this.getHtmlColNumberByColName(oTable, keyCol): keyCol;
        updateCol = (!AppUtil.isNumber(updateCol))? this.getHtmlColNumberByColName(oTable, updateCol): updateCol;
        
        //console.log(keyCol + "," + updateCol);
        
        if (!AppUtil.isNumber(keyCol) || !AppUtil.isNumber(updateCol)) return;
        
        var theTd, foundTd;
        $(oTable).find('tbody').find('tr').each(function() {
            theTd = $(this).find('td:nth-child('+ keyCol + ')');
            if (theTd.length > 0 && theTd.html() == keyValue) {
                $(this).find('td:nth-child('+ updateCol + ')').html(updateVal);
                foundTd = theTd;
            }
      
        });
        return foundTd;
    },
    
    // หาตำแหน่งของ html col จากชื่อที่กำหนดไว้ที่ aoColumn เช่น input เป็น row_id ,output ได้  1,2,3 (colNumber เริ่มที่ 1)
    getHtmlColNumberByColName: function(oTable, colName) {    
        if (AppUtil.isEmpty(colName)) return colName;
       
        var settings = oTable.fnSettings();
        if (AppUtil.isEmpty(settings) || AppUtil.isEmpty(settings["aoColumns"]) ) return colName;
        
        var aoColumns = settings["aoColumns"];
        
        for (var i = 0; i < aoColumns.length; i++) {
            if ( aoColumns[i]["mData"] == colName) {
                return i+1;
            }
        }
        return colName;
    },
    
    /* set ค่า ที่แสดงบนตาราง (ไม่ได้ แก้ค่าที่อยู่ใน ตัวแปรของ datatable) 
    updateHtmlColValue : function(tableId, colIndex, value) {
        var oTable = $("#"+tableId).dataTable();
        data = oTable.fnGetData();
        if (data.length == 0) {
            return;
        }
        
        $(oTable).find('tbody').find('tr').each(function() {
            $(this).find('td:nth-child('+ colIndex + ')').html(data);
        });
    },
    */
    
    /* แก้ค่าที่อยู่ใน ตัวแปรของ datatable */    
    updateInnerColValue : function(tableId, colIndex, value) {
        var oTable = $("#"+tableId).dataTable();
        data = oTable.fnGetData();
        if (data.length == 0) {
            return;
        }

        oTable.find('tbody').find('tr').each(function() {        
            oTable.fnUpdate( value, this, colIndex, false );
        });
    },
    
    getAllDataAsArray: function(tableId) {
        var  $table = $("#"+tableId);
        //var  $headerCells = $table.find("thead th");
        var  $rows = $table.find("tbody tr");
        
        //var headers = [];
        var rows = [];
        
        //$headerCells.each(function(k,v) {
        //   headers[headers.length] = $(this).text();
        //});
        
        $rows.each(function(row,v) {  // row is number -> 0,1,2
            if (typeof rows[row] === 'undefined') {
                rows[row] = [];
            }            
            
            $(this).find("td").each(function(cell,v) {  // cell is number -> 0,1,2
                rows[row][cell] = $(this).text();
            });
        });
        
        return rows;
    },
    
    isReady: function(oTable) {
        if (AppUtil.isEmpty(oTable) || AppUtil.isEmpty(oTable.fnSettings()) ) return false;
        return true;
    },
    
    hideColumn: function(oTable, columnClass) {
        oTable.find('.'+columnClass).each(function() {        
            $(this).css('display', 'none'); 
        });
    },
    
    
    showColumn: function(oTable, columnClass) {
        oTable.find('.'+columnClass).each(function() {        
            $(this).css('display', ''); 
        });
    },
    
    setColumnsVisible: function(oTable, columns, visible, redraw) {
        for ( var i = 0; i < columns.length; i++) {
            this.setColumnVisible(oTable, columns[i] , visible, redraw);
        }
        
    },
    
    setColumnVisible: function(oTable, colIndex, visible, redraw) {
        console.log(oTable);
        if (!this.isReady(oTable)) return;
        
        var aoColumns = oTable.fnSettings().aoColumns;
        radraw = (redraw)? true: false;
   
        for ( var i = 0; i < aoColumns.length; i++) {
            if (aoColumns[i].mData == colIndex) {
                oTable.fnSetColumnVis( i, visible, radraw );
                
            }
        }
    },
    
    isDataEmpty: function(oTable) {
        //if (AppUtil.isEmpty(oTable) || !oTable.hasOwnProperty('fnGetData')) return false;
        
        var data = oTable.fnGetData();
        if (data.length == 0) {
            return true;
        }
        
        return false;
    },
    
    
    getLastRowCellData: function(datas , index) {
       if (AppUtil.isEmpty(datas) || AppUtil.isEmpty(index)) return "";
       
       var lastRow = datas.length - 1;
       var lastData = datas[ lastRow ];
       if (AppUtil.isNotEmpty(lastData) ) {
           return lastData[index];
       }
       return "";
    },
    
    debug: function() {
        
    }
};