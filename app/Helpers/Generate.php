<?php
namespace App\Helpers;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class Generate
{

    public static function money($value = 0, $currency = 'Rp.')
    {
        return $currency . ' ' . number_format($value, 0, '', '.') . ',-';
    }

    public static function getFileExtension($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }

    public static function parseNumber($number, $dec_point = null)
    {
        if (empty($dec_point)) {
            $locale = localeconv();
            $dec_point = $locale['decimal_point'];
        }
        return floatval(str_replace($dec_point, '.', preg_replace('/[^\d' .
        preg_quote($dec_point) . ']/', '', $number)));
    }


    public static function randomDigitsLame($numDigits)
    {
        $digits = '';

        for ($i = 0; $i < $numDigits; ++$i) {
            $digits .= mt_rand(0, 9);
        }

        return $digits;
    }

    public static function generateRandomStringAndNumber($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomNumber($length = 10)
    {
        $characters = '1234567890';
        $charactersLength = strlen($characters);
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomNumber;
    }

    public static function maskedNumber($number, $x = 0)
    {
        $count_char = strlen($number);
        $var = substr_replace($number, str_repeat("*", $count_char - (($x + 1) * 2)), $x, $count_char -
        (( $x + 1 ) * 2));
        return $var;
    }

    public static function maskedEmail($email, $x = 0)
    {
        $a = explode('@', $email);
        $b = explode('.', $a[1]);
        $var[0] = substr_replace($a[0], str_repeat("*", strlen($a[0]) - $x), $x, strlen($a[0]) - $x);
        $var[1] = substr_replace($b[0], str_repeat("*", strlen($b[0]) - $x), $x, strlen($b[0]) - $x);
        $result = $var[0] . '@' . $var[1];
        foreach ($b as $index => $row) {
            if ($index != 0) {
                $result .= '.' . $row;
            }
        }
        return $result;
    }


    public static function uuid()
    {
        $uuid = Uuid::uuid1();
        return $uuid->toString();
    }

    public static function abbreviate ($s) {
        if(preg_match_all('/\b(\w)/',strtoupper($s),$m)) {
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        return $v;
    }

    public static function reNumberFormat($value, $saparator = ",") {

        $clean_string = preg_replace('/([^0-9\.,])/i', '', $value);
        $only_number_string = preg_replace('/([^0-9])/i', '', $value);

        $separators_count_to_be_erased = strlen($clean_string) - strlen($only_number_string) - 1;

        $string_with_comma_or_dot = preg_replace('/([,\.])/', '', $clean_string, $separators_count_to_be_erased);
        $removed_thousand_separator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $string_with_comma_or_dot);

        return (float) str_replace(',', '.', $removed_thousand_separator);
    }


    public static function changeZeroTo62($nohp) {
        $nohp = str_replace(" ","",$nohp);
        $nohp = str_replace("(","",$nohp);
        $nohp = str_replace(")","",$nohp);
        $nohp = str_replace(".","",$nohp);

        if(!preg_match('/[^+0-9]/',trim($nohp))){
            if(substr(trim($nohp), 0, 3)=='62'){
                $hp = trim($nohp);
            }
            elseif(substr(trim($nohp), 0, 1)=='0'){
                $hp = '62'.substr(trim($nohp), 1);
            }
        }
        return $hp;
    }

    public static function dadJoke () {
        try {
            $header = [
                'Accept' => 'application/json',
            ];
            $url = 'https://icanhazdadjoke.com/';

            $data = Http::withHeaders($header)->get($url);
        } catch (\Exception $e) {
            return "hello is it me you're looking for";
        }
        return $data['joke'] ?? "hello is it me you're looking for";
    }

    public static function parse_number($number, $dec_point=null) {
        if (empty($dec_point)) {
            $locale = localeconv();
            $dec_point = $locale['decimal_point'];
        }
        return floatval(str_replace($dec_point, '.', preg_replace('/[^\d'.preg_quote($dec_point).']/', '', $number)));
    }


    public static function initJson()
    {
        return [
            'code' => [],
            'status' => [],
            'message' => [],
            'records' => [],
            'pagination' => []
        ];
    }

    public static function responseTime(){
        $diff = microtime(true) - session()->get('initTime');
        session()->forget('initTime');
        $sec = intval($diff);
        $micro = $diff - $sec;
        return round($micro * 1000, 4) . " ms";
    }

    public static function getClientDetail(){
        $agent = new Agent();
        return [
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'type' => ($agent->isDesktop()) ? 'is_desktop' : 'is_phone'
        ];
    }


    public static function generateInvoice($object,$code) {
        $dateNow = Carbon::now();
        $transc_no = $object->where('created_at','>=',$dateNow->format('Y-m-d')." 00:00:00")->where('created_at','<=',$dateNow->format('Y-m-d')." 23:59:59")->count()+1;
        $invoice = "INV-{$code}-{$dateNow->format('dmY')}-{$transc_no}";
        return $invoice;
    }

    public static function toCamelCase ($string, $saparator = "_", $capitalizeFirstCharacter = false) {
        $str = str_replace( $saparator, '', ucwords($string,  $saparator));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public static function toSnakeCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public static function camelCaseAlias ($string) {
        return $string ." as ". Generate::toCamelCase($string,"_");
    }

    public static function booleanValue($boolean) {
        return $boolean == 'true' ? true : false;
    }

    public static function generateWord ($number_of_words = 2) {

        try {
            $data = file_get_contents("https://random-word-api.herokuapp.com/word?number=" . $number_of_words);
        } catch (\Exception $e) {
            return [
                Generate::generateRandomStringAndNumber(5),
                Generate::generateRandomStringAndNumber(5),
            ];
        }

        return json_decode($data, true);
    }

    public static function stringToLowerDashCase($string) {
        return (str_replace(' ', '-', strtolower($string)));
    }

    public static function encodeString ($string, $code, $saparator) {
        $string_pass = str_replace("=","",base64_encode( $code)) . $saparator. str_replace("=","",base64_encode($string));
        return $string_pass;
    }

    public static function decodeString ($string_pass, $saparator) {
        $data =  explode($saparator, $string_pass);
        return base64_decode($data[1]. "==");
    }

    public static function addTableNameToColumn ($table, $data) {
        foreach ($data as $index => $row) {
            $data[$index] = $table . "." . $row;
        }
        return $data;
    }

    public static function checkAndGetCountryCodeByArray ($number)  {
        foreach( config('phone_country_code') as $key=>$value )
        {
            if ( substr( $number, 0, strlen( $key ) ) == $key )
            {
                return $key;
                break;
            }
        }
        return null;
    }

    public static function checkAndGetCountryCodeByIndex ($index)  {
        return config('phone_country_code')[$index] ?? null;
    }


    public static function integerToPhoneNumberFormat ($number) {

        if( is_numeric($number) )
        {
            $country_code = Generate::checkAndGetCountryCodeByArray($number);
            if ($country_code != null) {

                $number = substr ($number, strlen($country_code)) ;
                return "+". $country_code . " " .preg_replace("/(\d{3})(\d{4})(\d)/", "$1-$2-$3", $number);

            }
        }
        return $number;
    }

    public static function phoneNumberFormatToInteger ($string) {
        $pattern = '/\d+/';
        $matches = array();
        preg_match_all($pattern, $string, $matches);
        return join ('',$matches[0]);
    }

    public static function getUppercaseIndexFromNumber ($num) {
        // 0 = A, 26 = AA, etc
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return Generate::getUppercaseIndexFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    public static function getLowercaseIndexFromNumber ($num) {
        // 0 = a, 26 = aa, etc
        $numeric = $num % 26;
        $letter = chr(97 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return Generate::getLowercaseIndexFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }


}
