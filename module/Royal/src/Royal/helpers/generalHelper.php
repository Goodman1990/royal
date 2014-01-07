<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artgen2
 * Date: 12.09.13
 * Time: 12:17
 * To change this template use File | Settings | File Templates.
 */

namespace Royal\helpers;

use Zend\Crypt\BlockCipher;

class generalFunction {


    public function encodeUtf8($str) {

        $str = json_encode($str);
        $arr_replace_utf = array(
            '\u0410', '\u0430', '\u0411', '\u0431', '\u0412', '\u0432',
            '\u0413', '\u0433', '\u0414', '\u0434', '\u0415', '\u0435', '\u0401', '\u0451', '\u0416',
            '\u0436', '\u0417', '\u0437', '\u0418', '\u0438', '\u0419', '\u0439', '\u041a', '\u043a',
            '\u041b', '\u043b', '\u041c', '\u043c', '\u041d', '\u043d', '\u041e', '\u043e', '\u041f',
            '\u043f', '\u0420', '\u0440', '\u0421', '\u0441', '\u0422', '\u0442', '\u0423', '\u0443',
            '\u0424', '\u0444', '\u0425', '\u0445', '\u0426', '\u0446', '\u0427', '\u0447', '\u0428',
            '\u0448', '\u0429', '\u0449', '\u042a', '\u044a', '\u042b', '\u044b', '\u042c', '\u044c',
            '\u042d', '\u044d', '\u042e', '\u044e', '\u042f', '\u044f'
        );

        $arr_replace_cyr = array(
            'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
            'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о',
            'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш',
            'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я'
        );

        $str2 = str_replace($arr_replace_utf, $arr_replace_cyr, $str);

        return $str2;
    }
    public  function transliteration($str){


        $arr_replace_utf = array(
            'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
            'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о',
            'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш',
            'Щ', 'щ','Ы', 'ы','Э', 'э', 'Ю', 'ю', 'Я', 'я',' ','ь','ъ'

        );

        $arr_replace_cyr = array(
            'A', 'a', 'B', 'b', 'V', 'v',
            'G', 'g', 'D', 'd', 'Ye', 'ye', 'E', 'e', 'zh',
            'Zh', 'Z', 'z', 'I', 'i', 'Y', 'y', 'K', 'k',
            'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P',
            'p', 'R','r','S', 's', 'T', 't', 'U', 'u', 'F', 'f',
            'X', 'x', 'C', 'c', 'Сh', 'ch', 'Sh', 'sh', 'Sch',
            'sch', 'Y', 'y', 'E', 'e', 'Yu', 'yu', 'Ya', 'ya','_','',''

        );

        $str2 = str_replace($arr_replace_utf, $arr_replace_cyr, $str);

        $pattern = "/([^A-Za-z1-9\_])/";
        $str2= preg_replace($pattern,'', $str2);

        return $str2;

    }

    public  function generatePinCode(){

        $keyNumbers='';
        $keyLetters='';
        $key='';
        $sum = 0;
        $length=0;
        $numbers  = "1234659784645613252136455988963789411236547895565287442698746321239";//генерируем ключ для шифрования
        $letters = "qqsxzaqedcvfQWERFTGHJKJNHBGFGBHfghjkadetutijndfvdscbmNJMKLMNadDFASFDFSDaswreyhdqwBVCXZXDFGYHUJIOPLKMNrtyhjmmmnASDASDbvtyuASDioplmnbvghjk";
        $string=  time();

        while($length<2)
        $length= (int) substr($string, rand(0, strlen($string)-1), 1);

        for ($i=0; $i<$length; $i++)
            $keyNumbers.= substr($numbers, rand(0, strlen($numbers)-1), 1);

        for ($i=0; $i<strlen($keyNumbers); $i++)
           $sum+=(int) $keyNumbers{$i};

        for ($i=0; $i<$sum; $i++)
            $keyLetters.= substr($letters, rand(0, strlen($letters)-1), 1);


        if(strlen($keyNumbers)>strlen($keyLetters)){

            for($i=0;$i<strlen($keyNumbers);$i++)
                $key.= substr_replace($keyNumbers[$i],$keyLetters[rand(0, strlen($keyLetters)-1)],rand(0, strlen($keyNumbers)-1) , 0);
            $key.= substr($keyNumbers, strlen($key));

        }else{

            for($i=0;$i<strlen($keyLetters);$i++)
                $key.= substr_replace($keyLetters[$i],$keyNumbers[rand(0, strlen($keyNumbers)-1)    ],rand(0, strlen($keyLetters)-1) , 0);
            $key.= substr($keyLetters, strlen($key));

        }
//        $key = substr($bufLink, strlen($key));
//        $newstring.=$links;
//        echo $key;
//        exit;
        $salt = sha1(md5($key));
        $key = md5($key.$salt);




        return $key;

    }

