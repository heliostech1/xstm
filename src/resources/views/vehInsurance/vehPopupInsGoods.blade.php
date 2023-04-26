

@section('popupInsGoodsHelper')

@endsection

@section('popupInsGoodsJs')

    $("#popupInsGoods").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupInsGoods_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupInsGoods").dialog('close');
            }
        }      
    });
    

    
     <?php $popupInsGoodsFields = array("insType","insNo","company","insPerson","benefitPerson",
        "agreeDate","issueDate","insStartDate","insEndDate","amount",
        "fund", 
     ); ?>


            
    
    window.popupInsGoods_submit = function() {           
        var rowId = $("#popupInsGoods_rowId").val();

        if (popupInsGoods_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

            
        var data = {
            <?php foreach ($popupInsGoodsFields as $field): ?>
               <?php echo " '".$field."' : $('#popupInsGoods_".$field."').val()," ?>
            <?php endforeach; ?>
        
            "fileDatas": popupInsGoods_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupInsGoodsSubmitCallback(rowId, data);     

        $("#popupInsGoods").dialog('close');
    }
    
    
    window.popupInsGoods_clearData = function() { 
        $("#popupInsGoods_rowId").val(""); 
        
        <?php foreach ($popupInsGoodsFields as $field): ?>
           <?php echo "  $('#popupInsGoods_".$field."').val(''); " ?>
        <?php endforeach; ?>
        
        popupInsGoods_uploader.clearAllItem();
        
    }
    
    window.popupInsGoods_openPopupForAdd = function(callback) { 
        window.popupInsGoodsSubmitCallback = callback;   
      
        popupInsGoods_clearData();
        $("#popupInsGoods").dialog('open'); 
    }
    
    window.popupInsGoods_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupInsGoods_rowId").val(rowId); 

        <?php foreach ($popupInsGoodsFields as $field): ?>
           <?php echo " $('#popupInsGoods_".$field."').val( data['".$field."'] ); " ?>
        <?php endforeach; ?>
        
        popupInsGoods_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupInsGoodsSubmitCallback = callback;        
        $("#popupInsGoods").dialog('open'); 
    }
    

    window.popupInsGoods_uploader = new BatchUploader( {
        uploaderName: "popupInsGoods_uploader",
        containerId: "popupInsGoods_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    



    $('#popupInsGoods_agreeDate').datepicker();
    $('#popupInsGoods_issueDate').datepicker();
    $('#popupInsGoods_insStartDate').datepicker();
    $('#popupInsGoods_insEndDate').datepicker();       
    
    
@endsection



@section('popupInsGoodsHtml')


<div id="popupInsGoods" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupInsGoods_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >ประเภทประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id='popupInsGoods_insType'></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">กรมธรรม์ประกันภัยเลขที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_insNo"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ชื่อบริษัทประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_company"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ผู้เอาประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupInsGoods_insPerson"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ผู้รับผลประโยชน์:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupInsGoods_benefitPerson"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr >
                            <td class="formLabel" >วันทำสัญญาประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_agreeDate" autocomplete="off"  ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">วันทำกรมธรรม์ประกันภัย:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_issueDate" autocomplete="off" ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ระยะเวลาประกันภัย:</td>
                            <td>เริ่มต้น &nbsp;<input class="textInput" type="text" style="width:90px"  id="popupInsGoods_insStartDate" autocomplete="off" > 
                                &nbsp;ถึง &nbsp; <input class="textInput" type="text" style="width:90px"  id="popupInsGoods_insEndDate" autocomplete="off" >
                            </td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เบี้ยประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_amount"></td>
                        </tr> 
   
                        <!-- ======================================================= -->
      
                        <tr >
                            <td class="formLabel"  >ทุนประกัน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupInsGoods_fund"></td>
                        </tr>    
                        <tr>
                            <td class="formLabel">แนบไฟล์กรมธรรม์:</td>
                            <td><div id='popupInsGoods_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

