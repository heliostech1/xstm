

@section('popupInsActHelper')

@endsection

@section('popupInsActJs')

    $("#popupInsAct").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupInsAct_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupInsAct").dialog('close');
            }
        }      
    });
    
    <?php $popupInsActFields = array("company","insNo","insPerson","address","agreeDate",
        "amount","insStartDate","insEndDate","carCode","carName",
        "carLicensePlate","carBodyNumber","carBodyType","carSize"
     ); ?>

            
    window.popupInsAct_submit = function() {           
        var rowId = $("#popupInsAct_rowId").val();

        if (popupInsAct_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }
            
        var data = {
            <?php foreach ($popupInsActFields as $field): ?>
               <?php echo " '".$field."' : $('#popupInsAct_".$field."').val()," ?>
            <?php endforeach; ?>
        
            "fileDatas": popupInsAct_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupInsActSubmitCallback(rowId, data);     

        $("#popupInsAct").dialog('close');
    }
    
            
    
    window.popupInsAct_clearData = function() { 

        $("#popupInsAct_rowId").val(""); 
        
        <?php foreach ($popupInsActFields as $field): ?>
           <?php echo "  $('#popupInsAct_".$field."').val('');" ?>
        <?php endforeach; ?>
         
        popupInsAct_uploader.clearAllItem();
        
    }
    
    window.popupInsAct_openPopupForAdd = function(callback) { 
        window.popupInsActSubmitCallback = callback;   
      
        popupInsAct_clearData();
        $("#popupInsAct").dialog('open'); 
    }
    
    window.popupInsAct_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupInsAct_rowId").val(rowId); 
        
        <?php foreach ($popupInsActFields as $field): ?>
           <?php echo " $('#popupInsAct_".$field."').val( data['".$field."'] );" ?>
        <?php endforeach; ?>
             
        popupInsAct_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupInsActSubmitCallback = callback;        
        $("#popupInsAct").dialog('open'); 
    }
    

    window.popupInsAct_uploader = new BatchUploader( {
        uploaderName: "popupInsAct_uploader",
        containerId: "popupInsAct_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    
    
    $('#partInsAct_addCarDefaultLink').click(function(e) {
        e.preventDefault();
        $('#popupInsAct_carName').val('<?php echo $defaultCarName ?>');
        $('#popupInsAct_carLicensePlate').val('<?php echo $defaultLicensePlate ?>');
        $('#popupInsAct_carBodyNumber').val('<?php echo $defaultCarBody ?>');
      
    });    

    $('#popupInsAct_agreeDate').datepicker();
    $('#popupInsAct_insStartDate').datepicker();
    $('#popupInsAct_insEndDate').datepicker();    
    
    
@endsection



@section('popupInsActHtml')



            
<div id="popupInsAct" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupInsAct_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >บริษัทประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id='popupInsAct_company'></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">กรมธรรม์ประกันภัยเลขที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_insNo"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ชื่อผู้เอาประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_insPerson"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ที่อยู่:</td>
                            <td><textarea class="textAreaInput" type="text" style="width:400px" value='' id="popupInsAct_address"></textarea></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันที่ทำพรบ:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupInsAct_agreeDate"  autocomplete="off"  ></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr >
                            <td class="formLabel" >จำนวนเบี้ยประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_amount"></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">ระยะเวลาประกันภัย:</td>
                            <td>เริ่มต้น &nbsp;<input class="textInput" type="text" style="width:90px"  id="popupInsAct_insStartDate"  autocomplete="off"  > 
                                &nbsp;ถึง &nbsp; <input class="textInput" type="text" style="width:90px"  id="popupInsAct_insEndDate"  autocomplete="off"  >
                            </td>
                        </tr> 

                        <tr>
                            <td class="formLabel" style="padding-top:10px"><b>รายการรถยนต์ที่เอาประกันภัย</b></td>
                            <td style="padding-top:10px">&nbsp;<a id="partInsAct_addCarDefaultLink" href="javascript:void(0);" >(เติมอัตโนมัติ)</a></td>
                        </tr>   
                        
                        <tr>
                            <td class="formLabel">รหัส:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carCode"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ชื่อรถยนต์/รุ่น:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carName"></td>
                        </tr> 
                        
                        
                        <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">เลขทะเบียน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carLicensePlate"></td>
                        </tr>  

                        <tr>
                            <td class="formLabel">เลขตัวถัง:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carBodyNumber"></td>
                        </tr>        
                        
                        <tr >
                            <td class="formLabel"  >แบบตัวถัง:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carBodyType"></td>
                        </tr>    
                        <tr >
                            <td class="formLabel"  >จำนวนที่นั่ง/ขนาด/น้ำหนัก:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsAct_carSize"></td>
                        </tr>                           
                        <tr>
                            <td class="formLabel">ภาพตารางกรมธรรม์:</td>
                            <td><div id='popupInsAct_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

