<?php
class Token {
	private $_db;
	public function __construct($token = null) {
		$this->_db = DB::getInstance();
	}
	public function create($fields) {
		if (!$this->_db->insert('users_token', $fields)) {
			throw new Exception("There was a problem.");
		}
	}
	public function update($id = null, $token = array()) {
		$data = $this->_db->update('users_token', $id, array('token' => $token));
		return $data;
	}
	public function delete($token = null) {
		$data = $this->_db->action('DELETE ', 'users_token', array('token', '=', $token));
		return $data;
	}
	public function exists($token = null) {
		$data = $this->_db->action('SELECT *', 'users_token', array('token', '=', $token))->_results;
		return (!empty($data)) ? true : false;
	}
	public function get($token = null) {
		$data = $this->_db->action('SELECT *', 'users_token', array('token', '=', $token))->first();
		return $data;
	}
	public function getByUserId($user_id = null) {
		$data = $this->_db->action('SELECT *', 'users_token', array('user_id', '=', $user_id))->first();
		return $data;
	}
}