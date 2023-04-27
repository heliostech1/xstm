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

  <div class="login-container" >
    <div class="form-group text-center">
              <form method="POST" action="{{ url('/login') }}">
                  {{ csrf_field() }}
                  <img class="card-img-top" src="{{ Url::asset('images/logo/company_bottom.png')}}" > <br>
                  <input id="accountIdInput" type="text" class="form-control" placeholder="บัญชี"name="accountId" value="{{ old('accountId') }}"> <br>
                  <input id="userIdInput" type="text" class="form-control"  placeholder="ผู้ใช้"name="userId" value="{{ old('userId') }}"> <br>
                  <input id="passwordInput" type="password" class="form-control"  placeholder="รหัสผ่าน"name="password" > <br>
                  <input id="buttonSubmit" type="submit" value="เข้าสู่ระบบ" class="btn btn-orange" name="submit" >&nbsp;
              </form>
      </div>
  </div>


@endsection
