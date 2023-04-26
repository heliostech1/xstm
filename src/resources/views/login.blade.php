@extends('layouts.app')

@section('header')

<style>

.accountLoginTitle {
  border-bottom: 1px solid #000;
  margin-bottom: 12px;
  padding:5px;
  font-size:17px;
  font-weight: bold;
}

TD.accountLoginFieldLabel {
    padding-right: 4px;
    text-align: left;
    height:25px;
}

.infoLoginContent {
    padding-top: 10px;
    font-style: oblique;
    text-align: center;
}

.infoLoginContent div{
    padding:5px;
}


</style>


<script type='text/javascript'>
$(document).ready(function() {
    $('#clearBtn').click(function() { 
        $('#accountIdInput').val("");
        $('#userIdInput').val("");
        $('#passwordInput').val("");
    }); 

    $('#passwordInput').val("");
    $('#accountIdInput').focus();
    
} );
</script>


@endsection


@section('content')

    <div class="accountLoginTitle">กรุณาระบุข้อมูลผู้ใช้และรหัสผ่าน </div>    
    <div style='text-align: center; padding: 10px 0px 20px 0px;'>
    
    <form method="POST" action="{{ url('/login') }}">
        {{ csrf_field() }}
        
        <table cellspacing="0" cellpadding="0" border="0" style='margin: 0 auto; text-align:left'>
            <tbody>    
                <!--    -->
                <tr>
                    <td class="accountLoginFieldLabel">บัญชี:</td>
                    <td class="accountLoginFieldValue">
                    <input id="accountIdInput" type="text" class="textInput" style='width:220px' name="accountId" value="{{ old('accountId') }}">
                    </td>
                </tr>  
               
                <tr>
                    <td class="accountLoginFieldLabel">ผู้ใช้:</td>
                    <td class="accountLoginFieldValue">
                    <input id="userIdInput" type="text" class="textInput" style='width:220px' name="userId" value="{{ old('userId') }}">
                    </td>
                </tr>
                <tr>
                    <td class="accountLoginFieldLabel">รหัสผ่าน:</td>
                    <td class="accountLoginFieldValue">
                      <input id="passwordInput" type="password" class="textInput" style='width:220px' name="password">
                    </td>
                </tr>
            </tbody>
        </table>
        
        <br>  <span style="font-size: 8pt; padding-left: 10px;"> 
        <input type="submit" value="เข้าสู่ระบบ" class='formButton' name="submit" style="width:80px">&nbsp;
        <input type="button" value="ล้าง"  class='formButton'  id='clearBtn' style="width:60px">
        </span>
    </form> 
    </div>

    <div class="infoLoginContent" id="infoMessage">
    <?php if (isset($message)) echo $message;?>
    
    @if($errors->any())
       @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
       @endforeach
    @endif            
    </div>


@endsection
