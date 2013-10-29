<?php
namespace Application\Helpers;

class UrlHelper {
	protected $base = '';

	public $cachedUrl = '';

	public $cachedPath = '';

	public $cachedQuery = '';

	protected $pathList = array();

	protected $queryDict = array();

	protected $absolute = true;

	protected $scheme = '';

	protected $host = '';

	protected $port = '';

	protected $user = '';

	protected $password = '';

	protected $fragment = '';

	public function __construct($urlString = '') {
		$this->cachedUrl = $urlString;
		$this->parse();
	}

	public function __toString() {
		$url = '';

		if ($this->absolute) {
			if ( $this->host ) {
				if ( $this->scheme ) {

					$url .= $this->scheme . ':';

				}

				$url .= '//' . $this->host . '/';

			} else {
				$url .= '//';
			}

			$base = trim($this->base, '/');

			$url .= ('' === $base ? '': $base . '/');
		}

		$url .= $this->path();

		if (sizeof($this->queryDict)) {
			$url .= '?' . implode('&',
					array_map( function($tup) {
							return is_numeric($tup[0]) ? $tup[1] : $tup[0] . '=' . $tup[1];
						}, 
						array_map(
							null,
							array_keys($this->queryDict),
							array_values($this->queryDict) ) ) );
		}
		return $url;
	}

	public function absolute($bool = null) {
		if ( is_null($bool) ) {
			$this->absolute = true;
		} else {
			$this->absolute = $bool;
		}
		return $this;
	}

	public function base() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->base;
		} else if ( 0 < sizeof($arguments) ) {
			$this->base = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function copy() {
		$clone = clone $this;
		return $clone;
	}

	public function fragment() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->fragment;
		} else if ( 0 < sizeof($arguments) ) {
			$this->fragment = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function host() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->host;
		} else if ( 0 < sizeof($arguments) ) {
			$this->host = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function join($pathA, $pathB) {
		return rtrim($pathA, '/') . '/' . ltrim($pathB, '/');
	}
	
	function parse($urlString = false) {
		if (false === $urlString) {
			$urlString = $this->cachedUrl;
		} else {
			$this->cachedUrl = $urlString;
		}

		$parsed = parse_url($urlString);

		foreach ( array(
			'scheme',
			'host',
			'port',
			'user',
			'pass',
			'user',
			'password',
			'query',
			'fragment' )
			as $name ) {

			if ('path' === $name) {
				$this->path($parsed['path']);

			} else if ('query' === $name) {
				$this->parseQuery($parsed['query']);

			} else if ( isset($parsed[$name]) && isset($this->$name) ) {
				$this->$name = $parsed[$name];
				
			}


		}

		return $this;
	}

	public function parseQuery($query) {
		$this->cachedQuery = $query;

		foreach ( 
			array_map(
				function ($elem) {
					return explode('=', $elem);
				},
				explode('&', trim($this->cachedQuery, '&') ) )

			as $index => $keyVal) {

			if (1 == sizeof($keyVal)) {

				$this->queryDict[$index] = $keyVal[0];

			} else {

				list($key, $val) = $keyVal;

				$this->queryDict[$key] = $val;

			}

		}
	}

	public function password() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->password;
		} else if ( 0 < sizeof($arguments) ) {
			$this->password = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function path() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return implode('/', $this->pathList);
		} else if ( 0 < sizeof($arguments) ) {
			$path = $arguments[0];
			if (is_array($path)) {
				$path = implode('/', array_values($path));
			}
			$base = trim($this->base, '/');

			if ( strlen($base) && '' === strstr($path, $base, true) ) {
				$path = substr($path, strlen($base));
			}

			$this->cachedPath = $path;
			$this->pathList = explode('/', trim($this->cachedPath, '/') );
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function port() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->port;
		} else if ( 0 < sizeof($arguments) ) {
			$this->port = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function query($param) {
		$arguments = func_get_args();
		if ( 1 === sizeof($arguments) ) {
			if ( is_array($param) ) {
				foreach ($param as $key => $value) {
					$this->queryDict[$key] = $value;
				}
			} else if ( is_numeric($param) ) {
				$value = array_values($this->queryDict);
				return $value[$param];
			} else {
				return $this->queryDict[$param];
			}
		} else if ( 1 < sizeof($arguments) ) {
			if ( is_numeric($param) ) {
				$keys = array_keys($this->queryDict);
				$value[$keys[$param]] = $arguments[1]; 
			} else {
				$value[$param] = $arguments[1]; 
			}
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function relative($bool = null) {
		if ( is_null($bool) ) {
			$this->absolute = false;
		} else {
			$this->absolute = ! $bool;
		}
		return $this;
	}

	public function scheme() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->scheme;
		} else if ( 0 < sizeof($arguments) ) {
			$this->scheme = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	public function user() {
		$arguments = func_get_args();
		if ( 0 === sizeof($arguments) ) {
			return $this->user;
		} else if ( 0 < sizeof($arguments) ) {
			$this->user = $arguments[0];
			return $this;
		} else {
			throw new \InvalidArgumentException();
		}
	}
}