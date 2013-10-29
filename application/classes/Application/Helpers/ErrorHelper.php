<?php
namespace Application\Helpers;

class ErrorHelper {
	protected $components;
	protected $assetsUrl;

	public function __construct($components) {
		$this->components = $components;
	}

	public function assetsUrl($assetsUrl) {
		$this->assetsUrl = $assetsUrl;
		return $this;
	}

	public function error($pageTitle, $errorTitle, $errorMessage, $httpStatus = 'HTTP/1.0 422 Unprocessable Entity') {
		$viewsDir = $this->components->config['viewsDir'];

		$errorMessage = preg_replace("`\n[[:blank:]]*\n`", "</p><p>", $errorMessage);
		$errorMessage = nl2br($errorMessage);

		ob_start();
		include $viewsDir . '/error.php';
		$content = ob_get_clean();

		$title = $errorTitle;

		if (! $this->assetsUrl) {
			$urlHelper = $this->components->urlHelper;
			$this->assetsUrl = $urlHelper->path('')->get();
		}

		$assetsUrl = $this->assetsUrl;

		header($httpStatus);
		include $viewsDir . '/layout.php';
		exit;
	}
}