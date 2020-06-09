<?php
namespace Pluguns\menus;

use App\Services\Data\Data;

class MenuItemData extends Data {
    private $text = '';
    private $href = '';
    private $children = [];

    public function __construct(string $href, string $text){
        $this->href = $href;
        $this->text = $text;
    }

    public function setText(string $text){
        $this->text = $text;
    }

    public function getText(){
        return $this->text;
    }

    public function setHref(string $href){
        $this->href = $href;
    }

    public function getHref(){
        return $this->href;
    }

    public function addChild(MenuItemData $child){
        $this->children []= $child;
    }

    public function removeChild(MenuItemData $child){
        $i = array_search($child, $this->children);
        if($i !== false){
            unset($this->children[$i]);
        }
    }

    public function getChildren(){
        return $this->children;
    }
    
    public function serialize(){
        $children = [];
        foreach($this->getChildren() as $child){
            $children []= $child->serialize();
        }

        return [
            'text' => $this->text,
            'href' => $this->href,
            'children' => $children,
        ];
    }

    public static function deserialize_raw(array $data){
        Data::assertValueType($data['text'], 'string');
        Data::assertValueType($data['href'], 'string');

        $item = new MenuItemData($data['href'], $data['text']);

        $children = $data['children']??null;
        if(is_array($children)){
            foreach($children as $child){
                $child = MenuItemData::deserialize($child);
                if(!is_null($child)){
                    $item->addChild($child);
                }
            }
        }

        return $item;
    }
}