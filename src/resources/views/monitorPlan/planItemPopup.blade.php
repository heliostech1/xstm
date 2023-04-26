

@section('planItemPopupJs')


    $("#planItemPopup").dialog({
        width:680,
        height:400,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 planItemPopup_submit();
            },                 
            "ยกเลิก": function() {
                $("#planItemPopup").dialog('close');
            }
        }      
    });
    
    
    window.planItemPopup_submit = function() {           
        var rowId = $("#planItemPopup_rowId").val();

        if ( AppUtil.isEmpty( $("#planItemPopup_monitorTopic").val() )) {
           alert("โปรดระบุข้อมูลหัวข้อซ่อมบำรุง");
           return;
        }
        
    //    if ( AppUtil.isEmpty( $("#planItemPopup_itemName").val() )) {
    //       alert("โปรดระบุข้อมูลชื่อ");
    //       return;
    //    }
        
     //   if (planItemPopup_uploader.uploadStatus == "uploading") {
     //      alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
     //      return;
     //   }
                
        var data = {
           "monitorTopic": $("#planItemPopup_monitorTopic").val(),
           "itemName": $("#planItemPopup_itemName").val(), 
           "itemCode": $("#planItemPopup_itemCode").val(),
           "dataType": $("#planItemPopup_dataType").val(),
           "warnAmount": $("#planItemPopup_warnAmount").val(), 
           "alertAmount": $("#planItemPopup_alertAmount").val(),
                    
        };
        
        window.planItemPopupSubmitCallback(rowId, data);     
        
        
        $("#planItemPopup").dialog('close');
    }
    
    
    window.planItemPopup_clearData = function() { 
        $("#planItemPopup_rowId").val(""); 
        
        $("#planItemPopup_monitorTopic").val("");         
        $("#planItemPopup_itemName").val("");   
        $("#planItemPopup_itemCode").val("");   
        $("#planItemPopup_dataType").val(""); 
        $("#planItemPopup_warnAmount").val("");   
        $("#planItemPopup_alertAmount").val("");   
        
        $("#planItemPopup").dialog('open'); 
    }
    
    window.planItemPopup_openPopupForAdd = function() { 
        planItemPopup_clearData();
        ImageFileUtil.simpleClear( planItemPopup_uploader );
                
        $("#planItemPopup").dialog('open'); 
    }
    
    window.planItemPopup_openPopupForEdit = function(rowId, data) { 
    
        $("#planItemPopup_rowId").val(rowId); 
        
        $("#planItemPopup_monitorTopic").val(data['monitorTopic']);         
        $("#planItemPopup_itemName").val(data['itemName']);   
        $("#planItemPopup_itemCode").val(data['itemCode']);   
        $("#planItemPopup_dataType").val(data['dataType']); 
        $("#planItemPopup_warnAmount").val(data['warnAmount']);   
        $("#planItemPopup_alertAmount").val(data['alertAmount']);  
        
        //ImageFileUtil.simpleInsert( planItemPopup_uploader,  data['image']);
                
        $("#planItemPopup").dialog('open'); 
    }
    
    if (!window.planItemPopupSubmitCallback) {
         window.planItemPopupSubmitCallback = function(rowId, data) {} ;
    }    


<?php if ($pageOptFileUpload):?>
    
        window.planItemPopup_uploader = ImageFileUtil.createSimpleUploader( {
            uploaderName: "planItemPopup_uploader1",
            containerId: "planItemPopup_uploaderCont",
            valueId: "planItemPopup_image",
            delete: true,
        });

<?php endif;?> 
        
        
@endsection



@section('planItemPopupHtml')

        
        
<div id="planItemPopup" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="planItemPopup_rowId"  />   
                <input type='hidden' id='planItemPopup_image' />

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
                
                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>  
                        <tr>
                            <td class="formLabel" style='width:150px'>ห้วข้อการซ่อมบำรุง:</td>
                            <td>{!! SiteHelper::dropdown('', $monitorTopicOpt, '', "class='textInput'  id='planItemPopup_monitorTopic' style='width:350px' ") !!}
                        </tr>    
                        <tr>
                            <td class="formLabel">ชื่อ:</td>
                            <td><input class="textInput"  type="text" style="width:350px"  id="planItemPopup_itemName" value='' ></td>
                        </tr>                           
                        <tr>
                            <td class="formLabel">รหัส:</td>
                            <td><input class="textInput"  type="text" style="width:350px"  id="planItemPopup_itemCode" value='' ></td>
                        </tr>           
                        <tr>
                            <td class="formLabel">ชนิดข้อมูลที่ใช้ตรวจสอบ:</td>
                            <td>{!! SiteHelper::dropdown('', $monitorDataTypeOpt, '', "class='textInput'  id='planItemPopup_dataType' style='width:350px' ") !!}
                        </tr>  
                        <tr>
                            <td class="formLabel">แจ้ง Warning ทุกๆ:</td>
                            <td><input class="textInput"  type="text" style="width:350px"  id="planItemPopup_warnAmount" value='' ></td>
                        </tr>  
                        <tr>
                            <td class="formLabel">แจ้ง Alert ทุกๆ:</td>
                            <td><input class="textInput"  type="text" style="width:350px"  id="planItemPopup_alertAmount" value='' ></td>
                        </tr>                          
                    </tbody>
                </table>
                
        </div>

</div> 
 <!-- -->
@endsection

