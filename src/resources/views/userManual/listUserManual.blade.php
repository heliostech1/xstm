@extends('layouts.app')

@section('header')

<script type='text/javascript'>
    

var ResultTableHelper = {
    autoRunId: 9991,

    getAutoRunId: function() {
        this.autoRunId++;
        return this.autoRunId;
    },

    setTable: function(table) {
        this.table = table;
    },

    addDataFromServer : function(datas) {
        
        if (AppUtil.isEmpty(datas) || datas.length <= 0) {
            return;
        }
        
        var output = [];
        for ( var i = 0; i < datas.length; i++) {
            var row = datas[i];
            row["rowId"] = this.getAutoRunId();   
            row["fileLink"] = ImageFileUtil.getImageLink( row["file"] );                
            output.push(row);
        }
           
        this.table.fnAddData(output);
    },
    
    
    debug: function() {

    }
};



        
$(document).ready(function() {
   

    var oTable = $('#resultListTable').dataTable( // make it global
            {
                "oLanguage": DTHelper.thaiLang,
                "bPaginate": false,
                "bFilter": false,
                "bSearchable":false,
                "bProcessing": true,
                "bInfo":false,
                "bSort": false,
                "bAutoWidth": false,
                
                <?php if (!empty($tableSelectedId))
                     echo "'fnRowCallback': DTHelper.getSelectRowCallback('$tableSelectedId','ruleId')," 
                 ?>
                        
                        
                "aoColumns": [                    
                              { "mData": "memberCounter"},             
                              { "mData": "itemName"}, 
                              { "mData": "fileLink"}                               
                ],
                        
                "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                    var isTopic = aData["isTopic"];   
                    
                    if ( isTopic == "Y" ) {
                        $(nRow).css("background-color", "#cfe5ff");
                    }

                    return nRow;
                }
            }
     );
        

    DTHelper.applySelectable(oTable, "resultListTable");

    ResultTableHelper.setTable(oTable);
    ResultTableHelper.addDataFromServer(<?=$tableDatas?>);
    

    $('#editBtn').click( function() {
        submitPageData("./edit", oTable, '');
    } );
    
} );


function submitPageData(target, oTable, rowId, newWindow) {
    var form = document.hiddenCriteriaForm;
    var targetBlank = (newWindow === true)?  "_blank": "_self";
    form.setAttribute("target", targetBlank);     

    <?php foreach ($fieldNames as $name): ?>
        form.<?php echo $name?>.value = $('#<?php echo $name?>').val();  
    <?php endforeach; ?>
               
    form.<?php echo $fieldPrefix?>_tableDisplayStart.value = oTable.fnSettings()._iDisplayStart;
    form.<?php echo $fieldPrefix?>_tableDisplayLength.value = oTable.fnSettings()._iDisplayLength;
    form.<?php echo $fieldPrefix?>_tableSelectedId.value = rowId;
    form.action = target;
    form.submit();
}

</script>


@endsection

@section('content')

<div id="pageTitle"><?php echo $sitePageName?></div>
<div id="pageInstructions"><?php echo $sitePageDesc?></div>
<hr class="titleSectionSep">

 <?php if (!empty($message)) echo "<div id='' class='infoMessage'>$message</div>"?>
<div id='datatableMessage' class='infoMessage' style="display: none"></div>

<form name='hiddenCriteriaForm' method='post' style='display:none;'> 
 {{ csrf_field() }}
<?php foreach ($fieldNames as $name): ?>
    <input type='hidden' name='<?php echo $name?>' />
<?php endforeach; ?>
    
<input type='hidden' name='<?php echo $fieldPrefix?>_tableDisplayStart' />
<input type='hidden' name='<?php echo $fieldPrefix?>_tableDisplayLength' />
<input type='hidden' name='<?php echo $fieldPrefix?>_tableSelectedId' />
</form>


<div style='height: 10px; font-size: 0px'></div>


                              
<table id='resultListTable' cellspacing='0' cellpadding='0' class='display'>
    <thead>
        <tr>
    
            <th  width='25'>&nbsp;</th>                
            <th  width='300'>ชื่อรายการ</th> 
            <th  width='300'>คู่มือ</th>        
        </tr>      
    </thead>
    <tbody>
    </tbody>    
</table>

<div class='footerBtnCont'>
   {!! SiteHelper::footerBtn('userManual/edit', ' value="แก้ไข" id="editBtn" '); !!}

   <div style='clear: both'></div>
</div>


@endsection



