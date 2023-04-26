

@section('popupRegisHelper')


@endsection


@section('popupRegisJs')

    var RegisGasNumberTable = new MySimpleDataListTable('popupRegis_gasTankTable');

    $("#popupRegis").dialog({
        width:800,
        height:550,
       // modal: true, // ******* ทำให้ plUploader ใช้ไม่ได้ ไม่รู้สาเหตู
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupRegis_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupRegis").dialog('close');
            }
        }      
    });
    
    
    window.popupRegis_submit = function() {           
        var rowId = $("#popupRegis_rowId").val();

        if (popupRegis_uploader.uploadStatus == "uploading") {
           alert("กรุณารอให้อัพโหลดไฟล์เสร็จสิ้นก่อน");
           return;
        }

        var data = {
            "regisDate" : $('#popupRegis_regisDate').val(),
            "regisNumber": $('#popupRegis_regisNumber').val(),
            "province": $('#popupRegis_province').val(),
            "vehicleType": $('#popupRegis_vehicleType').val(),
            "vehicleRegisType": $('#popupRegis_vehicleRegisType').val(),
            
            "look": $('#popupRegis_look').val(),     
            "brand": $('#popupRegis_brand').val(),   
            "design": $('#popupRegis_design').val(),      
            "model": $('#popupRegis_model').val(),
            "color": $('#popupRegis_color').val(),
            
            "bodyNumber": $('#popupRegis_bodyNumber').val(),
            "address": $('#popupRegis_address').val(),
            "engineBrand": $('#popupRegis_engineBrand').val(),
            "engineNumber": $('#popupRegis_engineNumber').val(),
            "fuel": $('#popupRegis_fuel').val(),
            
            "gasTankNumber": RegisGasNumberTable.getDataForSubmit(),
            "loop": $('#popupRegis_loop').val(),
            "cc": $('#popupRegis_cc').val(),
            "horsePower": $('#popupRegis_horsePower').val(),
            "wheel": $('#popupRegis_wheel').val(),
            
            "carWeight": $('#popupRegis_carWeight').val(),
            "loadWeight": $('#popupRegis_loadWeight').val(),
            "totalWeight": $('#popupRegis_totalWeight').val(),
            "seat": $('#popupRegis_seat').val(),
            "fileDatas": popupRegis_uploader.getDataStringForSubmit(),    
        };
         
        
        window.popupRegisSubmitCallback(rowId, data);     

        $("#popupRegis").dialog('close');
    }
    
    
    window.popupRegis_clearData = function() { 
        $("#popupRegis_rowId").val(""); 
        
        
        $('#popupRegis_regisDate').val("");
        $('#popupRegis_regisNumber').val("");
        $("#popupRegis_province").select2("val", "");
        $('#popupRegis_vehicleType').val("");
        $('#popupRegis_vehicleRegisType').val("");

        $('#popupRegis_look').val("");     
        $('#popupRegis_brand').val("");   
        $('#popupRegis_design').val("");      
        $('#popupRegis_model').val("");
        $('#popupRegis_color').val("");

        $('#popupRegis_bodyNumber').val("");
        $('#popupRegis_address').val("");
        $('#popupRegis_engineBrand').val("");
        $('#popupRegis_engineNumber').val("");
        $('#popupRegis_fuel').val("");

        RegisGasNumberTable.clearDatas();
        $('#popupRegis_loop').val("");
        $('#popupRegis_cc').val("");
        $('#popupRegis_horsePower').val("");
        $('#popupRegis_wheel').val("");

        $('#popupRegis_carWeight').val("");
        $('#popupRegis_loadWeight').val("");
        $('#popupRegis_totalWeight').val("");
        $('#popupRegis_seat').val("");
        popupRegis_uploader.clearAllItem();
        
    }
    
    window.popupRegis_openPopupForAdd = function(callback) { 
        window.popupRegisSubmitCallback = callback;   
      
        popupRegis_clearData();
        $("#popupRegis").dialog('open'); 
    }
    
    window.popupRegis_openPopupForEdit = function(rowId, data, callback) { 
        $("#popupRegis_rowId").val(rowId); 

       
            
        $('#popupRegis_regisDate').val( data['regisDate'] );
        $('#popupRegis_regisNumber').val( data['regisNumber'] );
        $('#popupRegis_province').val( data['province'] );
        $('#popupRegis_vehicleType').val( data['vehicleType'] );
        $('#popupRegis_vehicleRegisType').val( data['vehicleRegisType'] );

        $('#popupRegis_look').val( data['look'] );     
        $('#popupRegis_brand').val( data['brand'] );   
        $('#popupRegis_design').val( data['design'] );      
        $('#popupRegis_model').val( data['model'] );
        $('#popupRegis_color').val( data['color'] );

        $('#popupRegis_bodyNumber').val( data['bodyNumber'] );
        $('#popupRegis_address').val( data['address'] );
        $('#popupRegis_engineBrand').val( data['engineBrand'] );
        $('#popupRegis_engineNumber').val( data['engineNumber'] );
        $('#popupRegis_fuel').val( data['fuel'] );

        RegisGasNumberTable.addDataFromServer(  data['gasTankNumber'] );
        $('#popupRegis_loop').val( data['loop'] );
        $('#popupRegis_cc').val( data['cc'] );
        $('#popupRegis_horsePower').val( data['horsePower'] );
        $('#popupRegis_wheel').val( data['wheel'] );

        $('#popupRegis_carWeight').val( data['carWeight'] );
        $('#popupRegis_loadWeight').val( data['loadWeight'] );
        $('#popupRegis_totalWeight').val( data['totalWeight'] );
        $('#popupRegis_seat').val( data['seat'] );
        popupRegis_uploader.addDataStringFromServer( data['fileDatas']); 
   
        
        window.popupRegisSubmitCallback = callback;        
        $("#popupRegis").dialog('open'); 
    }
    

    window.popupRegis_uploader = new BatchUploader( {
        uploaderName: "popupRegis_uploader",
        containerId: "popupRegis_uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    

    $('#popupRegis_gasTankTableAddLink').click(function(e) {
        e.preventDefault();
        RegisGasNumberTable.addData();
    });


    $('#popupRegis_regisDate').datepicker();
    $('#popupRegis_province').select2();
        
        
@endsection



@section('popupRegisHtml')


<div id="popupRegis" style='padding:2px 0px; display:none;' title="เพิ่ม/แก้ไขข้อมูล"> <!--  -->


        <div style="padding:20px">
                <input  type="hidden" id="popupRegis_rowId"  />   

                <span class="ui-helper-hidden-accessible"><input type="text"/></span>
                        

                <input type='hidden' id='popupRegis_gasTankNumber'  />

                <table cellspacing="0" border="0" cellpadding="0" class="formTable">
                    <tbody>

                        <tr>
                            <td class="formLabel" style='width:200px' >วันจดทะเบียน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_regisDate"  autocomplete="off" ></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">เลขทะเบียน:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_regisNumber"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">จังหวัด:</td>
                            <td> {!! SiteHelper::dropdown("", $provinceOpt, '', "  class='textInput' style='width:400px' id='popupRegis_province' ") !!} </td> 
                        </tr> 
                        <tr>
                            <td class="formLabel">ประเภทรถ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_vehicleType"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">รย.:</td>
                            <td><input class="textInput" type="text" style="width:400px" id="popupRegis_vehicleRegisType"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">ลักษณะ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_look"></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">ยี่ห้อรถ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_brand"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">แบบ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_design"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">รุ่น:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_model"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">สี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_color"></td>
                        </tr> 

                        <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">เลขตัวรถ:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_bodyNumber"></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">อยู่ที่:</td>
                            <td><textarea class="textAreaInput" type="text" style="width:400px" id="popupRegis_address"></textarea></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ยี่ห้อเครื่องยนต์:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_engineBrand"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เลขเครื่องยนต์:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_engineNumber"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">เชื้อเพลิง:</td>
                            <td> {!! SiteHelper::dropdown("", $fuelOpt, '', "  class='textInput' style='width:400px' id='popupRegis_fuel' ") !!} </td> 
                        </tr> 
                       <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">เลขถังแก๊ส:</td>


                            <td>
                                 <div style='padding:0px 20px 1px 0px'>
                                      <div style='float: left; width: 400px; text-align: right;' >
                                         <a id="popupRegis_gasTankTableAddLink" href="javascript:void(0);" >เพิ่ม</a> 
                                      </div>
                                      <div style='clear: both'></div>
                                 </div>

                                 <div class='customTableStyle' > 
                                 <table id='popupRegis_gasTankTable' cellspacing='0' cellpadding='0' class='tableInnerDisplay'style='width:400px' >
                                     <thead>
                                     <tr class='nodrop' >
                                         <th  width='20' >&nbsp;</th>
                                         <th  width='20' ></th>     
                                         <th  width='350'>ข้อมูล</th>
                                         <th  width='70'>ลบ</th>
                                     </tr
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                                 </div>


                             </td>            
                       </tr>        
                        <tr>
                            <td class="formLabel">จำนวน (สูบ):</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_loop"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ซีซี:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_cc"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">แรงม้า:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_horsePower"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">จำนวนเพลา/ล้อ/ยาง:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_wheel"></td>
                        </tr> 

                       <!-- ======================================================= -->

                        <tr>
                            <td class="formLabel">น้ำหนักรถ (กก.):</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_carWeight"></td>
                        </tr>        
                        <tr>
                            <td class="formLabel">น้ำหนักบรรทุก/น้ำหนักลงเพลา (กก.):</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_loadWeight"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">น้ำหนักรวม:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_totalWeight"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ที่นั่ง:</td>
                            <td><input class="textInput" type="text" style="width:400px"  id="popupRegis_seat"></td>
                        </tr> 
                        <tr>
                            <td class="formLabel">ภาพข้อมูลจดทะเบียน:</td>
                            <td><div id='popupRegis_uploaderCont' style='padding:0px'></div></td>            
                        </tr> 


                    </tbody>
                </table>


                
        </div>

</div> 
 <!-- -->
@endsection

