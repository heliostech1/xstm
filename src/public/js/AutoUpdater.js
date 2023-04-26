

var AutoUpdater = {
    interval : 5000, // milli second
    autoUpdateButtonId: "autoUpdateButtonId",
    autoUpdateCheckboxId: "autoUpdateCheckboxId",
    autoUpdateTimeId: "autoUpdateTimeId",
    autoUpdateFuncName: "autoUpdateFuncName",
    
    autoUpdateTimer: null,
    isTimerRunning: false,
    
    init: function(funcName) {
       this.autoUpdateFuncName = funcName;
       
       $("#"+this.autoUpdateButtonId).click(function() {
           window[funcName]();
           AutoUpdater.stop();
       });

       // set listener to checkbox
       $("#"+this.autoUpdateCheckboxId).click(function() {
           if (AutoUpdater.isEnableAutoUpdate()) {
               AutoUpdater.start();
           }
           else {
               AutoUpdater.stop();
           }
       });
       
       this.setStartUpdateTime();
       //this.start();
    },
    
    start: function() {
        //this.stop();
        if (this.isEnableAutoUpdate() && this.isTimerRunning == false) {
            this.autoUpdateTimer = setTimeout( this.autoUpdateFuncName +"()", this.interval ); // setTimeout
            this.isTimerRunning = true;
        }
    },
    
    stop: function() {
        if (this.autoUpdateTimer != null) {
            clearTimeout(this.autoUpdateTimer); // clearTimeout
            this.autoUpdateTimer = null;
        }
        this.isTimerRunning = false;
    },
    
    setStartUpdateTime: function() {
       var d = new Date();
       $("#" + this.autoUpdateTimeId).html(
               this.padZero(d.getHours())  + ":" + 
               this.padZero(d.getMinutes())+ ":" +
               this.padZero(d.getSeconds())   
       );
    },
    
    isEnableAutoUpdate: function() {
        var el = document.getElementById(this.autoUpdateCheckboxId);
        if (el && el.checked === true) {
            return true;
        }
        return false;
    },
    
    padZero: function(str) {
        if (str != null && (str +"").length == 1) {
            return "0" + str;
        }
        return str;
    },
    
    lightOn: function(id) {
        $("#"+id).addClass('lightOn');
    },
    
    lightOff: function(id) {
        $("#"+id).removeClass('lightOn');
    }

};
