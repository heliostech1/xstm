@extends('layouts.app')

@section('header')

<script type='text/javascript'>
$(document).ready(function() {


} );


</script>


@endsection

@section('content')

<span id="pageTitle">TEST ADD IMAGE,FILE</span><br/>
<hr class="titleSectionSep">

 <?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display: none"></div>




<form action="../commonService/addFile" method="post" enctype="multipart/form-data">
    
  <table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

        <tr>
            <td class="formLabel" >Token:</td>
            <td><input type="text" name="token"  value="token@abc1234"></td> <!-- token@abc1234 -->
        </tr>
        <tr>
            <td class="formLabel">Image:</td>
            <td><input type="file" name="file" ></td>
        </tr>          
  
    </tbody>
</table>
    
<div style='padding:20px'>
  <input type="submit" value="Upload Image" name="submit">
</div>
    
</form>

@endsection



