


<script type="text/php">
    if ( isset($pdf) ) {
        $x = 550;
        $y = 10;
        $text = "{PAGE_NUM}/{PAGE_COUNT}";
        $font = $fontMetrics->get_font("helvetica");
        $size = 9;

        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
