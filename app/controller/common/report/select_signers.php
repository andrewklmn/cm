<?php

/*
 * Форма выбора подписантов
 */

    if (!isset($c)) exit;
    
    // Получаем список всех подписантов из базы
    $signers = get_assoc_array_from_sql('
        SELECT
            `ExternalUsers`.`ExternalUserId`,
            `ExternalUsers`.`ExternalUserPost`,
            `ExternalUsers`.`ExternalUserName`
        FROM 
            `cashmaster`.`ExternalUsers`
        ORDER BY `ExternalUsers`.`ExternalUserName` ASC
    ;');

    //echo '<pre>';
    //print_r($_POST);
    //echo '</pre>';
    
    
    ?>
    <div class="container">
        <h4><?php echo $_SESSION[$program]['lang']['select_signers']; ?>:</h4>
        <form method="POST">
            <select style="width:400px;height:200px;" name="signers[]" multiple="multiple">                
                <?php
                    foreach ($signers as $value) {
                        ?>
                            <option value="<?php echo $value['ExternalUserId']; ?>">
                                <?php echo $value['ExternalUserName'],' - ',$value['ExternalUserPost']; ?>
                            </option>
                        <?php
                    };
                ?>     
            </select> <span style="font-size:14pt; color: red;">*</span>
            <br/>
            <?php echo htmlfix($_SESSION[$program]['lang']['for_select_more_than_one_signers']); ?>
            <hr/>
            <a class="btn btn-primary btn-large" href="?c=reports"><?php echo htmlfix($_SESSION[$program]['lang']['back']); ?></a>
            <input type="submit" 
                   class="btn btn-warning btn-large" 
                   name="action" 
                   value="<?php echo htmlfix($_SESSION[$program]['lang']['continue']); ?>"/>
        </form>
    </div>
        

