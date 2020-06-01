<?php
namespace App\Services;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use PDO;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

class SiteManager extends Manager {
    private $twig = null;
    private $themem = null;
    private $entitym = null;
    private $settings = null;
    private $encoder = null;
    private $postm = null;

    public function __construct(Environment $twig, ThemeManager $themem, PostManager $postm, EntityManagerInterface $entitym, UserPasswordEncoderInterface $encoder){
        $this->twig = $twig;
        $this->themem = $themem;
        $this->entitym = $entitym;
        $this->encoder = $encoder;
        $this->postm = $postm;

        if(file_exists(SERVER_ROOT.'/data/settings.yaml')){
            $this->settings = Yaml::parseFile(SERVER_ROOT.'/data/settings.yaml');
        }
    }

    public function databaseServerAvailable(){
        try {
            $this->entitym->getConnection()->getSchemaManager()->listDatabases();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function isInstalled(){
        if(!$this->settings){
            return false;
        }

        return $this->settings['installed'];
    }

    public function hasSettings(){
        return !is_null($this->settings);
    }

    public function resetPages(){
        if(!file_exists(SERVER_ROOT.'/data/pages')){
            mkdir(SERVER_ROOT.'/data/pages');
        }

        file_put_contents(SERVER_ROOT.'/data/pages/index.yaml', Yaml::dump([
            'title' => 'Главная',
            'page_content' => [
                'type' => 'static',
                'content' => '<h1>Это шаблон страницы. Используйте <a href="/admin">конструктор</a>, чтобы изменить её.</h1>'
            ],
            'blocks' => null
        ]));
        file_put_contents(SERVER_ROOT.'/data/pages/_post.yaml', Yaml::dump([
            'title' => 'Страница отображения публикаций',
            'page_content' => [
                'type' => 'static',
                'content' => '',
            ],
            'blocks' => null
        ]));

        $global_data = [
            'title' => 'Общие настройки страниц',
            'page_content' => [
                'type' => 'static',
                'content' => '',
            ],
            'blocks' => []
        ];

        $theme_data = $this->themem->getThemeData();
        foreach($theme_data['blocks'] as $block_name => $tmp){
            $global_data['blocks'][$block_name] = [
                'active' => false,
                'modules' => null
            ];
        }
        file_put_contents(SERVER_ROOT.'/data/pages/_global.yaml', Yaml::dump($global_data));
    }    

    public function databaseExists(string $name){
        if(!$this->settings){
            return false;
        }
        $pdo = new PDO(
            'mysql:host='.$this->settings['db_host'].';port='.$this->settings['db_port'].';dbname=INFORMATION_SCHEMA',
            $this->settings['db_user'],
            $this->settings['db_password']
        );

        $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'");
        return (bool) $stmt->fetchColumn();
    }

    public function detectDBVersion(string $host, int $port, string $login = null, string $password = null){
        try {
            $pdo = new PDO('mysql:host='.$host.';port='.$port, $login, $password);
            $info = $pdo->query("SHOW VARIABLES like '%version%'")->fetchAll(PDO::FETCH_KEY_PAIR);
            return $info['version'];
        } catch (\Throwable $th) {
            return null;
        }  
    }

    public function createDatabase(string $name){
        if(!$this->settings){
            return false;
        }
        try {
            $pdo = new PDO(
                'mysql:host='.$this->settings['db_host'].';port='.$this->settings['db_port'],
                $this->settings['db_user'],
                $this->settings['db_password']
            );
    
            $stmt = $pdo->query("CREATE DATABASE $name");
            return true;
        } catch (\Throwable $th) {echo $th->getMessage();}
        return false;
    }

    public function initializeDBScheme(){
        try{
            $tool = new SchemaTool($this->entitym);
            $tool->createSchema($this->entitym->getMetadataFactory()->getAllMetadata());
        }catch(\Throwable $th){
            throw $th;
            return false;
        }
        return true;
    }

    public function createAdminAccount(string $username, string $password, array $roles = ['ROLE_PANEL']){
        $admin = new Admin();
        $admin->setUsername($username);
        $admin->setRoles($roles);

        $password = $this->encoder->encodePassword($admin, $password);
        $admin->setPassword($password);

        $this->entitym->persist($admin);
        $this->entitym->flush();
    }

    private function getNewSiteSettings(){
        return [
            'installed' => false,
            'db_host' => 'localhost',
            'db_port' => 3306,
            'db_driver' => 'pdo_mysql',
            'db_name' => '',
            'db_user' => '',
            'db_password' => '',
            'db_version' => '0',

            'admin_login' => '',
            'admin_password' => '',
        ];
    }

    public function saveSettings(){
        if(!file_exists(SERVER_ROOT.'/data')){
            mkdir(SERVER_ROOT.'/data');
        }

        if($this->settings){
            file_put_contents(SERVER_ROOT.'/data/settings.yaml', Yaml::dump($this->settings));
        }
    }

    public function renderInstaller(){
        return $this->twig->render('twig_util/site-install.html.twig');
    }

    public function updateSettings(array $data){
        $settings = $this->settings ?? $this->getNewSiteSettings();

        $settings['db_host'] = $data['db_host'];
        $settings['db_port'] = $data['db_port'];
        $settings['db_user'] = $data['db_login'];
        $settings['db_password'] = $data['db_password'];

        $settings['admin_login'] = $data['admin_login']??$settings['admin_login'];
        $settings['admin_password'] = $data['admin_password']??$settings['admin_password'];

        $settings['db_version'] = $this->detectDBVersion($settings['db_host'], $settings['db_port'], $settings['db_user'], $settings['db_password']);
        if(is_null($settings['db_version'])){
            throw new Exception('Не удалось определить версию MySQL');
        }

        $settings['db_name'] = $data['db_name'];

        $this->settings = $settings;
        $this->saveSettings();
    }

    public function installSite(){
        if($this->isInstalled()){
            throw new Exception('Сайт уже установлен');
        }
        
        $settings = $this->settings;
        if(!$settings){
            throw new Exception('Отсутствует конфигурация БД');
        }

        if(!$this->databaseExists($settings['db_name'])){
            if(!$this->createDatabase($settings['db_name'])){
                throw new Exception('Не удалось создать базу данных');
            }
        }

        if(!$this->initializeDBScheme()){
            throw new Exception('Не удалось создать таблицы в базе данных');
        }
        
        $this->createAdminAccount($settings['admin_login'], $settings['admin_password'], ['ROLE_SUPER_ADMIN']);
        
        $settings['installed'] = true;
        $this->settings = $settings;
        $this->saveSettings();
        $this->postm->initializeDB();
        $this->resetPages();
    }
}