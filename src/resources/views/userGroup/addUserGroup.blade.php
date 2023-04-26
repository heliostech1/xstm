@extends('layouts.app')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

$(document).ready(function() {

    <?php if ($pageMode == 'edit'): ?>

    FieldHelper.applyViewMode('userGroupIdInput');    
    
    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
       
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        document.mainForm.submit();
    }); 

    jscolor.dir = "../js/jscolor/";
    jscolor.init();
        
} );


function changeHeaderColor() {
    var val = $('#colorInput').val();
    if (AppUtil.isEmpty(val)) return;

    var color = "#"+val;
    var paleColor = "";
    
    $("#pageSiteTitleText").css("box-shadow", "0 -30px 50px "+color+" inset");
    $("#pageSiteTitleText").css("background-color", color);
    $("#pageSiteTopBorder").css("background-color", color);
}


</script>

@endsection

@section('content')


<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php endif; ?>

 {{ csrf_field() }}


<table cellspacing="0" border="0" cellpadding="0" id='userForm' class="formTable">
    <tbody>
        <tr>
            <td class="formLabel">รหัส:</td>
            <td><input class="textInput"  type="text" style="width:260px" value='{{ $userGroupId }}' name="userGroupId" id="userGroupIdInput" ></td>
        </tr>    
        <tr>
            <td class="formLabel">ชื่อกลุ่มผู้ใช้:</td>
            <td><input class="textInput"  type="text" style="width:260px" value='{{ $user_group_name }}' name="user_group_name" ></td>
        </tr>
        <tr>
            <td class="formLabel">สี:</td>
            <td><input class="textInput color {required:false}" type="text" style="width:260px" 
            onchange="changeHeaderColor();"
            value='{{ $color }}' name="color" id="colorInput" ></td>
        </tr>           
        <tr>
            <td class="formLabel">รายละเอียด:</td>
            <td><textarea class="textAreaInput" name="description">{{ $description }}</textarea></td>
        </tr>            
    </tbody>
</table>


</form>
 


<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'add'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
    
    <?php elseif ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>


@endsection




   


