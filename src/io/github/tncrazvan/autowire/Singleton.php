<?php
namespace io\github\tncrazvan\autowire;

trait Singleton{
    private static $singleton = null;
    public static function singleton(...$args){
        if(static::$singleton === null){
            static::$singleton = new static();
            if(method_exists(static::$singleton,"auto_inject"))
                static::$singleton->auto_inject();
        }
        static::$singleton->run(...$args);
        return static::$singleton;
    }
    public function run():void{}
}