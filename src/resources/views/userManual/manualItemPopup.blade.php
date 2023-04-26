

@section('manualItemPopupJs')


    $("#manualItemPopup").dialog({
        width:680,
        height:340,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 

            "ตกลง": function() {
                 manualItemPopup_submit();
            },                 
            "ยกเลิก": function() {
                $("#manualItemPopup").dialog('close');
            }
        }      
    });
    
    
    window.manualItemPopup_submit = function() {           
        var rowId = $("#manualItemPopup_rowId").val();
        var type = $("#manualItemPopup_itemType").val(); 
        var name = $("#manualItemPopup_itemName").val(); 
        
        if (AppUtil.isEmpty(type) || AppUtil.isEmpty(name)) {
           alert("โปรดระบุข้อมูลประเภทและชื่อ");
           return;
        }
        
        if (manualItemPopup_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }
                
        
        var data = {
           "itemType": type,
           "itemName": name, 
           "file": $("#manualItemPopup_file").val(),
        };
        
        window.manualItemPopupSubmitCallback(rowId, data);     
        
        
        $("#manualItemPopup").dialog('close');
    }
    
    window.manualItemPopup_openPopupForAdd = function() { 

        $("#manualItemPopup_rowId").val(""); 
        $("#manualItemPopup_itemType").val("");         
        $("#manualItemPopup_itemName").val("");   
        $("#manualItemPopup_file").val("");   
        ImageFileUtil.simpleClear( manualItemPopup_uploader );   
        
        $("#manualItemPopup").dialog('open'); 
    }
    
    window.manualItemPopup_openPopupForEdit = function(rowId, data) { 
        $("#manualItemPopup_rowId").val(rowId); 
        $("#manualItemPopup_itemType").val(data['itemType']);         
        $("#manualItemPopup_itemName").val(data['itemName']);   
        $("#manualItemPopup_file").val(data['file']);   
        
        ImageFileUtil.simpleInsert( manualItemPopup_uploader,  data['image']);        
        $("#manualItemPopup").dialog('open'); 
    }
    
    if (!window.manualItemPopupSubmitCallback) {
         window.manualItemPopupSubmitCallback = function(rowId, data) {} ;
    }    

    

<?php if ($pageOptFileUpload):?>
    
        window.manualItemPopup_uploader = ImageFileUtil.createSimpleUploader( {
            uploaderName: "manualItemPopup_uploader1",
            containerId: "manualItemPopup_uploaderCont",
            valueId: "manualItemPopup_file",
            delete: true,
            fileType: 'pdf'
        });

<?php endif;?> 
        
        
@endsection



@section('manualItemPopupHtml')


<div id="manualItemPopup" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล">


        <div style="padding:20px">
                <input  type="hidden" id="manualItemPopup_rowId"  />   
                <input type='hidden' id='manualItemPopup_file' />                
                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
                
                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>
                        <tr>
                            <td class="formLabel">ประเภท:</td>
                             <td><select id="manualItemPopup_itemType" style="width:500px">
                                     <option value='รายการ'>รายการ</option>
                                     <option value='กลุ่ม'>กลุ่ม</option>       
                                </select>
                            </td>
                        </tr>   
                        <tr>
                            <td class="formLabel">ชื่อ:</td>
                            <td><input class="textInput"  type="text" style="width:500px"  id="manualItemPopup_itemName" value='' ></td>
                        </tr>      
                        <tr>
                            <td class="formLabel">ไฟล์คู่มือ:</td>
                            <td >
                                <div  id="manualItemPopup_uploaderCont"></div>
                            </td>   
                        </tr>         

                    </tbody>
                </table>
                
        </div>

</div>

@endsection

