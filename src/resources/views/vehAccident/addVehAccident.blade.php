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
            document.mainForm.fileDatas.value = uploader.getDataStringForSubmit();
            document.mainForm.submit();
        }, 300); 
    }); 
  
    window.uploader = new BatchUploader( {
        uploaderName: "uploader",
        containerId: "uploaderCont",
        enableInfo: true,
        mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    
    uploader.addDataStringFromServer(  "{{ $fileDatas }}" ); 
        
    $('#accDateInput').datepicker();
    $('#fixStartDateInput').datepicker();    
    $('#fixEndDateInput').datepicker();    
    
    $('#vehicleIdInput').select2();
    
    
} );


</script>
<style>
    .textInput{
        // color:#3C4C59;
        // border: 1px solid #3C4C59;
    }
    .textReadOnly{
        // color:#3C4C59;
        // border: 1px solid #3C4C59;
    }
    .formLabel{
        text-align: right;
    }
    .textAreaInput{
        // color:#3C4C59;
        // border: 1px solid #3C4C59;
    }
</style>
@endsection

@section('content')


<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
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
<input type='hidden' name='fileDatas'  />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>


<?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
        <tr>
            <td class="formLabel" style='width:200px'>ทะเบียนรถ:</td>
            <td>{!! SiteHelper::dropdown('vehicleId', $vehicleOpt, $vehicleId, "class='textInput' id='vehicleIdInput' style='width:400px' ") !!}
            </td>
        </tr>  
<?php endif; ?>   
        
<?php if ($pageMode == 'view'): ?>  
        <tr>
             <td class="formLabel"  style='width:200px' >รหัสรถ:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $vehicleId }}' name="vehicleId"></td>
        </tr>         
        <tr>
            <td class="formLabel">ทะเบียนรถ:</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $licensePlate }}' name="$icensePlate"></td>          
        </tr>             
        <tr>
             <td class="formLabel"  >ครั้งที่:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $times }}' name="times"></td>
        </tr>   
<?php endif; ?>  
         
        <tr>
             <td class="formLabel">วันที่เกิดเหตุ:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $accDate }}' name="accDate" id="accDateInput" autocomplete="off" ></td>
        </tr> 
        <tr>
            <td class="formLabel">สถานที่เกิดเหตุ:</td>
            <td><textarea class="textAreaInput" type="text" style="width:400px"  name="accPlace">{{ $accPlace }}</textarea></td>
        </tr> 
        <tr>
             <td class="formLabel">ชื่อผู้ขับขี่:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $driverName }}' name="driverName"></td>
        </tr> 
        
        
        <tr>
             <td class="formLabel">วันที่เข้าซ่อม:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $fixStartDate }}' name="fixStartDate" id="fixStartDateInput" autocomplete="off" ></td>
        </tr> 
        <tr>
             <td class="formLabel">วันที่ซ่อมเสร็จ:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $fixEndDate }}' name="fixEndDate" id="fixEndDateInput" autocomplete="off" ></td>
        </tr> 
        <tr>
             <td class="formLabel">ค่าใช้จ่ายในการซ่อมแซม:</td>
             <td><input class="textInput" type="text" style="width:400px"  value='{{ $cost }}' name="cost"></td>
        </tr> 
        <tr>
            <td class="formLabel">แนบเอกสาร:</td>
            <td><div id='uploaderCont' style='padding:0px'></div></td>            
        </tr> 
                        
    </tbody>
</table>


</form>


  

<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'add'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
    
    <?php elseif ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='blackBtn' value="ตกลง" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='blackBtn' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>


@endsection




   


