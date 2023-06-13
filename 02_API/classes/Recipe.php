<?php
class Recipe {
	private $_db,
			$_data,
			$_sessionName,
			$_cookieName;
	public function __construct($recipe = null) {
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');
		if ($recipe) {
			$this->find($recipe);
		}
	}
	public function recipes() {
		$data = $this->_db->action('SELECT *', 'recipes', array('active', '=','1'));
		if ($data->count()) {
			return $data->_results;
		}
		return false;
	}
	public function get_by_ids($ids = null) {
		$ids = implode(',', $ids);
		$data = $this->_db->query('SELECT * FROM recipes WHERE id IN (' . $ids . ') AND active = ?', array(1));	
		if ($data->count()) {
			return $data->_results;
		} else {
			return 'Error: No entries';
		}
	}
	public function data() {
		return $this->_data;
	}
}