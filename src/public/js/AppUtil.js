
String.prototype.trim = function() { 
    return this.replace(/^\s+|\s+$/g,''); 
};

String.prototype.startsWith = function(s) { 
    try {
        
        if (!s || (s == "")) {
            return true;
        } else
        if (s.length > this.length) {
            return false;
        }

        return (s === this.substr(0, s.length));
        
    } catch (e) {
        return false;
    }
};

Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
};

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


AppUtil = {
    
    loadWarehouseProbeByBranch: function(probeInputId, branchInputId) {            
        var url = "../warehouse_probe/get_warehouse_probe_info_array";
        var params = ({ branchId: $('#'+branchInputId).val() });
        AppUtil.loadComboboxData(probeInputId, url, params);        
    },
        
    loadWarehouseRoomByBranch: function(roomInputId, branchInputId) {
        var url = "../warehouse_room/get_warehouse_room_array";
        var params = ({ branchId: $('#'+branchInputId).val() });
        AppUtil.loadComboboxData(roomInputId, url, params);
    },

    loadWarehouseAreaByBranch: function(areaInputId, branchInputId) {
        var url = "../warehouse_area/get_warehouse_area_array";
        var params = ({ branchId: $('#'+branchInputId).val() });
        AppUtil.loadComboboxData(areaInputId, url, params);
    },

    loadWarehouseStackByBranch: function(stackInputId, branchInputId, roomInputId) {
        var url = "../warehouse_stack/get_warehouse_stack_array";
        var params = ({ branchId: $('#'+branchInputId).val() ,  room_id: $('#'+roomInputId).val() });
        AppUtil.loadAutoCompleteData(stackInputId, url, params);                
    },
   
    loadNcDataByStyleId: function(ncDataInputId, styleInputId) {
        var url = "../product_nc/get_product_nc_array_by_style_id";
        var params = ({ style_id: $('#'+styleInputId).val() });
        AppUtil.loadComboboxData(ncDataInputId, url, params);
    },
    
    loadComboboxData : function(inputId, urlData, params) {
        $("#"+inputId).html(FieldHelper.chooseOptionTxt);    

        $.ajax({
            url: urlData,
            data: params,
            dataType: "json",
            beforeSend: function() { $('#'+inputId).addClass('loadingInput'); },
            success: function(json){
                $('#'+inputId).removeClass('loadingInput');
                FieldHelper.setOptions(inputId, json );                
            }
        });  
    },
    
    loadAutoCompleteData : function(inputId, urlData, params) {
        $.ajax({
            url: urlData,
            data: params,
            dataType: "json",
            beforeSend: function() { $('#'+inputId).addClass('loadingInput'); },
            success: function(json){
                $('#'+inputId).removeClass('loadingInput');
                FieldHelper.applyAutoComplete(inputId, json ); 
            }
        });  
    },
    

    
    //---------------------------------------------------
    // gts, bsrd        
    // type are "customer", "place"  
    viewMap: function(param_id, type) {
        AppUtil.openResizableWindow("../map/view_geopoint?param_id="+param_id,'',860, 500); //viewMap
    },
    
    viewMapByLatLon: function(lat, lon) {
        AppUtil.openResizableWindow("../map/view_geopoint_by_latlon?lat="+lat+"&lon="+lon,'',860, 500);//viewMap
    },
    
    editMap: function(param_id, mode) {
        AppUtil.openResizableWindow("../map/edit_geopoint?param_id="+param_id +"&mode="+mode,'editMap',860, 500);
    },

    editMapByAddress: function(param_id, mode, type) {
        var url = "../map/edit_geopoint_by_address?param_id="+param_id+"&mode="+mode;
        url += (type != null)? "&type="+ type : "";
        AppUtil.openResizableWindow(url, 'editMapByAddress',860, 500);
    },

    viewGeozone: function(param_id) {
        AppUtil.openResizableWindow("../map/view_geozone?param_id="+param_id,'',860, 500);//viewZone
    },
    
    editGeozone: function(param_id) {
        AppUtil.openResizableWindow("../map/edit_geozone?param_id="+param_id,'editZone',860, 500);
    },
    
    //-------------------------------------------------------------------------
    // gts, bsrd     
    viewMapRouteFromGts: function(tm_id, start, finish, itemIds) {
        AppUtil.openResizableWindow("../map/view_route_from_gts?tm_id="+tm_id+"&start="+start+
                "&finish="+finish+ "&item_ids="+itemIds, '',860, 500); //viewGtsMap
    },
 
    viewMapRouteFromEvd: function(vehicle_id, start, finish) {
        AppUtil.openResizableWindow("../map/view_route_from_eventdata?vehicle_id="+vehicle_id+"&start="+start+
                "&finish="+finish , '',860, 500); //viewEvdMap
    },
    
    //---------------------------------------------------------------------------
    // gts, bsrd     
    
    viewOperationDetailReport: function(tm_id, mode) {
        var url = '../operation_report/view_operation_detail_report_sub?tm_id=' + tm_id;
        url += (mode != null)? "&mode="+mode: "";
        window.location.href  = url;
    },

    viewSimRefillReport: function(date, to_data, box_id) {
        var url = '../box_report/view_sim_refill_report?date=' + date +
                "&to_date=" + to_data + "&box_id=" + box_id;
        window.location.href  = url;
    },
    
    viewFuelGraph: function (date, licensePlate) {
        AppUtil.openResizableWindow("../driving_report/view_fuel_graph?"+
                "date="+date+
                "&to_date="+date+
                "&license_plate="+licensePlate
                ,'fuelGraph',1040, 500);
    },
    
    //-------------------------------------------------------------------
    // bsis
    
    viewTemperatureProductionGraph: function(id) {
        AppUtil.openResizableWindow("../trace_temperature/view_temperature_production_graph?" +
               "receiveProductionId="+id
               ,'viewTemperature',1040, 500);
    },
   
    viewTemperatureDashboard: function () {
        AppUtil.openResizableWindow("../temperature_dashboard/view_temperature_dashboard"
                ,'temperature_dashboard', 1200, 570, 40);
    },
    
    viewAlarmDashboard: function () {
        AppUtil.openResizableWindow("../alarm_dashboard/view_alarm_dashboard"
                ,'alarm_dashboard', 1200, 570, 40);
    },
    
    updateAlarmCount: function(json) {
        var count = (json && AppUtil.isNotEmpty(json.alarm_count))? json.alarm_count: 0;
        
        if (AppUtil.isEmptyOrZero(count)) {
            $('#alarmCountCont').hide();
        }
        else {
            $('#alarmCountCont').show();
            $('#alarmCountCont').html(count);  
        }
                
    },
    
    /** ex. http://drux.co/B2889C6IHDVGSVR7HI90  => B2889C6IHDVGSVR7HI90 */
    
    isGidNumber: function(gid) {
         if (AppUtil.isEmpty(gid)) return false;
        
        gid = AppUtil.parseGidNumber(gid);
         if (gid.length == 20) {
             return true;
         }
         
         return false;
    },
    
    parseGidNumber: function(gid) {
         if (AppUtil.isEmpty(gid)) return gid;
         
         gid = gid.trim();
         if ( gid.indexOf('/') > -1 )  {
             var parts = gid.split('/');
             gid = parts[ parts.length-1 ];
         }
         return gid;
    },
    
    endFunction: function() {        
    }
     
};


