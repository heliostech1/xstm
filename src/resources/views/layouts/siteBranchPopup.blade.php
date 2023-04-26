
@section('siteBranchPopupJs')
        
    $("#siteBranchPopup").dialog({
        width:700,
        height:440,
        modal: true,
        autoOpen: false,
        buttons: { 
            "OK": function() {
                 siteBranchPopup_submit();
            },                 
            "Cancel": function() {
                $("#siteBranchPopup").dialog('close');
            }
         }      
    });
    
     
    window.siteBranchPopup_submit = function() {
        
         var dataId = $("#siteBranchPopup_dataId").val();
         if (AppUtil.isEmpty(dataId)) {
             alert("Please select item.");
             return false;
         }
         
         siteBranchPopup_doLoad(dataId);

        $("#siteBranchPopup").dialog('close');
    }
    
    siteBranchPopup_choose = function(divEl, dataId) {
       $('.selectionLogoCont').removeClass("selected");
       $(divEl).addClass("selected");
       
       $('#siteBranchPopup_dataId').val(dataId);
    };
    
    siteBranchPopup_doLoad = function(dataId) {
        var currentUrl = window.location.href;
          
         window.location.href = "{{ URL::asset('selectBranch') }}" 
              + "?data_id=" + dataId + "&redirect=" +  encodeURIComponent(currentUrl);
         
    };
    
    window.siteBranchPopup_openPopup = function() {    
        var currentId = "<?=$siteBranchId?>";
    
        $("#siteBranchPopup").dialog('open');     
         
        $('.selectionLogoCont').each(function() {
	       if ($(this).attr("dataId") == currentId) {
	           siteBranchPopup_choose(this, currentId);
	       }
	    }); 
       
    }
    
     $('.selectionLogoCont').dblclick(function() { 
          siteBranchPopup_submit();
     }); 
     
    <?php if (!empty($showBranchPopup) &&  $showBranchPopup === true):?>
       siteBranchPopup_openPopup();
    <?php endif;?>
     
@endsection

@section('siteBranchPopupHtml')


<div id="siteBranchPopup" style='padding:2px 0px; display:none;' title="เลือกสาขา" >

<input type='hidden' id="siteBranchPopup_dataId" />

<div style='padding:40px'>

<?php foreach ($siteBranchDatas as $siteData):?>
   <div class='selectionLogoCont'  onclick="siteBranchPopup_choose(this, '<?=$siteData['branchId']?>');" 
    dataId = "<?=$siteData['branchId']?>">
      
       <div class='selectionLogo' style='text-align:center'>
        <div style="width:100%; font-size:27px; text-align:center; font-weight:bold;"><?=$siteData['name']?></div>
       </div>

   </div>
<?php endforeach;?>

</div>

</div>

@endsection
