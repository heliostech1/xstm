
@section('siteFooterInfoHtml')
<style>
    .footerbox{
        width:100%;
        height: 100%;
        padding-left:150px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        
    }
    .firstfoot h4{
        font-weight: 500;
        font-size:12px; 
    }
    .firstfoot p{
        font-weight: 300;
        font-size:12px; 
    }
    .midfoot{
        display:flex;
        flex-direction: row;
        padding-top: 40px;
    }
    .midfoot p{
        margin: 0px;
    }
    .midfoothead{
        margin-right: 10px;
    }
    .midfoothead p{
        font-weight: 500;
        font-size:10px;
    }
    .midfootinfo p{
        font-weight: 300;
        font-size:10px; 
    }
    .lastfoot img{
        float: right;
    }
    .lastfoot{
        display: flex;
        flex-direction: column;
    }
    .lastfootinfo{
        display: flex;
        flex-direction: column;
        text-align: right;
        font-size: 10px;
    }
    .lastfootinfo p{
        margin: 0px;
    }
    .footlogo{
        margin: 10px 0px;
    }
</style>

<div  class = "footerbox">
    <div class="firstfoot">
        <h4>Contact Us</h4>
        <p> 
        Xsense Information Service Co., Ltd<br>
        No.8, 6th Floor,Soi Sukhapiban 5 Soi-32 Tha Raeng<br>
        Bang Khen Bangkok 10220 Thailand.
        </p>
    </div>
    <div class="midfoot">
        <div class="midfoothead">
            <p>call</p>
            <p>Fax</p>
            <p>Email</p>
        </div>
        <div class="midfootinfo">
            <p>02-115-0131</p>
            <p>02-115-0132</p>
            <p>marketing@the-xsense.com</p>
        </div>
    </div>
    <div class="lastfoot">
        <div class="footlogo">
            <img src='{{ Url::asset('images/logo1.svg')}}'  style="width:50px; height:20px;" />
        </div>
        <div class="lastfootinfo">
            <p style = "font-weight:500">Xsense Information Service Co., Ltd</p>
            <p style = "font-weight:300">
                We provide a full range of service logistics event <br>
                management experience
            </p>
        </div>
    </div>

</div>

@endsection
