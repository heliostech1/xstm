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

        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >รหัสรถ</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $vehicleId }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >ทะเบียนรถ</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $licensePlate }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >ชนิดตู้สินค้า</td>
            <td> {!! SiteHelper::dropdown("partContainer_containerType", $goodsContainerOpt, $partContainer_containerType, "  class='textInput' style='width:400px'  ") !!} </td> 
        </tr> 
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>ขนาดภายในตู้ (เมตร)</td>
            <td> กว้าง <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_width }}' name="partContainer_width">
            &nbsp;&nbsp; ยาว  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_long }}' name="partContainer_long">
            &nbsp;&nbsp; สูง  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_height }}' name="partContainer_height">
            
            </td>
        </tr> 
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>ความสูงภายในใต้แอร์ถึงพื้นตู้ (เมตร)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partContainer_airInnerHeight }}' name="partContainer_airInnerHeight">
            </td>
        </tr>   
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>ขนาดตู้ภายนอก (เมตร)</td>
            <td> กว้าง <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerWidth }}' name="partContainer_outerWidth">
            &nbsp;&nbsp; ยาว  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerLong }}' name="partContainer_outerLong">
            &nbsp;&nbsp; สูง  <input class="textInput" type="text" style="width:100px" value='{{ $partContainer_outerHeight }}' name="partContainer_outerHeight">
            
            </td>
        </tr>     
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>ความสูงพื้นรถจากพื้นดิน (เมตร)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partContainer_groundHeight }}' name="partContainer_groundHeight">
            </td>
        </tr>         
    </tbody>
</table>


@endsection
