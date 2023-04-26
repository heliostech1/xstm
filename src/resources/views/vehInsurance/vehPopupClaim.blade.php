

@section('popupClaimHelper')

@endsection

@section('popupClaimJs')

    $("#popupClaim").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupClaim_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupClaim").dialog('close');
            }
        }      
    });
    

            
    <?php $popupClaimFields = array("times","claimDate","claimType","insNo","claimNo",
        "actDate", "actTime", "actDriver","fixStartDate","fixEndDate",
        "fixCost","detail"
     ); ?>
    
    window.popupClaim_submit = function() {           
        var rowId = $("#popupClaim_rowId").val();

        if (popupClaim_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

            
        var data = {
            <?php foreach ($popupClaimFields as $field): ?>
               <?php echo " '".$field."' : $('#popupClaim_".$field."').val()," ?>
            <?php endforeach; ?>
        
            "fileDatas": popupClaim_uploader.getDataStringForSubmit(),    
        };
         

        window.popupClaimSubmitCallback(rowId, data);     

        $("#popupClaim").dialog('close');
    }
    
    
    window.popupClaim_clearData = function() { 
        $("#popupClaim_rowId").val(""); 
        
        
        <?php foreach ($popupClaimFields as $field): ?>
           <?php echo "  $('#popupClaim_".$field."').val(''); " ?>
        <?php endforeach; ?>
        
        popupClaim_uploader.clearAllItem();
        
    }
    
    window.popupClaim_openPopupForAdd = function(callback) { 
        window.popupClaimSubmitCallback = callback;   
      
        popupClaim_clearData();
        $("#popupClaim").dialog('open'); 
    }
    
    window.popupClaim_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupClaim_rowId").val(rowId); 

        <?php foreach ($popupClaimFields as $field): ?>
           <?php echo " $('#popupClaim_".$field."').val( data['".$field."'] ); " ?>
        <?php endforeach; ?>
        
        popupClaim_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupClaimSubmitCallback = callback;        
        $("#popupClaim").dialog('open'); 
    }
    

    window.popupClaim_uploader = new BatchUploader( {
        uploaderName: "popupClaim_uploader",
        containerId: "popupClaim_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    


    $('#popupClaim_claimDate').datepicker();
    $('#popupClaim_actDate').datepicker();
    $('#popupClaim_fixStartDate').datepicker();
    $('#popupClaim_fixEndDate').datepicker();
    
    AppUtil.setAsTimeInput('#popupClaim_actTime');
      
    $('#popupClaim_insNoAddLink').click(function(e) {
        e.preventDefault();
        
        var result = popupClaim_getDefaultInsNo();
        if (result) {
           $('#popupClaim_insNo').val(result);
        }        
    });    

    
    window.popupClaim_getDefaultInsNo = function() { 
       var claimType = $('#popupClaim_claimType').val();

       if (claimType == 'พรบ') {
           var datas = PartInsActTableHelper.getDataForSubmit();
           return DTHelper.getLastRowCellData(datas, 'insNo');
       }
       else if (claimType == 'ประกันภัยรถยนต์') {
           var datas = PartInsCarTableHelper.getDataForSubmit();
           return DTHelper.getLastRowCellData(datas, 'insNo');
       }
       else if (claimType == 'ประกันภัยสินค้า') {
           var datas = PartInsGoodsTableHelper.getDataForSubmit();
           return DTHelper.getLastRowCellData(datas, 'insNo');
       }
       
       return "";
    }
    
@endsection



@section('popupClaimHtml')


<div id="popupClaim" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->



        <div style="padding:20px">
                <input  type="hidden" id="popupClaim_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
            
                <input type='hidden' id='popupClaim_gasTankNumber'  />

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >ครั้งที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id='popupClaim_times'></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">วันที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupClaim_claimDate"  autocomplete="off" ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ชนิดการเคลม:</td>
                            <td> {!! SiteHelper::dropdown("", $claimTypeOpt, '', " id='popupClaim_claimType' class='textInput' style='width:400px'  ") !!} </td> 
                        </tr>                         
                        <tr>
                            <td class="formLabel">เลขที่กรมธรรม์:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupClaim_insNo">
                                &nbsp;<a id="popupClaim_insNoAddLink" href="javascript:void(0);" >(เติมอัตโนมัติ)</a>                            
                            </td>
                        </tr> 
                        <tr>
                            <td class="formLabel">หมายเลขการเคลม:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupClaim_claimNo"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr >
                            <td class="formLabel" >วันเวลาที่เกิดเหตุ:</td>
                            <td>&nbsp; วัน &nbsp;<input class="textInput" type="text" style="width:90px"  id="popupClaim_actDate" autocomplete="off" >
                                &nbsp; เวลา &nbsp;<input class="textInput" type="text" style="width:60px"  id="popupClaim_actTime" autocomplete="off" >
                            </td>
                        </tr>        
                        <tr>
                            <td class="formLabel">ชื่อผู้ขับขี่ขณะเกิดเหตุ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupClaim_actDriver"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันที่เข้าซ่อม:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupClaim_fixStartDate" autocomplete="off" ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันที่ซ่อมเสร็จ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupClaim_fixEndDate" autocomplete="off" ></td>
                        </tr> 
        

                        <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">ค่าใช้จ่ายในการเคลม:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupClaim_fixCost"></td>
                        </tr>        
                        <tr >
                            <td class="formLabel"  >บันทึกข้อมูล:</td>  
                            <td><textarea class="textAreaInput" type="text" style="width:400px" value='' id="popupClaim_detail"></textarea></td>
                        </tr>                         
                        <tr>
                            <td class="formLabel">แนบเอกสาร:</td>
                            <td><div id='popupClaim_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

