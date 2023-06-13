<template>
  <ion-page :data-klimaranking="getKlimaScoreRanking(recipe.co2_emission)">
    <ion-header>
      <ion-toolbar>
        <ion-title>{{ recipe.title }}</ion-title>
        <ion-buttons slot="start">
          <ion-back-button default-href="/kochen/rezepte" text="zurück">
            <!-- <ion-icon name="arrow-back-outline"></ion-icon> -->
            <!-- <ion-icon class="o-angle-left-1" fill="solid" slot="start"></ion-icon> -->
            <!-- <ion-label>Zurück</ion-label> -->
          </ion-back-button>
        </ion-buttons>
        <ion-buttons slot="end">
          <ion-button class="like-heart" @click="favoriteRecipeToggle($event)" :data-recipeid="recipe.id">
            <ion-icon
              v-if="userFavoriteRecipeIds && JSON.parse(userFavoriteRecipeIds).includes(recipe.id)"
              class="o-heart-2"
              fill="solid"
              slot="icon-only"
            ></ion-icon>
            <ion-icon v-else class="o-heart-1" fill="solid" slot="icon-only"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">{{ recipe.title }}</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-img v-if="recipe.main_image" :src="`${tblVars.baseCdnUrl}/img/recipe/${recipe.main_image}`"></ion-img>

      <div id="recipe-single-content">
        <h3 v-if="recipe.co2_emission > 0 || parseInt(recipe.water_balance) > 0">Nachhaltigkeit</h3>
        <p v-if="recipe.co2_emission !== 0">
          Emissionen:
          <strong>{{ recipe.co2_emission }}</strong
          ><span style="font-size: 0.75em;"> g CO2 / Portion</span>
        </p>
        <p v-if="recipe.co2_emission && klimaPercent" style="font-size:12px; line-height: 1.5em; opacity: 0.75;">
          Dieses Rezept produziert pro Portion
          <strong>{{ Math.abs(klimaPercent) }}% {{ klimaPercent > 0 ? 'mehr' : 'weniger' }} CO2</strong> als ein
          durchschnittliches Rezept.
        </p>
        <hr v-if="parseInt(recipe.water_balance) >= 1" style="background: var(--ion-color-step-100);" />
        <p v-if="parseInt(recipe.water_balance) >= 1">
          Wasserverbrauch:
          <strong>{{ Math.round(parseInt(recipe.water_balance)) }}</strong>
          <span style="font-size: 0.75em;"> Liter knappes Wasser</span>
        </p>
        <p v-if="recipe.water_balance && waterPercent" style="font-size:12px; line-height: 1.5em; opacity: 0.75;">
          Dieses Rezept benötigt pro Portion
          <strong>{{ Math.abs(waterPercent) }}% {{ waterPercent > 0 ? 'mehr' : 'weniger' }} knappes Wasser</strong>
          als ein durchschnittliches Rezept.
        </p>

        <h3 v-if="recipe.duration_active > 0 || recipe.nutrition_value > 0 || recipe.vita_score > 0">
          Dauer & Gesundheit
        </h3>
        <p v-if="recipe.duration_active !== 0">
          Kochzeit:
          <strong>{{ recipe.duration_active }} min</strong>
          <span v-if="recipe.duration_passive !== 0" style="font-size: 0.75em;">
            (+{{ recipe.duration_passive }} passiv)</span
          >
        </p>
        <hr v-if="recipe.duration_active !== 0" style="background: var(--ion-color-step-100);" />
        <p v-if="recipe.nutrition_value !== 0">
          Nährwert:
          <strong>{{ recipe.nutrition_value }}</strong
          ><span style="font-size: 0.75em;"> kcal / Portion</span>
        </p>
        <hr v-if="recipe.nutrition_value !== 0" style="background: var(--ion-color-step-100);" />
        <p v-if="recipe.vita_score >= 1">
          Gesundheit:
          <strong>{{ Math.round(recipe.vita_score) }}</strong>
          <span style="font-size: 0.75em;"> Risikopunkte</span>
        </p>
        <p v-if="recipe.vita_score && vitaPercent" style="font-size:12px; line-height: 1.5em; opacity: 0.75;">
          Dieses Rezept hat
          <strong>{{ Math.abs(vitaPercent) }}% {{ vitaPercent > 0 ? 'mehr' : 'weniger' }} Risikopunkte</strong>
          und ist somit {{ vitaPercent > 0 ? 'unterdurchschnittlich' : 'überdurchschnittlich' }} gesund.
        </p>

        <!-- <h3 v-if="recipePartnerInfo && recipePartnerInfo.title">Partner</h3>
        <a v-if="recipePartnerInfo && recipePartnerInfo.title" @click="openInAppBrowser(recipePartnerInfo.url)">{{
          recipePartnerInfo.title
        }}</a> -->

        <!-- <h3 v-if="Object.values(JSON.parse(recipe.categories)).length > 0">Kategorie</h3>
        <p v-for="category in Object.values(JSON.parse(recipe.categories))" :key="category">{{ category }}</p> -->

        <!-- <h3 v-if="Object.values(JSON.parse(recipe.tags)).length > 0">
          {{ Object.values(JSON.parse(recipe.tags)).length === 1 ? 'Schlagwort' : 'Schlagwörter' }}
        </h3>
        <p v-for="tag in Object.values(JSON.parse(recipe.tags))" :key="tag">{{ tag }}</p> -->
        <h3 v-if="ingredientInfoList.length > 0">Zutaten</h3>
        <ion-chip
          v-for="(ingredient, index) in ingredientInfoList"
          :key="ingredient.id"
          :data-klimaranking="getKlimaScoreRanking(ingredient.co2_16fu_ohne_flug)"
          @click="openIngredientModal(ingredient)"
        >
          <ion-label
            >{{ ingredient.title }}
            <span style="font-size:.8em;"
              >({{ ingredientList[index].quantity }} {{ ingredientList[index].unit }})</span
            ></ion-label
          >
        </ion-chip>
      </div>
    </ion-content>
    <ion-footer v-if="recipe.external_url">
      <ion-toolbar>
        <ion-button expand="block" fill="solid" @click="openInAppBrowser(recipe.external_url)">
          zum Rezept
          <!-- <ion-icon
            style="margin-left: 16px;"
            class="o-external-link-1"
            fill="var(--ion-color-tertiary, red)"
            slot="end"
          ></ion-icon> -->
        </ion-button>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  modalController,
  alertController,
  IonPage,
  IonContent,
  IonHeader,
  IonFooter,
  IonToolbar,
  IonTitle,
  IonImg,
  IonChip,
  IonLabel,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
} from '@ionic/vue';
import axios from 'axios';
import store from '@/store';
import { openInAppBrowser } from '@/utils/methods/capacitorMethods';
import { SingleRecipe } from '@/types/recipeTypes';
import { SingleIngredient, RecipeIngredient } from '@/types/ingredientTypes';
import { SingleRecipePartner } from '@/types/TaxonomyTypes';
import IngredientModal from '@/components/modals/IngredientModal.vue';
import router from '@/router';
import { getCookie } from '@/utils/methods/cookieMethods';
import { validateUser, setUserInfo } from '@/utils/methods/userMethods';
import { openToastOptions } from '@/utils/ionCustomHelpers';
import { ToastColor } from '@/types/ionicTypes';
import {
  getKlimaScoreRanking,
  calculateKlimaPercent,
  calculateWaterPercent,
  calculateVitaPercent,
} from '@/utils/methods/scoreMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  components: {
    IonPage,
    IonContent,
    IonHeader,
    IonFooter,
    IonToolbar,
    IonTitle,
    IonImg,
    IonChip,
    IonLabel,
    IonButtons,
    IonButton,
    IonBackButton,
    IonIcon,
  },
  data() {
    return {
      currentSlug: this.$route.params.slug,
      recipe: {} as SingleRecipe,
      ingredientInfoList: [] as Array<SingleIngredient>,
      recipePartnerInfo: {} as SingleRecipePartner,
      tblVars,
    };
  },
  mounted() {
    this.fetchRecipe(this.currentSlug);
    // this.fetchRecipeBySlug(this.currentSlug);
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
    ingredientList(): Array<RecipeIngredient> {
      if (this.recipe.ingredients) {
        const ingredientObject = JSON.parse(this.recipe.ingredients);
        return Object.values(ingredientObject);
      } else {
        return [];
      }
    },
    userFavoriteRecipeIds: () => {
      return store.getters.getUserFavoriteRecipes;
    },
    klimaPercent(): number | null {
      if (!this.recipe || this.recipe.co2_emission === 0) return null;
      return calculateKlimaPercent(this.recipe.co2_emission);
    },
    waterPercent(): number | null {
      if (!this.recipe || parseInt(this.recipe.water_balance) < 1) return null;
      return calculateWaterPercent(parseInt(this.recipe.water_balance));
    },
    vitaPercent(): number | null {
      if (!this.recipe || this.recipe.vita_score < 1) return null;
      return calculateVitaPercent(this.recipe.vita_score);
    },
  },
  watch: {
    ingredientList(ingredientArray: Array<RecipeIngredient>) {
      const ingredientIdArray = ingredientArray.map(async (ingredient: RecipeIngredient) => {
        const ingredientInfoList = await axios
          .post(`${tblVars.baseApiUrl}/index.php`, {
            action: 'getIngredientInfo',
            ingredientId: ingredient.id,
          })
          .then(response => {
            return response.data;
          })
          .catch(error => {
            console.error(error);
          });
        return ingredientInfoList;
      });
      Promise.all(ingredientIdArray)
        .then(results => {
          const completeIngredientList = results.map((ingredient, index) => {
            // temporarily eased the following operator != because old ingredient id was a string, new one is a number
            if (ingredientArray[index].id != ingredient.id) throw 'ingredient lists could not be matched';

            return {
              ...ingredientArray[index],
              ...ingredient,
            };
          });
          this.ingredientInfoList = completeIngredientList;
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
  methods: {
    // fetchRecipeBySlug(slug: string | string[]) {
    //   axios.post(`${tblVars.baseApiUrl}/index.php`, {
    //     action: 'getBySlug',
    //     slug: slug,
    //     table: 'recipes'
    //   })
    //   .then( response => {
    //     console.log(response);
    //   })
    //   .catch( error => {
    //     console.error(error);
    //   })
    // },
    openInAppBrowser,
    getKlimaScoreRanking,
    fetchRecipe(slug: string | string[]) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'getBySlug',
          slug: slug,
          table: 'recipes',
        })
        .then(response => {
          if (response.data && typeof response.data !== 'string') {
            this.recipe = response.data;
            if (response.data.partner) {
              this.getRecipePartner(response.data.partner);
            }
          } else if (typeof slug === 'string' && !isNaN(parseInt(slug))) {
            // adding support for recipe id as slugs
            axios
              .post('https://app.eatable.ch/index.php', {
                action: 'getSingleRecipe',
                recipeId: slug,
              })
              .then(response => {
                if (response.data && typeof response.data !== 'string') {
                  this.recipe = response.data;
                  if (response.data.partner) {
                    this.getRecipePartner(response.data.partner);
                  }
                } else {
                  this.$router.push('/404?source=recipe'); // no recipe found – redirect to 404 page
                }
              })
              .catch(error => {
                console.log(error);
              });
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    async openIngredientModal(ingredientObject: SingleIngredient) {
      const modal = await modalController.create({
        component: IngredientModal,
        cssClass: 'ingredient-modal',
        componentProps: {
          ingredient: ingredientObject,
          klimaRanking: ingredientObject.co2_16fu_ohne_flug
            ? this.getKlimaScoreRanking(ingredientObject.co2_16fu_ohne_flug)
            : '',
        },
      });
      return modal.present();
    },
    getRecipePartner(id: string) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `0,1`,
          orderby: 'title',
          searchTable: 'partners',
          searchColumn: 'id',
          sort: 'ASC',
          term: id,
        })
        .then(response => {
          this.recipePartnerInfo = response.data[0];
        })
        .catch(error => {
          console.error(error);
        });
    },
    favoriteRecipeToggle(event: any) {
      event.stopPropagation();
      if (!this.userLoggedIn) {
        this.userLoginPrompt();
      }
      const sessionToken = getCookie('sessionToken');
      if (!sessionToken) return;

      validateUser(sessionToken).then(userId => {
        let ajaxAction;
        let toastTitle = 'Zur Sammlung hinzugefügt';
        let toastColor: ToastColor = 'success';
        if (this.userFavoriteRecipeIds && JSON.parse(this.userFavoriteRecipeIds).includes(this.recipe.id)) {
          ajaxAction = 'removeUserFavoriteRecipe';
          toastTitle = 'Aus deiner Sammlung entfernt';
          toastColor = 'tertiary';
        } else {
          ajaxAction = 'addUserFavoriteRecipe';
        }
        axios
          .post(`${tblVars.baseApiUrl}/index.php`, {
            headers: {
              authorization: sessionToken,
            },
            action: ajaxAction,
            userId: userId,
            favoriteRecipe: this.recipe.id,
          })
          .then(() => {
            setUserInfo(sessionToken, userId);
            openToastOptions(toastTitle, toastColor, 2000, 'top');
          })
          .catch(error => {
            console.error(error);
          });
      });
    },
    async userLoginPrompt() {
      const alert = await alertController.create({
        header: 'Es freut uns, dass dir dieses Rezept gefällt!',
        message: 'Melde dich an, um es in deiner Sammlung zu speichern...',
        buttons: [
          {
            text: 'Konto erstellen',
            role: 'register',
            handler: () => router.push('/profil/registrierung'),
          },
          {
            text: 'zum Login',
            role: 'login',
            handler: () => router.push('/profil/login'),
          },
          {
            text: 'abbrechen',
            role: 'cancel',
          },
        ],
      });
      return alert.present();
    },
  },
});
</script>
<style scoped>
ion-chip[data-klimaranking='A'],
div.ion-page[data-klimaranking='A'] {
  --klimascore-background: var(--klimascore-a-bg);
}
ion-chip[data-klimaranking='B'],
div.ion-page[data-klimaranking='B'] {
  --klimascore-background: var(--klimascore-b-bg);
}
ion-chip[data-klimaranking='C'],
div.ion-page[data-klimaranking='C'] {
  --klimascore-background: var(--klimascore-c-bg);
}
ion-chip[data-klimaranking='D'],
div.ion-page[data-klimaranking='D'] {
  --klimascore-background: var(--klimascore-d-bg);
}
ion-chip[data-klimaranking='E'],
div.ion-page[data-klimaranking='E'] {
  --klimascore-background: var(--klimascore-e-bg);
}
ion-chip {
  background: var(--klimascore-background);
}
/* ion-footer ion-toolbar {
  --background: var(--ion-background-color);
  --border-color: var(--ion-color-secondary);
  box-shadow: 0px 0px 20px 20px var(--ion-background-color);
} */
ion-header {
  --background: var(--klimascore-background, var(--ion-color-light));
  --ion-background-color: var(--klimascore-background, var(--ion-color-light));
  --ion-toolbar-background: var(--klimascore-background, var(--ion-color-light));
}
ion-content {
  --background: linear-gradient(180deg, var(--klimascore-background, var(--ion-color-light)), #fff9ee);
}
ion-footer ion-toolbar {
  --background: var(--ion-color-tertiary, var(--ion-background-color));
  --border-color: var(--ion-color-tertiary, var(--ion-background-color));
  box-shadow: 0px 0px 20px 20px var(--ion-color-tertiary, var(--ion-background-color));
}
#recipe-single-content {
  padding: 0px 16px 24px 16px;
}
#recipe-single-content h3 {
  margin-top: 32px;
  margin-bottom: 16px;
}
</style>