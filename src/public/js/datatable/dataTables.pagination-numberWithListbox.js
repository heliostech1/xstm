

$.fn.dataTableExt.oPagination.full_numbers = {
        
    "fnInit": function ( oSettings, nPaging, fnCallbackDraw )
    {
        var oLang = oSettings.oLanguage.oPaginate;
        var oClasses = oSettings.oClasses;
        var fnClickHandler = function ( e ) {
            if ( oSettings.oApi._fnPageChange( oSettings, e.data.action ) )
            {
                fnCallbackDraw( oSettings );
            }
        };
        
        $(nPaging).addClass("paging_full_numbers");
    
        
        $(nPaging).append(
            '<div style="display:inline;padding-right:10px">'+
            '<a tabindex="0" class="paginate_button" >&darr;</a>'+
            '<a tabindex="0" class="paginate_button" >&uarr;</a>'+
            '</div>'
        );
        
        $(nPaging).append(
            '<div style="display:inline">'+
            '<a  tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButton+" "+oClasses.sPageFirst+'">'+oLang.sFirst+'</a>'+
            '<a  tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButton+" "+oClasses.sPagePrevious+'">'+oLang.sPrevious+'</a>'+
            '<span class="paging_full_numbers_item_container"></span>'+
            '<a tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButton+" "+oClasses.sPageNext+'">'+oLang.sNext+'</a>'+
            '<a tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButton+" "+oClasses.sPageLast+'">'+oLang.sLast+'</a>'+
            '</div>'
        );
        
        var els = $('a', nPaging);
        //var nFirst = els[0],nPrev = els[1],nNext = els[2],nLast = els[3];  // CHANGED TO ..
        var nDown = els[0],nUp = els[1],nFirst = els[2],nPrev = els[3],nNext = els[4],nLast = els[5];
        
        //oSettings.oApi._fnBindAction( nDown, {action: "down"},    fnClickHandler );// ADDED
        //oSettings.oApi._fnBindAction( nUp, {action: "up"},    fnClickHandler );// ADDED
        
        oSettings.oApi._fnBindAction( nFirst, {action: "first"},    fnClickHandler );
        oSettings.oApi._fnBindAction( nPrev,  {action: "previous"}, fnClickHandler );
        oSettings.oApi._fnBindAction( nNext,  {action: "next"},     fnClickHandler );
        oSettings.oApi._fnBindAction( nLast,  {action: "last"},     fnClickHandler );
        
        /* ID the first elements only */
        if ( !oSettings.aanFeatures.p )
        {
            nDown.id =oSettings.sTableId+'_down'; // ADDED
            nUp.id =oSettings.sTableId+'_up';// ADDED
            
            nPaging.id = oSettings.sTableId+'_paginate';
            nFirst.id =oSettings.sTableId+'_first';
            nPrev.id =oSettings.sTableId+'_previous';
            nNext.id =oSettings.sTableId+'_next';
            nLast.id =oSettings.sTableId+'_last';
        }

        
        
        this.initListbox(oSettings, nPaging, fnCallbackDraw);            
    },
    
 
    /*
     * Function: oPagination.full_numbers.fnUpdate
     * Purpose:  Update the list of page buttons shows
     * Returns:  -
     * Inputs:   object:oSettings - dataTables settings object
     *           function:fnCallbackDraw - draw function to call on page change
     */
    "fnUpdate": function ( oSettings, fnCallbackDraw )
    {
        if ( !oSettings.aanFeatures.p )
        {
            return;
        }
        
        
        this.updateListbox(oSettings, fnCallbackDraw);
        
        var iPageCount = 5; //DataTable.ext.oPagination.iFullNumbersShowPages;
        var iPageCountHalf = Math.floor(iPageCount / 2);
        var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
        var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
        var sList = "";
        var iStartButton, iEndButton, i, iLen;
        var oClasses = oSettings.oClasses;
        var anButtons, anStatic, nPaginateList, nNode;
        var an = oSettings.aanFeatures.p;
        var fnBind = function (j) {
            oSettings.oApi._fnBindAction( this, {"page": j+iStartButton-1}, function(e) {
                /* Use the information in the element to jump to the required page */
                oSettings.oApi._fnPageChange( oSettings, e.data.page );
                fnCallbackDraw( oSettings );
                e.preventDefault();
            } );
        };
        
        /* Pages calculation */
        if ( oSettings._iDisplayLength === -1 )
        {
            iStartButton = 1;
            iEndButton = 1;
            iCurrentPage = 1;
        }
        else if (iPages < iPageCount)
        {
            iStartButton = 1;
            iEndButton = iPages;
        }
        else if (iCurrentPage <= iPageCountHalf)
        {
            iStartButton = 1;
            iEndButton = iPageCount;
        }
        else if (iCurrentPage >= (iPages - iPageCountHalf))
        {
            iStartButton = iPages - iPageCount + 1;
            iEndButton = iPages;
        }
        else
        {
            iStartButton = iCurrentPage - Math.ceil(iPageCount / 2) + 1;
            iEndButton = iStartButton + iPageCount - 1;
        }

        
        /* Build the dynamic list */
        for ( i=iStartButton ; i<=iEndButton ; i++ )
        {
            sList += (iCurrentPage !== i) ?
                '<a tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButton+'">'+oSettings.fnFormatNumber(i)+'</a>' :
                '<a tabindex="'+oSettings.iTabIndex+'" class="'+oClasses.sPageButtonActive+'">'+oSettings.fnFormatNumber(i)+'</a>';
        }
        
        /* Loop over each instance of the pager */
        for ( i=0, iLen=an.length ; i<iLen ; i++ )
        {
            nNode = an[i];
            if ( !nNode.hasChildNodes() )
            {
                continue;
            }
            
            /* Build up the dynamic list first - html and listeners */
            $('span.paging_full_numbers_item_container', nNode)
                .html( sList )
                .children('a').each( fnBind );
            
            /* Update the permanent button's classes */
            anButtons = nNode.getElementsByTagName('a');
            anStatic = [
                //anButtons[0], anButtons[1], CHANGED TO
                anButtons[2], anButtons[3], // ADDED
                anButtons[anButtons.length-2], anButtons[anButtons.length-1]
            ];

            $(anStatic).removeClass( oClasses.sPageButton+" "+oClasses.sPageButtonActive+" "+oClasses.sPageButtonStaticDisabled );
            $([anStatic[0], anStatic[1]]).addClass( 
                (iCurrentPage==1) ?
                    oClasses.sPageButtonStaticDisabled :
                    oClasses.sPageButton
            );
            $([anStatic[2], anStatic[3]]).addClass(
                (iPages===0 || iCurrentPage===iPages || oSettings._iDisplayLength===-1) ?
                    oClasses.sPageButtonStaticDisabled :
                    oClasses.sPageButton
            );
        }

    },
    
    
    "initListbox": function (oSettings, nPaging, fnCallbackDraw) {
        var nContainer = $("<div style='display:inline;' class='dataTable_paginationListboxCont' >");
        var nInput = $("<select style='display:inline' tabIndex='-1' >");

        nContainer.append(nInput);
        nContainer.append(" / ");
        nContainer.append("<span class='paging_full_numbers_total_page_container' >");
        
        $(nPaging).append(nContainer);

        $(nInput).change(function (e) { 
            //window.scroll(0,0); //scroll to top of page
            
            if (this.value === "" || this.value.match(/[^0-9]/)) { /* Nothing entered or non-numeric character */
                return;
            }
            var iNewStart = oSettings._iDisplayLength * (this.value - 1);
            if (iNewStart > oSettings.fnRecordsDisplay()) { /* Display overrun */
                oSettings._iDisplayStart = (Math.ceil((oSettings.fnRecordsDisplay() - 1) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
                fnCallbackDraw(oSettings);
                return;
            }
            oSettings._iDisplayStart = iNewStart;
            fnCallbackDraw(oSettings);
        }); /* Take the brutal approach to cancelling text selection */
        
        $('span', nPaging).bind('mousedown', function () {
            return false;
        });
        
        $('span', nPaging).bind('selectstart', function () {
            return false;
        });
    },
         
    
    /*
     * Function: oPagination.listbox.fnUpdate
     * Purpose:  Update the listbox element
     * Returns:  -
     * Inputs:   object:oSettings - dataTables settings object
     *             function:fnCallbackDraw - draw function which must be called on update
     */
    "updateListbox": function (oSettings, fnCallbackDraw) {
        if (!oSettings.aanFeatures.p) {
            return;
        }
        
        var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
        var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1; /* Loop over each instance of the pager */
        var an = oSettings.aanFeatures.p;
        
        var pageDatas = this.pageSelector(iCurrentPage, iPages);
        var pageCount = pageDatas.length;
 
        for (var i = 0, iLen = an.length; i < iLen; i++) {
            
            var nNode = an[i];
            $('span.paging_full_numbers_total_page_container', nNode).html( iPages );

            var inputs = an[i].getElementsByTagName('select');
            var elSel = inputs[0];

            elSel.options.length = 0; //clear the listbox contents
            
            for (var j = 0; j < pageCount; j++) { //add the pages
                var oOption = document.createElement('option');
                var pageData = pageDatas[j];
                
                oOption.text = pageData;
                oOption.value = pageData;
                
                try {
                    elSel.add(oOption, null); // standards compliant; doesn't work in IE
                } catch (ex) {
                    elSel.add(oOption); // IE only
                }
            }
             
            elSel.value = iCurrentPage;
        }
    },
    
    "pageSelector": function($pageNow, $nbTotalPage) {
        
        var $showAll = 200, $sliceStart = 5, $sliceEnd = 5, $percent = 20, $range = 10;
        
        var $increment = Math.floor($nbTotalPage / $percent);
         
        $pageNowMinusRange = ($pageNow - $range);
        $pageNowPlusRange = ($pageNow + $range);

        var $pages = [];
        
        // CASE: SIMPLE ==========================================
        if ($nbTotalPage < $showAll) {
            for (var $i = 1; $i <= $nbTotalPage; $i++) {
                $pages.push($i);
            }
            return $pages;
        } 
            

        // CASE: COMPLEX ============================================
        // Always show first X pages
        for (var $i = 1; $i <= $sliceStart; $i++) {
            $pages.push($i);
        }

        // Always show last X pages
        for (var $i = $nbTotalPage - $sliceEnd; $i <= $nbTotalPage; $i++) {
            $pages.push($i);
        }

        $i = $sliceStart;
        $x = $nbTotalPage - $sliceEnd;
        $met_boundary = false;

        while ($i <= $x) {
            if ($i >= $pageNowMinusRange && $i <= $pageNowPlusRange) {
                $i++;
                $met_boundary = true;
            } else {
                $i += $increment;

                // Make sure that we do not cross our boundaries.
                if ($i > $pageNowMinusRange && ! $met_boundary) {
                    $i = $pageNowMinusRange;
                }
            }

            if ($i > 0 && $i <= $x) {
                $pages.push($i);
            }
        }
        
        $i = $pageNow;
        $dist = 1;
        while ($i < $x) {
            $dist = 2 * $dist;
            $i = $pageNow + $dist;
            if ($i > 0 && $i <= $x) {
                $pages.push( $i);
            }
        }

        $i = $pageNow;
        $dist = 1;
        while ($i >0) {
            $dist = 2 * $dist;
            $i = $pageNow - $dist;
            if ($i > 0 && $i <= $x) {
                $pages.push( $i);
            }
        }

        $pages.sort( function numOrdA(a, b){ return (a-b); } );       
        $pages = this.arrayUnique($pages);
        return $pages;
    }, 
    
    "arrayUnique" : function(a) {
        return a.reduce(function(p, c) {
            if (p.indexOf(c) < 0) p.push(c);
            return p;
        }, []);
    },
    


};


