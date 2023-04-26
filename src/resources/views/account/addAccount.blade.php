@extends('layouts.app')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>


$(document).ready(function() {

    <?php if ($pageMode == 'edit'): ?>

    FieldHelper.applyViewMode('accountIdInput');
    applyChangePasswordField();
    
    $("#changePassword").click(function() {
        applyChangePasswordField();
    });
    
    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
       
    <?php endif; ?>


    $("#defaultSaleDate").datepicker();
    
    $('#saveBtn').click(function() { 
        document.mainForm.submit();
    }); 
  
} );

function applyChangePasswordField() {
    
    if ($("#changePassword").is(":checked")) {
        FieldHelper.applyEditMode('userId');
        FieldHelper.applyEditMode('password');
        FieldHelper.applyEditMode('passwordConfirm');      
    }
    else {
        FieldHelper.applyViewModeClear('userId');
        FieldHelper.applyViewModeClear('password');
        FieldHelper.applyViewModeClear('passwordConfirm'); 
        $('#userId').val("{{ $userId }}");
    }
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

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel" style='width:200px' >บัญชี:</td>
            <td><input class="textInput" type="text" style="width:260px" id='accountIdInput' value='{{ $accountId }}' name="accountId" autocomplete="off">
        </tr>
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>        
        <tr>
            <td class="formLabel">สถานะ:</td>
            <td>{!! SiteHelper::dropdown('active', $activeOpt, $active, "class='textInput' style='width:264px' ") !!}
            </td>
        </tr>
<?php endif; ?>          
        <tr>
            <!--
            <td class="formLabel">แผนการใช้งาน:</td>
            <td>{ !! SiteHelper::dropdown('app_plan_id', $app_plan_opt, $app_plan_id, "class='textInput' style='width:264px' ") !! }
            </td>
            -->
        </tr>        
        <tr>
            <td class="formLabel">รายละเอียด:</td>
            <td><input class="textInput" type="text" style="width:260px"  value='{{ $description }}'  name="description" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel">ชื่อที่ติดต่อได้:</td>
            <td><input class="textInput" type="text" style="width:260px" value='{{ $contactName }}' name="contactName"></td>
        </tr>
        <tr>
            <td class="formLabel">โทรศัพท์ที่ติดต่อได้:</td>
            <td><input class="textInput" type="text" style="width:260px" value='{{ $contactPhone }}' name="contactPhone"></td>
        </tr>
        <tr>
            <td class="formLabel">อีเมล์ที่ติดต่อได้:</td>
            <td><input class="textInput" type="text" style="width:260px" value='{{ $contactEmail }}' name="contactEmail"></td>
        </tr>
<?php if ($pageMode == 'edit'): ?>  
        <tr>
            <td class="formLabel">ต้องการเปลี่ยนข้อมูลผู้ใช้เริ่มต้น?:</td>
            <td><input type="checkbox" id="changePassword" name="change_password" {{ $change_password }} ></td>
        </tr>
<?php endif; ?>
<?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
        <tr>
            <td class="formLabel">รหัสผู้ใช้เรื่มต้น:</td>
            <td><input class="textInput" type="text" style="width:260px" value='{{ $userId }}' id="userId"  name="userId" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel">รหัสผ่าน:</td>
            <td><input class="textInput" type="password" value='{{ $password }}' style="width:260px" id="password" name="password" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel">ยืนยันรหัสผ่าน:</td>
            <td><input class="textInput" type="password" value='{{ $password_confirm }}' style="width:260px" id="passwordConfirm" name="password_confirm" autocomplete="off"></td>
        </tr>    
<?php endif; ?> 
<?php if ($pageMode == 'view'): ?> 
        <tr>
            <td class="formLabel">รหัสผู้ใช้เริ่มต้น:</td>
            <td><input class="textInput" type="text" style="width:260px" id="userId" value='{{ $userId }}' name="userId" autocomplete="off"></td>
        </tr>

 <?php endif; ?>            
    </tbody>
</table>

<div style='height:5px;font-size:0px'></div>
<div class='sectionTitleMiddle'>ข้อมูลบริษัท</div>

 <table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel" style='width:200px' >ชือบริษัท:</td>
            <td><input class="textInput" type="text" style="width:260px" id="companyName" value='{{ $company_name }}' name="company_name"></td>
        </tr>
        <tr>
            <td class="formLabel">เลขประจำตัวผู้เสียภาษี:</td>
            <td><input class="textInput" type="text" style="width:260px" id="taxId" value='{{ $tax_id }}' name="tax_id"></td>
        </tr>
        <tr>
            <td class="formLabel">ที่อยู่:</td>
            <td><textarea class="textAreaInput"  style='width:350px; height:40px'  name="company_address">{{ $company_address }}</textarea></td>            
        </tr>        
        <tr>
            <td class="formLabel">ที่อยู่สำหรับใบกำกับภาษี(ไทย):</td>
            <td><input class="textInput" type="text" style="width:820px"  value='{{ $invoice_address_th }}' name="invoice_address_th"></td>
                      
        </tr>  
        <tr>
            <td class="formLabel">ที่อยู่สำหรับใบกำกับภาษี(อังกฤษ):</td>
             <td><input class="textInput" type="text" style="width:820px"  value='{{ $invoice_address_en }}' name="invoice_address_en"></td>           
        </tr>          
    </tbody>   
</table>

<!--
<div style='height:5px;font-size:0px'></div>
<div class='sectionTitleMiddle'>การตั้งค่าอื่นๆ</div>

 <table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel" style='width:200px' >ค่าเริ่มต้นวันที่ขาย:</td>
            <td><input class="textInput" type="text" style="width:80px" id="defaultSaleDate" value='{{ $default_sale_date }}' name="default_sale_date" /> (ไม่ระบุจะเป็นวันที่ปัจจุบัน)</td>
        </tr>
        <tr>
            <td class="formLabel" style='width:200px' >ข้อความท้ายรายการสินค้าใบราคา:</td>
            <td><input class="textInput" type="text" style="width:260px"  value='{{ $config_quote_doc_add_text }}' name="config_quote_doc_add_text" /> </td>
        </tr>         
    </tbody>   
</table>
-->

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




   


