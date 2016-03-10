<?php

    function e($text) {
        $var = 'x'.rand(10000000, 90000000).'y'.rand(10000000, 90000000);
        $var1 = 'x'.rand(10000000, 90000000).'y'.rand(10000000, 90000000);
        $base64_decode = 'var _0x11d7=[\'\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7A\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x2B\x2F\x3D\',\'\',\'\x63\x68\x61\x72\x41\x74\',\'\x69\x6E\x64\x65\x78\x4F\x66\',\'\x66\x72\x6F\x6D\x43\x68\x61\x72\x43\x6F\x64\x65\',\'\x6C\x65\x6E\x67\x74\x68\'];function base64_decode(data){var b64=_0x11d7[0];var o1,o2,o3,h1,h2,h3,h4,bits,i=0,enc=_0x11d7[1];do{h1=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h2=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h3=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h4=b64[_0x11d7[3]](data[_0x11d7[2]](i++));bits=h1<<18|h2<<12|h3<<6|h4;o1=bits>>16&0xff;o2=bits>>8&0xff;o3=bits&0xff;if(h3==64){enc+=String[_0x11d7[4]](o1);} else {if(h4==64){enc+=String[_0x11d7[4]](o1,o2);} else {enc+=String[_0x11d7[4]](o1,o2,o3);} ;} ;} while(i<data[_0x11d7[5]]);;return enc;} ;';
        $decoder = $var.'=\''.base64_encode(urlencode(escape('
            function '.$var1.'(t) {
                return unescape(decodeURI(base64_decode(base64_decode(t))));
            };
        '))).'\';eval(unescape(decodeURI(base64_decode('.$var.'))));';
        $text = $base64_decode.$decoder.' eval('.$var1.'("'.base64_encode(base64_encode(urlencode(escape($text)))).'"));';
        return $text;
    };
    
    function d($text) {
        $t = explode('"', $text);
        $text = unescape(urldecode(base64_decode(base64_decode($t[1]))));
        return $text;
    };
    
    function unescape($str)
    {
        $str = explode('%u', $str);
        $out = '';
        for ($i = 0; $i < count($str); $i++) 
        {
            $out .= pack('H*', $str[$i]);
        }
        $out = mb_convert_encoding($out, 'UTF-8', 'UTF-16');
        return $out;
    }
    
    function escape($str)
    {
        $str = mb_convert_encoding($str, 'UTF-16', 'UTF-8');
        $out = '';
        for ($i = 0; $i < mb_strlen($str, 'UTF-16'); $i++) 
        {
            $out .= '%u'.bin2hex(mb_substr($str, $i, 1, 'UTF-16'));
        }
        return $out;
    } 
?>
