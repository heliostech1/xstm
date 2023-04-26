@extends('layouts.app')

@include('vehicle.attachFilePopup')

@include('vehicle.vehPopupRegis')
@include('vehicle.vehPopupOwner')
@include('vehicle.vehPopupTax')
@include('vehicle.vehPopupCare')

@include('vehicle.vehPartBase')
@include('vehicle.vehPartCare')
@include('vehicle.vehPartChiller')
@include('vehicle.vehPartContainer')
@include('vehicle.vehPartFuel')

@include('vehicle.vehPartOwner')
@include('vehicle.vehPartRegis')
@include('vehicle.vehPartTax')
@include('vehicle.vehPartMonitor')
@include('vehicle.vehPartMonitorView')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

@yield('popupRegisHelper')    
@yield('popupOwnerHelper')    
@yield('popupTaxHelper')   
@yield('popupCareHelper')  

@yield('partBaseHelper')    
@yield('partCareHelper')    
@yield('partChillerHelper')    
@yield('partContainerHelper')    
@yield('partFuelHelper')    

@yield('partOwnerHelper')  
@yield('partRegisHelper')    
@yield('partTaxHelper')    
@yield('partMonitorHelper')    
@yield('partMonitorViewHelper')  

$(document).ready(function() {

    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        if (!partBaseCheckOdometer()) {
            return;
        }
        
        setTimeout(function() {
            //document.mainForm.valueDatas.value = $.toJSON(ResultTableHelper.getDataForSubmit());
            //console.debug(document.mainForm.valueDatas.value );
            //document.mainForm.partRegis_fileDatas.value = $.toJSON(partRegisUploader.getDataStringForSubmit());     
            //document.mainForm.partOwner_fileDatas.value = $.toJSON(partOwnerUploader.getDataStringForSubmit()); 
            //document.mainForm.partTax_fileDatas.value = $.toJSON(partTaxUploader.getDataStringForSubmit()); 
            document.mainForm.partFuel_fileDatas.value = $.toJSON(partFuelUploader.getDataStringForSubmit());     
            
            
            @yield('partCareSubmit')  
            @yield('partChillerSubmit')  
            @yield('partContainerSubmit')  
            @yield('partFuelSubmit')  
            @yield('partOwnerSubmit')  
            @yield('partRegisSubmit')              
            @yield('partTaxSubmit')  
            @yield('partMonitorSubmit')
            
            document.mainForm.submit();
        }, 300); 
    }); 
    
    /*
    window.partRegisUploader = new BatchUploader( {
        uploaderName: "partRegis_uploader", containerId: "partRegis_fileContainer",
        enableInfo: true,  mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    window.partOwnerUploader = new BatchUploader( {
        uploaderName: "partOwner_uploader", containerId: "partOwner_fileContainer",
        enableInfo: true,  mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
    window.partTaxUploader = new BatchUploader( {
        uploaderName: "partTax_uploader", containerId: "partTax_fileContainer",
        enableInfo: true,  mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
     */
    window.partFuelUploader = new BatchUploader( {
        uploaderName: "partFuel_uploader", containerId: "partFuel_fileContainer",
        enableInfo: true,  mode: "<?=($pageMode == "view")? 'view':'edit'?>"
    });
   
    //partRegisUploader.addDataStringFromServer(< ?php echo $partRegis_fileDatas?>);    
    //partOwnerUploader.addDataStringFromServer(< ?php echo $partOwner_fileDatas?>);  
    //partTaxUploader.addDataStringFromServer(< ?php echo $partTax_fileDatas?>);  
    partFuelUploader.addDataStringFromServer(<?php echo $partFuel_fileDatas?>);  
    
    AppUtil.initSpTabs();
    
    @yield('attachFilePopupJs')    
    @yield('popupRegisJs')   
    @yield('popupOwnerJs')   
    @yield('popupTaxJs')   
    @yield('popupCareJs') 
    
    @yield('partBaseJs')    
    @yield('partCareJs')    
    @yield('partChillerJs')    
    @yield('partContainerJs')    
    @yield('partFuelJs')   
    
    @yield('partOwnerJs')    
    @yield('partRegisJs')    
    @yield('partTaxJs')        
    <?php if ($pageMode == 'view'): ?>
         @yield('partMonitorViewJs') 
    <?php else: ?> 
         @yield('partMonitorJs')       
    <?php endif; ?>

    
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

