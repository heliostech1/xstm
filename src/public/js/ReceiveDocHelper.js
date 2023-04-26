

ReceiveDocHelper = {

    getLotIdRenderer: function() {
        var renderer = function ( data, type, full ){ 
             return (AppUtil.isNotEmpty(data)) ?  AppUtil.padZero(data, 6): "";
        };
        return renderer;         
    },
    
    getProductSummaryHtml : function (table, onlyPlan) {
        
        var sum = 0, sumReal = 0, quantity = 0, realQuantity = 0,weight = 0, realWeight = 0, sumWeight=0, sumRealWeight=0;
        var datas = table.fnGetData();
        
        for ( var i = 0; i < datas.length; i++) {
            quantity = datas[i]["quantity"];
            realQuantity = datas[i]["real_quantity"];
            weight = datas[i]["weight"];
            realWeight = datas[i]["real_weight"];
            
            if (AppUtil.isNumber( quantity )) {
                sum = sum + AppUtil.numParseFloat(quantity , 0);
            }
            if (AppUtil.isNumber( realQuantity )) {
                sumReal = sumReal + AppUtil.numParseFloat(realQuantity , 0);
            }
            if (AppUtil.isNumber( weight )) {
                sumWeight = sumWeight + AppUtil.numParseFloat(weight , 0);
            }
            if (AppUtil.isNumber( realWeight )) {
                sumRealWeight = sumRealWeight + AppUtil.numParseFloat(realWeight , 0);
            }           
        }
        
        var html = "";        

        html += "<b>รวมตามยอด: </b>" +  AppUtil.numFormatFloat(sum, 2, true) + " หน่วย, " +
               AppUtil.numFormatFloat(sumWeight, 2, true)+ " กก.";
        
        
        if (onlyPlan !== true) {     
            html += "&nbsp;|&nbsp;<b>รวมรับจริง: </b>"+ AppUtil.numFormatFloat(sumReal, 2, true) + " หน่วย, " + 
                     AppUtil.numFormatFloat(sumRealWeight, 2, true)+ " กก.";
        }
       
         return html;

    }

};
