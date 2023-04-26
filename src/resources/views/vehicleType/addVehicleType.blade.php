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


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
<?php endif; ?>

 {{ csrf_field() }}
        
<input type='hidden' name='mongoId' value='<?php echo $mongoId?>' />
<input type='hidden' name='valueDatas'  />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

         
        <tr>
            <td class="formLabel" style='width:150px'>ชื่อ:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $name }}' name="name"></td>
        </tr>  
<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  
        <tr>
            <td class="formLabel">สถานะ:</td>
            <td>{!! SiteHelper::dropdown('active', $activeOpt, $active, "class='textInput' style='width:264px' ") !!}
            </td>
        </tr>
            
<?php endif; ?>   
       
        
    </tbody>
</table>

<div class='sectionTitleMiddle'>แผนการตรวจรถ</div>
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel" style='width:150px'>แผนตรวจเช็คประจำวัน:</td>
            <td>{!! SiteHelper::dropdown('vcDailyCheckProfileId', $vcDailyCheckProfileOpt, $vcDailyCheckProfileId, "class='textInput' style='width:264px' ") !!}
            </td>
        </tr>
        <tr>
            <td class="formLabel">แผนเช็คของเหลว:</td>
            <td>{!! SiteHelper::dropdown('vcLiquidProfileId', $vcLiquidProfileOpt, $vcLiquidProfileId, "class='textInput' style='width:264px' ") !!}
            </td>
        </tr>            
        
    </tbody>
</table


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




   


