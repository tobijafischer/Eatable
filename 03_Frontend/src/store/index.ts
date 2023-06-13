import { createStore } from "vuex";
import { eraseCookie } from '@/utils/methods/cookieMethods';
import { resetUserInfo } from '@/utils/methods/userMethods';
import { SingleUser } from '@/types/userTypes';
import { RecipeFilter } from '@/types/recipeTypes';

interface UserAuthObject {
  userId: number;
  authToken: string;
}

export default createStore({
    state: {
      user: {
        loggedIn: null,
        id: null,
        authToken: '',
        userInfo: {} as SingleUser
      },
      filter: {} as RecipeFilter
    },
    mutations: {
      SET_LOGIN_STATE(state, bool) {
        state.user.loggedIn = bool;
      },
      SET_USER_ID(state, id) {
        state.user.id = id;
      },
      SET_AUTH_TOKEN(state, token) {
        state.user.authToken = token;
      },
      SET_USER_INFO(state, userInfos: SingleUser) {
        state.user.userInfo = userInfos;
      },
      SET_FILTERS(state, filterObject: RecipeFilter) {
        state.filter = filterObject;
      },
      SET_FILTER_RECIPE_CATEGORIES(state, categoryArray: Array<number>) {
        state.filter.categories = categoryArray;
      },
      SET_FILTER_RECIPE_TAGS(state, tagArray: Array<number>) {
        state.filter.tags = tagArray;
      },
      SET_FILTER_RECIPE_STOCK(state, stockArray: Array<number>) {
        state.filter.ingredientStock.ingredients = stockArray;
      }
    },
    actions: {
      initiateUserLogin(context: any, userObject: UserAuthObject ) {
        context.commit('SET_USER_ID', userObject.userId);
        context.commit('SET_AUTH_TOKEN', userObject.authToken);
        context.commit('SET_LOGIN_STATE', true);
      },
      initiateUserLogout(context) {
        context.commit('SET_LOGIN_STATE', false);
        context.commit('SET_USER_ID', null);
        context.commit('SET_AUTH_TOKEN', '');
        eraseCookie('sessionToken');
        resetUserInfo();
        console.log('User logged out');
      },
      setUserInfo(context, userInfos: SingleUser) {
        context.commit('SET_USER_INFO', userInfos);
      },
      setFilters(context, filterObject: RecipeFilter) {
        context.commit('SET_FILTERS', filterObject);
      },
      setFilterRecipeCategories(context, categoryArray: Array<number>) {
        context.commit('SET_FILTER_RECIPE_CATEGORIES', categoryArray)
      },
      setFilterRecipeTags(context, tagArray: Array<number>) {
        context.commit('SET_FILTER_RECIPE_TAGS', tagArray)
      },
      setFilterRecipeStock(context, stockArray: Array<number>) {
        context.commit('SET_FILTER_RECIPE_STOCK', stockArray)
      },
    },
    getters: {
      getLoginState(state) {
        return state.user.loggedIn;
      },
      getUserInfo(state) {
        return state.user.userInfo;
      },
      getUserFavoriteShops(state) {
        return state.user.userInfo.favorite_shops;
      },
      getUserFavoriteRecipes(state) {
        return state.user.userInfo.favorite_recipes;
      },
      getFilters(state) {
        return state.filter;
      }
    },
    modules: {},
  });