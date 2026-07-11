<?php

namespace app\Core;

class Application
{
    public static $app;
    public $router;
    public $basePath;
    public $plugins = [];

    public function __construct($basePath)
    {
        self::$app = $this;
        $this->basePath = rtrim($basePath, '/') . '/';
        
        // Define aliases for backward compatibility
        class_alias(\app\Core\Router::class, 'MVC\Router');
        class_alias(\app\Core\Request::class, 'MVC\Request');
        
        $this->bootstrap();
        $this->router = new Router($this->basePath);
    }

    protected function bootstrap()
    {
        // Load configuration and legacy files
        if (file_exists($this->basePath . 'config/app.php')) {
            require_once $this->basePath . 'config/app.php';
        }
    }

    public function run()
    {
        // Trigger boot event
        Event::trigger('app.boot', $this);

        // Load modules
        $this->loadModules();

        // Load plugins
        $this->loadPlugins();

        // Load core routes
        $this->loadRoutes();

        // Trigger request event
        Event::trigger('app.request', $this);

        // Run the router to resolve the request
        $this->router->Rutas();

        // Trigger response event
        Event::trigger('app.response', $this);
    }

    protected function loadRoutes()
    {
        // Web routes (no prefix)
        if (file_exists($this->basePath . 'routes/web.php')) {
            $this->router->group([], $this->basePath . 'routes/web.php');
        }

        // API routes (prefix '/api')
        if (file_exists($this->basePath . 'routes/api.php')) {
            $this->router->group(['prefix' => 'api'], $this->basePath . 'routes/api.php');
        }

        // Admin routes (prefix '/admin')
        if (file_exists($this->basePath . 'routes/admin.php')) {
            $this->router->group(['prefix' => 'admin'], $this->basePath . 'routes/admin.php');
        }
    }

    protected function loadModules()
    {
        $modulesDir = $this->basePath . 'modules';
        if (!is_dir($modulesDir)) {
            return;
        }

        $dirs = glob($modulesDir . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $moduleName = basename($dir);
            
            // Load routes if present
            $routesFile = $dir . '/routes.php';
            if (file_exists($routesFile)) {
                // Pass router explicitly so module routes can define their own groups or prefixes
                $router = $this->router;
                include_once $routesFile;
            }

            // Load bootstrap/init file if present
            $bootFile = $dir . '/boot.php';
            if (file_exists($bootFile)) {
                include_once $bootFile;
            }
        }
    }

    protected function loadPlugins()
    {
        $pluginsDir = $this->basePath . 'plugins';
        if (!is_dir($pluginsDir)) {
            return;
        }

        $dirs = glob($pluginsDir . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $pluginName = basename($dir);
            $pluginFile = $dir . '/' . $pluginName . 'Plugin.php';

            if (file_exists($pluginFile)) {
                require_once $pluginFile;
                
                $className = "plugins\\" . $pluginName . "\\" . $pluginName . "Plugin";
                if (class_exists($className)) {
                    $plugin = new $className($this);
                    $plugin->init();
                    $this->plugins[$pluginName] = $plugin;
                }
            }
        }
    }
}
