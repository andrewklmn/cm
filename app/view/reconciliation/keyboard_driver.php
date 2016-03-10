<?php

/*
 * Recon keyboard driver
 */

?>
<script>
    $(document).keyup(function(event){
        switch (event.keyCode) {
            // F2 key initiate reconciliation process
            case 113:
            case 106:
                $(event.target).blur();
                $('input#finish').click();
                break;
            default:
                //alert(event.keyCode);
                break;
        };
    });
</script>