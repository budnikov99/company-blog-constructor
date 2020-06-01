<?php
namespace App\Services\Data;

class BlockData extends Data {
    private $active = null;
    private $modules = [];

    public function __construct(bool $active = null)
    {
        $this->active = $active;
    }

    public function addModule(BlockModuleData $module){
        $this->modules[] = $module;
    }

    public function getModules(){
        return $this->modules;
    }

    /**
     * Устанавливает статус блока
     *
     * @param boolean|null $active - false - блок выключен. true - включен. null - блок наследуется из _global.
     * @return void
     */
    public function setActive(bool $active = null){
        $this->active = $active;
    }

    public function getActive(){
        return $this->active;
    }

    protected static function deserialize_raw(array $data){

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