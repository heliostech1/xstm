@extends('layouts.app')


@include('alarmSetting.partTimeTable')

@include('alarmSetting.partTimeOdoTable')


@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

    
    @yield('partTimeTableHelper') 
   
    @yield('partTimeOdoTableHelper') 
    
$(document).ready(function() {


    
    @yield('partTimeTableJs')    
    
    @yield('partTimeOdoTableJs')  
    
    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
        FormHelper.applyViewMode('mainForm');

        
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        setTimeout(function() {
                
            document.mainForm.alarmTimeForCheckDate.value = TimeTableHelper.getDataForSubmit();            
            document.mainForm.alarmTimeForCheckOdo.value = TimeOdoTableHelper.getDataForSubmit();
           // console.log(document.mainForm.email.value );
            document.mainForm.submit();
        }, 300); 
    }); 
  
} );


</script>

@endsection

@section('content')


<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


 <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >

 {{ csrf_field() }}
        

<input type='hidden' name='valueDatas'  />
<input type='hidden' name='alarmTimeForCheckDate'  />
<input type='hidden' name='alarmTimeForCheckOdo'  />

<input  type="hidden"  value='{{ $settingId }}' name="settingId" >

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

        <tr>
            <td class="formLabel" style='width:200px'>ชื่อ:</td>
            <td><input class="textReadOnly" readonly  type="text" style="width:400px" value='{{ $name }}' name="name"></td>
        </tr>  
        <tr>
            <td class="formLabel" style='width:200px'>เปิดใช้งาน:</td>
            <td>{!! SiteHelper::dropdown('enable', $yesNoOpt, $enable, "class='textInput' style='width:400px' ") !!}  
            </td>
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px'>เวลาแจ้งเตือน (ตรวจสอบวันที่):</td>
            <td>
                
               @yield('partTimeTableHtml')   
                
            </td>
        </tr> 
        <tr>
            <td class="formLabel" style='width:200px'>เวลาแจ้งเตือน (ตรวจสอบเลขไมล์):</td>
            <td>
                
               @yield('partTimeOdoTableHtml')   
                
            </td>
        </tr> 

    </tbody>
</table>
    


</form>



<!-- SECTION BUTTON PANEL -->

<div class='footerBtnCont'>

    <?php if ($pageMode == 'edit'): ?>
    <div class='footerBtnLeft'><input type="button" class='formButton' value="OK" id="saveBtn" /></div>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='ยกเลิก' /></div>
   
    <?php elseif ($pageMode == 'view'): ?>
    <div class='footerBtnLeft'><input type='button' class='formButton' onClick="window.location.href='./index?keep=1';" value='กลับ' /></div>

    <?php endif; ?>
       
    <div style='clear: both'></div>
</div>


@endsection




   


