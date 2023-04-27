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

.form-control input{
  background: transparent;
}
.form-group input:not(#buttonSubmit) {
    background: transparent;
    border: none;
    border-bottom: 2px solid #FFA500;
    border-radius: 0;
    box-shadow: none;
    padding: 0.5rem;
    font-size: 1rem;
    color: #495057;
}
.form-group input:focus :not(#buttonSubmit){
  outline: none;
  box-shadow: none;
  border-color: #F57D00;
}
.btn-orange {
  background: linear-gradient(88.95deg, #EF5F00 1.35%, #F57D00 63.97%, #F1B679 98.62%);
  color: #FFFFFF;
  border: none;
  border-radius: 4px;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;

}

.btn-orange:hover {
  transition: all .4s ease;
  transition-delay: 0s;
  background: #D94E00;
  color:#FFFFFF;
}
.card-img-top {
  max-width: 100%;
  width: 100px; /* เพิ่มความกว้างของโลโก้ให้เป็น 100px */
  max-height: 100px; /* เพิ่มความสูงของโลโก้ให้เป็น 100px */
  display: block; /* ตั้งค่าให้โลโก้เป็น block element */
  margin: 0 auto;
}
.login-container {
  box-sizing: border-box;
  border: 1px solid orange;
  border-radius: 10px;
  background: rgba(245, 125, 0, 0.08);
  position: absolute; /* กำหนดให้ container login อยู่ในตำแหน่งแบบ absolute */
  top: 50%; /* กำหนดให้ container login อยู่ตรงกลางจอในแนวดิ่ง */
  left: 50%; /* กำหนดให้ container login อยู่ตรงกลางจอในแนวนอน */
  transform: translate(+70%, -50%); /* กำหนดให้ container login ย้ายตำแหน่งไปอยู่ที่กลางจอ */
  max-width: 500px; /* กำหนดขนาดสูงสุดของ container login */
  width: 315px; /* กำหนดขนาดความกว้างของ container login เป็น 100% */
  height: 350px;
  padding: 20px; /* กำหนดระยะห่างของขอบภายใน container login */
}
.login-container input::placeholder {
  color: #FFFFFF;
}
.footerbox{
  background: rgba(245, 125, 0, 0.5);
  padding: 0px;
  border: 1.5px solid rgba(245, 125, 0, 1);
  color: #FFFFFF;
}
.siteFooter{
  padding:0px;
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
