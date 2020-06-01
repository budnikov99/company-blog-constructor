<?php

namespace App\Services;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Symfony\Component\Yaml\Yaml;

class DatabaseConnectionFactory extends ConnectionFactory {

    public function createConnection(array $params, Configuration $config = null, EventManager $eventManager = null, array $mappingTypes = array())
    {
        $params['driver'] = 'pdo_mysql';
        $params['host'] = 'localhost';
        $params['port'] = 3306;

        $params['dbname'] = '';
        $params['user'] = '';
        $params['password'] = '';

        if(file_exists(SERVER_ROOT.'/data/settings.yaml')){
            $data = Yaml::parseFile(SERVER_ROOT.'/data/settings.yaml');

            $params['driver'] = $data['db_driver'];
            $params['host'] = $data['db_host'];
            $params['port'] = $data['db_port'];

            $params['dbname'] = $data['db_name'];
            $params['user'] = $data['db_user'];
            $params['password'] = $data['db_password'];

            $params['serverVersion'] = $data['db_version'];
        }
        
        $connection = parent::createConnection($params, $config, $eventManager, $mappingTypes);
        
        return $connection;
    }

}