<?php 
class ValidateUser {
	private $_passed = false,
			$_errors = array(),
			$_db = null;
	public function __construct() {
		$this->_db = DB::getInstance();
	}
	public function check($source, $items = array()) {
		foreach ($items as $item => $rules) {
			foreach ($rules as $rule => $rule_value) {
				$value = $source[$item];
				$item = escape($item);
				if ($rule === 'required' && empty($value)) {
					$field = '';
					switch ($item) {
						case 'email':
							$field = 'E-Mail';
							break;
						case 'firstname':
							$field = 'Vorname';
							break;
						case 'lastname':
							$field = 'Nachname';
							break;
						case 'username':
							$field = 'Benutzername';
							break;
						case 'password':
							$field = 'Passwort';
							break;						
						default:
							break;
					}
					$this->addError("{$field}");
				} else if (!empty($value)) {
					switch ($rule) {
						case 'min':
							if (strlen($value) < $rule_value) {
								$this->addError("{$item} must be minimum of {$rule_value} characters");
							}
							break;
						case 'max':
							if (strlen($value) > $rule_value) {
								$this->addError("{$item} must be maximum of {$rule_value} characters");
							}
							break;
						case 'matches':
							if ($value != $source[$rule_value]) {
								$this->addError("{$rule_value} must match {$item}");
							}
							break;
						// case 'required':
						// 	if (empty($value)) {
						// 		$this->addError("{$item} required");
						// 	}
						// 	break;
						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if ($check->count()) {
								$this->addError("{$item} already exists");
							}
							break;
						default:
						break;
					}
				}

			}
		}
		if (empty($this->_errors)) {
			$this->_passed = true;
		}
		return $this;
	}
	private function addError($error) {
		$this->_errors[] = $error;
	}
	public function errors() {
		return $this->_errors;
	}
	public function passed() {
		return $this->_passed;
	}
}