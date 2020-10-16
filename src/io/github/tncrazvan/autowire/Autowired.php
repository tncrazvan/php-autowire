<?php
namespace io\github\tncrazvan\autowire;

trait Autowired{
    public static $injecting = false;
    protected array $autoinjected = [];
    protected function auto_inject():array{
        self::$injecting = true;
        $reflection = new \ReflectionClass(\get_called_class());
        $props = $reflection->getProperties();
        foreach($props as &$prop){
            $prop->setAccessible(true);
            if($prop->isInitialized($this))
                continue;
            $classname = $prop->getType()->getName();
            if('' === $classname || 'string' === $classname || 'array' === $classname || 'int' === $classname || 'bool' === $classname)
                continue;
            $object = new \ReflectionClass($classname);
            try{
                
                $inject = $object->getMethod("singleton");
                $name = $prop->getName();
                $prop->setValue($this,$inject->invoke(null));
                //$this->$name = $inject->invoke(null);
                $this->autoinjected[] = $this->$name;
            }catch(\ReflectionException $e){
                //echo "$name is not injectable because it does not specify a static 'inject' method.\n";
            }
            $prop->setAccessible(false);
        }
        return $this->autoinjected;
    }
}
