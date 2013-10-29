<?php
namespace Application\Framework;

class ComponentManager {
	protected $components = array();
	protected $callbacks = array();

	public function __get($name) {
        if (! array_key_exists($name, $this->components)) {
        	throw new \Exception("Undefined component “$name”");
        }

        if (is_null($this->components[$name])) {
        	$this->components[$name] = $this->callbacks[$name]($this);
        }

        return $this->components[$name];
	}

	public function __set($name, $object) {
		if (isset($this->components[$name])) {
			// Est-ce qu'on permet de ré-attacher un nom déjà utilisé ? (Oui, pour l'instant)
			// throw new \Exception("Cannot attach component “$name” (already used)");
		}

		if (is_callable($object)) {
			$this->components[$name] = NULL;
			$this->callbacks[$name] = $object;
			return $this;
		}

		$this->components[$name] = $object;

		return $this;
	}

	public function listComponents() {
        return array_keys($this->components);
	}
}