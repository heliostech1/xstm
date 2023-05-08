@extends('layouts.app')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>



$(document).ready(function() {

    <?php if ($pageMode == 'edit'): ?>
    
    applyChangePasswordField();
     
    $("#changePassword").click(function() {
         applyChangePasswordField();
    });
    
    
    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
       
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        document.mainForm.submit();
    }); 
    
} );

function applyChangePasswordField() {

    if ($("#changePassword").is(":checked")) {
       FieldHelper.applyEditMode('password');
       FieldHelper.applyEditMode('passwordConfirm');      
     }
     else {
       FieldHelper.applyViewModeClear('password');
       FieldHelper.applyViewModeClear('passwordConfirm'); 
     }     
}

</script>

@endsection

@section('content')


<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if ($pageMode == 'edit'): ?>
    <div class="topInfoTitle">รหัสผู้ใช้ที่แก้ไข: <?php echo $old_userId?></div>

<?php elseif ($pageMode == 'view'): ?>
    <div class="topInfoTitle">รหัสผู้ใช้ที่เรียกดู: <?php echo $old_userId?></div>
<?php endif; ?>


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php endif; ?>

 {{ csrf_field() }}

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel" style=" text-align: right;">รหัสผู้ใช้:</td>
            <td><input class="textInput" type="text" value='{{ $userId }}' name="userId" autocomplete="off">
            <input type='hidden' value='{{ $old_userId }}' name="old_userId"/></td>
        </tr>
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>        
        <tr>
            <td class="formLabel" style=" text-align: right;">สถานะ:</td>
            <td>{!! SiteHelper::dropdown('active', $activeOpt, $active, "class='textInput' ") !!}
            </td>
        </tr>
<?php endif; ?>          
        <tr>
            <td class="formLabel" style=" text-align: right;">รายละเอียดผู้ใช้:</td>
            <td><input class="textInput" type="text"   value='{{ $description }}' 
            name="description" autocomplete="off"></td>
        </tr>

<?php if ($pageMode == 'edit'): ?>
        <tr>
            <td class="formLabel" style=" text-align: right;">ต้องการเปลี่ยนรหัสผ่าน?:</td>
            <td style="text-align: left;"><input type="checkbox" id="changePassword" name="change_password" {{ $change_password }} class="orangecheckbox"></td>
        </tr>
<?php endif; ?>        
<?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>
        <tr>
            <td class="formLabel" style=" text-align: right;">รหัสผ่าน:</td>
            <td><input class="textInput" type="password"  id="password" name="password" value='{{ $password }}'   autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel" style=" text-align: right;">ยืนยันรหัสผ่าน:</td>
            <td><input class="textInput" type="password"  id="passwordConfirm" name="password_confirm" value='{{ $password_confirm }}' autocomplete="off"></td>
        </tr>
<?php endif; ?>    
        <!--      
        <tr>
            <td class="formLabel" style=" text-align: right;">สาขา:</td>
            <td>{!! SiteHelper::dropdown('branchId', $branch_opt, $branchId, "class='textInput'  ") !!}
        </tr>
         -->
        <tr>
            <td class="formLabel" style=" text-align: right;">กลุ่มผู้ใช้:</td>
            <td>{!! SiteHelper::dropdown('userGroupId', $user_group_opt, $userGroupId, "class='textInput'  ") !!}
        </tr>
        <tr>
            <td class="formLabel" style=" text-align: right;">ชื่อที่ติดต่อได้:</td>
            <td><input class="textInput" type="text"  value='{{ $contactName }}' name="contactName"></td>
        </tr>
        <tr>
            <td class="formLabel" style=" text-align: right;">โทรศัพท์ที่ติดต่อได้:</td>
            <td><input class="textInput" type="text"  value='{{ $contactPhone }}' name="contactPhone"></td>
        </tr>
        <tr>
            <td class="formLabel" style=" text-align: right;">อีเมล์ที่ติดต่อได้:</td>
            <td><input class="textInput" type="text"  value='{{ $contactEmail }}' name="contactEmail"></td>
        </tr>

    </tbody>
</table>

</form>
 


<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'add'): ?>
    <div class='footerBtnLeft'  style="padding-left:100px;"><input type="button" class='blackBtn' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'  ><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
    
    <?php elseif ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft' style="padding-left:150px;"><input type="button" class='blackBtn' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>


@endsection




   


