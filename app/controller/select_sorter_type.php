<?php

/*
 * Simulated Sorter Type Selector
 */

        if (!isset($c)) exit;
        
        if(isset($_POST['action']) AND $_POST['action']=='change_sorter') {
            if (isset($_POST['new_default']) 
                    AND $_POST['new_default']!=''
                    AND isset($_POST['new_cashroom']) 
                    AND $_POST['new_cashroom']!='') {
                // установка нового сортера по умолчанию
                $_SESSION[$program]['simulated_sorter'] = $_POST['new_default'];
                $_SESSION[$program]['simulated_cashroom'] = $_POST['new_cashroom'];
                
                $sql = '
                    SELECT
                        MachineDBId
                    FROM
                        Machines
                    WHERE
                        SorterName="Simulator"
                ;';
                $id = fetch_row_from_sql($sql);
                
                do_sql('
                    UPDATE 
                        `cashmaster`.`Machines`
                    SET
                        `CashRoomId` = "'.  addslashes($_POST['new_cashroom']).'"
                    WHERE 
                        `MachineDBId` = "'.  addslashes($id[0]).'"
                ;');
                
            } else {
                // страница выбора сортера 
                ?>
                    <div class="container">
                        <div class="alert alert-info">  
                          <form method="POST" style="padding:0px;margin:0px;">
                          <strong>Select Sorter:</strong>
                              &nbsp;&nbsp;&nbsp;
                              <select style="width:200px;margin: 0px;" name="new_default">
                                  <?php 
                                      $sql = '
                                            SELECT
                                                SorterTypeId,
                                                SorterType
                                            FROM
                                                SorterTypes
                                            ORDER BY SorterType ASC
                                      ;';
                                      $sorters = get_array_from_sql($sql);
                                      foreach ($sorters as $key => $value) {
                                          if ($value[0]==$_SESSION[$program]['simulated_sorter']) {
                                              echo '<option selected value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          } else {
                                              echo '<option value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          };
                                      };
                                  ?>
                              </select>
                              &nbsp;&nbsp;&nbsp;
                              <strong>Select cash room:</strong>
                              <select style="width:200px;margin: 0px;" name="new_cashroom">
                                  <?php 
                                      $sql = '
                                            SELECT
                                                *
                                            FROM
                                                CashRooms
                                            ORDER BY CashRoomName ASC
                                      ;';
                                      $rooms = get_array_from_sql($sql);
                                      foreach ($rooms as $key => $value) {
                                          if ($value[0]==$_SESSION[$program]['simulated_cashroom']) {
                                              echo '<option selected value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          } else {
                                              echo '<option value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          }
                                      };
                                  ?>
                              </select>
                              &nbsp;&nbsp;&nbsp;
                              <input type="hidden" name="action" value="change_sorter"/>
                              <button type="submit" class="btn btn-small btn-primary">
                                  Change Sorter
                              </button>
                          </form>
                        </div> 
                    </div>
                <?php
                exit;
            };
        };
        
        if (!isset($_SESSION[$program]['simulated_sorter'])) {
            $sql = '
                SELECT
                    SorterTypeId
                FROM
                    Machines
                WHERE
                    SorterName="Simulator"
            ;';
            $id = fetch_row_from_sql($sql);
            $_SESSION[$program]['simulated_sorter'] = $id[0];
        };
        // просто инфо с кнопкой смены сортера
        

?>
            <div class="container">
                <div class="alert alert-info">  
                  <form method="POST" style="padding:0px;margin:0px;">
                  <strong>Simulated Sorter Type:</strong>
                  <?php 
                      $sql = '
                            SELECT
                                SorterTypeId,
                                SorterType
                            FROM
                                SorterTypes
                            WHERE
                                SorterTypeId = "'.$_SESSION[$program]['simulated_sorter'].'"
                      ;';
                      $row = fetch_row_from_sql($sql);    
                      $_SESSION[$program]['simulated_sorter_type'] = $row[1];
                      
                      $sorter = fetch_assoc_row_from_sql('
                            SELECT
                                *
                            FROM
                                Machines
                            LEFT JOIN
                                CashRooms ON CashRooms.Id = Machines.CashRoomId
                            WHERE
                                SorterName="Simulator"
                      ;');
                      
                  ?>
                      &nbsp;&nbsp;&nbsp;<font size="5" style="color:red;"><?php echo htmlfix($row[1]); ?></font>&nbsp;&nbsp;&nbsp;
                      <input type="hidden" name="action" value="change_sorter"/>
                      &nbsp;&nbsp;&nbsp;
                      <strong>Cash room: </strong><?php echo htmlfix($sorter['CashRoomName']); ?>
                      &nbsp;&nbsp;&nbsp;
                      <button type="submit" class="btn btn-small btn-info">
                          Change Sorter Type
                      </button>
                  </form>
                </div> 
            </div>