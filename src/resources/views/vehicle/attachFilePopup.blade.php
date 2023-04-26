

@section('attachFilePopupJs')


    $("#attachFilePopup").dialog({
        width:680,
        height:350,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 attachFilePopup_submit();
            },                 
            "ยกเลิก": function() {
                $("#attachFilePopup").dialog('close');
            }
        }      
    });
    
    
    window.attachFilePopup_submit = function() {           
        var rowId = $("#attachFilePopup_rowId").val();

        if (attachFilePopup_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }
                
        var data = {
           "file": attachFilePopup_uploader.getDataStringForSubmit(),
        };
        
        window.attachFilePopupSubmitCallback(rowId, data);     

        $("#attachFilePopup").dialog('close');
    }
    
    
    window.attachFilePopup_clearData = function() { 
        $("#attachFilePopup_rowId").val(""); 
        $("#attachFilePopup").dialog('open'); 
    }
    
    window.attachFilePopup_openPopupForAdd = function() { 
        attachFilePopup_clearData();
           
        attachFilePopup_uploader.clearAllItem();
        
        $("#attachFilePopup").dialog('open'); 
    }
    
    window.attachFilePopup_openPopupForEdit = function(rowId, data, callback) { 
        $("#attachFilePopup_rowId").val(rowId); 

        window.attachFilePopupSubmitCallback = callback;

        attachFilePopup_uploader.addDataStringFromServer( data['fileDatas']); 
        
        $("#attachFilePopup").dialog('open'); 
    }
    


<?php if ($pageOptFileUpload):?>
    

    window.attachFilePopup_uploader = new BatchUploader( {
        uploaderName: "attachFilePopup_uploader1",
        containerId: "attachFilePopup_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    
    
<?php endif;?> 
        
        
@endsection



@section('attachFilePopupHtml')


<div id="attachFilePopup" style='padding:2px 0px; display:none;' title="แนบไฟล์"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="attachFilePopup_rowId"  />   
      
                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
                
                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>  
                        <tr>
                            <td class="formLabel">แนบไฟล์:</td>
                            <td >
                                <div id="attachFilePopup_uploaderCont"></div>
                            </td>   
                        </tr>    
                    </tbody>
                </table>
                
        </div>

</div> 
 <!-- -->
@endsection

