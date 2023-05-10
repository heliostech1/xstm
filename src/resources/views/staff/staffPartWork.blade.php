@section('partWorkHelper')




@endsection



@section('partWorkJs')


    <?php if ($pageMode == 'add' || $pageMode == 'edit'): ?>  
       $('#partWork_startDate').datepicker();
       
    <?php endif; ?>  


@endsection


@section('partWorkSubmit')
   
@endsection



@section('partWorkHtml')


            
<table cellspacing="0" border="0" cellpadding="0" class="formTable" >
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >รหัสพนักงาน</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $staffCode }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >ชื่อ นามสกุล</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $staffName }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   

    </tbody>
</table>



<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>      
        <tr>
            <td class="formLabel"  style='width:200px; text-align:right;' >วันที่เริ่มงาน</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partWork_startDate }}' name="partWork_startDate" id='partWork_startDate'></td>
        </tr>            
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>สถานะการทำงาน</td>
            <td> {!! SiteHelper::dropdown("partWork_workStatus", $workStatusOpt, $partWork_workStatus, "  id='partWork_workStatus'  class='textInput' style='width:400px'  ") !!} </td> 
        </tr>           
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>เงินเดือน(ต่อเดือน)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partWork_amount }}' name="partWork_amount"></td>
        </tr>      
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;'>เงินเดือน(ต่อวัน)</td>
            <td><input class="textInput" type="text" style="width:400px" value='{{ $partWork_amountDay }}' name="partWork_amountDay"></td>
        </tr>          
  
       
    </tbody>
</table>



    
@endsection
