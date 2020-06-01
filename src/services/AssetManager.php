<?php
namespace App\Services;

use App\Services\Data\ThemeBlockData;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\Server\Server;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Yaml\Yaml;

class AssetManager extends Manager {
    private function loadFile($path){
        if(file_exists($path)){
            return file_get_contents($path);
        }else{
            return null;
        }
    }

    public function getAssetFile($filepath){

        if(in_array(pathinfo($filepath, PATHINFO_EXTENSION), ['scss', 'sass', 'cache'])){
            return $this->compileScss($filepath);
        }else{
            return $this->loadFile($filepath);
        }
    }

    public function getAssetMimeType($filename){
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        //Файлы типа .scss и .sass компилируются в css перед выдачей
        if(in_array($ext, ['sass', 'scss'])){
            return 'text/css';
        }

        $mime = (new MimeTypes())->getMimeTypes($ext);
        $mime = $mime[0]??'text/plain';
        return $mime;
    }

    private function compileScss($filepath){
        if(substr($filepath, -6) == '.cache'){
            $filepath = substr($filepath, 0, -6);
        }

        if(!file_exists($filepath)){
            return null;
        }

        if(in_array(substr($filepath, -5), ['.scss', '.sass'])){
            $cache_filepath = $filepath.'.cache';

            if(!file_exists($cache_filepath) || filemtime($filepath) > filemtime($cache_filepath)){
                $scss = new Compiler();
                $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
                $scss->setImportPaths(pathinfo($filepath, PATHINFO_DIRNAME));

                $file = $scss->compile($this->loadFile($filepath));  
                file_put_contents($cache_filepath, $file); 
                return $file;
            }else{
                return $this->loadFile($cache_filepath);
            }
        }else{
            return $this->loadFile($filepath);
        }
    }

}