<?php

/*
 * Javascript function for set and remove wait image
 * 
 */
?>
<style>
    div#status {
            position: fixed;
            left:10px;
            top:10px;
            z-index: 1031;
    }
</style>
<script>
    function set_wait() {
            $("body").append("<div id='status'><img src='css/img/ajax.gif'></div>");
    };

    function remove_wait(){	
            setTimeout('$("div#status").remove();',300);
    };
    
    function sleep(mSec)
    {
       var
         start=new Date(),
         stop,
         between;

       start=start.getTime();
       while(1)
       {
          stop=new Date();
          between=stop.getTime()-start;
          if(between>mSec)
            break;
       }
    };
</script>