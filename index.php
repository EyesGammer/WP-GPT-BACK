<?php
/**
 * @author Kum0
 *
 * style.css generated using TailwindCSS
 */
use src\Router;
use src\Database;
use src\HookEngine;
use src\Security;
use src\TemplatesEngine;

require_once 'config.php';
require_once 'autoload.php';
require_once 'functions.php';

global $router, $security, $hookEngine, $templatesEngine, $database;

$router = new Router();
$security = new Security();
$hookEngine = new HookEngine();
$templatesEngine = new TemplatesEngine( 'index' );
$templatesEngine
    ->setDirectory( __DIR__ . '/templates' )
    ->scanTemplates();
$database = new Database( GPT_HOST, GPT_USER, GPT_PASS, GPT_NAME );

try {
    $router->register('controllers\User');
    $router->register( 'controllers\API' );
    $router->register( 'controllers\Homepage' );
    $router->register( 'controllers\Client' );
    $router->register( 'controllers\Settings' );
    $router->register( 'controllers\Prompts' );
    $router->register( 'controllers\Options' );
    $router->register( 'controllers\Stats' );
    $router->register( 'controllers\Access' );
    $router->register( 'controllers\Dashboard' );
    $router->register( 'controllers\Account' );
} catch (ReflectionException $e) {
    die( "An unexpected error has occurred." );
}

$router->start();

// Silence is golden