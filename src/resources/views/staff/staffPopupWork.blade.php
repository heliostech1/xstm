

@section('popupWorkHelper')

@endsection

@section('popupWorkJs')

    $("#popupWork").dialog({
        width:800,
        height:400,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupWork_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupWork").dialog('close');
            }
        }      
    });
    
    
    window.popupWork_submit = function() {           
        var rowId = $("#popupWork_rowId").val();

        if (popupWork_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }


            
        var data = {
            "taxDate" : $('#popupWork_taxDate').val(),
            "dueDate": $('#popupWork_dueDate').val(),
            "taxAmount": $('#popupWork_taxAmount').val(),
            "extraAmount": $('#popupWork_extraAmount').val(),
            "fileDatas": popupWork_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupWorkSubmitCallback(rowId, data);     

        $("#popupWork").dialog('close');
    }
    
    
    window.popupWork_clearData = function() { 
        $("#popupWork_rowId").val(""); 
        
        
        $('#popupWork_taxDate').val("");
        $('#popupWork_dueDate').val("");
        $("#popupWork_taxAmount").val("");
        $('#popupWork_extraAmount').val("");
        popupWork_uploader.clearAllItem();
        
    }
    
    window.popupWork_openPopupForAdd = function(callback) { 
        window.popupWorkSubmitCallback = callback;   
      
        popupWork_clearData();
        $("#popupWork").dialog('open'); 
    }
    
    window.popupWork_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupWork_rowId").val(rowId); 

        $('#popupWork_taxDate').val( data['taxDate']  );
        $('#popupWork_dueDate').val( data['dueDate']  );
        $("#popupWork_taxAmount").val( data['taxAmount']  );
        $('#popupWork_extraAmount').val( data['extraAmount']  );
        popupWork_uploader.addDataStringFromServer( data['fileDatas']); 
   
        window.popupWorkSubmitCallback = callback;        
        $("#popupWork").dialog('open'); 
    }
    

    window.popupWork_uploader = new BatchUploader( {
        uploaderName: "popupWork_uploader",
        containerId: "popupWork_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    $('#popupWork_gasTankTableAddLink').click(function(e) {
        e.preventDefault();
        RegisGasNumberTable.addData();
    });

    $('#popupWork_taxDate').datepicker();
    $('#popupWork_dueDate').datepicker();

@endsection



@section('popupWorkHtml')


<div id="popupWork" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->

        <div style="padding:20px">
                <input  type="hidden" id="popupWork_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
            
                <input type='hidden' id='popupWork_gasTankNumber'  />

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >วันเสียภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupWork_taxDate"  autocomplete="off"  ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">วันครบกำหนดเสียภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupWork_dueDate"  autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ค่าภาษี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupWork_taxAmount"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เงินเพิ่ม:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupWork_extraAmount"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ภาพรายการเสียภาษี:</td>
                            <td><div id='popupWork_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

