<?php
namespace App\services\data;

use Monolog\Logger;
use PhpParser\Node\Stmt\TryCatch;

abstract class Data {
    /**
     * Создаёт объект данных на основе переданного словаря. Если данные не содержат необходимых полей, бросает исключение.
     *
     * @param array $data - словарь
     * @return void - объект
     */
    protected abstract static function deserialize_raw(array $data);

    /**
     * Создаёт объект данных, на основе словаря.
     *
     * @param array $data - словарь.
     * @return void - Объект данных, либо null, если словарь не содержит необходимых полей.
     */
    public static function deserialize(array $data){
        //try {
            return (static::class)::deserialize_raw($data);
        /*} catch (\Throwable $th) {
            return null;
        }*/
        
    }

    /**
     * Возвращает словарь данных на основе объекта.
     *
     * @return array
     */
    public abstract function serialize();
}