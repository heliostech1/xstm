@section('partContainerHelper')




@endsection



@section('partContainerJs')




@endsection


@section('partContainerSubmit')
   

@endsection




@section('partContainerHtml')

            
            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr >
            <td class="formLabel" style='width:200px' >รหัสรถ</td>
            <td style="text-align: left"><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px;"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr id=2-11>
            <td class="formLabel" style='width:200px' >ทะเบียนรถ</td>
            <td style="text-align: left"><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   
        <tr>
            <td class="formLabel" style='width:200px' >ชนิดตู้สินค้า</td>
            <td> {!! SiteHelper::dropdown("partContainer_containerType", $goodsContainerOpt, $partContainer_containerType, "  class='textInput' style='width:400px'id=Two-oneone ") !!} </td> 
        </tr> 
        <tr >
            <td class="formLabel">ขนาดภายในตู้ (เมตร)</td>
            <td> กว้าง <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_width }}' name="partContainer_width" id=Two-oneone>
            &nbsp;&nbsp; ยาว  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_long }}' name="partContainer_long" id=Two-oneone>
            &nbsp;&nbsp; สูง  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_height }}' name="partContainer_height" id=Two-oneone>
            
            </td>
        </tr> 
        <tr>
            <td class="formLabel">ความสูงภายในใต้แอร์ถึงพื้นตู้ (เมตร)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partContainer_airInnerHeight }}' name="partContainer_airInnerHeight" id=Two-oneone>
            </td>
        </tr>   
        <tr>
            <td class="formLabel">ขนาดตู้ภายนอก (เมตร)</td>
            <td> กว้าง <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerWidth }}' name="partContainer_outerWidth" id=Two-oneone>
            &nbsp;&nbsp; ยาว  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerLong }}' name="partContainer_outerLong" id=Two-oneone>
            &nbsp;&nbsp; สูง  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerHeight }}' name="partContainer_outerHeight" id=Two-oneone>
            
            </td>
        </tr>     
        <tr>
            <td class="formLabel">ความสูงพื้นรถจากพื้นดิน (เมตร)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partContainer_groundHeight }}' name="partContainer_groundHeight" id=Two-oneone>
            </td>
        </tr>         
    </tbody>
</table>


@endsection
