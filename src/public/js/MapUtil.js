

MapUtil = {
    mapIconConfig: null, 
        
    getSpeedImage : function (speed, heading) {
        var image = null;
        var color = "red";
        var config = this.mapIconConfig;
        
        if (config) {
            if (config['yellow'] && speed > config['yellow']) {
                color = "yellow";
            } 
            if (config['green'] && speed > config['green']) {
                color = "green";
            }
            if (config['blue'] && speed > config['blue']) {
                color = "blue";
            }
            if (config['purple'] && speed > config['purple']) {
                color = "purple";
            }
            if (config['gray'] && speed > config['gray']) {
                color = "gray";
            }  
        }
                

        //-----------------------------
        
        if (color == 'red') {
            image = "pin30_red_dot.png";
        }
        else if (heading) {
            var x = Math.round(heading / 45.0) % 8;
            image =  "pin30_"+color+"_h"+x+".png";
        }
        else {
            image =  "pin30_"+color+"_dot.png";
        }

        
        return image;
    }

};
