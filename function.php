<?php

/**
 * js_locked_right_click()
 *
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('js_locked_right_click')) {
    function js_locked_right_click() {
        echo '
        <script>
            document.oncontextmenu = function() {
                window.event.returnValue = false;
            }
        </script>;';
    }
}

/**
 * js_alert()
 *.
 * @param String $message 訊息內容
 * @param String $url 網址位置
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('js_alert')) {
    function js_alert($message = '', $url = '') {
        echo '
        <script>
            document.onkeydown = function() {
                e = window.event;
                if (e.keyCode==116) {
                    e.keyCode = 0;
                    e.cancelBubble = true;
                    return false;
                }
            }
            alert("'.$message.'");
            location.href = "'.$url.'";
        </script>';
        exit();
    }
}

/**
 * data_encode()
 *.
 * @param String $data 內文
 * @param String $key 密鑰
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('data_encode')) {
    function data_encode($data, $key) {
        $method = 'aes-256-cbc';
        if (in_array($method, openssl_get_cipher_methods())) {
            if (empty($data) || is_null($data) || is_array($data)) {
                return '';
            } else {
                $encryption_key = base64_decode($key);
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
                $encrypted = openssl_encrypt($data, $method, $encryption_key, 0, $iv);
                return base64_encode($encrypted.'::'.$iv);
            }
        } else {
            die('不支持該加密算法!');
        }
    }
}

/**
 * data_decode()
 *.
 * @param String $data 密文
 * @param String $key 密鑰
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('data_decode')) {
    function data_decode($data, $key) {
        $method = 'aes-256-cbc';
        if (in_array($method, openssl_get_cipher_methods())) {
            if (empty($data) || is_null($data) || is_array($data)) {
                return '';
            } else {
                $data = str_replace("BJD", "+", $data);
                $data = str_replace("CAD", "-", $data);
                $data = str_replace("FAD", "/", $data);
                $encryption_key = base64_decode($key);
                list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
                return openssl_decrypt($encrypted_data, $method, $encryption_key, 0, $iv);
            }
        } else {
            die('不支持該加密算法!');
        }
    }
}

/**
 * get_token()
 *.
 * @param String $key 密鑰
 * @return Array
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('get_token')) {
    function get_token($key) {
        $randString = random_string(10);
        $token = data_encode($randString, $key);
        $token = str_replace("+","BJD", $token);
        $token = str_replace("-","CAD", $token);
        $token = str_replace("/","FAD", $token);
        return [$randString, $token];
    }
}

/**
 * check_token()
 *.
 * @param String $name session 名稱
 * @param String $key 密鑰
 * @param String $method Request 方法
 * @return Boolen
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('check_token')) {
    function check_token($name = '', $key = '', $method = 'post') {
        if (empty($name)) {
            return false;
        }

        if (!isset($_SESSION[$name])) {
            return false;
        }

        if ($method == 'post') {
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            unset($_POST['token']);
        } else {
            $token = isset($_GET['token']) ? $_GET['token'] : null;
        }

        if (is_null($token)) {
            return false;
        }

        if (data_decode($token, $key) != $_SESSION[$name]) {
            return false;
        }

        unset($_SESSION[$name]);
        return true;
    }
}

/**
 * get_user_ip()
 *.
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('get_user_ip')) {
    function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return = $_SERVER['REMOTE_ADDR'];
        }
    }
}

/**
 * random_string()
 *.
 * @param Int $length 亂數長度
 * @param String $type 類型
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('random_string')) {
    function random_string($length = 4, $type = '') {
        $alpha   = 'ABCDEFGHIJKLMNOPQRTUVWXYZ';
        $numeric = '123456789';
        $symbols = '!@#$%&=+-*';
        $random_string = '';

        switch ($type) {
            case 'mix':
                $rand_word = $alpha.$numeric.$symbols;
                break;

            case 'alpha':
                $rand_word = $alpha;
                break;

            case 'numeric':
                $rand_word = $numeric;
                break;

            case 'symbols':
                $rand_word = $symbols;
                break;

            default:
                $rand_word = $alpha.$numeric;
                break;
        }

        $word_array = str_split(str_shuffle($rand_word));
        $rand = array_rand($word_array, $length);

        foreach ($rand as $k) {
            $random_string .= $word_array[$k];
        }

        return $random_string;
    }
}

/**
 * pre_p()
 *.
 * @param String $data 內容
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('pre_p')) {
    function pre_p($data) {
        echo '<pre>';
        print_r($data);
        echo '<hr>';
    }
}

/**
 * pre_v()
 *.
 * @param String $data 內容
 * @return String
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('pre_v')) {
    function pre_v($data) {
        echo '<pre>';
        var_dump($data);
        echo '<hr>';
    }
}

/**
 * check_date()
 *.
 * @param String $date 日期
 * @return Boolen
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('check_date')) {
    function check_date($date = ''){
        if (empty($date)) {
            return false;
        }

        if(!preg_match("/^[0-9]{4}-[1-12]{2}-[1-31]{2}$/", $date)){
            return false;
        }

        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        return checkdate($month, $day, $year);
    }
}

/**
 * check_phone()
 *.
 * @param String $phone 手機號碼
 * @return Boolen
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('check_phone')) {
    function check_phone($phone = ''){
        if (empty($phone)) {
            return false;
        }

        if (preg_match("/^09[0-9]{2}-[0-9]{3}-[0-9]{3}$/", $phone)) {
            return true;    // 09xx-xxx-xxx
        } else if(preg_match("/^09[0-9]{2}-[0-9]{6}$/", $phone)) {
            return true;    // 09xx-xxxxxx
        } else if(preg_match("/^09[0-9]{8}$/", $phone)) {
            return true;    // 09xxxxxxxx
        } else {
            return false;
        }
    }
}

/**
 * check_identity()
 *.
 * @param String $identity 身分證
 * @return Boolen
 *
 * @author Ian
 * @version 1.0
 **/
