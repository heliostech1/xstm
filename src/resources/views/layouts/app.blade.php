<!DOCTYPE html>
<html lang="en">
    
@include('layouts.siteBranchPopup')
@include('layouts.siteFooterInfo')
@include('layouts.siteFooterlogin')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appTitle }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type='text/css' href="{{ SiteHelper::url('css/bootstrap/bootstrap-3.3.7.css') }}"/>
    
    <link rel="stylesheet" type="text/css" href="{{ SiteHelper::url('css/jquery/smoothness/jquery-ui-1.8.18.custom.css') }}" />    
    <link rel="stylesheet" type='text/css' href="{{ SiteHelper::url('css/jquery.ui-custom.css') }}"/>
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/jquery.dataTables-1.9.4.css') }}"/>
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/jquery.dataTables-custom.css') }}" />
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/jquery.kw-datepick.css') }}" />
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/jquery-ui-timepicker-addon.css') }}" />
    <link rel="stylesheet" type='text/css' href="{{ SiteHelper::url('css/jquery.ui-custom.css') }}"/>
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/diQuery-collapsiblePanel.css') }}" />
    <link rel="stylesheet" type='text/css' href="{{ SiteHelper::url('css/select2/select2.css') }} "/>
    <link rel="stylesheet" type='text/css' href="{{ SiteHelper::url('css/select2/select2.custom.css') }} "/>
    
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/General.css') }}" />
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/Controls.css') }}" />
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/MenuDropdown.css') }}" />
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/NestedSideMenu.css') }}" />        
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('css/CustomTab.css') }}" />   
   
    <!-- Javascript -->
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery/jquery-1.9.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery/jquery-ui-1.8.23.custom.min.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery/jquery.ui.datepicker-th.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery/i18n/jquery.ui.datepicker-th.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery/jquery-ui-timepicker-addon.js') }}"></script>
    
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.ui.touch-punch.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/datatable/jquery.dataTables-1.9.4.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/datatable/jquery.dataTables-plugin.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/datatable/dataTables.pagination-numberWithListbox.js') }}"></script>
    <!--  <script type="text/javascript" src="{{ SiteHelper::url('js/datatable/keyTable-1.1.7.js') }}"></script>  -->
    
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.tablednd_0_5-edit.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.jeditable-1.7.1.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.maskedinput-1.3.1.js') }}"></script>
    
    <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.json-2.3.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ SiteHelper::url('js/jquery.blockUI.js') }}"></script> -->
    <script type="text/javascript" src="{{ SiteHelper::url('js/jscolor/jscolor.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/diQuery-collapsiblePanel.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/bootstrap/bootstrap-3.3.7.js') }}"></script>
    
    <script type="text/javascript" src="{{ SiteHelper::url('js/DTHelper.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/FieldHelper.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/FormHelper.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/AppUtil.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/DateUtil.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/MapUtil.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/AutoUpdater.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/MyUpdater.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/ImageFileUtil.js') }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/BatchUploader.js') }}"></script>    
    <script type="text/javascript" src="{{ SiteHelper::url('js/MyTableHelper.js') }}"></script>  
    
<?php if ($pageOptFileUpload):?>
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('js/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css') }}" />
    <script type="text/javascript" src="{{ SiteHelper::url('js/plupload/plupload.full.min.js')  }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/plupload/jquery.ui.plupload/jquery.ui.plupload.js')  }}"></script>
    <script type="text/javascript" src="{{ SiteHelper::url('js/plupload/i18n/th.js') }}"></script>
<?php endif;?>
    
<?php if ($pageOptChart):?>
    <!-- https://www.codewall.co.uk/best-javascript-chart-libraries/ -->
    <link rel='stylesheet' type='text/css' href="{{ SiteHelper::url('js/chart/dygraph.css') }}" />
    <script type="text/javascript" src="{{ SiteHelper::url('js/chart/dygraph.js') }}"></script>     
<?php endif;?>
    
<style>
    /* Remove the navbar's default margin-bottom and rounded borders */
    .navbar {
      margin-bottom: 0;
      border-radius: 0;
    }
    
    /* Add a gray background color and some padding to the footer */
    footer {
     /* background-color: #f2f2f2;
      padding: 25px; */
    }
    

    
#siteWrapper {
    min-height: 100%;
    height: auto !important; /* */
    
    /*  background-color: #f9f8f8; */
     /* border:2px solid yellow;    */

}

    
.siteBody {
   padding:100px 100px 0px 99px !important;  
   background-color:#FFFFFF !important;   
   height: 100vh;
   
  /* border:2px solid blue; */
   
}

.loginBody {
   min-height: 100vh;
   height: auto;
   background-image: url( {{ Url::asset('images/bglogin.png')}} );
   background-repeat: no-repeat;
   background-size: 100% 100%; 
}
    
.siteTitleText {
    font-family: 'Trebuchet MS',arial,verdana, sans-serif;
    font-size: 26px;
    font-weight: bold;

    color: #FFF !important;
    text-shadow: 1px 1px 5px #000000;
}
    

    
.sitePageContainer{
    min-height: 100%; 
    padding: 0px;
    border-style: solid;
    
    /* border-color: #333; */
    border-width: 0px;
    background-color: transparent; 
    vertical-align: top;
    text-align: left;
  /*  box-shadow: 0px 5px 20px 7px #D5D5D5; 
    box-shadow: 0px 4px 20px 5px #C2C2C2; */

     border-radius: 10px;
     
     margin-left: 230px;

}