//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------


BaseAppUtil = {
    cloneObject: function(obj) {
       return JSON.parse( JSON.stringify( obj ) );    
    },
    
    setExtend: function(superUtil) {
        for (var prop in BaseAppUtil) {
            if (BaseAppUtil.hasOwnProperty(prop)) {
                superUtil[prop] = BaseAppUtil[prop];
            }
        }
    },    
    
    openNewTab: function (url) {
        var win = window.open(url, '_blank');
        win.focus();
    },

    /* open a resizable window and display specified URL */
    openResizableWindow : function(url, name, W, H) {
        // "resizable=[yes|no]"
        // "width='#',height='#'"
        // "screenX='#',screenY='#',left='#',top='#'"
        // "status=[yes|no]"
        // "scrollbars=[yes|no]"
        var attr = "resizable=yes";
        attr += ",menubar=no,toolbar=no";
        if ((W > 0) && (H > 0)) {
            attr += ",width=" + W + ",height=" + H;
            var L = ((screen.width - W) / 2), T = ((screen.height - H) / 2);
            attr += ",screenX=" + L + ",screenY=" + T + ",left=" + L + ",top="
                    + T;
        }
        attr += ",status=yes,scrollbars=yes";
        var win = window.open(url, name, attr, false);
        if (win) {
            // if (!(typeof win.moveTo == "undefined")) { win.moveTo(L,T); }
            if (!(typeof win.focus == "undefined")) {
                win.focus();
            }
            return win;
        } else {
            return null;
        }
    },

    isNotEmpty: function(object) {
        if (object !== null && object !== "" && object !== "undefined"   && typeof object != 'undefined') {
            return true;
        }
        return false;
        
    },
    
    isEmpty: function(object) {
       return !this.isNotEmpty(object);
    },
    
    isEmptyOrZero: function(object)  {
        return (this.isEmpty(object) || object === 0 || object === "0");
    },
    
    isDefined: function(object) {
        if (typeof object !== 'undefined') {
            return true;
        }        
        return false;
    },
    
    hasProperty: function(obj, prop) {
       if (this.isEmpty(obj)) return false;
       
       return  obj.hasOwnProperty(prop);
       /*
       var proto = obj.__proto__ || obj.constructor.prototype;
       return (prop in obj) &&
           (!(prop in proto) || proto[prop] !== obj[prop]);
       */ 
    },
    
    
    checkEmpty: function( data ) {
        if (data == null || data.length == 0) {
           alert("กรุณาเลือกรายการข้อมูล");
           return false; 
        }

        return true;
    },
    
    cancelBubble: function(e) {
        if (!e) var e = window.event;
        // stop event
        e.cancelBubble = true;
        if (e.stopPropagation) e.stopPropagation();
    },
  
    
     _trimZeros: function(val) {
        if (typeof val == "string") {
            while (val.startsWith(" ")) { val = val.substring(1); }
            while ((val.length > 1) && val.startsWith("0")) { val = val.substring(1); }
        }
        return val;
    },
    
    /* parse float value */
    numParseInteger: function (val, dft){
        if (val === null || val === "" || !AppUtil.isNumber( this.removeCommas(val) )) return dft;
            
        var num = parseInt( this._trimZeros( this.removeCommas(val) ) );
        if (isNaN(num)) { num = dft; }
        return num;
    },
    
    /* parse float value */
    numParseFloat: function (val, dft){
        if (val === null || val === "" || !AppUtil.isNumber( this.removeCommas(val) )) return dft;
            
        var num = parseFloat( this._trimZeros( this.removeCommas(val) ) );
        if (isNaN(num)) { num = dft; }
        return num;
    },
    
    numFormatFloat : function(val, dec, comma){
        var num = this.numParseFloat(val,0);
        if (dec > 0) {
            var neg = (num >= 0)? '' : '-';
            num = Math.abs(num);
            var d;
            for (d = 0; d < dec; d++) { num *= 10; }
            num = parseInt(num + 0.5);
            var str = new String(num);
            while (str.length <= dec) { str = '0' + str; }
            str = str.substring(0, str.length - dec) + '.' + str.substring(str.length - dec);
            return neg + ((comma)? this.addCommas(str): str);
        } else {
            num = parseInt((num >= 0)? (num + 0.5) : (num - 0.5));
            return new String((comma)? this.addCommas(num): num);
        }
    },
    
    /* ex.  34.9990002 =>  34.99,  80.00 => 80 */
    formatDecimalActual: function(val, limit) {
        if (!AppUtil.isNumber(val)) return val;
        
        limit = (AppUtil.isEmpty(limit))? 2 : limit;
        result =  AppUtil.numToFixed( parseFloat(val), limit );
        return AppUtil.removeTrailZero(result);        
    },
    
    formatDecimalActualWithComma: function(val, limit) {
        value = AppUtil.formatDecimalActual(val, limit);      
        return AppUtil.addCommas(value);
    },
    
    formatDecimal: function(val, limit) {
        if (!AppUtil.isNumber(val)) return val;
        
        limit = (AppUtil.isEmpty(limit))? 2 : limit;
        result = AppUtil.numToFixed( parseFloat(val), limit ); 
        return result;        
    },
    
    formatDecimalWithComma: function(val, limit) {
        value = AppUtil.formatDecimal(val, limit);      
        return AppUtil.addCommas(value);
    },
    
    addCommas : function(nStr) {
        if (!AppUtil.isNumber(nStr)) return nStr;
        
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    },

    removeCommas: function(str) {
        str += '';
        return str.replace(/,/g, '');
    },
    
    /* 2011-09-13 13:40:40 -> 13/09/2554 13:40:40 */
    formatThaiTime: function(str) {
       if (str && str.length == 19) {
            var year = parseInt(str.substr(0,4));
            if (!isNaN(year)) {
              return  str.substr(8,2) + "/" +  str.substr(5,2) + "/" +
               (year + 543) + str.substr(10,9);
           }
       }
       
       return str;
    },
    
    /* 2011-09-13 -> 13/09/2554  */
    formatThaiDate: function(str) {
        if (str && str.length == 10) {
             var year = parseInt(str.substr(0,4));
             if (!isNaN(year)) {
               return  str.substr(8,2) + "/" +  str.substr(5,2) + "/" +
                (year + 543);
            }
        }
        
        return str;
     },
     
    
    getWindowHeight : function() {
        if (typeof(window.innerHeight) == 'number')
        return window.innerHeight;
        
        if (document.documentElement && document.documentElement.clientHeight)
        return document.documentElement.clientHeight;
        
        if (document.body && document.body.clientHeight)
        return document.body.clientHeight;
        
        return 800;
    },

    getWindowWidth : function() {
        if (typeof(window.innerWidth) == 'number')
        return window.innerWidth;
        
        if (document.documentElement && document.documentElement.clientWidth)
        return document.documentElement.clientWidth;
        
        if (document.body && document.body.clientWidth)
        return document.body.clientWidth;
        
        return 1200;
    },
    
    padZero: function(number, length) {
           
        var str = '' + number;
        while (str.length < length) {
            str = '0' + str;
        }
        return str;
    },
    
    /* integet and decimal */ 
    isNumber: function(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    },
    
    /*
     * /^(\+|-)?[1-9]\d*(\.\d*)?$/ (ทศนิยม +-)
     * /^[1-9]\d*$/  (จำนวนเต็มไม่  +-)
     */
    isNumberInt: function(n) {
        return !isNaN(parseFloat(n)) && isFinite(n) && ( /^[0-9]$/.test(n) || /^[1-9]\d*$/.test(n));
    },
    
    normalizeHeading: function(heading) {
        while (heading < 0.0) {
            heading += 360.0;
        }
        while (heading >= 360.0) {
            heading -= 360.0;
        }
        return heading;
    },
    
    blockUI_setDefaultProperties: function() {
        
        $.blockUI.defaults.css.color = '#777';
        $.blockUI.defaults.css.border = '3px solid #aaa';
        $.blockUI.defaults.css.backgroundColor = '#fff';
        $.blockUI.defaults.css.cursor = 'default';
        $.blockUI.defaults.css.fontSize = '25px';
        $.blockUI.defaults.css.height = '50px';
        $.blockUI.defaults.css.padding = '15px 0 2px';
        $.blockUI.defaults.overlayCSS.cursor = 'default';
        $.blockUI.defaults.overlayCSS.opacity = 0.2;
        
    },
    
    apply : function(o, c){
        if(o && c && typeof c == 'object'){
            for(var p in c){
                o[p] = c[p];
            }
        }
        return o;
    },
    
    getSortedKey: function(obj) {
        var keys = [];
        for (var key in obj)  {
            if(obj.hasOwnProperty(key))  {
                keys.push(key);
            }
        }
        keys.sort();
        return keys;
    },
    
    arrayAppendFirst: function( arr, data) {
        if (this.isNotEmpty(data)) {
            return arr.unshift( data );
        }   
    },
    
    arrayAppendLast: function( arr, data) {
        if (this.isNotEmpty(data)) {
            return arr.push( data );
        }   
    },
   
    
    rgb2hex: function (rgb) {
        if (AppUtil.isEmpty(rgb)) return "";
        
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    },
    
    devideFixLimit: function(a, b, limit) {
        if (!AppUtil.isNumber(a) || !AppUtil.isNumber(b) ) return "";
        
        a = AppUtil.numParseFloat(a,0);
        b = AppUtil.numParseFloat(b,0);
        if (b == 0) return "";
        
        result =  AppUtil.numToFixed( a/b, limit );
        return AppUtil.removeTrailZero(result);
    },
    
    // https://stackoverflow.com/questions/10015027/javascript-tofixed-not-rounding
    numToFixed: function( num, precision ) {
        return (+(Math.round(+(num + 'e' + precision)) + 'e' + -precision)).toFixed(precision);
    },
    
    
    removeTrailZero: function(str) {
        str = str.replace(/(\.[0-9]*?)0+$/, "$1"); // remove trailing zeros
        str = str.replace(/\.$/, "");
        return str;
    },
    
    sumNumber: function(a, b) {
        if (!AppUtil.isNumber(a) || !AppUtil.isNumber(b) ) return "";
        return AppUtil.numParseFloat(a, 0) + AppUtil.numParseFloat(b, 0);
    },
        
    hexIncrement: function(str) {
        var hex = str.match(/[0-9a-f]/gi);
        var digit = hex.length;
        var carry = 1;

        while (digit-- && carry) {
            var dec = parseInt(hex[digit], 16) + carry;
            carry = Math.floor(dec / 16);
            dec %= 16;
            hex[digit] = dec.toString(16);
        }
        return(hex.join(""));
    },
    
    lightOn: function(id) {
        $("#"+id).addClass('lightOn');
    },
    
    lightOff: function(id) {
        $("#"+id).removeClass('lightOn');
    },
    
    isPhoneNo: function(input) {
        var regExp = /^0[0-9]{8,9}$/i;
        return regExp.test(input);
    },
    
    removeDash: function (input) {
        if (AppUtil.isEmpty(input)) return input;
        return input.replace(/-/g, "");
    },
    
    removeSpace: function (input) {
        if (AppUtil.isEmpty(input)) return input;
        return input.replace(/ /g, '');
    },
    
    nl2br: function (input) {
        if (AppUtil.isEmpty(input)) return input;
        return input.replace(/\n/g, '<br />');
    },
    
    br2nl: function (input) {
        if (AppUtil.isEmpty(input)) return input;
        return input.replace(/<br\s*\/?>/mg,"\n");
    },
    
    getKeyByValue: function(json, value) {
        var key;
        for (key in json) {
            if (json.hasOwnProperty(key) && json[key] == value) {
                return key;
            }
        }
        return value;
    },
    
    hasSubString: function(mainString, subString) {
        if (AppUtil.isEmpty(mainString)|| AppUtil.isEmpty(subString)) return false;
        return (mainString.indexOf( subString ) !== -1);
    },
    
    hasSubStrings: function(mainString, subStrings) {
        if (AppUtil.isEmpty(mainString)|| AppUtil.isEmpty(subStrings)) return false;
        
        for ( var i = 0; i < subStrings.length; i++) {
            if (mainString.indexOf( subStrings[i] ) !== -1) {
                //console.debug("OK: "+mainString+ "/" + subStrings[i]);
                return true;
            }
        }
        //console.debug("FAIL:" + mainString);
        return false;
    },
    
    truncate: function(myString, limit) {
        if (AppUtil.isEmpty(myString)) return "";
        
         var newString =  myString.substring(0,limit);
         if (myString.length != newString.length) {
              newString = newString + "...";
         }
         return newString;
    },
    
    stringToArray: function(data) {
        if (AppUtil.isNotEmpty(data)) {
            if ($.isArray(data)) {
                return data;                
            }
            else {
                return data.split(','); 
            }
        }
        return [];
    },

    arrayToString: function(data) {
        if (AppUtil.isNotEmpty(data)) {
            if ($.isArray(data)) {
                return data.join(",");             
            }
            else {
                return data;     
            }
        }
         return data; 
    },  
    
    isInArray: function(item, arrays) {
        if ($.inArray(item, arrays) != -1) {
            return true;
        }
        return false;
    },
    
    setCookie: function(name, value, exdays, path, domain, secure) {
        var expires=new Date();
        expires.setDate(expires.getDate() + exdays);
        
        document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
    },

    getCookie: function(name, defaultValue) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0)
                return (defaultValue)? defaultValue: null;
        }
        else {
            begin += 2;
        }
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
        return unescape(dc.substring(begin + prefix.length, end));
    },

    deleteCookie: function(name, path, domain) {
        if (getCookie(name)) {
            document.cookie = name + "=" +
                ((path) ? "; path=" + path : "") +
                ((domain) ? "; domain=" + domain : "") +
                "; expires=Thu, 01-Jan-70 00:00:01 GMT";
        }
    },
    
    setStorage: function(key, value) {        
    },
    
    getStorage: function(key) {       
    },
    
    setStorageObject: function(key, object) {
        if (localStorage) {
            localStorage.setItem( key, JSON.stringify( object ));
        }
    },
    
    getStorageObject: function(key) {
        if (localStorage) {
            var sData = localStorage.getItem( key );
            var oData;
            try {
                oData = JSON.parse(sData);
            } catch (e) {
                oData = null;
            }
            return oData;            
        }
    },
    
    checkGoogleMapValid: function() {
        if (typeof google === 'object' && typeof google.maps === 'object') {
            return true;            
        }
        alert("ไม่สามารถโหลดแผนที่จาก Google Map กรุณาตรวจสอบการเชื่อมต่ออินเตอร์เน็ต");
        return false;
    },
    
    isMobileDevice: function() {        
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return true;
        }
        return false;
    },
    
    /* แก้บัก google v3 มีการเปลี่ยนแปลง ที่ทำให้  infoWindow เกิด scrollbar */
    wrapScrollFix: function(html) {
        return '<div class="GmapV3_scrollFix">' + html + '</div>';
    },
    
    getRoundPriceKip: function(price) {
        var fragment = price%1000;
        price = price - fragment;
        
        if ( fragment < 250 ) {
           fragment = 0;
        }
        else if (fragment >= 250 && fragment < 500) {
           fragment = 500;
        }
        else if (fragment >= 500 && fragment < 750) {
           fragment = 500;
        }
        else if (fragment >= 750 ) {
            fragment = 1000;
        }
        price = price + fragment;
        return price;    
    },
    
    
    setFilterTitleToogleForm: function(filterId, formId)  {
        
        $("#" + filterId).click(function(){
           $("#" + formId).toggle();

            if ($('#' + formId).css('display') == 'none') {
               $('#' + filterId).html('Filter &#9658');
            }
            else {
               $('#' + filterId).html('Filter &#9660');
            }

        });
    },
    
    getFileExtension: function(fileName) {
        if (AppUtil.isEmpty(fileName)) return "";
        var fileExt = fileName.split('.').pop();

        return (AppUtil.isEmpty(fileExt))? "": fileExt.toLowerCase();
    },
    
    isImageFile: function(fileName) {
        var fileExt = AppUtil.getFileExtension(fileName);
        var imageExtList = ["jpg","gif","png","jpeg"];
        return (imageExtList.indexOf(fileExt) > -1)? true: false;
        
    },
    
    isVdoFile: function(fileName) {
        var fileExt = AppUtil.getFileExtension(fileName);
        var imageExtList = ["mp4"];
        return (imageExtList.indexOf(fileExt) > -1)? true: false;
    },
    
    
    initSpTabs: function() {

        $(".spTabs li").click(function() {
            AppUtil.activateSpTabs(this);
            return false;
        });
    

    },
    
    activateSpTabs: function(tabEl) {
        $(tabEl).parent().find("li").removeClass('active');
        $(tabEl).addClass("active");   
        $(".spTabs_item").hide();

        var selectedTab = $(tabEl).find("a").attr("href");
        //console.log(selectedTab);
        $(selectedTab).show();
    },
    
    setAsTimeInput: function(selector) {
        $.mask.definitions['h']='[0-2]';
        $.mask.definitions['m']='[0-5]';

        $(selector).each(function(i, obj) {
            $(this).mask("h9:m9");  
        });
    
    },

    getDropdownText: function(id, value) {
        if (AppUtil.isEmpty(value)) return "";        
        return $("#" + id +" option[value='" + value + "']").text();
    },
    
   isIdExist: function(id) {
        if( $("#" + id).length == 0) {
          return false;
        }
        return true;
   },
    
    
    debug: function() {
      //............
    }
    
      
    
};

BaseAppUtil.setExtend(AppUtil);

