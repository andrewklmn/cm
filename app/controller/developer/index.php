<?php

/*
 * Главное меню Администратора
 */

        if (!isset($c)) exit;
        
        $data['title'] = "Developer's Workspace";
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/draw_table.php';
        
?>
        <div class="container">
            <h3>Select action:</h3>
            <table>
                <tr>
                    <td style="vertical-align: top;padding-right: 40px;">
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=sql">Raw SQL Wizard</a>
                        - direct access to Cashmaster tables by SQL commands
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=simulator">Sorter Simulation</a>
                        - sorter simulation and education page
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=errors">Collect Errors</a>
                        - page with list of collection error with deletion possibility
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=scenario_config">Scenario Configuration</a>
                        - scenario configuration page
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=obfuscator">PHP Obfuscator</a>
                        - php script code obfuscator/deobfuscator
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=jobfuscator">JS Obfuscator</a>
                        - javascript code obfuscator/deobfuscator
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=language">Translator</a>
                        - User interface translation tool
                        <br/>
                    </td>
                    <td style="vertical-align: top;">
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=set_unset_od">Operation Date</a>
                        - Set/Unset operation status 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=upload_report">Upload Report</a>
                        - Upload report file 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=files">Filemanager</a>
                        - Filemanager 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=refill_integrity">Integrity check</a>
                        - Refill integrity check table 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=restore">Restore database</a>
                        - Restore database data from archive 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' class="btn btn-primary btn-medium" href="?c=update">Manage APP zip</a>
                        - Upload/Download app Cashmaster ZIP archive 
                        <br/>
                        <a style='margin-bottom: 7px;width:150px;' 
                           onclick="$('body').html('Base Reset in progress... please, wait.<br/>');"
                           class="btn btn-danger btn-medium" href="?c=reset_db">Reset Database</a>
                        - Reset DATABASE
                        <br/>
                    </td>
                </tr>
            </table>
        </div>    
    </body>
</html>
