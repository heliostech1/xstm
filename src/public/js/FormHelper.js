/*
 *  Form Helper
 */

var FormHelper = {
    
    applyViewMode : function(formId) {
        var selector = "#" + formId;
        
        $(selector).find(".textInput").addClass('textReadOnly').removeClass(
                "textInput").attr('readonly', 'true');
        
        $(selector).find(".textAreaInput").addClass('textAreaReadOnly').removeClass(
               "textAreaInput").attr('readonly', 'true');
        
        $(selector).find("select").attr('disabled', true);
        $(selector).find("input:checkbox").attr('disabled', true);
    },
    
    applyEditMode : function(formId) {
        var selector = "#" + formId;
        
        $(selector).find(".textReadOnly").addClass('textInput').removeClass(
                "textReadOnly").attr('readonly', 'false');
        
        $(selector).find(".textAreaReadOnly").addClass('textAreaInput').removeClass(
                "textAreaReadOnly").attr('readonly', 'false');
        
        $(selector).find("select").attr('disabled', false);
        
    },
    
    clearValue: function(formId) {
        var selector = "#" + formId;
        $(selector).find(".textInput").val('');
        
        $( selector + " select.textInput.select2-offscreen" ).each(function( index ) {
            //console.log( index + ": " + $( this ).attr('id') );
            var select2Id = $( this ).attr('id') ;
            $("#" + select2Id).select2("val", "");
        });

        //$(selector).find("select").val('');
    },
    
    // ============================================= make enter behave like tab
    setEnterBehaveTab: function() {

       // input ทั่วไปใช้ keydown 
        $('body').on('keydown', 'input.textInput, select', function(e) { 
            FormHelper.applyEnterBehaveTab(this, e);
        });

       // input select2 ใช้ keyup ( keyup อาจจะเกิดปัญหา เช่น focus ผิดตอนเปิด dialog จึงเลี่ยงไม่ใช้กับ input ทั่วไป
        $("body").on('keyup', 'input, textarea', function(e) { 
            if ( $(this).hasClass("textInput")) {
                return;
            }
            
            FormHelper.applyEnterBehaveTab(this, e);
        });
        
    },
    
    applyEnterBehaveTab: function(obj, e) {
        if (e.keyCode !== 13) return;

        var self = $(obj), focusable, next, dialog, cont;
        //console.log(self);

        if (self.is(":button") || self.attr('type') == "submit" || self.hasClass("ignoreEnterBehaveTab") ) {
            return true;
        }

        cont = self.parents('form:eq(0), div.ui-dialog, fieldset'); // find container
        cont = (cont && cont.length && cont.length > 0) ? cont : null;

        if (cont) {
            // console.log(cont);
            focusable = cont.find('input,a,select,button,textarea').filter(':visible');
            // focusable = focusable.filter(':input:not([readonly])');
            //console.log(focusable);

            for (var i = focusable.length - 1; i >= 0; i--) {
                if (focusable[i]['tabIndex'] == -1  ) {
                    focusable.splice(i, 1);
                }
            }
            //console.log(focusable);   
            next = focusable.eq(focusable.index(obj) + 1);

            if (next.length) {
                next.focus();
            } else {
                //form.submit();
            }
            return false;
        }
                    
    },
    
    
    debug: function() {
      //............
    }
        
};
