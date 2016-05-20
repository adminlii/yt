<?php
class Ec_CookieCode {

    public static function md_cookie ($temp_cookie, $md = 0) {
        $hx_arr = array(
                'a' => 'C',
                'b' => 'A',
                'c' => '6',
                'd' => 'B',
                'e' => '9',
                'f' => 'W',
                'g' => 'f',
                'h' => '5',
                'i' => 'I',
                'j' => 'T',
                'k' => 'F',
                'l' => 'z',
                'm' => 'E',
                'n' => 'm',
                'o' => 'g',
                'p' => 's',
                'q' => 'i',
                'r' => 'L',
                's' => '2',
                't' => 'w',
                'u' => 'q',
                'v' => 'U',
                'w' => 'a',
                'x' => 'R',
                'y' => 'd',
                'z' => 'M',
                'A' => 'J',
                'B' => 'O',
                'C' => 'y',
                'D' => '4',
                'E' => 'e',
                'F' => 'D',
                'G' => 'H',
                'H' => 'n',
                'I' => 'K',
                'J' => 'p',
                'K' => '0',
                'L' => 'V',
                'M' => '8',
                'N' => 'S',
                'O' => 'u',
                'P' => 'j',
                'Q' => 'k',
                'R' => 'o',
                'S' => 'h',
                'T' => 'l',
                'U' => '7',
                'V' => '1',
                'W' => 'N',
                'X' => '3',
                'Y' => 'Q',
                'Z' => 'c',
                '0' => 't',
                '1' => 'v',
                '2' => 'P',
                '3' => 'r',
                '4' => 'Y',
                '5' => 'X',
                '6' => 'G',
                '7' => 'b',
                '8' => 'x',
                '9' => 'Z',
                '=' => '@'
        );
        $t_arr_v = '';
        if ($md == 0) {
            $t_cookie = base64_encode($temp_cookie);
            $t_cookie_len = strlen($t_cookie);
            for ($i = 0; $i < $t_cookie_len; $i ++) {
                $t_arr_k = substr($t_cookie, $i, 1);
                $hx_arr[$t_arr_k] == '' && $hx_arr[$t_arr_k] = $t_arr_k;
                $t_arr_v .= $hx_arr[$t_arr_k];
            }
        } elseif ($md == 1) {
            $t_cookie_len = strlen($temp_cookie);
            for ($i = 0; $i < $t_cookie_len; $i ++) {
                $t_arr_k = substr($temp_cookie, $i, 1);
                $s_k = array_search($t_arr_k, $hx_arr);
                $s_k == '' && $s_k = $t_arr_k;
                $t_arr_v .= $s_k;
            }
            $t_arr_v = base64_decode($t_arr_v);
        } else {
            return false;
        }
        return $t_arr_v;
    }
    
    public static function test(){

        // 测试部分
        $a = 'aaadd安世高dss稍等s123';
        echo $a . '<br>';
        $b = Ec_CookieCode::md_cookie($a);
        echo $b . '<br>';
        $c = Ec_CookieCode::md_cookie($b, 1);
        echo $c . '<br>';
    }
} // endclass
  