<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Kochen</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Kochen</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-progress-bar v-if="loading.recipes" type="indeterminate"></ion-progress-bar>
      <div class="card-wrapper">
        <p v-if="loading.recipes" style="text-align: center;opacity: 0.75; font-size:0.8em;">
          Rezepte werden geladen
        </p>
        <p v-else-if="recipeEntriesFiltered.length < 1" style="text-align: center;">
          Keine passenden Rezepte gefunden...
        </p>
        <p v-else style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{ `${recipeEntriesFiltered.length} Rezept${recipeEntriesFiltered.length > 1 ? 'e' : ''} gefunden` }}
        </p>
        <RecipeCard
          v-for="(recipe, index) in recipeEntriesFiltered.slice(0, numberOfDisplayedRecipes)"
          :key="index"
          :recipe-object="recipe"
          :is-favorite="userFavoriteRecipeIds ? userFavoriteRecipeIds.includes(recipe.id) : false"
        ></RecipeCard>

        <ion-infinite-scroll
          @ionInfinite="loadMoreRecipes(10, $event)"
          threshold="100px"
          id="infinite-scroll"
          :disabled="infiniteScrollFinished"
        >
          <ion-infinite-scroll-content loading-spinner="bubbles" loading-text="Mehr Rezepte werden geladen...">
          </ion-infinite-scroll-content>
        </ion-infinite-scroll>
        <div style="height: 24px;"></div>
        <h6 v-if="infiniteScrollFinished && recipeEntriesFiltered.length > 0" class="infinite-loader-finished">
          Alle verfügbaren Rezepte wurden geladen
        </h6>
      </div>
    </ion-content>
    <ion-footer class="search-bar">
      <ion-toolbar>
        <ion-item>
          <ion-label v-if="false" position="floating">Nach Rezept / Zutaten suchen</ion-label>
          <ion-input placeholder="Nach Rezept (oder Zutat) suchen" v-model.lazy="recipeSearchTerm"></ion-input>
          <ion-icon class="o-search-1" slot="end"></ion-icon>
          <!-- <div id="recipe-filter-button" slot="end">
            <ion-icon class="o-settings-1"></ion-icon>
            <ion-badge>1</ion-badge>
          </div> -->
        </ion-item>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonInfiniteScroll,
  IonInfiniteScrollContent,
  IonInput,
  IonLabel,
  IonItem,
  IonIcon,
  IonFooter,
  IonProgressBar,
} from '@ionic/vue';
import RecipeCard from '@/components/cards/RecipeCard.vue';
import axios from 'axios';
import store from '@/store';
// import { getCookie } from '@/utils/methods/cookieMethods';
// import { validateUser } from '@/utils/methods/userMethods';
import { SingleRecipe, RecipeFilter } from '@/types/recipeTypes';
import { SingleIngredient } from '@/types/ingredientTypes';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'KochenRezepte',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    IonInput,
    IonLabel,
    IonItem,
    IonIcon,
    IonFooter,
    IonProgressBar,
    RecipeCard,
  },
  data() {
    return {
      infiniteScrollFinished: false,
      recipeEntries: [] as Array<SingleRecipe>,
      recipeEntriesFiltered: [] as Array<SingleRecipe>,
      timeout: null as any,
      debouncedSearchTerm: '',
      recipeSearchedIngredients: [] as Array<number>,
      numberOfDisplayedRecipes: 20,
      loading: {
        recipes: true,
      },
    };
  },
  mounted() {
    this.recipeResults();
  },
  computed: {
    userFavoriteRecipeIds: () => {
      return store.getters.getUserFavoriteRecipes;
    },
    recipeSorting: function() {
      return store.getters.getFilters;
    },
    recipeSearchTerm: {
      get(): string {
        return this.debouncedSearchTerm;
      },
      set(val: string) {
        if (this.timeout) clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
          this.debouncedSearchTerm = val;
        }, 250);
      },
    },
  },
  watch: {
    debouncedSearchTerm(value) {
      setTimeout(() => {
        this.filterRecipeEntries(this.recipeEntries, value);
      }, 100);
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `0, 1000`,
          orderby: 'title',
          searchTable: 'ingredients',
          searchColumn: 'title',
          sort: 'ASC',
          term: value,
        })
        .then(response => {
          this.recipeSearchedIngredients = response.data.map((ingredient: SingleIngredient) => ingredient.id);
        })
        .catch(error => {
          console.error(error);
        });
    },
    recipeSorting: {
      handler() {
        this.filterRecipeEntries(this.recipeEntries, this.debouncedSearchTerm);
      },
      deep: true,
    },
  },
  methods: {
    recipeResults(quantity = '1000', offset = '0') {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `${offset},${quantity}`,
          orderby: 'title',
          searchTable: 'recipes',
          searchColumn: 'title',
          sort: 'ASC',
          term: '',
        })
        .then(response => {
          this.recipeEntries = response.data;
          this.filterRecipeEntries(this.recipeEntries, this.debouncedSearchTerm);
        })
        .catch(error => {
          console.error(error);
        });
    },
    loadMoreRecipes(quantity = 20, event: any) {
      this.numberOfDisplayedRecipes += quantity;

      if (this.numberOfDisplayedRecipes > this.recipeEntries.length) {
        this.numberOfDisplayedRecipes = this.recipeEntries.length;
        this.infiniteScrollFinished = true; // no more infinite loading happening
      } else {
        const infiniteScroll: any = document.getElementById('infinite-scroll');
        if (infiniteScroll) {
          event.target.complete(); // infinite scroll finished, ready for new triggering
        }
      }
    },
    filterRecipeEntries(initialRecipeArray: Array<SingleRecipe>, searchTerm: string) {
      if (!this.recipeSorting || Object.values(this.recipeSorting).length < 1) {
        this.setFilteredRecipeArray(initialRecipeArray, searchTerm);

        this.$eatableStorage
          .get('recipeFilter')
          .then(response => {
            const filterObject = JSON.parse(response);
            store.dispatch('setFilters', filterObject);
          })
          .catch(error => {
            console.error(error);
          });
      } else {
        const recipeFilterObject: RecipeFilter = this.recipeSorting;
        const stockIncluded = recipeFilterObject.ingredientStock.filterInclude;
        const stockIngredients = recipeFilterObject.ingredientStock.ingredients;
        const recipeFilterCategories = recipeFilterObject.categories;
        const recipeFilterTags = recipeFilterObject.tags;

        const ingredientsFilteredArray = initialRecipeArray;
        if (stockIncluded && stockIngredients.length > 0) {
          ingredientsFilteredArray.forEach(recipe => {
            // check (& filter) for user ingredient stock
            const ingredientArray = JSON.parse(recipe.ingredients).map((ingredient: any) => ingredient.id);
            const intersectingIngredients = ingredientArray.filter((id: string) =>
              stockIngredients.includes(parseInt(id))
            );
            recipe.ingredientOverlap = intersectingIngredients;
          });
        }

        const taxonomiesFilteredArray = ingredientsFilteredArray.filter(recipe => {
          if (
            (!recipe.ingredientOverlap || recipe.ingredientOverlap.length < 1) &&
            stockIncluded &&
            stockIngredients.length > 0
          )
            return false;
          // check (& filter) for filtered recipe categories
          if (!recipe.categories) return false;
          if (recipeFilterCategories.length > 0) {
            const filteredCategories = recipeFilterCategories.some(currentValue => {
              const categoryArrays = JSON.parse(recipe.categories);
              return categoryArrays.includes(currentValue);
            });
            if (!filteredCategories) return false;
          }

          if (!recipe.tags) return false;
          if (recipeFilterTags.length > 0) {
            // check (& filter) for filtered recipe tags
            const filteredTags = recipeFilterTags.every(currentValue => JSON.parse(recipe.tags).includes(currentValue));
            if (!filteredTags) return false;
          }

          return true;
        });
        this.setFilteredRecipeArray(taxonomiesFilteredArray, searchTerm);
      }
    },
    setFilteredRecipeArray(filteredArray: any, searchTerm: string) {
      const finalArray: Array<SingleRecipe> = filteredArray.filter((recipe: SingleRecipe) => {
        if (searchTerm) {
          const titleBool = recipe.title.toLowerCase().includes(searchTerm.toLowerCase());

          const recipeIngredientArray = JSON.parse(recipe.ingredients).map((ingredient: any) =>
            parseInt(ingredient.id)
          );
          const ingredientBool = this.recipeSearchedIngredients.some(ingredientId => {
            return recipeIngredientArray.includes(ingredientId);
          });
          return titleBool || ingredientBool;
        } else {
          return true;
        }
      });

      this.recipeEntriesFiltered = finalArray.sort((a, b) => {
        if (searchTerm) {
          if (
            a.title.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !b.title.toLowerCase().includes(searchTerm.toLowerCase())
          )
            return -1;
          if (
            !a.title.toLowerCase().includes(searchTerm.toLowerCase()) &&
            b.title.toLowerCase().includes(searchTerm.toLowerCase())
          )
            return 1;
        }
        if (a.ingredientOverlap === b.ingredientOverlap) {
          if (a.co2_emission === 0) {
            return 1;
          } else if (b.co2_emission === 0) {
            return -1;
          } else {
            return a.co2_emission - b.co2_emission;
          }
        } else if (a.ingredientOverlap && b.ingredientOverlap) {
          return a.ingredientOverlap.length - b.ingredientOverlap.length;
        } else {
          if (a.co2_emission === 0) {
            return 1;
          } else if (b.co2_emission === 0) {
            return -1;
          } else {
            return a.co2_emission - b.co2_emission;
          }
        }
      });
      this.loading.recipes = false;
    },
  },
});
</script>
<style scoped>
/* COLOR CODING */
/* ion-card {
  --background: linear-gradient(0deg, var(--klimascore-background, red), var(--ion-color-secondary) 50%);
} */

/* #recipe-filter-button {
  position: relative;
  overflow: visible;
  margin: 7px;
  font-size: 1.6em;
  display: inline-block;
  width: 1em;
  height: 1em;
  box-sizing: content-box !important;
}
#recipe-filter-button ion-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  font-size: 10px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 0 1px 0;
} */

/* material / android styles */
/* .footer-md::before {
  opacity: 0;
} */

.infinite-loader-finished {
  grid-column: 1 / -1;
  margin: 16px 0;
  text-align: center;
}
</style>