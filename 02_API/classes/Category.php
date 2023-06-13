<?php
class Category {
	private $_db,
			$_data,
			$_sessionName,
			$_cookieName;
	public function __construct($category = null) {
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');
		if ($category) {
			$this->find($category);
		}
	}
	public function categories() {
		$data = $this->_db->action('SELECT *', 'categories', array('active', '=','1'));
		if ($data->count()) {
			return $data->_results;
		}
		return false;
	}
	public function get_by_ids($ids = null) {
		$ids = implode(',', $ids);
		$data = $this->_db->query('SELECT * FROM categories WHERE id IN (' . $ids . ') AND active = ?', array(1));	
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