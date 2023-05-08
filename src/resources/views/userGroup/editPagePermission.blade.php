
@extends('layouts.app')

@section('header')

<style>
    /* Hide the default checkbox */
.hidden-checkbox {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom circular checkmark */
.checkmark {
  position: relative;
  display: inline-block;
  width: 20px;
  height: 20px;
  background-color: #fff;
  border: 2px solid #4CAF50; /* Change border color to green */
  border-radius: 50%;
  cursor: pointer;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
}

/* Style the checkmark when the checkbox is checked */
.hidden-checkbox:checked ~ .checkmark {
  background-color: #4CAF50; /* Change background color to green */
  border: 2px solid #4CAF50;
}

/* Create the checkmark indicator when the checkbox is checked */
.hidden-checkbox:checked ~ .checkmark:after {
  content: "";
  position: absolute;
  display: block;
  left: 6px;
  top: 3px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

/* Style the checkmark on hover */
.custom-checkbox:hover .checkmark {
  border: 2px solid #3f9a40; /* Change hover border color */
}

</style>
<script type='text/javascript'>


var PermissionTableHelper = {

    setTable: function(table) {
        this.table = table;
    },
    
    addDataFromServer : function(datas) {
        this.table.fnClearTable();
        this.table.fnAddData(datas);
    },
    
    getDataForSubmit: function() {
        var datas = [], result, pageId, mode;
        this.table.find('tbody').find('tr').each(function() {
            pageId = $(this).find('td:nth-child(1)').find('span').html();
            result = $(this).find('td').hasClass('cellPass')? 1: 0;
            mode = $(this).find('td:nth-child(5)').find('select').val();
            mode = AppUtil.isEmpty(mode)? "normal": mode;
             
            datas.push({"page_id":pageId, "permission": result, "permission_mode": mode });
        });        
        return datas;
    },

    tickResult: function(el, tick) {
        var nRow = $(el).parents('tr')[0];
        var passTd = $(nRow).find('td');  
        var passCb = $(passTd).find('input');

        if (tick === true) {
            passCb.prop('checked', true);        
        }
        else if (tick === false) {
            passCb.prop('checked', false);
        }
        
        if (passCb.is(':checked')) {
            passTd.addClass('cellPass');
        }
        else {
            passTd.removeClass('cellPass');
        }
    },

    tickAll: function(el, tick) {
        var nRow = $(el).parents('tr')[0];
        var table = PermissionTableHelper.table;
        var menuName =  $(nRow).find('td:nth-child(2)').html();
        var start = false;
        
        table.find('tbody').find('tr').each(function() {
            isMenu = $(this).hasClass('permissionTableRow_menu');
            nameTd = $(this).find('td:nth-child(2)');
            //console.debug(start+"|"+menuName+"|"+nameTd.html());
            start = (isMenu && menuName == nameTd.html())? true: start;
            start = (isMenu && menuName != nameTd.html())? false: start;

            if (start) {
                PermissionTableHelper.tickResult( nameTd , tick);
            }
        });
    }

};



$(document).ready(function() {
    window.permissionTable = $('#permissionTable').dataTable( // make it global
    {
        "oLanguage": DTHelper.thaiLang,
        "sDom": 'lrtip',
        "bPaginate": false,
        "bFilter": true,
        "bSearchable":false,
        "bProcessing": true,
        "bInfo": false,
        "bSort": false,
        "bAutoWidth": false,

        "aoColumns": [
                      { "mData": "order",
                          "mRender": function ( data, type, full ){  
                              return  data +  "<span style='display:none'>"+ full.page_id + "</span>"
                          }
                      },
                      { "mData": "name"},
                      { "mData": "description"},
                      { "mData": "permission" , "sClass": "cellCenter",
                          "mRender": function ( data, type, full ){  
                              var isViewMode = <?php echo ($pageMode == "view")? "true": "false"?>;

                              if (full.type == "menu" && isViewMode) {
                                  return "";
                              }
                              else if (full.type == "menu") { 
                                  return "<span style='text-decoration:underline; cursor:pointer' onClick='PermissionTableHelper.tickAll(this,true)'>เลือก</span>"+
                                  "|<span style='text-decoration:underline; cursor:pointer' onClick='PermissionTableHelper.tickAll(this,false)'>ไม่เลือก</span>";
                              }
                              else { 
                                  var passCheck = (data == '1')? "checked":"";
                                  var disableCb = (isViewMode)? "disabled": "";
                                  return "<label class='custom-checkbox'><input type='checkbox' class='hidden-checkbox' onclick='PermissionTableHelper.tickResult(this)' "+passCheck+"  "+disableCb+"><span class='checkmark'></span></label>";

                                //   return  "<input type='checkbox' onClick='PermissionTableHelper.tickResult(this)' "+passCheck+"  "+disableCb+"></input>";
                              }
                          }
                      },
                      { "mData": "mode",
                          "mRender": function ( data, type, full ){  
                              var mode_opt = full.mode_opt;
                              if (mode_opt.length <= 0) return "";

                              var isViewMode = <?php echo ($pageMode == "view")? "true": "false"?>;
                              var disableCb = (isViewMode)? "disabled": "";
                              
                              var all_modes = <?php echo json_encode($allPermissionModes)?>;
                              var select = $('<select class="textInput" style="width:98%; font-size:11px" '+ disableCb +' >');

                              for (var i = 0; i < all_modes.length; i++) {                                  
                                 if ( $.inArray(  all_modes[i].name , mode_opt ) >= 0) {
                                     var selected = ( all_modes[i].name == data )? "selected" :"";
                                     select.append($('<option '+ selected +  ' >').val( all_modes[i].name ).append( all_modes[i].description ));
                                 }
                              }

                              return select[0].outerHTML;
                          }

                      }
                 ],
                         
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            var typeData = aData["type"], className;   
            if ( typeData == "menu" ) {
                className = "permissionTableRow_menu";
            }
            else if ( typeData == "main" ) {
                className = "permissionTableRow_main";
            }
            else {
                className  = "permissionTableRow_other";
            }
            
            $(nRow).addClass(className);
            
            //--------------
            
            var passTd = $(nRow).find('td');     
            var passCb = $(passTd).find('input');

            if (passCb.is(':checked')) {
                passTd.addClass('cellPass');
            }
            
            return nRow;
        }
    });
    
    //DTHelper.applySelectable(permissionTable, "permissionTable");
    PermissionTableHelper.setTable(permissionTable);
    PermissionTableHelper.addDataFromServer(<?php echo $permissionDatas?>);
    
    $('#chooseUserGroupBtn').click( function() {
        getPagePermissionDatas();
    });

    $('#saveBtn').click(function() { 
        if (DTHelper.isEmpty(permissionTable)) {
           alert("กรุณาระบุข้อมูลสิทธิ์การใช้");
           return;
        }
        
        document.mainForm.result_datas.value = $.toJSON(PermissionTableHelper.getDataForSubmit());
        //console.debug(document.mainForm.result_datas.value);
        document.mainForm.submit();
    }); 
   
    if ( AppUtil.isNotEmpty( $('#selectUserGroupId').val() ) &&
        DTHelper.isEmpty(permissionTable)
    ) {
        //getPagePermissionDatas();
    }

    getPagePermissionDatas();
    setPermissionTitleDesc();

} );


function setPermissionTitleDesc() {
    var userGroupId  = $('#userGroupId').val();
    userGroupDesc = AppUtil.isNotEmpty(userGroupId)? $("#selectUserGroupId option[value='" + userGroupId + "']").text(): "-";

    //var appPlanId  = $('#appPlanId').val();
   // appPlanId = AppUtil.isNotEmpty(appPlanId)? $("#appPlanInput option[value='" + appPlanId + "']").text(): "-";
    
    //console.debug( userGroupDesc);
    if (AppUtil.isNotEmpty(userGroupDesc)) {
        $("#permissionTitleDesc").html(" (กล่มผู้ใช้: " + userGroupDesc  + ")"); // + ", แผนการใช้งาน: " +  appPlanId
    }
}

function getPagePermissionDatas() {
    var userGroupId = $('#userGroupId').val();
   // var appPlanId = $('#appPlanInput').val();
    
    if (AppUtil.isEmpty(userGroupId) ) { // || AppUtil.isEmpty(appPlanId)
        alert("กรุณาระบุข้อมูลให้ครบถ้วน");
        return;
    }
    
    $.ajax({
        url: "./getPagePermissionDataTable",
        data: ({ 'userGroupId' : userGroupId  }), // , "app_plan_id": appPlanId
        dataType: "json",
        type: "post",
        beforeSend: function() { 
            $("#permissionTitleDesc").html("(กำลังโหลด..)");
            DTHelper.showProcessing(permissionTable); 
        },
        success: function(json){
            $('#userGroupId').val(userGroupId);
         //   $('#appPlanId').val(appPlanId);
            setPermissionTitleDesc();
            
            DTHelper.hideProcessing(permissionTable); 
            PermissionTableHelper.addDataFromServer(json);
        }
    });    
}

</script>

@endsection

@section('content')


<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?>



<form action="editPagePermissionSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >

 {{ csrf_field() }}
 
<input type='hidden' name='result_datas'/>
<input type='hidden' name='userGroupId' id='userGroupId' value='<?php echo $userGroupId?>' />
<input type='hidden' name='app_plan_id' id='appPlanId' />

<div class='sectionTitleLarge'>ข้อมูลกลุ่มผู้ใช้</div>

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
        <tr><td>
             &nbsp;&nbsp;
            <span class="formLabel">กลุ่มผู้ใช้:</span>       
            {!! SiteHelper::dropdown('selectUserGroupId', $userGroupOpt, $userGroupId, " disabled readonly class='textReadOnly' style='width:264px' id='selectUserGroupId' ") !!}
            &nbsp;
            <!--
            <span class="formLabel">แผนการใช้งาน:</span>       
             { !! SiteHelper::dropdown('app_plan_input', $app_plan_opt, "", "class='textInput' style='width:150px' id='appPlanInput' ") !! }
              -->
      <!--       &nbsp;&nbsp;<input type="button" class='formButton' value="ตกลง" id="chooseUserGroupBtn" /> -->
        </td></tr>
</table>
</form>

<div class='sectionSepHr' style='margin:15px 0'></div>

<!--  ************************************************************************************* -->

<div style='padding-bottom:8px'>
<span class='sectionTitleLarge'>สิทธิ์การใช้หน้า</span> <span id='permissionTitleDesc'></span><br>
</div>

<table id='permissionTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr class='nodrop'>
           <th width='20'>ลำดับ</th>
           <th width='300'>ชื่อหน้า</th>
           <th width='350'>รายละเอียด</th> 
           <th width='100'>มีสิทธิ์ใช้</th>           
           <th width='80'>โหมด</th>
        </tr>
    </thead>
</table>


<div class='footerBtnCont' style='margin-top:10px'>
    <?php if ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="บันทึก" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="กลับ" onClick="window.location.href='./index?keep=1';" /></div>    
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="กลับ" onClick="window.location.href='./index?keep=1';" /></div>    
    <?php endif; ?>
        <div style='clear: both'></div>
</div>

@endsection


