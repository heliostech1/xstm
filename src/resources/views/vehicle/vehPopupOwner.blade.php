

@section('popupOwnerHelper')

@endsection

@section('popupOwnerJs')

    $("#popupOwner").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupOwner_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupOwner").dialog('close');
            }
        }      
    });
    
    
    window.popupOwner_submit = function() {           
        var rowId = $("#popupOwner_rowId").val();

        if (popupOwner_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

            
        var data = {
            "ownerDate" : $('#popupOwner_ownerDate').val(),
            "ownerName": $('#popupOwner_ownerName').val(),
            "ownerBirthDate": $('#popupOwner_ownerBirthDate').val(),
            "ownerAddress": $('#popupOwner_ownerAddress').val(),
            "ownerPhone": $('#popupOwner_ownerPhone').val(),
            
            "holderName": $('#popupOwner_holderName').val(),     
            "cardNumber": $('#popupOwner_cardNumber').val(),   
            "holderBirthDate": $('#popupOwner_holderBirthDate').val(),      
            "holderNation": $('#popupOwner_holderNation').val(),
            "holderAddress": $('#popupOwner_holderAddress').val(),
            
            "holderPhone": $('#popupOwner_holderPhone').val(),
            "leaseContractNumber": $('#popupOwner_leaseContractNumber').val(),
            "fileDatas": popupOwner_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupOwnerSubmitCallback(rowId, data);     

        $("#popupOwner").dialog('close');
    }
    
    
    window.popupOwner_clearData = function() { 
        $("#popupOwner_rowId").val(""); 
        
        
        $('#popupOwner_ownerDate').val("");
        $('#popupOwner_ownerName').val("");
        $("#popupOwner_ownerBirthDate").val("");
        $('#popupOwner_ownerAddress').val("");
        $('#popupOwner_ownerPhone').val("");

        $('#popupOwner_holderName').val("");     
        $('#popupOwner_cardNumber').val("");   
        $('#popupOwner_holderBirthDate').val("");      
        $('#popupOwner_holderNation').val("");
        $('#popupOwner_holderAddress').val("");

        $('#popupOwner_holderPhone').val("");
        $('#popupOwner_leaseContractNumber').val("");
        popupOwner_uploader.clearAllItem();
        
    }
    
    window.popupOwner_openPopupForAdd = function(callback) { 
        window.popupOwnerSubmitCallback = callback;   
      
        popupOwner_clearData();
        $("#popupOwner").dialog('open'); 
    }
    
    window.popupOwner_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupOwner_rowId").val(rowId); 

        $('#popupOwner_ownerDate').val( data['ownerDate']  );
        $('#popupOwner_ownerName').val( data['ownerName']  );
        $("#popupOwner_ownerBirthDate").val( data['ownerBirthDate']  );
        $('#popupOwner_ownerAddress').val( data['ownerAddress']  );
        $('#popupOwner_ownerPhone').val( data['ownerPhone']  );

        $('#popupOwner_holderName').val( data['holderName']  );     
        $('#popupOwner_cardNumber').val( data['cardNumber']  );   
        $('#popupOwner_holderBirthDate').val( data['holderBirthDate']  );      
        $('#popupOwner_holderNation').val( data['holderNation']  );
        $('#popupOwner_holderAddress').val( data['holderAddress']  );

        $('#popupOwner_holderPhone').val( data['holderPhone']  );
        $('#popupOwner_leaseContractNumber').val( data['leaseContractNumber']  );
        popupOwner_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupOwnerSubmitCallback = callback;        
        $("#popupOwner").dialog('open'); 
    }
    

    window.popupOwner_uploader = new BatchUploader( {
        uploaderName: "popupOwner_uploader",
        containerId: "popupOwner_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    $('#popupOwner_gasTankTableAddLink').click(function(e) {
        e.preventDefault();
        RegisGasNumberTable.addData();
    });



    $('#popupOwner_ownerDate').datepicker();
    $('#popupOwner_ownerBirthDate').datepicker();
    $('#popupOwner_holderBirthDate').datepicker();    
@endsection



@section('popupOwnerHtml')


<div id="popupOwner" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupOwner_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        
            
                <input type='hidden' id='popupOwner_gasTankNumber'  />

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >วันที่ครอบครองรถ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_ownerDate"  autocomplete="off"  ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">ผู้ถือกรรมสิทธ์:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_ownerName"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันเกิด:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_ownerBirthDate"  autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ที่อยู่:</td>
                            <td><textarea class="textAreaInput" type="text" style="width:400px" value='' id="popupOwner_ownerAddress"></textarea></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">โทร.:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupOwner_ownerPhone"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr >
                            <td class="formLabel" >ผู้ครอบครอง:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_holderName"></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">เลขที่บัตร:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_cardNumber"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">วันเกิด:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_holderBirthDate"  autocomplete="off"  ></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">สัญชาติ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_holderNation"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ที่อยู่:</td>
                            <td><textarea class="textAreaInput" type="text" style="width:400px" value='' id="popupOwner_holderAddress"></textarea></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">โทร.:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_holderPhone"></td>
                        </tr>        
                        <tr >
                            <td class="formLabel"  >สัญญาเช่าซื้อเลขที่:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupOwner_leaseContractNumber"></td>
                        </tr>    
                        <tr>
                            <td class="formLabel">ภาพข้อมูลการครอบครองรถ:</td>
                            <td><div id='popupOwner_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

