@section('partAbsentHelper')




@endsection



@section('partAbsentJs')




@endsection


@section('partAbsentSubmit')
   
@endsection



@section('partAbsentHtml')


            
<table cellspacing="0" border="0" cellpadding="0" class="formTable">
    <tbody>

<?php if ($pageMode == 'edit' || $pageMode == 'view'): ?>  

        <tr>
            <td class="formLabel" style='width:200px ; text-align:right;' >รหัสพนักงาน</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px"  value='{{ $staffCode }}'  autocomplete="off">
        </tr>        
        <tr>
            <td class="formLabel" style='width:200px; text-align:right;' >ชื่อ นามสกุล</td>
            <td><input class="textReadOnly" readonly type="text" style="width:400px" value='{{ $staffName }}'  autocomplete="off">
        </tr>  
            
<?php endif; ?>   

    </tbody>
</table>

    
@endsection
