<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/config.php';

$applicationDir = $config['applicationDir'];
$modelsDir = $config['modelsDir'];
$vendorDir = $config['vendorDir'];
$viewsDir = $config['viewsDir'];

$baseUrl = $config['baseUrl'];
$assetsUrl = $config['assetsUrl'];

// ini_set('session.save_path', $config['sessionsDir']);

$components = new Application\Framework\ComponentManager($config);

// Enregistrer les composantes

$components->config = $config;
$components->db = function($components) { return new Application\Framework\Database($components->config['db']); };
$components->urlHelper = function($components) { return new Application\Helpers\UrlHelper(); };
$components->stringHelper = function($components) { return new Application\Helpers\StringHelper(); };
$components->formHelper = function($components) { return new Application\Helpers\FormHelper(); };

// Enregistrer les contrôleurs

$controllers = new Application\Framework\ComponentManager($config);

$controllers->loginController = function($components) { 
	return function($route, & $stopPropagation) { 
		$logged = isset($_GET['logged']) ? true: false;
		if ( ! $logged ) { 
			$stopPropagation = true; 
			echo 'Pas connecté'; 
		} 
	}; 
};

$controllers->indexController = function($components) { 
	return function($route, & $stopPropagation) { 
		if ('' === $route) { 
			$stopPropagation = true; 
			echo 'Index'; 
		} 
	}; 
};

$controllers->error404Controller = function($components) { 
	return function($route, & $stopPropagation) { 
		$stopPropagation = true; 
		echo 'Erreur 404'; 
	}; 
};

// Passer la requête à la chaîne de contrôleurs

$route = isset($_GET['route']) ? $_GET['route']: '';
$stopPropagation = false;

foreach ($controllers->listComponents() as $controllerId) {

	$controller = $controllers->$controllerId;

	if ( is_callable($controller) ) {

		$controller($route, $stopPropagation);

		if ($stopPropagation) {
			exit;
		}

	}

}