@extends('layouts.app')

@include('staff.staffPopupLicense')
@include('staff.staffPopupWork')

@include('staff.staffPartBase')
@include('staff.staffPartLicense')
@include('staff.staffPartWork')
@include('staff.staffPartAbsent')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

@yield('popupLicenseHelper')    
@yield('popupWorkHelper') 

@yield('partBaseHelper')    
@yield('partLicenseHelper')    
@yield('partWorkHelper')    
@yield('partAbsentHelper')    

$(document).ready(function() {

    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        setTimeout(function() {
            
            @yield('partBaseSubmit')  
            @yield('partLicenseSubmit')  
            @yield('partWorkSubmit')  
            @yield('partAbsentSubmit')  
            
            document.mainForm.submit();
        }, 300); 
    }); 
    
    AppUtil.initSpTabs();
     
    @yield('popupLicenseJs')   
    @yield('popupWorkJs')   

    
    @yield('partBaseJs')    
    @yield('partLicenseJs')    
    @yield('partWorkJs')    
    @yield('partAbsentJs')    

} );


</script>
<style>
    .textInput{
        color:#3C4C59;
        border: 1px solid #3C4C59;
    }
    .textReadOnly{
        color:#3C4C59;
        border: 1px solid #3C4C59;
    }
</style>
@endsection

@section('content')


<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">


<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?> 


<?php if ($pageMode == 'add'): ?>
    <form action="./addSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm" autocomplete="off"   >
<?php else: ?>
    <form action="./editSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  autocomplete="off" >
<?php endif; ?>

 {{ csrf_field() }}
        
<input type='hidden' name='mongoId' value='<?php echo $mongoId?>' />
<input type='hidden' name='valueDatas'  />

<div class="spTabs_container">
    <ul class="spTabs">
        <li class="active"><a href="#tabBase">ข้อมูลพื้นฐาน</a></li>
        <?php if ($pageMode == 'view'): ?>
        {!! SiteHelper::checkPermission('staff/viewLicense', "<li ><a href='#tabLicense' >ข้อมูลใบขับขี่</a></li>") !!}
        {!! SiteHelper::checkPermission('staff/viewWork', "<li ><a href='#tabWork' >ข้อมูลการทำงาน</a></li>") !!}
        {!! SiteHelper::checkPermission('staff/viewAbsent', "<li ><a href='#tabAbsent' >ข้อมูลวันหยุด/ลา</a></li>") !!}
        <?php else: ?>
        {!! SiteHelper::checkPermission('staff/editLicense', "<li ><a href='#tabLicense' >ข้อมูลใบขับขี่</a></li>") !!}
        {!! SiteHelper::checkPermission('staff/editWork', "<li ><a href='#tabWork' >ข้อมูลการทำงาน</a></li>") !!}
        {!! SiteHelper::checkPermission('staff/editAbsent', "<li ><a href='#tabAbsent' >ข้อมูลวันหยุด/ลา</a></li>") !!}

        
        <?php endif; ?>

        
    </ul>
</div>

    
<div class="spTabs_body" style=" padding: 10px">
    <div  class='spTabs_item'  id="tabBase">
        @yield('partBaseHtml') 
    </div>    
    <div  class='spTabs_item'  style='display:none'  id="tabLicense">
        @yield('partLicenseHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabWork">
        @yield('partWorkHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabAbsent">
        @yield('partAbsentHtml') 
    </div>
    
</div>
        
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

@yield('popupLicenseHtml')
@yield('popupWorkHtml')

@endsection

   


