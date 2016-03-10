<?php

if (!isset($c)) exit;

echo '<pre>';
$text ='
    <?xml version="1.0" encoding="UTF-8"?>
    <CONFIRM xmlns="urn:cbr-ru:csm:v1.0" FilesCount="3">
            <FileTransfers FileName="RECRESULT_2009-12-02T14-22-38_0015.xml" CreateTime="2009-12-02T14:22:38" FileSize="3199"/>
            <FileTransfers FileName="AKT0402145_2009-12-02T14-22-40_0002.xml" CreateTime="2009-12-02T14:22:40" FileSize="583"/>
            <FileTransfers FileName="AKT0402198_2009-12-02T15-17-41_0025.xml" CreateTime="2009-12-02T15:17:41" FileSize="907"/>
    </CONFIRM>            
';
echo htmlfix($text);
echo '</pre>';

?>