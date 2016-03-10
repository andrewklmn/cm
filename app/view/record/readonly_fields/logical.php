<?php

/*
 * logical readonly field
 */

        echo '<td 
                style="padding-left:10px;">',
                    htmlfix(($d==0)
                            ?$_SESSION[$program]['lang']['no']
                            :$_SESSION[$program]['lang']['yes']),'
              </td>';

?>
