

@section('partBaseHelper')

function partBaseCheckOdometer() {
   var newOdo = $("#odometerInput").val();
   var oldOdo = $("#oldOdometerInput").val();
   
   if (!AppUtil.isEmpty(oldOdo) && newOdo < oldOdo) {
       if (confirm("ข้อมูลเลฃไมล์มีค่าน้อยกว่าเดิม ยืนยันการบันทึกหรือไม่")) {
          return true;
       }
       else {
          return false;
       }
   }
   
   return true;
   
}


@endsection



@section('partBaseJs')

  $('#provinceInput').select2();


@endsection


@section('partBaseHtml')

<input  type="hidden" style="width:400px" value='{{ $oldOdometer }}' name="oldOdometer" id="oldOdometerInput" />

<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px' >รหัสรถ</td>
            <td><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px"  value='{{ $vehicleId }}' name="vehicleId" autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel">สถานะ</td>
            <td>{!! SiteHelper::dropdown('active', $activeOpt, $active, "class='textInput' style='width:200px' id=Two-oneone ") !!}
            </td>
        </tr>
            
<?php endif; ?>   
        <tr>
            <td class="formLabel" style='width:200px'  >ทะเบียนรถ</td>
            <td><input id=Two-oneone class="textInput" type="text" style="width:200px" id='licensePlate' value='{{ $licensePlate }}' name="licensePlate" autocomplete="off">
        </tr>               
        <tr>
            <td class="formLabel">จังหวัด</td>
            <td>
            {!! SiteHelper::dropdown("province", $provinceOpt, $province, "  class='textInput' style='width:200px' id='provinceInput' ") !!}
           </td>            
        </tr> 
        <tr>
            <td class="formLabel">เลขไมล์</td>
            <td><input id=Two-oneone class="textInput" type="text" style="width:200px" value='{{ $odometer }}' name="odometer" id="odometerInput"></td>
        </tr>      
        <?php if ($pageMode == 'view'): ?>  
        <tr>
            <td class="formLabel"  >อายุรถ (ปี):</td>
            <td><input id=Two-oneone class="textReadOnly" readonly type="text" style="width:200px" value='{{ $ageYear }}'  autocomplete="off">
        </tr>  
        <?php endif; ?>          
    </tbody>
</table>



@endsection