    public  function encryption($data){

        $encryptionData =array();
        for($i=0;$i<count($data);$i++){

            $serializer =  \Zend\Serializer\Serializer::factory('phpserialize');
            $y  =  $serializer->serialize($data[$i]); //<~ serialized !
//            echo $y;
//            exit;
            $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
            $blockCipher->setKey('JKHJKHkGObhJGJKUfufHUOfTUIFd'); // устанавливаем ключь для шифрования
            $encryptionData[] = $blockCipher->encrypt($y); //шифруем id
//        echo $encryptionData;
//        exit;
        }

//        print_r($data[0]);
//        exit;
        return $encryptionData;

    }

    public  function decryption($data){

//        echo strlen('cce4e2f9cd570583ece4566928577ed15715267634ee59e9ab13712e577a9033WRTAd9dc8/WW+0nrT/+YUsh6iB2ltDFbyhfD75TGmCHniJ3nN0jrXw0IONBRxgqGWfRPgnScqQgOa6BiCgd+XJNmECzdFiS2aT2IOaD/7rEhLHHVnbY5DAAPbP7i9IsHdrxAxmKXWdYGhp9Jf/Yti6eT0T166Pxji2W9aUA6IhwnBa9arnRLGkeow6svN2N+gK9BcqTrrWyg8gMP+AzYqCFOfQcB5H+9ehGO464p/x960BI/dsCRbzuVZ6zBH6aRM/73souCQ7LWS+uZ15g0O7ZWO3F31usQm9rfj03tNkHsBJPNjU6Fiw5FMq3jbW6GNHhOmR835gH3w64/qCvK0H2O3khEHxJv2lItwnFgLcY+7wBCaIYy+2okV0SGsHDWjxv3SHW1CLuemD/3ijbGcXOhWxr7jDuVZJIle0js1fJ6vZriK/n15gst68LLyyW5QXyLbAZ3QCIzrZHRI/X6gxQdV6MpNglwOroM4pY+tKUy/EnUDvb/szPxY7+WF0GpuXYB0E7OmSz/nxBFUDUaUiVRwlkz+dNYXGu2JFhfk3Awj+K6wv/MCw/Ru2BNPOfrrxD+oM+Nh4Ow5272BEkLV1+e5gfKyQ66yYC9sNL6XehnAoBsIIrhXrcydiqxvAkZO8nrY4rHPfevtFpxeud7OQ==/');
//        echo '<br />';
//        echo strlen($data);
//        exit;

    //        $serializer =  \Zend\Serializer\Serializer::factory('phpserialize');
    //        $y  =  $serializer->unserialize($data); //<~ serialized !
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey('JKHJKHkGObhJGJKUfufHUOfTUIFd'); // устанавливаем ключь для шифрования
        $decryptData = $blockCipher->decrypt($data); //шифруем id
//        echo $decryptData;
//        exit;
        $serializer =  \Zend\Serializer\Serializer::factory('phpserialize');
        $data  =  $serializer->unserialize($decryptData);



        return $data;

    }



}