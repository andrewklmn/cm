<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_grade(elem){
            window.location.replace('?c=grade_edit&id=' + elem.id);            
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `Grades`.`GradeId`,
            `Grades`.`GradeName`,
            `Grades`.`GradeLabel`
        FROM 
            `cashmaster`.`Grades`
        ORDER BY 
            `Grades`.`GradeId` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['denoms_grades_header']);
    $table['width'] = array( 60,150,250);
    $table['align'] = explode('|','center|left|left');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_grade(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>