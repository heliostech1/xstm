

@section('popupTaxHelper')

@endsection

@section('popupTaxJs')

    $("#popupTax").dialog({
        width:800,
        height:400,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupTax_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupTax").dialog('close');
            }
        }      
    });
    
    
    window.popupTax_submit = function() {           
        var rowId = $("#popupTax_rowId").val();

        if (popupTax_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }


            
        var data = {
            "taxDate" : $('#popupTax_taxDate').val(),
            "dueDate": $('#popupTax_dueDate').val(),
            "taxAmount": $('#popupTax_taxAmount').val(),
            "extraAmount": $('#popupTax_extraAmount').val(),
            "fileDatas": popupTax_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupTaxSubmitCallback(rowId, data);     

        $("#popupTax").dialog('close');
    }
    
    
    window.popupTax_clearData = function() { 
        $("#popupTax_rowId").val(""); 
        
        
        $('#popupTax_taxDate').val("");
        $('#popupTax_dueDate').val("");
        $("#popupTax_taxAmount").val("");
        $('#popupTax_extraAmount').val("");
        popupTax_uploader.clearAllItem();
        
    }
    
    window.popupTax_openPopupForAdd = function(callback) { 
        window.popupTaxSubmitCallback = callback;   
      
        popupTax_clearData();
        $("#popupTax").dialog('open'); 
    }
    
    window.popupTax_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupTax_rowId").val(rowId); 

        $('#popupTax_taxDate').val( data['taxDate']  );
        $('#popupTax_dueDate').val( data['dueDate']  );
        $("#popupTax_taxAmount").val( data['taxAmount']  );
        $('#popupTax_extraAmount').val( data['extraAmount']  );
        popupTax_uploader.addDataStringFromServer( data['fileDatas']); 
   
        window.popupTaxSubmitCallback = callback;        
        $("#popupTax").dialog('open'); 
    }
    

    window.popupTax_uploader = new BatchUploader( {
        uploaderName: "popupTax_uploader",
        containerId: "popupTax_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    $('#popupTax_gasTankTableAddLink').click(function(e) {
        e.preventDefault();
        RegisGasNumberTable.addData();
    });

    $('#popupTax_taxDate').datepicker();
    $('#popupTax_dueDate').datepicker();

@endsection



@section('popupTaxHtml')


<div id="popupTax" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->

        <div style="padding:20px">
                <input  type="hidden" id="popupTax_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
            
                <input type='hidden' id='popupTax_gasTankNumber'  />

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >วันเสียภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupTax_taxDate"  autocomplete="off"  ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">วันครบกำหนดเสียภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupTax_dueDate"  autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ค่าภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupTax_taxAmount"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เงินเพิ่ม:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupTax_extraAmount"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ภาพรายการเสียภาษี:</td>
                            <td><div id='popupTax_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

