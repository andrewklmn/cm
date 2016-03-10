<?php

/*
 * Возвращает список классов в виде массива по коду сценария
 */
    function get_all_grade_ids_by_scenario_id($scenario_id) {
        global $db;
        global $program;

        $recon = get_scenario_recon_grades($scenario_id);
        $sorter = get_scenario_sorter_grades($scenario_id);
        
        foreach ($recon as $value) {
            $add = true;
            foreach ($sorter as $val) {
                if ($val == $value) $add = false;
            };
            if ($add == true) {
                $sorter[] = $value;
            };
        };
        $a = array();
        foreach ($sorter as $value) {
            $a[] = $value[0];
        };
        return $a;
        
    };
?>
