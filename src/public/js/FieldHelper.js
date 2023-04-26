
var FieldHelper = {
    chooseOptionTxt : "<option value=''>-- กรุณาเลือก --</option>",
   
    datePickerConfig : {
        changeMonth : true,
        changeYear : true
    },
        
    setOptions : function(id, json) {
        $("#"+id).html("");
        $.each(json, function(index, data) {
            if (data && data.text) {
                $("#"+id).append('<option value="'+data.value+'">' + data.text + '</option>');
            }           
        });
    },
    
    applyViewMode : function(id) {
        var selector = "#" + id;
        
        $(selector).addClass('textReadOnly').removeClass("textInput").attr(
                'readonly', 'true');

        if( $(selector).is("select")) {
            $(selector).attr('disabled', true); 
        }

    },
    
    applyComboboxViewMode : function(id) {
        var selector = "#" + id;
        
        $(selector).addClass('textReadOnly').removeClass("textInput").attr(
                'readonly', 'true');
        $(selector).attr('disabled', true);
    },
    
    applyTextAreaViewMode: function(id) {
        var selector = "#" + id;
        
        $(selector).addClass('textAreaReadOnly').removeClass("textAreaInput").attr(
                'readonly', 'true');        
        $(selector).attr('disabled', true);
    },
    
    applyViewModeClear : function(id) {
        var selector = "#" + id;
        
        $(selector).addClass('textReadOnly').removeClass("textInput").attr(
                'readonly', 'true').val("");
    },
    
    applyEditMode : function(id) {
        var selector = "#" + id;
        
        $(selector).removeAttr('readonly').removeAttr('disabled');
        
        if ($(selector).hasClass('textReadOnly')) {
            $(selector).addClass('textInput').removeClass("textReadOnly");
        }
        else if ($(selector).hasClass('textAreaReadOnly')) {
            $(selector).addClass('textAreaInput').removeClass("textAreaReadOnly");
        }

    },
    
    applyAutoComplete: function(id, list) {
        var selector = "#" + id;

        $(selector).autocomplete({
            minLength : 0,
            source : list
        }).focus(function() {
            $(this).autocomplete('search', '');
            return false;
        });

    },
    
    isValueExistInSelect: function(selectEl, value) {     
        
        if( selectEl.find(' option[value='+value+']').length > 0) {
            return true;
        }        
        return false;
    },
    
    getHourNumberList : function() {
        return  ['00','01','02','03','04','05','06','07','08','09',
                 '10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
    },
    
    getMinuteNumberList : function() {
        return   ['00','01','02','03','04','05','06','07','08','09',
                  '10','11','12','13','14','15','16','17','18','19',
                  '20','21','22','23','24','25','26','27','28','29',
                  '30','31','32','33','34','35','36','37','38','39',
                  '40','41','42','43','44','45','46','47','48','49',
                  '50','51','52','53','54','55','56','57','58','59',
                  ]; 
    },
    
    getDateTimeValue : function (id) {    
        var date = $('#' + id ).val();        
        var hour = $('#' + id + 'Hour').val();    
        var minute = $('#' + id + 'Minute').val();  
        var second = $('#' + id+ 'Second').val();  
        
        if (AppUtil.isEmpty(date)) {
            return "";
        }
        
        hour = (AppUtil.isEmpty(hour))? "00": hour;
        minute = (AppUtil.isEmpty(minute))? "00": minute;
        second = (AppUtil.isEmpty(second))? "00": second;
        
        return date + " " +hour+":"+minute+":"+second;            
    },

    setDateTimeValue : function(id, value) {    
        var date = "", hour = "", minute="", second="";
        
        if (!AppUtil.isEmpty(value)) {
            var part = value.split(" ");
            var time = "";

            if (part && part.length > 1) {
                date = part[0];
                time = part[1];
            }
            
            var timepart = time.split(":");
            if (timepart && timepart.length > 1) {
                hour = timepart[0];
                minute = timepart[1];
                second = timepart[2];
            }
            
        }
        
        $('#' + id ).val(date);  
        $('#' + id + 'Hour').val(hour);    
        $('#' + id+ 'Minute').val(minute);  
        $('#' + id + 'Second').val(second);          
    },
    
    initRemoteSelect2: function(select2Input, idInput, nameInput, urlParam, callback) {
        
        $("#"+select2Input).select2({
            ajax: {
              url: urlParam,
              dataType: 'json',
              "type": "POST", 
              delay: 250,
              data: function (params) {
                return {
                  term: params
                };
              },
              results: function(data, page) {
                  return {
                      results: data
                  };
              },                                         
              cache: true
            },
            minimumInputLength: 3,
            formatResult: function (data) {
                return data.name;
            },
            formatSelection: function (data) {
                return data.name;
            }
        });

        // CACHE LAST SEARCH ================
        window.select2_last_search = '';
        $(  "#"+select2Input ).on('select2-open', function () {
            if (select2_last_search) {
                $('.select2-search').find('input').val(select2_last_search ).trigger('paste');
            }
        });
        
        $(  "#"+select2Input  ).on('select2-loaded', function () {
            select2_last_search  = $('.select2-search').find('input').val();
        });
        
        $( "#"+select2Input ).on("select2-selected", function(e) { 
            var data =  $( "#"+select2Input ).select2("data");
            
            if (AppUtil.isNotEmpty(callback)) {
                callback(data);
            }
            
            //console.debug(data);            
            if (AppUtil.isNotEmpty(data)) {    
                $("#" + idInput).val(data.id);
                $("#" + nameInput).val(data.name);
            }
         });
        
        // SET VALUE ===========================
        var idValue = $("#" + idInput).val();
        var nameValue = $("#" + nameInput).val();
     
        if (AppUtil.isNotEmpty(idValue) && AppUtil.isNotEmpty(nameValue) ) {
            $(  "#"+select2Input ).select2("data", { "id":idValue , "name": nameValue });
        }        
    },
    
    setProductRemoteSelect2: function(select2Input, nameInput) {
        
       var urlParam = "../product/getDataBySelect2";
  
        $("#"+select2Input).select2({
            allowClear: true,         // not work
            ajax: {
              url: urlParam,
              dataType: 'json',
              "type": "POST", 
              delay: 250,
              data: function (params) {
                return {
                  term: params
                };
              },
              results: function(data, page) {
                  return {
                      results: data
                  };
              },                                         
              cache: true
            },
            minimumInputLength: 2,
           
            formatResult: function (data) {
                return data.name;
            },
            formatSelection: function (data) {
                return data.name;
            },
                      
        });
          
        $(  "#"+select2Input  ).on('select2-loaded', function () {
            select2_last_search  = $('.select2-search').find('input').val();
        });
        
        $( "#"+select2Input ).on("select2-selected", function(e) { 
            var data =  $( "#"+select2Input ).select2("data");
           
            //console.debug(data);            
            if (AppUtil.isNotEmpty(data)) {    
                $("#" + nameInput).val(data.name);
            }
         });
        
        // SET VALUE ===========================
        var idValue = $("#" + select2Input).val();
        var nameValue = $("#" + nameInput).val();
     
        if (AppUtil.isNotEmpty(idValue) && AppUtil.isNotEmpty(nameValue) ) {
            $(  "#"+select2Input ).select2("data", { "id":idValue , "name": nameValue });
        }                
    },
    
    /**
     * productData:[{id:0,text:'AAAAAA'},{id:1,text:'Bbb'},{id:2,text:'CCCCCC'},{id:3,text:'DDDD'},{id:4,text:'wontfix'}]
     */
    setProductSelect2ByCategoryId: function( productData, productInputId, categoryInputId ) {
        var categoryId = $('#'+ categoryInputId).val();
        var newData = [{id: "", text: "-- กรุณาเลือก --"}];
        for (var i=0; i<productData.length; i++) {
            if (productData[i].category_id == categoryId || AppUtil.isEmpty(categoryId)) {
                newData.push(productData[i]);
            }
        }
                
        $('#'+ productInputId).select2({  "data":newData  });        
    },
    
    /**
     * productData:[{id:0,text:'AAAAAA'},{id:1,text:'Bbb'},{id:2,text:'CCCCCC'},{id:3,text:'DDDD'},{id:4,text:'wontfix'}]
     */
    setSupplierProductSelect2BySupplierId: function( productData, productInputId, supplierInputId ) {
        var supplierId = $('#'+ supplierInputId).val();
        var newData = [{id: "", text: "-- กรุณาเลือก --"}];
        if (AppUtil.isNotEmpty(supplierId)) {
            for (var i=0; i<productData.length; i++) {
                if (productData[i].supplier_id == supplierId || AppUtil.isEmpty(supplierId)) {
                    newData.push(productData[i]);
                }
            }
        }

        $('#'+ productInputId).select2({  "data":newData  });        
    },
    
    
    setProductAutoCompleteByCategoryId: function( productData, productInputId, categoryInputId ) {
        var categoryId = $('#'+ categoryInputId).val();
        var newData = [];
        for (var i=0; i<productData.length; i++) {
            if (productData[i].category_id == categoryId || AppUtil.isEmpty(categoryId)) {
                newData.push(productData[i].text);
            }
        }
                
        $('#'+ productInputId).autocomplete('option', 'source', newData);
    },
    
};


