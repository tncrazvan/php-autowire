<?php
namespace io\github\tncrazvan\autowire;

use ReflectionProperty;

trait Autowired{
    public static $injecting = false;
    protected array $autoinjected = [];
    protected function auto_inject():array{
        self::$injecting = true;
        $reflection = new \ReflectionClass(\get_called_class());
        $props = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($props as &$prop){
            if($prop->isInitialized($this))
                continue;
            $classname = $prop->getType()->getName();
            if('' === $classname || 'string' === $classname || 'array' === $classname || 'int' === $classname || 'bool' === $classname)
                continue;
            $object = new \ReflectionClass($classname);
            try{
                $inject = $object->getMethod("singleton");
                $name = $prop->getName();
                $this->$name = $inject->invoke(null);
                $this->autoinjected[] = $this->$name;
            }catch(\ReflectionException $e){
                //echo "$name is not injectable because it does not specify a static 'inject' method.\n";
            }
        }
        return $this->autoinjected;
    }
}