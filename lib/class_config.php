<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2/19/2016
 * Time: 5:04 PM
 */
class config
{

    private $values = array();

    public function __construct()
    {
        $config = array();
        include "config.php";
        $this->values = $config;
        self::$_instance = $this;
    }

    public static function get($key_str)
    {
        $config = self::instance();
        $keys = explode(".",$key_str);
        $return = null;
        if(count($keys) > 0) {
            $return = $config->values;
            while (count($keys) > 0) {
                $key = array_shift($keys);
                if(isset($return[$key]))
                    $return = $return[$key];
                else {
                    throw new Exception("Key ['".str_replace(".","']['",$key_str)."'] not set in config",404);
                }
            }
        }

        return $return;
    }
    public static function getOrDefault($key,$default){
        $keys = explode(".",$key);
        if(count($keys)>0) {
            $value = null;
            try {
                $value = forward_static_call_array(["config", "get"],[$key]);
            }catch (Exception $e){
                if($e->getCode() == 404)
                    return $default;
            }
            return $value;
        }
        return null;
    }


    private static $_instance;

    public static function instance(){

        if(!isset(self::$_instance)) {
            return false;
        }
        return self::$_instance;
    }

}
$config = new config();