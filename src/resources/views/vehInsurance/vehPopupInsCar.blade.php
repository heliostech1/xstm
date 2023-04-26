

@section('popupInsCarHelper')

@endsection

@section('popupInsCarJs')

    $("#popupInsCar").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupInsCar_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupInsCar").dialog('close');
            }
        }      
    });   
            
    
    <?php $popupInsCarFields = array("insType","insNo","company","insPerson","benefitPerson",
        "agreeDate","issueDate","insStartDate","insEndDate","amount",
        "fundDamage", "fundLost"
     ); ?>

            
    
    window.popupInsCar_submit = function() {           
        var rowId = $("#popupInsCar_rowId").val();

        if (popupInsCar_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

            
        var data = {
            <?php foreach ($popupInsCarFields as $field): ?>
               <?php echo " '".$field."' : $('#popupInsCar_".$field."').val()," ?>
            <?php endforeach; ?>
        
            "fileDatas": popupInsCar_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupInsCarSubmitCallback(rowId, data);     

        $("#popupInsCar").dialog('close');
    }
    
    
    window.popupInsCar_clearData = function() { 
        $("#popupInsCar_rowId").val(""); 
        
        <?php foreach ($popupInsCarFields as $field): ?>
           <?php echo "  $('#popupInsCar_".$field."').val(''); " ?>
        <?php endforeach; ?>
        
        popupInsCar_uploader.clearAllItem();
        
    }
    
    window.popupInsCar_openPopupForAdd = function(callback) { 
        window.popupInsCarSubmitCallback = callback;   
      
        popupInsCar_clearData();
        $("#popupInsCar").dialog('open'); 
    }
    
    window.popupInsCar_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupInsCar_rowId").val(rowId); 

        <?php foreach ($popupInsCarFields as $field): ?>
           <?php echo " $('#popupInsCar_".$field."').val( data['".$field."'] ); " ?>
        <?php endforeach; ?>
        
        popupInsCar_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupInsCarSubmitCallback = callback;        
        $("#popupInsCar").dialog('open'); 
    }
    

    window.popupInsCar_uploader = new BatchUploader( {
        uploaderName: "popupInsCar_uploader",
        containerId: "popupInsCar_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    


    $('#popupInsCar_agreeDate').datepicker();
    $('#popupInsCar_issueDate').datepicker();
    $('#popupInsCar_insStartDate').datepicker();
    $('#popupInsCar_insEndDate').datepicker();       
@endsection



@section('popupInsCarHtml')


<div id="popupInsCar" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupInsCar_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:220px' >ประเภทประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id='popupInsCar_insType'></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">กรมธรรม์ประกันภัยเลขที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_insNo"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ชื่อบริษัทประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_company"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ผู้เอาประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupInsCar_insPerson"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ผู้รับผลประโยชน์:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupInsCar_benefitPerson"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr >
                            <td class="formLabel" >วันทำสัญญาประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_agreeDate"  autocomplete="off"  ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">วันทำกรมธรรม์ประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_issueDate"  autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ระยะเวลาประกันภัย:</td>
                            <td>เริ่มต้น &nbsp;<input class="textInput" type="text" style="width:90px"  id="popupInsCar_insStartDate"  autocomplete="off"  > 
                                &nbsp;ถึง &nbsp; <input class="textInput" type="text" style="width:90px"  id="popupInsCar_insEndDate"  autocomplete="off"  >
                            </td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เบี้ยประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_amount"></td>
                        </tr> 
   
                        <!-- ======================================================= -->
      
                        <tr >
                            <td class="formLabel"  >ทุนประกัน ความเสียหายต่อรถยนต์:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_fundDamage"></td>
                        </tr>    
                        <tr >
                            <td class="formLabel"  >ทุนประกัน รถยนต์สูญหายหรือไฟไหม้ :</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsCar_fundLost"></td>
                        </tr>       
                        <tr>
                            <td class="formLabel">แนบไฟล์กรมธรรม์:</td>
                            <td><div id='popupInsCar_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

