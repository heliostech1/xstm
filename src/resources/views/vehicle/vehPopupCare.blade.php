

@section('popupCareHelper')

@endsection

@section('popupCareJs')

    $("#popupCare").dialog({
        width:900,
        height:400,
        autoOpen: false,
        buttons: { 
            "ตกลง": function() {
                 popupCare_submit();
            },                 
            "ยกเลิก": function() {
                $("#popupCare").dialog('close');
            }
        }      
    });
    
   window.popupCare_staffTable = DTHelper.createPagingDatatable('popupCare_staffTable', '', 
            {
                "aaSorting": [[2,'asc']],
                "sAjaxSource": "../staff/getPopupDataTable",

                "aoColumns": [
                              { "mData": "counterColumn", "sClass": "cellCounter", "bSortable": false },  
                              { "mData": "mongoId" , "sClass": "forceHidden"},   
                            
                              { "mData": "staffName" },                               
                              { "mData": "phone" },
                              
                              { "mData": "workCompany" },
               
                ],
                
               "fnServerData": function ( sSource, aoData, fnCallback ) {

                    aoData.push( { "name": "criStaffName", "value": $('#popupCare_criStaffName').val()  } );
                    aoData.push( { "name": "criWorkCompany", "value": $('#popupCare_criWorkCompany').val() } );
                    aoData.push( { "name": "criStaffType", "value": $('#popupCare_criStaffType').val() } );
                    
                    $.ajax( {
                        "dataType": 'json', 
                        "type": "POST", 
                        "url": sSource, 
                        "data": aoData, 
                        "success": function (json) { 
                            DTHelper.handleSuccess('datatableMessage',json);
                            fnCallback(json);
                        },
                        "error": function (xhr, error, thrown) {
                            DTHelper.handleError('datatableMessage', xhr, error);
                        }
                    } );
                }

            }
     );
     
    DTHelper.applySelectable(popupCare_staffTable, 'popupCare_staffTable');
    
    $("#popupCare_staffTable tbody").dblclick(function() {
        popupCare_submit();
    });

    $('#popupCare_searchBtn').click( function() {
        popupCare_staffTable.fnPageChange('first');
    } );

    $('#popupCare_clearCriteriaBtn').click( function() {
         FormHelper.clearValue('criteriaForm');                    
    } );
     
    window.popupCare_submit = function() {
        var datas = DTHelper.getSelections(popupCare_staffTable);
        if (!datas || datas.length <= 0) {
            alert("กรุณาเลือกรายการข้อมูล");
            return;
        }
       
        window.popupCareSubmitCallback(datas);                  
        $("#popupCare").dialog('close');
    }
    
    
    window.popupCare_openPopupForAdd = function(callback) {    
        window.popupCareSubmitCallback = callback;   
        
        DTHelper.clearSelections(popupCare_staffTable);

        $("#popupCare").dialog('open');      

        popupCare_staffTable.fnPageChange('first');  
        
         
    }
    

@endsection



@section('popupCareHtml')


<div id="popupCare" style='padding:2px 0px; display:none;' title="เพิ่มข้อมูล"> <!--  -->

<fieldset class='sectionFieldset' style='margin: 0 10px'>
    <legend >ค้นหา</legend>

    <table cellspacing="0" border="0" cellpadding="0" class="formTable" id='criteriaForm'>     
        <tr>
           <td>           
                <span class="formLabel" >ชื่อ:</span>
                <input class="textInput" type="text" style="width:120px" id="popupCare_criStaffName" />
                &nbsp;
                
                <span class="formLabel">สังกัด:</span>
                {!! SiteHelper::dropdown("popupCare_criWorkCompany", $workCompanyOpt, '', "  id='popupCare_criWorkCompany'  class='textInput' style='width:140px'  ") !!} 
                &nbsp;

                <span class="formLabel">ชนิดพนักงาน:</span>
                {!! SiteHelper::dropdown("popupCare_criStaffType", $staffTypeOpt, '', "  id='popupCare_criStaffType'  class='textInput' style='width:140px'  ") !!} 
                
               &nbsp; &nbsp;
                                      
                <input type="button" class='formButton' value="ค้นหา" id="popupCare_searchBtn" />
                &nbsp;<input type="button" class='formButton' value="ล้าง" id="popupCare_clearCriteriaBtn" />                 
          </td>
        </tr>                   
              
    </table>
 
</fieldset>
    
<div style='height: 10px; font-size: 0px'></div>

<table id='popupCare_staffTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
            <th  width='15'>&nbsp;</th>   
            <th  width='15'>&nbsp;</th>  
     
            <th  width='100'>ชื่อ นามสกุล</th>   
            <th  width='100'>เบอร์โทร</th>
            <th  width='100'>สังกัด</th>

        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>
    
<br>


</div> 
 <!-- -->
@endsection

