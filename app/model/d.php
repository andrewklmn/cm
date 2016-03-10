<?php


    function e($text) {
        $var = 'x'.rand(10000000, 90000000).'y'.rand(10000000, 90000000);
        $var1 = 'x'.rand(10000000, 90000000).'y'.rand(10000000, 90000000);
        $decoder = '$'.$var.'="'.base64_encode(base64_encode('
            function '.$var.'($text) {
                    return(/*" IHdoaWxlICh4PnkpIHsgeD15KjIzMTMvMTg7IGlmKG9iZnVzY2F0ZSh5KT09J3l0NiZeVCY2ZyomRypZdScpe3JldHVybiB4O319"*/strrev(/*uyweicmpewcvnouqwejpc]]rwv67t283fhci87e2*/base64_decode(
                    base64_decode(/*iuiyh543i53i6uyi367noi7j4i67ji7jo4unh67o#$%^&*(*/strrev($text)))));
                    /*"aWYgKCFpc3NldFsnYXV0 aCddKSkgewogICAgICAgICAgICAvLyBDaGVjayBhdXRob3JpemF0aW9uCiAgICAgICAgICAgICAgICBpbmNsdWRlICdhcHAvdmlldy9sb2dpbi5waHAnOwogICAgICAgICAgICAgICAgLy9wcmludF9yKCQpOwogICAgICAgICAgICAgICAgZXhpdDsKICAgICAgICAgICAgfTsKICAgICAgICB9IGVsc2UgewogICAgICAgICAgICAvLyBHbyB0byBsb 2dpbiBwYWdlCiAgICAgICAgICAgIGluY2x1ZGUgJ2FwcC92aWV3L2xvZ2luLnBocCc7CiAgICAgICAgICAgIGV4aXQ7CiAgICAgICAgfTs"*/
            };
        ')).'";eval(/*"aWYgKCFpc3NldFsnYXV0aC'.rand(1000000,9000000).'vLyBDaGVjay BhdXRob3JpemF0aW9uCiAgICAgICAgICAgICAgICBpbmNsdWRlICdhcHAvdmlldy9sb2dpbi5waHAnOwogICAgICAgICAgICAgICAgLy9wcmludF9yKCQpOwogICAgICAgICAgICAgICAg ZXhpdDsKICAgICAgICAgICAgfTsKICAgICAgICB9IGVsc2UgewogICAgICAgICAgICAvL yBHbyB0byBsb2dpbiBwYWdlCiAgICAgICAgICAgIICAgfTs"*/base64_decode(/*" IHdoaWxlICh4PnkpIHsgeD15KjIzMTMvMT g7IGlmKG9iZnVzY2F0ZSh5KT09J3l0NiZeVCY2ZyomRy pZdScpe3JldHVybiB4O319"*/base64_decode($'.$var.')));';
        preg_match_all('#<\?php(.+?)\?>#is', $text, $matches);
        foreach ($matches[1] as $key=>$value){
            $text = str_replace($value, ' $x'.$var1.'="'
                .strrev(base64_encode(base64_encode(strrev($value)))).
                    '";eval(/*"aWYgKCFpc3NldFs nYXV0aC'.rand(1000000,9000000).'vL yBDaGVjay BhdXRob3JpemF0aW9uCiAgICAgICAgICAgICAgICBpbmNsdWRlICdhcHAvdmlldy9sb2dpbi5waHAnOwogICAgICAgICAgICA gICAgLy9wcmludF9yKCQpOwogICAgICAgICAgICAgICAgZXhpdDsKICAgICAgICAgICAgfTsKICAgICAgICB9IGVsc2UgewogICAgICAgICAgICAvLyBHbyB0byBsb2dpbiBwYWdlCiAgICAgICAgICAgIICAgfTs"*/'.$var.'($x'.$var1.'));', $text);
        };
        return '<?php '.$decoder.'?>'.$text;
    };
    
    function d($text) {
        preg_match_all('#<\?php(.+?)\?>#is', $text, $matches);
        foreach ($matches[1] as $key=>$value){
            if ($key>0) {
                $t = explode('"', $value);
                $text = str_replace($value, ' '
                        .strrev(base64_decode(base64_decode(strrev($t[1])))), $text);
            } else {
                $text = str_replace($value,'', $text);
            };
        };
        return str_replace('<?php?>', '', $text);
    };
    
?>
