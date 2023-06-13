<?php
class User {
	private $_db,
			$_data;
	public function __construct($user = null) {
		$this->_db = DB::getInstance();
	}
	public function update($fields = array(), $id = null) {
		if (!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}
		if (!$this->_db->update('users', $id, $fields)) {
			throw new Exception('There was a problem.');
		}
	}
	public function create($fields) {
		if (!$this->_db->insert('users', $fields)) {
			throw new Exception("There was a problem.");
		}
	}
	public function find($user = null) {
		if ($user) {
			$field = (is_numeric($user)) ? 'id' : 'email';
			$data = $this->_db->get('users', array($field, '=', $user));
			if ($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}
	public function get($user = null) {
		if ($user) {
			$field = (is_numeric($user)) ? 'id' : 'email';
			$data = $this->_db->action('SELECT id, email, firstname, lastname, username, joined, favorite_shops, favorite_recipes, avatar', 'users', array($field, '=', $user));
			if ($data->count()) {
				$output = $data->first();
				return $output;
			}
		}
		return false;
	}
	public function login($email = null, $password = null) {
		$user = $this->find($email);
		if ($user) {
			if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
				return true;
			} else {
				return 'invalid password';
			}
		} else {
			return 'no user with ' . $email;
		}
	}
	public function hasPermission($key) {
		$group = $this->_db->get('groups', array('id', '=', $this->data()->group));
		if ($group->count()) {
			$permissions = json_decode($group->first()->permissions, true);
			if ($permissions[$key] == true) {
				return true;
			}
		}
		return false;
	}
	public function getFavoriteShops($user = null) {
		if ($user) {
			$data = $this->_db->action('SELECT favorite_shops', 'users', array('id', '=', $user));
			if ($data->count()) {
				$output = $data->first();
				return $output;
			}
		}
		return false;
	}
	public function addFavoriteShop($user = null, $shop = null) {
		if ($user && $shop) {
			$favoriteShops = json_decode($this->getFavoriteShops($user)->favorite_shops);
			if ($favoriteShops == null) {
				$favoriteShops[] = (int) $shop;
				$this->update(
					array(
						'favorite_shops' => json_encode($favoriteShops)
					),
					$user
				);
				return 'Success: Shop added to favorites';
			}
			if (!in_array($shop, $favoriteShops)) {
				$favoriteShops[] = (int) $shop;
				$this->update(
					array(
						'favorite_shops' => json_encode($favoriteShops)
					),
					$user
				);
				return 'Success: Shop added to favorites';
			}
		}
		return false;
	}
	public function removeFavoriteShop($user = null, $shop = null) {
		if ($user && $shop) {
			$favoriteShops = json_decode($this->getFavoriteShops($user)->favorite_shops);
			if(($key = array_search($shop, $favoriteShops)) !== false){
				array_splice($favoriteShops, $key, 1);
				$this->update(
					array(
						'favorite_shops' => json_encode($favoriteShops)
					),
					$user
				);
				return 'Success: Shop removed from favorites';
			}
		}
		return false;
	}
	public function getFavoriteRecipes($user = null) {
		if ($user) {
			$data = $this->_db->action('SELECT favorite_recipes', 'users', array('id', '=', $user));
			if ($data->count()) {
				$output = $data->first();
				return $output;
			}
		}
		return false;
	}
	public function addFavoriteRecipe($user = null, $recipe = null) {
		if ($user && $recipe) {
			$favoriteRecipes = json_decode($this->getFavoriteRecipes($user)->favorite_recipes);
			if ($favoriteRecipes == null) {
				$favoriteRecipes[] = (int) $recipe;
				$this->update(
					array(
						'favorite_recipes' => json_encode($favoriteRecipes)
					),
					$user
				);
				return 'Success: Recipe added to favorites';
			}
			if (!in_array($recipe, $favoriteRecipes)) {
				$favoriteRecipes[] = (int) $recipe;
				$this->update(
					array(
						'favorite_recipes' => json_encode($favoriteRecipes)
					),
					$user
				);
				return 'Success: Recipe added to favorites';
			}
		}
		return false;
	}
	public function removeFavoriteRecipe($user = null, $recipe = null) {
		if ($user && $recipe) {
			$favoriteRecipes = json_decode($this->getFavoriteRecipes($user)->favorite_recipes);
			if(($key = array_search($recipe, $favoriteRecipes)) !== false){
				array_splice($favoriteRecipes, $key, 1);
				$this->update(
					array(
						'favorite_recipes' => json_encode($favoriteRecipes)
					),
					$user
				);
				return 'Success: Recipe removed from favorites';
			}
		}
		return false;
	}
	public function getDefaultDiet($user = null) {
		if ($user) {
			$data = $this->_db->action('SELECT default_diet', 'users', array('id', '=', $user));
			if ($data->count()) {
				$output = $data->first();
				return $output;
			}
		}
		return false;
	}
	public function addDefaultDiet($user = null, $tag = null) {
		if ($user && $tag) {
			$defaultDiet = json_decode($this->getDefaultDiet($user)->default_diet);
			if ($defaultDiet == null) {
				$defaultDiet[] = (int) $tag;
				$this->update(
					array(
						'default_diet' => json_encode($defaultDiet)
					),
					$user
				);
				return 'Success: Tag added to default diet';
			}
			if (!in_array($tag, $defaultDiet)) {
				$defaultDiet[] = (int) $tag;
				$this->update(
					array(
						'default_diet' => json_encode($defaultDiet)
					),
					$user
				);
				return 'Success: Tag added to default diet';
			}
		}
		return false;
	}
	public function removeDefaultDiet($user = null, $tag = null) {
		if ($user && $tag) {
			$defaultDiet = json_decode($this->getDefaultDiet($user)->default_diet);
			if(($key = array_search($tag, $defaultDiet)) !== false){
				array_splice($defaultDiet, $key, 1);
				$this->update(
					array(
						'default_diet' => json_encode($defaultDiet)
					),
					$user
				);
				return 'Success: Tag removed from default diet';
			}
		}
		return false;
	}
	public function getIngredientStock($user = null) {
		if ($user) {
			$data = $this->_db->action('SELECT ingredient_stock', 'users', array('id', '=', $user));
			if ($data->count()) {
				$output = $data->first();
				return $output;
			}
		}
		return false;
	}
	public function addIngredientStock($user = null, $ingredient = null) {
		if ($user && $ingredient) {
			$ingredientStock = json_decode($this->getIngredientStock($user)->ingredient_stock);
			if ($ingredientStock == null) {
				$ingredientStock[] = (int) $ingredient;
				$this->update(
					array(
						'ingredient_stock' => json_encode($ingredientStock)
					),
					$user
				);
				return 'Success: Ingredient added to ingredient stock';
			}
			if (!in_array($ingredient, $ingredientStock)) {
				$ingredientStock[] = (int) $ingredient;
				$this->update(
					array(
						'ingredient_stock' => json_encode($ingredientStock)
					),
					$user
				);
				return 'Success: Ingredient added to ingredient stock';
			}
		}
		return false;
	}
	public function removeIngredientStock($user = null, $ingredient = null) {
		if ($user && $ingredient) {
			$ingredientStock = json_decode($this->getIngredientStock($user)->ingredient_stock);
			if(($key = array_search($ingredient, $ingredientStock)) !== false){
				array_splice($ingredientStock, $key, 1);
				$this->update(array('ingredient_stock' => json_encode($ingredientStock)), $user);
				return 'Success: Ingredient removed from ingredient stock';
			}
		}
		return false;
	}
	public function data() {
		return $this->_data;
	}
}