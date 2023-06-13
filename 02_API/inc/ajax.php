<?php

	// some defaults
	$output = 'default';
	$limit = null;
	if (isset($input->limit)) {
		$limit = $input->limit;
	} else {
		$limit = '0,100000';
	}
	if (isset($input->order_by)) {
		$order_by = $input->order_by;
	} else {
		$order_by = '';
	}
	if (isset($input->sort)) {
		$sort = $input->sort;
	} else {
		$sort = '';
	}
	// $headersCheck = isset($input->headers->authorization);
	$headersCheck = true;
	$category = new Category;
	$discovery = new Discovery;
	$ingredient = new Ingredient;
	$recipe = new Recipe;
	$tag = new Tag;
	$token = new Token;
	$user = new User;

	// check check, one two...
	switch ($input->action) {

		// test
		case 'test':
			$output = 'test';
			break;
		// user
		case 'userValidate':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$user_id = $token->get($authToken)->user_id;
					$output = (int) $user_id;
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'login':
			$emailCheck = isset($input->email);
			$passwordCheck = isset($input->password);
			if ($headersCheck && $input->headers->authorization !== '' && $emailCheck && $passwordCheck) {
				// set set
				$authToken = escape($input->headers->authorization);
				$email = trim(escape($input->email));
				$password = escape($input->password);
				// check login
				$login = $user->login($email, $password);
				// update token
				if ($login === true) {
					$row = $token->getByUserId($user->get($email)->id);
					$tokenId = $row->id;
					// $token->update($tokenId, $authToken);
					$token->create(array(
						'token' => $authToken,
						'user_id' => $tokenId
					));
					$output = (int) $row->user_id;
				} else {
					die($output = $login);
				}
			}
			break;
		case 'register':
			$emailCheck = isset($input->email);
			$passwordCheck = isset($input->password);
			if ($headersCheck && $input->headers->authorization !== '' && $emailCheck && $passwordCheck) {
				// set set
				$authToken = escape($input->headers->authorization);
				$email = trim(escape($input->email));
				$firstname = trim(escape($input->firstname));
				$lastname = trim(escape($input->lastname));
				$password = trim(escape($input->password));
				$username = trim(escape($input->username));
				$input = (array) $input;
				// validate
				$validateUser = new ValidateUser;
				$validation = $validateUser->check($input, array(
					'email' => array(
						'required' => true,
						'min' => 2,
						'max' => 50,
						'unique' => 'users'
					),
					'password' => array(
						'required' => true,
						'min' => 6
					)
				));
				if ($validation->passed()) {
					$salt = Hash::salt(32);
					try {
						// create user
						$user->create(array(
							'firstname' => $firstname,
							'lastname' => $lastname,
							'email' => $email,
							'username' => $username,
							'password' => Hash::make($password, $salt),
							'salt' => $salt,
							'joined' => date('Y-m-d H:i:s')
						));
						// create token
						$token->create(array(
							'token' => $authToken,
							'user_id' => $user->get($email)->id
						));
						$output = array(
							'success' => (object) array('user registered'),
							'userId' => (int) $user->get($email)->id
						);
					} catch (Exception $e) {
						die($e->getMessage());
					}
				} else {
					$output = array(
						'errors' => (array) $validation->errors()
					);
				}
			}
			break;
		case 'getUser':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					if(isset($input->userId)) {
						$userId = escape($input->userId);
						$output = (object) $user->get($userId);
					} else {
						$output = 'Error: No User ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'deleteToken':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->delete($authToken)) {
					$output = 'Success: token deleted';
				} else {
					$output = 'Error: invalid token';
				}
			}
			break;
		case 'setUserDefaultLocation':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$defaultLocation = $input->defaultLocation;
					if ($userId) {
						$defaultLocation = $input->defaultLocation;
						if (!$user->update(array('default_location' => $defaultLocation), $userId)) {
							$output = 'Success: default_location updated';
						}
					} else {
						$output = 'Error: No User ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'addUserFavoriteShop':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$favoriteShop = escape($input->favoriteShop);
					if ($userId && $favoriteShop) {
						$output = $user->addFavoriteShop($userId, $favoriteShop);
					} else {
						$output = 'Error: No User or Shop ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'removeUserFavoriteShop':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$favoriteShop = escape($input->favoriteShop);
					if ($userId && $favoriteShop) {
						$output = $user->removeFavoriteShop($userId, $favoriteShop);
					} else {
						$output = 'Error: No User or Shop ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'addUserFavoriteRecipe':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$favoriteRecipe = escape($input->favoriteRecipe);
					if ($userId && $favoriteRecipe) {
						$output = $user->addFavoriteRecipe($userId, $favoriteRecipe);
					} else {
						$output = 'Error: No User or Recipe ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'removeUserFavoriteRecipe':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$favoriteRecipe = escape($input->favoriteRecipe);
					if ($userId && $favoriteRecipe) {
						$output = $user->removeFavoriteRecipe($userId, $favoriteRecipe);
					} else {
						$output = 'Error: No User or Recipe ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'addUserDefaultDiet':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$tagId = escape($input->tagId);
					if ($userId && $tagId) {
						$output = $user->addDefaultDiet($userId, $tagId);
					} else {
						$output = 'Error: No User or Tag ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'removeUserDefaultDiet':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$tagId = escape($input->tagId);
					if ($userId && $tagId) {
						$output = $user->removeDefaultDiet($userId, $tagId);
					} else {
						$output = 'Error: No User or Tag ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'addUserIngredientStock':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$ingredientId = escape($input->ingredientId);
					if ($userId && $ingredientId) {
						$output = $user->addIngredientStock($userId, $ingredientId);
					} else {
						$output = 'Error: No User or Tag ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		case 'removeUserIngredientStock':
			if ($headersCheck && $input->headers->authorization !== '') {
				$authToken = escape($input->headers->authorization);
				if ($token->exists($authToken)) {
					$userId = escape($input->userId);
					$ingredientId = escape($input->ingredientId);
					if ($userId && $ingredientId) {
						$output = $user->removeIngredientStock($userId, $ingredientId);
					} else {
						$output = 'Error: No User or Tag ID provided';
					}
				} else {
					die('invalid session token');
				}
			}
			break;
		// discoveries
		case 'getDiscoveries':
			$output = $discovery->discoveries();
			break;
		case 'getDiscoveriesByIds':
			$discoveriesIds = $input->discoveriesIds;
			if (count($discoveriesIds)) {
				$output = $discovery->get_by_ids($discoveriesIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// shops
		case 'getShops':
			$shop = new Shop;
			$output = $shop->shops($limit, $order_by, $sort);
			break;
		case 'getShopsByIds':
			$shopsIds = $input->shopsIds;
			if (count($shopsIds)) {
				$shop = new Shop;
				$output = $shop->get_by_ids($shopsIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// recipes
		case 'getRecipes':
			$output = $recipe->recipes();
			break;
		case 'getRecipesByIds':
			$recipesIds = $input->recipesIds;
			if (count($recipesIds)) {
				$output = $recipe->get_by_ids($recipesIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// ingredients
		case 'getIngredients':
			$output = $ingredient->ingredients();
			break;
		case 'getIngredientsByIds':
			$ingredientsIds = $input->ingredientsIds;
			if (count($ingredientsIds)) {
				$output = $ingredient->get_by_ids($ingredientsIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// tags
		case 'getTags':
			$output = $tag->tags();
			break;
		case 'getTagsByIds':
			$tagsIds = $input->tagsIds;
			if (count($tagsIds)) {
				$output = $tag->get_by_ids($tagsIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// categories
		case 'getCategories':
			$output = $category->categories();
			break;
		case 'getCategoriesByIds':
			$categoriesIds = $input->categoriesIds;
			if (count($categoriesIds)) {
				$output = $category->get_by_ids($categoriesIds);
			} else {
				$output = 'Error: No valid data provided';
			}
			break;
		// misc
		case 'getBySlug':
			if ($input->table && $input->slug) {
				$output = DB::getInstance()->get($input->table, array('slug', '=', $input->slug))->first();
			} else {
				$output = 'Error: No valid data provided';
			}
		break;
		case 'bigSearch':
			$orderby = trim(escape($input->orderby));
			$searchTable = trim(escape($input->searchTable));
			$searchColumn = trim(escape($input->searchColumn));
			$sort = trim(escape($input->sort));
			$term = trim($input->term);
			$output = DB::getInstance()->query('SELECT * FROM ' . $searchTable . ' WHERE INSTR(' . $searchColumn . ', ?) AND active = 1 ORDER BY ' . $orderby . ' ' . $sort . ' LIMIT ' . $limit, array($term))->_results;
		break;

		case 'searchRecipes':
			$column = trim(escape($input->orderby));
			$sort = trim(escape($input->sort));
			$term = '%' . trim(escape($input->term)) . '%';
			$output = DB::getInstance()->query('SELECT * FROM `recipes` WHERE CONCAT_WS(`|`,title,external_url) LIKE ?  ORDER BY ' . $column . ' ' . $sort . ' LIMIT ' . $limit, array($term))->_results;
		break;
		case 'getSingleRecipe':
				$recipeIdCheck = isset($input->recipeId);
				if($recipeIdCheck) {
					$recipeId = escape($input->recipeId);
					$output = (object) DB::getInstance()->get('recipes', array('id', '=', $recipeId))->first();
				} else {
					$output = 'Error: Couldn\'t resolve recipe ID';
				}
			break;
		case 'getSingleShop':
				$shopIdCheck = isset($input->shopId);
				if($shopIdCheck) {
					$shopId = escape($input->shopId);
					$output = (object) DB::getInstance()->get('shops', array('id', '=', $shopId))->first();
				} else {
					$output = 'Error: Couldn\'t resolve shop ID';
				}
			break;
		case 'getIngredientInfo':
			$ingredientCheck = isset($input->ingredientId);
			if($ingredientCheck) {
				$ingredientId = escape($input->ingredientId);
				$output = DB::getInstance()->action('SELECT *', 'ingredients', array('id', '=', $ingredientId))->first();
			} else {
				$output = 'Error: Couldn\'t resolve ingredient ID';
			}
			break;
		default:
			break;
	}
	die(json_encode($output));

