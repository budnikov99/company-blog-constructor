<?php
namespace App\Services\Data;

use App\Services\StaticLogger;
use AssertionError;
use Monolog\Logger;
use PhpParser\Node\Stmt\TryCatch;

abstract class Data {
    /**
     * Создаёт объект данных на основе переданного словаря. Если данные не содержат необходимых полей, бросает исключение.
     *
     * @param array $data - словарь
     * @return null|PageData|BlockData|BlockModuleData|ThemeBlockData|ModuleData|ModuleArgumentData - объект
     */
    protected abstract static function deserialize_raw(array $data);

    /**
     * Создаёт объект данных, на основе словаря.
     *
     * @param array $data - словарь.
     * @return null|PageData|BlockData|BlockModuleData|ThemeBlockData|ModuleData|ModuleArgumentData - Объект данных, либо null, если словарь не содержит необходимых полей.
     */
    public static function deserialize(array $data){
        try {
            return (static::class)::deserialize_raw($data);
        } catch (\Throwable $th) {
            StaticLogger::error('При десериализации конфига произошло исключение', 
                ['exception' => $th, 'class' => static::class]);
            return null;
        }
        
    }

    /**
     * Возвращает словарь данных на основе объекта.
     *
     * @return array
     */
    public abstract function serialize();

    protected static function assertValueType($value, string $type){
        if(gettype($value) != $type){
            throw new AssertionError('Значение '.$value.' должно иметь тип '.$type);
        }
    }
}