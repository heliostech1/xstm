/**
 * This pagination plug-in provides a `dt-tag select` menu with the list of the page
 * numbers that are available for viewing.
 *
 *  @name Select list
 *  @summary Show a `dt-tag select` list of pages the user can pick from.
 *  @author _jneilliii_
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable( {
 *            "sPaginationType": "listbox"
 *        } );
 *    } );
 */
    
$.fn.dataTableExt.oPagination.listbox = {
    /*
     * Function: oPagination.listbox.fnInit
     * Purpose:  Initalise dom elements required for pagination with listbox input
     * Returns:  -
     * Inputs:   object:oSettings - dataTables settings object
     *             node:nPaging - the DIV which contains this pagination control
     *             function:fnCallbackDraw - draw function which must be called on update
     */
    "fnInit": function (oSettings, nPaging, fnCallbackDraw) {
        var nInput = document.createElement('select');
        var nPage = document.createElement('span');
        var nOf = document.createElement('span');
        nOf.className = "paginate_of";
        nPage.className = "paginate_page";
        if (oSettings.sTableId !== '') {
            nPaging.setAttribute('id', oSettings.sTableId + '_paginate');
        }
        nInput.style.display = "inline";
        nPage.innerHTML = "";
        nPaging.appendChild(nPage);
        nPaging.appendChild(nInput);
        nPaging.appendChild(nOf);
        $(nInput).change(function (e) { // Set DataTables page property and redraw the grid on listbox change event.
            window.scroll(0,0); //scroll to top of page
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
    "fnUpdate": function (oSettings, fnCallbackDraw) {
        if (!oSettings.aanFeatures.p) {
            return;
        }
        var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
        var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1; /* Loop over each instance of the pager */
        var an = oSettings.aanFeatures.p;
        
        var pageDatas = this.pageSelector(iCurrentPage, iPages);
        var pageCount = pageDatas.length;
 
        for (var i = 0, iLen = an.length; i < iLen; i++) {
            var spans = an[i].getElementsByTagName('span');
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
            
            spans[1].innerHTML = " / " + iPages;       
            elSel.value = iCurrentPage;
        }
    },
    
    /*  \phpMyAdmin\libraries\Util.class.php 
     * 
     * Generate a pagination selector for browsing resultsets
     *
     * @param int    $pageNow     current page number
     * @param int    $nbTotalPage number of total pages
     * @param int    $showAll     If the number of pages is lower than this
     *                            variable, no pages will be omitted in pagination
     * @param int    $sliceStart  How many rows at the beginning should always
     *                            be shown?
     * @param int    $sliceEnd    How many rows at the end should always be shown?
     * @param int    $percent     Percentage of calculation page offsets to hop to a
     *                            next page
     * @param int    $range       Near the current page, how many pages should
     *                            be considered "nearby" and displayed as well?
     *
     */
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

        // Based on the number of results we add the specified
        // $percent percentage to each page number,
        // so that we have a representing page number every now and then to
        // immediately jump to specific pages.
        // As soon as we get near our currently chosen page ($pageNow -
        // $range), every page number will be shown.
        $i = $sliceStart;
        $x = $nbTotalPage - $sliceEnd;
        $met_boundary = false;

        while ($i <= $x) {
            if ($i >= $pageNowMinusRange && $i <= $pageNowPlusRange) {
                // If our pageselector comes near the current page, we use 1
                // counter increments
                $i++;
                $met_boundary = true;
            } else {
                // We add the percentage increment to our current page to
                // hop to the next one in range
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

        /*
        Add page numbers with "geometrically increasing" distances.

        This helps me a lot when navigating through giant tables.

        Test case: table with 2.28 million sets, 76190 pages. Page of interest
        is between 72376 and 76190.
        Selecting page 72376.
        Now, old version enumerated only +/- 10 pages around 72376 and the
        percentage increment produced steps of about 3000.

        The following code adds page numbers +/- 2,4,8,16,32,64,128,256 etc.
        around the current page.
        */
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

        // Since because of ellipsing of the current page some numbers may be
        // double, we unify our array:
        $pages.sort( function numOrdA(a, b){ return (a-b); } );       
        //console.log($pages);
        $pages = this.arrayUnique($pages);
        //console.log($pages);
        return $pages;
    }, // end funct
    
    "arrayUnique" : function(a) {
        return a.reduce(function(p, c) {
            if (p.indexOf(c) < 0) p.push(c);
            return p;
        }, []);
    }

};