.loginPageContainer{
    min-height: 100vh;
    /* border: 1px solid #333; */
    vertical-align: top;
    text-align: center;
    background: linear-gradient(180deg, rgba(245, 125, 0, 0.23) 0%, rgba(217, 217, 217, 0) 100%);
}


.siteTopHeader {
  background-color: #3C4C59;  
  position: fixed;
  top: 0;
  width: 100%;
  height: 75px; 
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;

}


.navbar-inverse .navbar-nav>li>a {
    color: #f7f7f7;
}

.siteTopMenu {
    border-bottom: 1px solid black;
    background-color: #D1D1D1;
    height: 25px;
    padding-left: 25px;    
     /*box-shadow:0 2px 5px 0 rgba(51, 51, 51, 0.24);*/
}    


.siteFooter {
    clear: both;
    width:93.25%;
    height: 120px;
    margin-bottom: -50px;
    bottom: 0;
    position: absolute;
    border-top: 1px solid #3C4C59;
    /* color: #eee; */
    background-color:#FFFFFF;    
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    z-index: 1;


}

.siteFooterlogin {
    clear: both;
    width:100%;
    height: 120px;

    position: absolute;
    bottom: 0;
    /* color: #eee; */
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    z-index: 1;
}

.siteBottomHeader {
    /* height: 2px;
    background-color: rgb(95, 95, 95); */
}

.siteLoginBottomHeader {
    height: 6px;
    background-color: rgb(95, 95, 95);
    border: 1px solid rgb(82, 82, 82);
    box-shadow: -1px 2px 12px 5px #bbbbbb;    
}

.siteLogoContLeft {
    float:left;

}


.siteTitleContLeft {
    float: left;
    height: 45px;
    padding: 15px 15px;
    font-size: 18px;
    line-height: 20px
    
}

.siteTitleContRight {
    font-size: 15px;
    color:  #FFFFFF;
}  

.siteTitleLoginBtn a {    
    color: #F57D00 !important;  /* d1ecff; */
    font-weight: bold;  
}

.sidenav {
    height: 100%;
    width: 225px;
    position: fixed;
    z-index: 5;
    top: 0;
    left: 0;
    /* overflow-x: hidden; */
    transition: 0.5s;
    padding-top: 10px;
    margin-top: 47px;
    background-color: #F57D00;
    /* border-top: 1px solid black; */
}



</style>
 

<script type="text/javascript" >

$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.fn.select2.defaults.formatNoMatches =  function () { return "ไม่พบรายการ"; };
    $.fn.select2.defaults.formatInputTooShort = function (input, min) { var n = min - input.length; return "โปรดระบุอีก " + n + " ตัวอักษร"; };
    $.fn.select2.defaults.formatLoadMore = function (pageNumber) { return "กำลังโหลดข้อมูลเพิ่มเติม..."; };
    $.fn.select2.defaults.formatSearching = function () { return "กำลังค้นหา..."; };
                
    $.datepicker.setDefaults({
        changeMonth: true,
        changeYear: true 
    });  

    @yield('siteBranchPopupJs')
    
    FormHelper.setEnterBehaveTab();
});

</script>
  
  @yield('header')  
  
</head>
<body>
    
<?php if ($pageName != 'login' && $pageOptChildPage !== true): ?>
<div id="mySidenav" class="sidenav">
  <?php echo $pageMenuList?>  
</div>
    
<?php endif;?>
    
    
<div id='siteWrapper'>    

    <?php if ($pageName != 'login'): ?>
    
        <div class="siteTopHeader">
         <div class="siteLogoContLeft" >
               <div style='padding:0px 0px 0px 20px' >
                   <img src='{{ Url::asset('images/logo/company_top.png')}}'  style="width:100px" />
               </div>
         </div>
        
        <div class="siteTitleContRight">
            <div style="color:white; padding:7px 5px 0px 0px">
              <div style='float:right' class='siteTitleLoginBtn'><?=$pageNavbar?> &nbsp;&nbsp;</div>                
              <div style='float:right'><?=$pageNavbarUser?> &nbsp;&nbsp;</div>          
              <div style='clear:both'></div>
            </div>

        </div>
            
    
    </div>
    <div class="siteBody" >
        <div class='sitePageContainer'>
             @yield('content')                   
        </div>
        <footer class='siteFooter' >       
            @yield('siteFooterInfoHtml')
        </footer>
    </div>
    <?php else:?>    
    <div class="loginBody" >
       <div class='loginPageContainer'>
           @yield('content') 
        <footer class='siteFooterlogin' >       
            @yield('siteFooterloginHtml')
        </footer>        
       </div> 
    </div>
    <?php endif;?>


</div> <!--  end of siteWrapper -->  



@yield('siteBranchPopupHtml')
<div id='plUploadContainer' style='display:none'></div>
    
</body>
</html>

