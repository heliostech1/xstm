
@section('siteFooterInfoHtml')


<div  style='width:100%; border: 0px solid red; padding-left:230px; white-space:nowrap; text-overflow:ellipsis;   margin: 0 auto;' > 
 
        

    <div style='float:left; width:150px; padding-top:1px;'>
             <img src='{{ Url::asset('images/logo1.svg')}}'  style="width:100px" />
    </div>

    <div style='float:left; width:350px; padding-top:0px;' >
            <div style='font-weight:bold' >Xsense Information Service Co., Ltd</div>
            <div>
                We provide a full range of service logistics event <br/>
                management experience <br/>

            </div>
     </div>

    <div style='float:left'  >

        <div style='font-weight:bold' >Contact Us</div>
        <div>
            <div style='float:left; width:400px' >
                <div class='siteFooterItemLogo'> <img src='{{ Url::asset('images/logo/location.png')}}'  /></div>
                <div class='siteFooterItemText'>Xsense Information Service Co., Ltd.<br/>No.8, 6th Floor, Soi Sukhapiban 5 Soi-32 Tha Raeng<br/>Bang Khen Bangkok 10220 Thailand.</div>
                <div style='clear:both'></div>
            </div>
            <div style='float:left'>
                <div class='siteFooterItemText'>Call 02-115-0131</div>            
                <div style='clear:both'></div>
                <div class='siteFooterItemText'>Fax 02-115-0132</div>            
                <div style='clear:both'></div>
                <div class='siteFooterItemText'>Email marketing@the-xsense.com</div>            
                <div style='clear:both'></div>            
            </div>
            <!--
            <div style='float:left'>
                <div class='siteFooterItemLogo'> <img src='{{ Url::asset('images/logo/facebook.png')}}'  /></div>
                <div class='siteFooterItemText' style='width:150px;'>รถตู้เย็น Catering Lines</div>            
                <div style='clear:both'></div>
                <div class='siteFooterItemLogo'> <img src='{{ Url::asset('images/logo/line.png')}}'  /></div>
                <div class='siteFooterItemText' style='width:150px;'>@cateringlines</div>            
                <div style='clear:both'></div>

            </div>   
            -->
            <div style='clear:both'></div>
        </div>

    </div>
    <div style='clear:both'></div>  
  

    <div colspan='2' style='text-align:center; padding-top:1px'>Updated at: <?php echo $pageBuildDate?></div>

   </div>

</div>

@endsection
