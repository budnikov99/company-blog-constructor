<?php
namespace Pluguns\menus;

use App\formats\MenuFormat;
use App\Services\Data\Data;

class MenuData extends Data {
    private $title = '';
    private $items = [];

    public function __construct(string $title){
        $this->title = $title;
    }

    public function setTitle(string $title){
        $this->title = $title;
    }

    public function getTitle(){
        return $this->title;
    }

    public function addItem(MenuItemData $item){
        $this->items []= $item;
    }

    public function removeItem(MenuItemData $item){
        $i = array_search($item, $this->items);
        if($i !== false){
            unset($this->items[$i]);
        }
    }

    public function getItems(){
        return $this->items;
    }

    public function toMenuFormat(){
        $format = new MenuFormat();
        foreach($this->items as $item){
            $i = $format->addItem($item->getHref(), $item->getText());
            if(!empty($item->getChildren())){
                foreach($item->getChildren() as $child){
                    $format->addChild($i, $child->getHref(), $child->getText());
                }
            }
        }

        return $format;
    }

    public function serialize(){
        $items = [];
        foreach($this->getItems() as $item){
            $items []= $item->serialize();
        }

        return [
            'title' => $this->title,
            'items' => $items,
        ];
    }

    public static function deserialize_raw(array $data){
        Data::assertValueType($data['title'], 'string');

        $menu = new MenuData($data['title']);

        $items = $data['items']??null;
        if(is_array($items)){
            foreach($items as $item){
                $item = MenuItemData::deserialize($item);
                if(!is_null($item)){
                    $menu->addItem($item);
                }
            }
        }

        return $menu;
    }
}