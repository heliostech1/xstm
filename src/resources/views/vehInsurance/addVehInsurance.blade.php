@extends('layouts.app')


@include('vehInsurance.vehPopupInsAct')
@include('vehInsurance.vehPopupInsCar')
@include('vehInsurance.vehPopupInsGoods')
@include('vehInsurance.vehPopupClaim')

@include('vehInsurance.vehPartInsAct')
@include('vehInsurance.vehPartInsCar')
@include('vehInsurance.vehPartInsGoods')
@include('vehInsurance.vehPartClaim')

@section('header')

<?php if (!isset($pageMode)) $pageMode = 'view'?>


<script type='text/javascript'>

@yield('popupInsActHelper')    
@yield('popupInsCarHelper')    
@yield('popupInsGoodsHelper')   
@yield('popupClaimHelper')  

@yield('partInsActHelper')    
@yield('partInsCarHelper')    
@yield('partInsGoodsHelper')    
@yield('partClaimHelper')    

$(document).ready(function() {

    
    <?php if ($pageMode == 'edit'): ?>

    //FieldHelper.applyViewMode('keyIdInput');

    <?php elseif ($pageMode == 'view'): ?>
    
    FormHelper.applyViewMode('mainForm');
    
    <?php endif; ?>

    $('#saveBtn').click(function() { 
        setTimeout(function() {
  
            
            @yield('partInsCarSubmit')  
            @yield('partInsActSubmit')              
            @yield('partInsGoodsSubmit')  
            @yield('partClaimSubmit')  
            
            document.mainForm.submit();
        }, 300); 
    }); 
    
   
    AppUtil.initSpTabs();
    
   
    @yield('popupInsActJs')   
    @yield('popupInsCarJs')   
    @yield('popupInsGoodsJs')   
    @yield('popupClaimJs')   
     
    @yield('partInsActJs')        
    @yield('partInsCarJs')    
    @yield('partInsGoodsJs')    
    @yield('partClaimJs')   

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
    .textAreaInput{
        color:#3C4C59;
        border: 1px solid #3C4C59;
    }
    .formLabel{
        text-align: right;
    }
    .ui-draggable .ui-dialog-titlebar {
        background: #3C4C59;
        color: #ffffff;
    }
    .ui-widget,.ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button {
        font-family: 'Noto Sans Thai', sans-serif;
    }
    .ui-dialog .ui-dialog-content {
        background: #FEF5EB;
    }
    .ui-dialog .ui-dialog-buttonpane button {
        margin: .5em .4em .5em 0;
        cursor: pointer;
        border: 1px solid rgba(60, 76, 89, 1);
        background-color: #fff;
        border-radius: 4px;
        color: rgba(60, 76, 89, 1);
    }
    .ui-dialog .ui-dialog-buttonpane button:hover {
        border: 1px solid rgba(60, 76, 89, 1);
        background-color: rgba(60, 76, 89, 1);
        border-radius: 4px;
        color: #FFF;
    }
    .ui-dialog .ui-dialog-buttonpane {
        background: #FEF5EB;
        margin: 0px;
    }
</style>
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


<div class="spTabs_container">
    <ul class="spTabs">
        <li class="active"><a href="#tabInsAct">&nbsp;&nbsp;พรบ&nbsp&nbsp;</a></li>
        <?php if ($pageMode == 'view'): ?>
        {!! SiteHelper::checkPermission('vehInsurance/viewInsCar', "<li ><a href='#tabInsCar' >ประกันภัยรถยนต์</a></li>") !!}
        {!! SiteHelper::checkPermission('vehInsurance/viewInsGoods', "<li ><a href='#tabInsGoods' >ประกันภัยสินค้า</a></li>") !!}
        {!! SiteHelper::checkPermission('vehInsurance/viewClaim', "<li ><a href='#tabClaim' >ประวัติการเคลม</a></li>") !!}
        
        <?php else: ?>
        {!! SiteHelper::checkPermission('vehInsurance/editInsCar', "<li ><a href='#tabInsCar' >ประกันภัยรถยนต์</a></li>") !!}
        {!! SiteHelper::checkPermission('vehInsurance/editInsGoods', "<li ><a href='#tabInsGoods' >ประกันภัยสินค้า</a></li>") !!}
        {!! SiteHelper::checkPermission('vehInsurance/editClaim', "<li ><a href='#tabClaim' >ประวัติการเคลม</a></li>") !!}
        
        
        <?php endif; ?>

        
    </ul>
</div>

    
<div class="spTabs_body" style=" padding: 10px">
 
    <div  class='spTabs_item'   id="tabInsAct">
        @yield('partInsActHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabInsCar">
        @yield('partInsCarHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabInsGoods">
        @yield('partInsGoodsHtml') 
    </div>
    <div  class='spTabs_item'  style='display:none'  id="tabClaim">
        @yield('partClaimHtml') 
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

@yield('popupInsActHtml')
@yield('popupInsCarHtml')
@yield('popupInsGoodsHtml')
@yield('popupClaimHtml')

@endsection

   