if (!function_exists('check_identity')) {
    function check_identity($identity = ''){
        if (empty($identity)) {
            return false;
        }

        //格式檢查
        if (!preg_match('/^[A-Z]{1}[12ABCD]{1}[0-9]{8}$/', $identity) || strlen($identity) != 10) {
            return false;
        }

        //各縣市
        $firstLetterArr = [
            'A' => 10, //台北市
            'B' => 11, //台中市
            'C' => 12, //基隆市
            'D' => 13, //台南市
            'E' => 14, //高雄市
            'F' => 15, //新北市
            'G' => 16, //宜蘭縣
            'H' => 17, //桃園市
            'I' => 34, //嘉義市
            'J' => 18, //新竹縣
            'K' => 19, //苗栗縣
            'L' => 20, //台中縣
            'M' => 21, //南投縣
            'N' => 22, //彰化縣
            'O' => 35, //新竹市
            'P' => 23, //雲林縣
            'Q' => 24, //嘉義縣
            'R' => 25, //台南縣
            'S' => 26, //高雄縣
            'T' => 27, //屏東縣
            'U' => 28, //花蓮縣
            'V' => 29, //台東縣
            'W' => 32, //金門縣
            'X' => 30, //澎湖縣
            'Y' => 31, //陽明山管理局
            'Z' => 33, //連江縣
        ];

        //轉成陣列
        $identityArr = str_split($identity);

        $letterArr = str_split($firstLetterArr[$identityArr[0]]);

        //大陸港澳 男 = A ; 女 = B (入境管理使用)
        //外國人 男 = C ; 女 = D (警察局外事科/課使用)
        $foreignLetterArr = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
        ];

        //外國人檢查，由第二碼判斷
        if (in_array($identityArr[1], ['A', 'B', 'C', 'D'])) {
            $identityArr[1] = $foreignLetterArr[$identityArr[1]];
        }

        $sum = 0;
        for ($i=1; $i < 9; $i++) {
            $sum += (int) $identityArr[$i] * (9 - $i);
        }

        $sum += (int) $letterArr[0] + (int) ($letterArr[1] * 9) + (int) $identityArr[9];

        return ($sum % 10 != 0) ? false : true;
    }
}
