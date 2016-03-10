<?php

/*
 * Добавляет файл XML для репортсета
 */

    function add_xml_file_to_reportset( $text, $filename ) {
        
        // $text - собственно текст самого файла для сохранения
        // $name - имя файла для сохранения
        // $date - дата файла для сохранения
        global 
            $ticket_xml_files, 
            $report_datetime,
            $xml_archive_directory,
            $xml_output_directory,
            $room;
        
        // создаем папку с кассой физически
        if (!file_exists($xml_archive_directory.'/'.$room)) {
            mkdir( $xml_archive_directory.'/'.$room, 0777);
            chmod( $xml_archive_directory.'/'.$room, 0777);
        };
        
        // создаем папку с датой физически
        if (!file_exists($xml_archive_directory.'/'.$room.'/'.$report_datetime)) {
            mkdir( $xml_archive_directory.'/'.$room.'/'.$report_datetime, 0777);
            chmod( $xml_archive_directory.'/'.$room.'/'.$report_datetime, 0777);
        };
        
        $fp = fopen( $xml_archive_directory.'/'.$room.'/'.$report_datetime.'/'.$filename, 'w');
        chmod( $xml_archive_directory.'/'.$room.'/'.$report_datetime.'/'.$filename, 0777);
        fwrite($fp, $text);
        fclose($fp);
        
        // 2. сохраяем его свойства в массив свойств для квитанции
        $ticket_xml_files[] = array(
            'FileName'=>$filename,
            'CreateTime' => date(
                                'Y-m-d\TH:i:s',
                                filectime($xml_archive_directory.'/'.$room.'/'.$report_datetime.'/'.$filename)),
            'FileSize' => filesize($xml_archive_directory.'/'.$room.'/'.$report_datetime.'/'.$filename)
        );
    };

?>