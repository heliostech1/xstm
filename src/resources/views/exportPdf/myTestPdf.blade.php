<!DOCTYPE html>
<html>
<head>
<title>Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>  
<style>

@include('exportPdf.domPdfCss')


.title1{
   font-family: "ThaiTest1";
}

.title2{
   font-family: "ThaiTest2";
}

.title3{
   font-family: "ThaiTest3";
}

.title4{
   font-family: "THSarabunNew";
}

.title5{
   font-family: "monospace";
}

.picture{
   width: 200px;
   height: 200px;
   background-image: url('images/shopping.png')  ;
}

.customPicture{
   width: 200px;
   height: 200px;
   background-image: url('http://localhost/xstm/src/public/fileUpload/view?name=20210409-171808-607029e050143.png&amp;thumb=true') ;
}

</style>    
</head>


<body>

    <div class="title1">หน้าทดสอบรายงาน 111(TEST REPORT)</div>
    <div class="title2">หน้าทดสอบรายงาน 222(TEST REPORT)</div>    
    <div class="title3">หน้าทดสอบรายงาน 333(TEST REPORT)</div>        
    <div class="title4">หน้า ทดสอบ รายงาน 444(THSarabunNewxxxx)</div>    
    <div class="title5">MONO SPACE XXX</div>        
    <div class="reportTitle">REPORT TITLE 444(TEST REPORT)</div>      
    <br/>
    
     <div >หน้า ทดสอบ รายงาน 444  ทั้งหมด(THSarabunNewxxxx)</div>   
    <span><b>จำนวน ทั้งหมด:</b>&nbsp;xxxx</span><span>&nbsp;&nbsp;&nbsp;</span>
    <span><b>จำนวน ผ่าน:</b>&nbsp;xxxx</span><span>&nbsp;&nbsp;&nbsp;</span>
    <span><b>จำนวน ไม่ผ่าน:</b>&nbsp;xxx</span><span>&nbsp;&nbsp;&nbsp;</span>
    <br/>
<span><b>จำนวนตรวจ:</b>&nbsp;xxxx</span><span>&nbsp;&nbsp;&nbsp;</span>
<span><b>จำนวนผ่าน:</b>&nbsp;xxxx</span><span>&nbsp;&nbsp;&nbsp;</span>
<span><b>จำนวนไม่ผ่านxx:</b>&nbsp;xxx</span>
    
   <div class="picture"></div>
https://github.com/barryvdh/laravel-dompdf

<br> 
 STORAGE: {{storage_path('fonts/') }}
 <br>
 PUBLIC: {{public_path('fonts/') }}
 <br>
 SiteHealper::URL : {{ SiteHelper::url('fonts/') }}
 <br>
 ASSET: {{ asset('fonts') }}
  <br>
 base_path: {{ base_path() }}
  <br>
 realpath(base_path): {{ realpath(base_path())}}
  <p/>
  <p/>
  IMAGE TAG: 
    <br>
   <img src='http://localhost/xstm/src/public/fileUpload/view?name=20210409-211151-607060a76c791x.jpg&thumb=true' width="100" height="100" />
  <p/>
     <img src='https://as2.ftcdn.net/v2/jpg/04/20/61/63/1000_F_420616397_N2XxqIhwAIRzcAQtbuhiIQkJYv3aCCYP.jpg' width="100" height="100" />

  <!--
  IMAGE BASE 64: 
    <br>
   <img src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAAUA
AAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO
9TXL0Y4OHwAAAABJRU5ErkJggg==" alt="Example" />
-->





@include('exportPdf.domPdfFooter')
    
</body>
</html>