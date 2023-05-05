@extends('layouts.app')

@section('content')

<div id="pageTitle"><h1><?php echo $sitePageName?></h1></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">

<?php if (!empty($message)) echo "<div class='infoMessage'>$message</div>"?>


<form action="./changePasswordSubmit" method="post" autocomplete="off" name="mainForm" id="mainForm"  >
 
  {{ csrf_field() }}
  
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>
        <tr>
            <td class="formLabel">รหัสผ่านเดิม:</td>
            <td><input class="textInput" type="password" style="width:260px"  name="old_password" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel">รหัสผ่านใหม่:</td>
            <td><input class="textInput" type="password"  style="width:260px" name="new_password" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="formLabel">ยืนยันรหัสผ่านใหม่:</td>
            <td><input class="textInput" type="password" style="width:260px" name="confirm_new_password" autocomplete="off"></td>
        </tr>   
    </tbody>
</table>


<div class='footerBtnCont'>
    <div class='footerBtnLeft'><input type="submit" class='blackBtn' value="เปลื่ยน" /></div>
     <div style='clear: both'></div>
</div>

</form>

@endsection

