<template>
  <ion-card
    :routerLink="`/kochen/rezepte/${recipeObject.slug}`"
    :data-klimaRanking="getKlimaScoreRanking(recipeObject.co2_emission)"
  >
    <ion-button class="like-heart" @click="favoriteRecipeToggle($event)" :data-recipeid="recipeObject.id">
      <ion-icon v-if="isFavorite" class="o-heart-2" fill="solid" slot="icon-only"></ion-icon>
      <ion-icon v-else class="o-heart-1" fill="solid" slot="icon-only"></ion-icon>
    </ion-button>
    <ion-img :src="`${tblVars.baseCdnUrl}/img/recipe/${recipeObject.main_image}`"></ion-img>
    <ion-card-header>
      <ion-card-subtitle v-if="recipeObject.ingredientOverlap && recipeObject.ingredientOverlap.length">
        {{ recipeObject.ingredientOverlap.length }} Zutat{{
          recipeObject.ingredientOverlap.length === 1 ? '' : 'en'
        }}
        aus Vorrat verwendet</ion-card-subtitle
      >
      <ion-card-title>{{ recipeObject.title }}</ion-card-title>
    </ion-card-header>

    <ion-card-content>
      <p v-if="recipeObject.duration_active !== 0">
        Kochzeit:
        <strong>{{ recipeObject.duration_active }} min</strong>
        <span v-if="recipeObject.duration_passive !== 0" style="font-size: 0.75em;">
          (+{{ recipeObject.duration_passive }} passiv)</span
        >
      </p>
      <p v-if="recipeObject.nutrition_value != 0">
        Nährwert:
        <strong>{{ recipeObject.nutrition_value }}</strong
        ><span style="font-size: 0.75em;"> kcal / Portion</span>
      </p>
      <p v-if="recipeObject.co2_emission !== 0">
        Emissionen:
        <strong>{{ recipeObject.co2_emission }}</strong
        ><span style="font-size: 0.75em;"> g CO2 / Portion</span>
      </p>
      <!-- <p v-if="parseInt(recipeObject.water_balance) >= 1">
        Wasserverbrauch:
        <strong>{{ Math.round(parseInt(recipeObject.water_balance)) }}</strong>
        <span style="font-size: 0.75em;"> Liter knappes Wasser</span>
      </p> -->
      <h4 v-if="recipeObject.co2_emission !== 0">{{ klimaPercent }}%</h4>
      <h4 v-else>?</h4>
      <ion-grid v-if="false">
        <ion-row>
          <ion-col v-if="recipeObject.rating"> rating: {{ recipeObject.rating }} </ion-col>
          <ion-col v-if="recipeObject.nutrition_value"> nutrition: {{ recipeObject.nutrition_value }} </ion-col>
          <ion-col v-if="recipeObject.external_url"> url: {{ recipeObject.external_url }} </ion-col>
        </ion-row>
      </ion-grid>
    </ion-card-content>
    <ion-button expand="block" fill="solid">Zum Rezept</ion-button>
  </ion-card>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  alertController,
  IonGrid,
  IonRow,
  IonCol,
  IonCard,
  IonCardHeader,
  IonCardSubtitle,
  IonCardTitle,
  IonCardContent,
  IonButton,
  IonIcon,
  IonImg,
} from '@ionic/vue';
import store from '@/store';
import axios from 'axios';
import router from '@/router';
import { getCookie } from '@/utils/methods/cookieMethods';
import { validateUser, setUserInfo } from '@/utils/methods/userMethods';
import { SingleRecipe } from '@/types/recipeTypes';
import { openToastOptions } from '@/utils/ionCustomHelpers';
import { ToastColor } from '@/types/ionicTypes';
import { getKlimaScoreRanking, calculateKlimaPercent } from '@/utils/methods/scoreMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'RecipeCard',
  components: {
    IonGrid,
    IonRow,
    IonCol,
    IonCard,
    IonCardHeader,
    IonCardSubtitle,
    IonCardTitle,
    IonCardContent,
    IonButton,
    IonIcon,
    IonImg,
  },
  props: {
    recipeObject: {
      type: Object as () => SingleRecipe,
      required: true,
    },
    isFavorite: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      tblVars,
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
    klimaPercent() {
      if (!this.recipeObject || this.recipeObject.co2_emission === 0) return null;
      return calculateKlimaPercent(this.recipeObject.co2_emission);
    },
  },
  methods: {
    getKlimaScoreRanking,
    favoriteRecipeToggle(event: any) {
      event.stopPropagation();
      if (!this.userLoggedIn) {
        this.userLoginPrompt();
      }
      const targetId = event.currentTarget.dataset.recipeid;
      const sessionToken = getCookie('sessionToken');
      if (!sessionToken) return;

      validateUser(sessionToken).then(userId => {
        let ajaxAction;
        let toastTitle = 'Zur Sammlung hinzugefügt';
        let toastColor: ToastColor = 'success';
        if (this.isFavorite) {
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
            favoriteRecipe: targetId,
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
ion-card {
  --klimascore-background: var(--ion-color-tertiary);
}
ion-card[data-klimaRanking='A'] {
  --klimascore-background: var(--klimascore-a-bg);
}
ion-card[data-klimaRanking='B'] {
  --klimascore-background: var(--klimascore-b-bg);
}
ion-card[data-klimaRanking='C'] {
  --klimascore-background: var(--klimascore-c-bg);
}
ion-card[data-klimaRanking='D'] {
  --klimascore-background: var(--klimascore-d-bg);
}
ion-card[data-klimaRanking='E'] {
  --klimascore-background: var(--klimascore-e-bg);
}
ion-card[data-klimaRanking] {
  /* --background: linear-gradient(0deg, var(--ion-color-tertiary), var(--klimascore-background) 65%); */
  --background: linear-gradient(0deg, var(--klimascore-background), var(--ion-color-tertiary) 65%);
}

/* Heart Icon */
ion-card ion-button.like-heart {
  position: absolute;
  z-index: 1;
  top: 0;
  right: -4px;
  background-color: transparent;
  visibility: visible;
  --background: none;
  --box-shadow: none;
  --background-activated: transparent;
  --background-hover: transparent;
  /* --ripple-color: transparent; */
  --color-activated: var(--ion-color-light);
  color: var(--ion-card-background, var(--ion-color-light));
}
ion-card-content {
  padding-bottom: 0;
}
ion-card ion-img {
  width: 100%;
  height: 175px;
  object-fit: cover;
}
ion-card h4 {
  font-size: 18px;
  margin-top: 8px;
  text-align: right;
}
ion-card ion-button[expand='block'] {
  margin: 16px 0 0 0;
  --border-radius: 0;
}
</style>
<style>
.eatable-toast {
  text-align: center;
  justify-content: center;
}
</style>