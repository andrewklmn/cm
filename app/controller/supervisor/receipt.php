<?php

/*
 * Reconciliation Controller
 */

        if (!isset($c)) exit;
        
        include_once 'app/model/reconciliation/get_reconciliation_by_id.php';
        

        if (!isset($_GET['id'])) {
            //Переход на стартовую страницу
            header("Location: index.php");
            exit;
        } else {
            
            $rec=get_reconciliation_by_id($_GET['id']);
            if ($rec['PrepOperatorId'] > 0 ) {
                $data['title'] = $_SESSION[$program]['lang']['recon_report_header'];
                include './app/view/html_header.php';
                include './app/view/page_header_with_logout.php';
                include './app/view/html_head_for_report.php';            


                $b=  explode('|', $_SESSION[$program]['lang']['recon_report_buttons']);
                $l=  explode('|', $_SESSION[$program]['lang']['receipt_labels']);

            ?>
                    <div class="container" style="padding-left: 50px;width:640px;">
                        <table id="receipt" width='100%'>
                            <tbody>
                                <tr>
                                    <th colspan="2">
                                        <img style="height: 60px;" src="index.php?c=barcode128&code=<?php     
                                               echo urlencode('CM-'.$_SESSION[$program]['SystemConfiguration']['CashCenterCode']
                                                    .'-'.str_pad(htmlfix($_GET['id']),6,'0', STR_PAD_LEFT));
                                            ?>">
                                        <br/>
                                        <br/>
                                        <?php echo htmlfix($l[0]); ?>
                                        <br/>
                                        <br/>
                                    </th>
                                </tr>
                                <tr>
                                    <th align="left" style="vertical-align: top;width: 150px;">
                                        <?php echo htmlfix($l[1]); ?>:
                                    </th>
                                    <td>
                                        <?php echo htmlfix($_SESSION[$program]['SystemConfiguration']['CashCenterName']); ?>
                                    </td>
                                </tr>
                                <?php 

                                    if ((int)$rec['CustomerId']>0) {

                                        $row = fetch_assoc_row_from_sql(' 
                                            SELECT
                                                *
                                            FROM 
                                                `Customers`
                                            WHERE
                                                CustomerId="'.$rec['CustomerId'].'"
                                        ;');

                                        ?>
                                            <tr>
                                                <th align="left" style="vertical-align: top;width: 150px;">
                                                    <?php echo htmlfix($l[2]); ?>:
                                                </th>
                                                <td>
                                                    <?php echo htmlfix($row['CustomerCode'].', '.$row['CustomerName']); ?>
                                                </td>
                                            </tr>
                                        <?php
                                    };
                                ?>
                                <tr>
                                        <th align="left">
                                                <?php echo htmlfix($l[3]); ?>:
                                        </th>
                                        <td>
                                                <?php echo htmlfix($rec['RecCreateDatetime']); ?>
                                        </td>
                                </tr>
                                <tr>
                                        <th align="left">
                                                <?php echo htmlfix($l[4]); ?>:
                                        </th>
                                        <td>
                                            <?php 
                                                echo $_SESSION[$program]['user_post']
                                                        .': '.$_SESSION[$program]['user_fio'];
                                            ?>
                                        </td>
                                </tr>
                                <tr>
                                        <th align="left">
                                                <?php echo htmlfix($l[5]); ?>:
                                        </th>
                                        <td>
                                                ___________________
                                        </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container no-print navbar navbar-fixed-bottom" 
                     style="background-color: white; padding: 20px;">
                        <button onclick="window.close();" class="btn btn-primary btn-large">
                            <?php echo htmlfix($b[0]); ?>
                        </button>
                    </div>
                <?php                
            } else {
                //Переход на стартовую страницу
                header("Location: index.php");
                exit;                
            };
        };
?>