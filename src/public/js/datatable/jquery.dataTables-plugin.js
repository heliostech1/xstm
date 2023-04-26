

$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
 
        oSettings.oApi._fnReDraw(oSettings);
 
        // iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
      
    // draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};


/*-------------------------------------------------
Custom Sorting
------------------------------------------------- */

DTSortingHelper = {
    isNotEmpty: function(object) {
        if (object != null && object != "") {
            return true;
        }
        return false;
        
    },
    
    isEmpty: function(object) {
        return !this.isNotEmpty(object);
    },
    
    padZero: function(number, length) {        
        var str = '' + number;
        while (str.length < length) {
            str = '0' + str;
        }
        return str;
    },
    
    getHtmlValue: function(data) {
        if( this.isNotEmpty(data) && data.match(/\<.+\>/)) {
            return $(data).html();
        }
        return data;
    },
    
    getDatetimeForCompare: function(str) {
        if (this.isEmpty(str)) return "";
        
        var parts = str.split(" ");
        var datePart = parts[0];
        var timePart = (parts.length > 1)? parts[1] : "";        
        var dateItems = datePart.split("/");
        if (dateItems.length != 3) return "";
        
        return dateItems[2]+ "-" + dateItems[1] + "-" + dateItems[0] + "-" + timePart;
    },
    
    getDurationForCompare: function(str) {
        if (this.isEmpty(str)) return "";
        
        var parts = str.split(":");
        var hourPart = parts[0];
        var minutePart = parts[1];        

        return parseInt(hourPart)*60 +  parseInt(minutePart);
    },
    
    getDeliveryResultForCompare: function(str) {
        if (this.isEmpty(str)) return "";
        
        var parts = str.split("/");
        var success = (parts[0])? parts[0] : 0;
        var total = (parts[1])? parts[1] : 0;       

        return parseInt(success)*100 +  parseInt(total);
    },
    //------------------------------------------
    parseNumber : function(data) {
        data = this.getHtmlValue(data);
        
        if (this.isNotEmpty(data)) {
            data = data.replace(/,/g , "");
            return parseFloat( data );
        }
        return -99999;
    },
   
    parseDatetimeThai: function(data) {
        data = this.getHtmlValue(data);
        
        if (this.isNotEmpty(data)) {
            data = this.getDatetimeForCompare(data);
            //console.debug("D:" + data);
            return data;
        }
        return "";
    },
    
    parseDurationSecond: function(data) {
        data = this.getHtmlValue(data);
        
        if (this.isNotEmpty(data)) {
            data = this.getDurationForCompare(data);
            //console.debug("D:" + data);
            return data;
        }
        return "";
    },

    // ex.  1/10, 5/10 , .... *
    parseDeliveryResult: function(data) {
        //data = this.getHtmlValue(data);
        
        if (this.isNotEmpty(data)) {
            data = this.getDeliveryResultForCompare(data);
            //console.debug("D:" + data);
            return data;
        }
        return "";
    }
    
};

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "numeric-formatted-asc": function ( a, b ) {
        a = DTSortingHelper.parseNumber(a);
        b = DTSortingHelper.parseNumber(b);
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "numeric-formatted-desc": function ( a, b ) {
        a = DTSortingHelper.parseNumber(a);
        b = DTSortingHelper.parseNumber(b);     
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    },
    
    //------------------------
        
    "datetime-thai-asc": function ( a, b ) {
        a = DTSortingHelper.parseDatetimeThai(a);
        b = DTSortingHelper.parseDatetimeThai(b);
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "datetime-thai-desc": function ( a, b ) {
        a = DTSortingHelper.parseDatetimeThai(a);
        b = DTSortingHelper.parseDatetimeThai(b);     
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    },
    
    //------------------------
    
    "duration-asc": function ( a, b ) {
        a = DTSortingHelper.parseDurationSecond(a);
        b = DTSortingHelper.parseDurationSecond(b);
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "duration-desc": function ( a, b ) {
        a = DTSortingHelper.parseDurationSecond(a);
        b = DTSortingHelper.parseDurationSecond(b);     
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    },
    
    //------------------------
    
    "delivery-result-asc": function ( a, b ) {
        a = DTSortingHelper.parseDeliveryResult(a);
        b = DTSortingHelper.parseDeliveryResult(b);
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "delivery-result-desc": function ( a, b ) {
        a = DTSortingHelper.parseDeliveryResult(a);
        b = DTSortingHelper.parseDeliveryResult(b);     
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    } 
} );

