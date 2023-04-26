@extends('layouts.app')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>



$(document).ready(function() {

    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        setTimeout(function() {
            //document.mainForm.valueDatas.value = $.toJSON(ResultTableHelper.getDataForSubmit());
            //console.debug(document.mainForm.valueDatas.value );
            document.mainForm.submit();
        }, 300); 
    }); 
  
} );


</script>

@endsection

@section('content')


<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


 <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >

 {{ csrf_field() }}
        

<input type='hidden' name='valueDatas'  />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>


        <tr>
            <td class="formLabel" >รหัส:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" id='keyIdInput' value='{{ $settingId }}' name="settingId" autocomplete="off">
        </tr>        
 
        <tr>
            <td class="formLabel">ประเภท:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $category }}' name="category"></td>
        </tr> 
        <tr>
            <td class="formLabel">ชื่อ:</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $name }}' name="name"></td>
        </tr> 
        <tr>
            <td class="formLabel">ค่า:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $value }}' name="value"></td>
        </tr> 
        <tr>
            <td class="formLabel">หน่วย:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $unit }}' name="unit"></td>
        </tr>         
    </tbody>
</table>

</form>


  

<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>

@endsection




   


