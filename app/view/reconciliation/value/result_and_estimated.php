<?php

/*
 * result and estimated data
 * 
 */
        if (!isset($c)) exit;
        
        $s = explode('|',$_SESSION[$program]['lang']['sum_and_total']);
        
        
        // Получаем текущее значение ожидаемой суммы
        $row = fetch_row_from_sql('
            SELECT
                `DepositCurrencyTotal`.`ExpectedDepositValue`
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($DepositRecId).'"
                AND `DepositCurrencyTotal`.`CurrencyId`="'.addslashes($currency[0]).'"
        ;');
        
        
        if(isset($row[0])) {
            $expected = $row[0];    
        } else {
            $expected = 0;
        };
?>
    <td style="<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1
                            AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3)?'display:none;':''; ?>font-size: 12px;font-weight: bold;color:gray;">
        <?php echo htmlfix($s[0]); ?>: <span class="total">0.00</span> <?php echo htmlfix($currency[1]); ?>
    </td>
    <td style="font-size: 12px;font-weight: bold;color:gray;">
        <?php echo htmlfix($s[0]); ?>: <span class="total">0.00</span> <?php echo htmlfix($currency[1]); ?>
    </td>
    <td style="<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1
                            AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3)?'display:none;':''; ?>font-size: 12px;font-weight: bold;color:gray;">
        <?php echo htmlfix($s[1]); ?>: <span class="total">0.00</span> <?php echo htmlfix($currency[1]); ?>
    </td>
</tr>
<tr>
    <th style="text-align: left; color: darkblue; padding-top: 10px;" colspan="<?php 
        if ($_SESSION[$program]['UserConfiguration']['Blind']==1
                AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3) {
            echo 2;
        } else {
            echo 3;
        };        
        ?>">
        Ожидалась сумма: 
        <input type="text" 
               id="estimated"
               style="
                    background-color: #D0F0FF;
                    color:darkblue;
                    font-weight: bold;
                    text-align: right;
                    border-color: darkblue;
                "
               onblur="blur_estimated(this);"
               onclick="$(this).css('color','darkblue');"
               class="span3 search-query" name="estimated" 
               oldvalue="<?php echo $expected; ?>" 
               value="<?php echo $expected; ?>"/>
        <?php echo htmlfix($currency[1]); ?>
    </th>