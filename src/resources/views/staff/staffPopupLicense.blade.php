

@section('popupLicenseHelper')

@endsection

@section('popupLicenseJs')

    $("#popupLicense").dialog({
        width:800,
        height:400,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupLicense_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupLicense").dialog('close');
            }
        }      
    });   
            
    
    <?php $popupLicenseFields = array("licenseType","issueNo","issueDate","expDate" ); ?>

            
    
    window.popupLicense_submit = function() {           
        var rowId = $("#popupLicense_rowId").val();

        if (popupLicense_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

            
        var data = {
            <?php foreach ($popupLicenseFields as $field): ?>
               <?php echo " '".$field."' : $('#popupLicense_".$field."').val()," ?>
            <?php endforeach; ?>
        
            "fileDatas": popupLicense_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupLicenseSubmitCallback(rowId, data);     

        $("#popupLicense").dialog('close');
    }
    
    
    window.popupLicense_clearData = function() { 
        $("#popupLicense_rowId").val(""); 
        
        <?php foreach ($popupLicenseFields as $field): ?>
           <?php echo "  $('#popupLicense_".$field."').val(''); " ?>
        <?php endforeach; ?>
        
        popupLicense_uploader.clearAllItem();
        
    }
    
    window.popupLicense_openPopupForAdd = function(callback) { 
        window.popupLicenseSubmitCallback = callback;   
      
        popupLicense_clearData();
        $("#popupLicense").dialog('open'); 
    }
    
    window.popupLicense_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupLicense_rowId").val(rowId); 

        <?php foreach ($popupLicenseFields as $field): ?>
           <?php echo " $('#popupLicense_".$field."').val( data['".$field."'] ); " ?>
        <?php endforeach; ?>
        
        popupLicense_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupLicenseSubmitCallback = callback;        
        $("#popupLicense").dialog('open'); 
    }
    

    window.popupLicense_uploader = new BatchUploader( {
        uploaderName: "popupLicense_uploader",
        containerId: "popupLicense_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    $('#popupLicense_issueDate').datepicker();
    $('#popupLicense_expDate').datepicker();
     
@endsection



@section('popupLicenseHtml')


<div id="popupLicense" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupLicense_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>

                
                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:220px' >ประเภทใบขับขี่:</td>
                            <td> {!! SiteHelper::dropdown("", $licenseTypeOpt, '', "  class='textInput' style='width:400px' id='popupLicense_licenseType' ") !!} </td> 
                        </tr>    
                        <tr>
                            <td class="formLabel">ฉบับที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupLicense_issueNo" ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันอนุญาต:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupLicense_issueDate" autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันหมดอายุ:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupLicense_expDate" autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ภาพใบขับขี่:</td>
                            <td><div id='popupLicense_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

