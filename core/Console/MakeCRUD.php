<?php

namespace Core\Console;

/**
 * @author Cadet Hugo
 */
class MakeCRUD {

    private $url;
    private $app;
    private $method;

    function __construct($arg)
    {
        $this->run($arg);
    }

    // Couleurs de la console
    const COLORS = [
        'RESET' => "\033[0m",
        'RED' => "\033[91m",
        'GREEN' => "\033[32m",
        'YELLOW' => "\033[93m",
    ];

    public function run($argv)
    {
        if(!empty($argv[1]) && $argv[1] === 'make' && $argv[2] === 'controller'){

            $this->routes();
            $this->controller();
            $this->template();
        }
    }

    public function routes()
    {
        $this->file = fopen('./config/routes.php','r+');
        fseek($this->file, -2, SEEK_END);
        echo self::COLORS['YELLOW']."Input the routes (as 'url' -> array('url','...','...'))".self::COLORS['RESET']."\n";
        echo self::COLORS['RED']. "Input the url :".self::COLORS['RESET']." \n";
        $this->url = trim(fgets(STDIN));
        echo self::COLORS['RED']. "Input the app's name :".self::COLORS['RESET']." \n";
        $this->app = trim(fgets(STDIN));
        echo self::COLORS['RED']. "Input the method's name :".self::COLORS['RESET']." \n";
        $this->method = trim(fgets(STDIN));
        fwrite($this->file, "    array(['GET'],'$this->url','$this->app','$this->method'),\n");
        fwrite($this->file,');');
    }


    public function controller()
    {
        $dirController = './src/Controller/';
        echo self::COLORS['YELLOW']."Input name (as 'exemple' -> ExempleController)".self::COLORS['RESET']."\n";
        echo self::COLORS['RED']."Controllers name :".self::COLORS['RESET']."\n";
        $controller_name = trim(ucfirst(fgets(STDIN)));
        $controller_nameController = $controller_name.'Controller';
        echo self::COLORS['RED']."-----------".self::COLORS['RESET']."\n";
        echo $controller_nameController."\n";

        $dataController = '<?php

            namespace App\Controller;
            
            use Core\Kernel\AbstractController;
            
            /**
             *
             */
            class '.$controller_nameController.' extends AbstractController
            {
                public function index()
                {
                    $message = "'.$controller_name.' Page";
                    //$this->dump($message);
                    $this->render("app.'.$this->app.'.'.$this->url.'",array(
                        "message" => $message,
                    ));
                }
            }
            ;';

        $this->dirTemplateApp = "./template/app/$this->app/";
        $this->dataTemplateApp = '<h1 style="text-align: center;font-size:33px;margin: 100px 0;color:#A67153;">
    <?= $message; ?>
</h1>';

        file_put_contents($dirController.$controller_nameController.'.php', $dataController);
    }

    public function template()
    {
        if (is_dir($this->dirTemplateApp)) {
            file_put_contents($this->dirTemplateApp.$this->url.'.php', $this->dataTemplateApp);
        } elseif (is_dir($this->app) === false) {
            mkdir($this->dirTemplateApp, 0700);
            echo self::COLORS['GREEN']."#### template/app/$this->app directory created.. ####".self::COLORS['RESET']."\n";
            file_put_contents($this->dirTemplateApp.$this->url.'.php', $this->dataTemplateApp);
        }
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

}
