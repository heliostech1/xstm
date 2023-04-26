
DateUtil = {
        
    padZero: function(number, length) {
        
        var str = '' + number;
        while (str.length < length) {
            str = '0' + str;
        }
        return str;
    },
    
    getMinDate: function() {
        var date = new Date();
        date.setFullYear(1900);
        date.setMonth(0);
        date.setDate(1);
        return date;
    },
    
    isToday: function(data) {
        var today = this.formatThai(new Date());
        
        if (data && data.trim() == today) {
            return true;
        }
        return false;
    },
    
    getTodayThai: function() {
       return this.formatThai(new Date());         
    },
    
    /**
     * input: object date
     * output: string thai 
     */
    
    formatThai: function(date) {
        var curr_date = date.getDate();
        var curr_month = date.getMonth() + 1; //Months are zero based
        var curr_year = date.getFullYear() + 543;
        return this.padZero(curr_date,2)+"/"+this.padZero(curr_month,2)+"/"+curr_year;
    },
    
    /**
     * input: object date
     * output: string sql ex. 2015-06-15
     */    
    formatSql: function(date) {
        var curr_date = date.getDate();
        var curr_month = date.getMonth() + 1; //Months are zero based
        var curr_year = date.getFullYear();
        return curr_year +"-"+this.padZero(curr_month,2)+"-"+ this.padZero(curr_date,2);
    },
    
    /**
     * input: string thai 
     * output:  object date
     */
    
    parseThai: function(str) {
        if (AppUtil.isEmpty(str)) return null;
        
        var strs = str.split("/");
        if (strs.length != 3) return null;
        
        var date = new Date();
        date.setFullYear(parseInt(strs[2]) - 543);
        date.setMonth(parseInt(strs[1])-1);
        date.setDate(parseInt(strs[0]));
        return date;
    },
    
    /**
     * input: string thai  ex. 15/06/2558
     * output:  string sql  ex. 2015-06-15
     */    
    thaiToSql: function(str) {
        date = this.parseThai(str);
        if (AppUtil.isEmpty(date)) return "";
        return this.formatSql(date);
    },
    
    /**
     * input: [object date, ..]
     * output: string "thai, .."
     */
    formatThaiArray: function(dates) {
        var strs = [];
        for ( var i = 0; i < dates.length; i++) {
            strs.push( DateUtil.formatThai(dates[i]));
        }
        return strs.join(",");
    },
    
    /**
     * input: string "thai, .."
     * output: [object date, ..]
     */
    parseThaiArray: function(str) {
        if (AppUtil.isEmpty(str)) return [];
        
        var strs = str.split(",");
        var dates = [];
        
        for ( var i = 0; i < strs.length; i++) {
            dates.push( DateUtil.parseThai(strs[i]));
        }
        return dates;
    },
    
    
    /**
     * input: object date, int numOfDays
     * output: [object date, ...] 
     */    
    getDateRange: function(startDate, numOfDays) {
        var list = [];
        list.push(startDate);
        for ( var i = 1; i < numOfDays; i++) {
            var date = new Date();
            date.setDate(startDate.getDate() + i);
            list.push(date);
        }
        return list;
    }
    
};

        