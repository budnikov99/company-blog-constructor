<?php
namespace App\services\data;

class BlockData extends Data {
    private $active = false;
    private $modules = [];

    public function __construct(bool $active = true)
    {
        $this->active = $active;
    }

    public function addModule(BlockModuleData $module){
        $this->modules[] = $module;
    }

    public function getModules(){
        return $this->modules;
    }


    public function isActive(){
        return $this->active;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['active'], 'boolean');

        $block = new BlockData($data['active']);
        if(is_array($data['modules'])){
            foreach($data['modules'] as $module_data){
                $module = BlockModuleData::deserialize($module_data);
                if(is_null($module)){
                    return null;
                }else{
                    $block->addModule($module);
                }
            }
        }

        return $block;
    }

    public function serialize(){
        $data = [
            'active' => $this->active,
            'modules' => []
        ];
        foreach($this->modules as $module){
            $data['modules'] []= $module->serialize();
        }

        return $data;
    }

}