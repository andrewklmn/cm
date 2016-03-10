<?php

/*
 * Отображение конверта для апдейта
 */

    if (!isset($c)) exit;
    

    if (isset($_POST['action']) AND $_POST['action']=='Clear envelope script files' ) {
        unset($_SESSION[$program]['envelope']['php']);
    };
    
    if (isset($_POST['action']) AND $_POST['action']=='Clear envelope sql texts' ) {
        unset($_SESSION[$program]['envelope']['sql']);
    };
    
    if (isset($_POST['action']) AND $_POST['action']=='Add to update envelope') {
        $files = explode('|', $_POST['files']);
        foreach ($files as $value) {
            if ($_POST['directory']=='') {
                if (substr($value, count($value)-5)=='.php'
                        OR substr($value, count($value)-4)=='.js'
                        OR substr($value, count($value)-5)=='.css') {
                    $_SESSION[$program]['envelope']['php'][] = $value;
                } else {
                    $_SESSION[$program]['envelope']['sql'][] = $value;
                };
            } else {
                if (substr($value, count($value)-5)=='.php'
                        OR substr($value, count($value)-4)=='.js'
                        OR substr($value, count($value)-5)=='.css') {
                    $_SESSION[$program]['envelope']['php'][] = substr($_POST['directory'],1).'/'.$value;
                } else {
                    $_SESSION[$program]['envelope']['sql'][] = substr($_POST['directory'],1).'/'.$value;
                };
            };
        };
        
        if (isset($_SESSION[$program]['envelope']['php'])) {
            $_SESSION[$program]['envelope']['php'] = array_unique($_SESSION[$program]['envelope']['php']);
            sort( $_SESSION[$program]['envelope']['php'] );
        };
        if (isset($_SESSION[$program]['envelope']['sql'])) {
            $_SESSION[$program]['envelope']['sql'] = array_unique($_SESSION[$program]['envelope']['sql']);
            sort( $_SESSION[$program]['envelope']['sql'] );
        };
        
    };
    

    if (isset($_SESSION[$program]['envelope']['php']) OR isset($_SESSION[$program]['envelope']['sql'])) {
        ?>
            <div class="container">
                <div class="alert alert-danger">
                    <h3>Envelope</h3>
                    <form method="POST">
                        <h4 style="padding-bottom:10px;">Script files for update:</h4>
                        <?php
                            echo '<pre style="color:darkgreen;">';
                            if (isset($_SESSION[$program]['envelope']['php'])) {
                                foreach ($_SESSION[$program]['envelope']['php'] as $value) {
                                    echo $value,"\n";
                                };
                            };
                            echo '</pre>';
                        ?>
                        <br/>
                        
                        <h4 style="padding-bottom:10px;">SQL files for execute on update:</h4>
                        <?php
                            echo '<pre style="color:blue;">';
                            if (isset($_SESSION[$program]['envelope']['sql'])) {
                                foreach ($_SESSION[$program]['envelope']['sql'] as $value) {
                                    echo $value,"\n";
                                };
                            };
                            echo '</pre>';
                        ?>
                        <br/>
                        <input type="submit"
                               class="btn btn-danger btn-medium" 
                               name="action" 
                               value="Clear envelope script files"/>
                        <input type="submit"
                               class="btn btn-warning btn-medium" 
                               name="action" 
                               value="Clear envelope sql texts"/>
                        <a target="_blank" href="?c=envelope"
                            class="btn btn-primary btn-medium">Download whole envelope for update</a>
                    </form>
                </div>
            </div>
        <?php
    };

?>