<input type='hidden' name='partRegis_fileDatas'  />
<input type='hidden' name='partOwner_fileDatas'  />
<input type='hidden' name='partTax_fileDatas'  />
<input type='hidden' name='partFuel_fileDatas'  />



<div class="spTabs_container">
    <ul class="spTabs">
        <li class="active"><a href="#tabBase">ข้อมูลพื้นฐาน</a></li>
        <?php if ($pageMode == 'view'): ?>
        
        {!! SiteHelper::checkPermission('vehicle/viewRegis', "<li ><a href='#tabRegis' >ข้อมูลรายการจดทะเบียน</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/viewOwner', "<li ><a href='#tabOwner' >ข้อมูลการครอบครองรถ</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/viewTax', "<li ><a href='#tabTax' >ข้อมูลรายการเสียภาษี</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/viewContainer', "<li ><a href='#tabContainer' >ข้อมูลตู้สินค้า</a></li>") !!}
        
        {!! SiteHelper::checkPermission('vehicle/viewFuel', "<li ><a href='#tabFuel' >ข้อมูลเชื้อเพลิง</a></li>") !!}        
        {!! SiteHelper::checkPermission('vehicle/viewChiller', "<li ><a href='#tabChiller' >ข้อมูลเครื่องทำความเย็น</a></li>") !!}     
        {!! SiteHelper::checkPermission('vehicle/viewCare', "<li ><a href='#tabCare' >ข้อมูลการให้บริการ</a></li>") !!}      
        {!! SiteHelper::checkPermission('vehicle/viewMonitor', "<li ><a href='#tabMonitor' >ข้อมูลการซ่อมบำรุง</a></li>") !!} 
        
        <?php else: ?>
        {!! SiteHelper::checkPermission('vehicle/editRegis', "<li ><a href='#tabRegis' >ข้อมูลรายการจดทะเบียน</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/editOwner', "<li ><a href='#tabOwner' >ข้อมูลการครอบครองรถ</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/editTax', "<li ><a href='#tabTax' >ข้อมูลรายการเสียภาษี</a></li>") !!}
        {!! SiteHelper::checkPermission('vehicle/editContainer', "<li ><a href='#tabContainer' >ข้อมูลตู้สินค้า</a></li>") !!}
        
        {!! SiteHelper::checkPermission('vehicle/editFuel', "<li ><a href='#tabFuel' >ข้อมูลเชื้อเพลิง</a></li>") !!}        
        {!! SiteHelper::checkPermission('vehicle/editChiller', "<li ><a href='#tabChiller' >ข้อมูลเครื่องทำความเย็น</a></li>") !!}     
        {!! SiteHelper::checkPermission('vehicle/editCare', "<li ><a href='#tabCare' >ข้อมูลการให้บริการ</a></li>") !!}  
        {!! SiteHelper::checkPermission('vehicle/editMonitor', "<li ><a href='#tabMonitor' >ข้อมูลการซ่อมบำรุง</a></li>") !!}         
        
        <?php endif; ?>

        
    </ul>
</div>

    
<div class="spTabs_body" style=" padding: 10px">
    <div  class='spTabs_item'  id="tabBase">
        @yield('partBaseHtml') 
    </div>    
    <div  class='spTabs_item'  style='display:none'  id="tabRegis">
        @yield('partRegisHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabOwner">
        @yield('partOwnerHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabTax">
        @yield('partTaxHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabContainer">
        @yield('partContainerHtml') 
    </div>
    
    <div  class='spTabs_item'  style='display:none'  id="tabFuel">
        @yield('partFuelHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabChiller">
        @yield('partChillerHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabCare">
        @yield('partCareHtml') 
    </div>    
    <div  class='spTabs_item'  style='display:none'  id="tabMonitor">
        
        <?php if ($pageMode == 'view'): ?>
             @yield('partMonitorViewHtml') 
        <?php else: ?> 
             @yield('partMonitorHtml')       
        <?php endif; ?>
       
    </div>      
</div>
        
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

@yield('popupRegisHtml')
@yield('popupOwnerHtml')
@yield('popupTaxHtml')
@yield('popupCareHtml')

@yield('attachFilePopupHtml')

@endsection

   


