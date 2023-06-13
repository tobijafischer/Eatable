<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Sammlung Rezepte</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Sammlung Rezepte</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-progress-bar v-if="loading.recipes && userLoggedIn" type="indeterminate"></ion-progress-bar>
      <PlaceholderIndicator
        v-if="!userLoggedIn"
        image="/img/onboarding/eatable-kochen.svg"
        title="Ready zum sammeln?"
        text="Erstelle ein eatable Konto oder logge dich ein, um Rezepte zu speichern."
        linkText="Konto erstellen!"
        link="/profil/registrierung"
        secondaryLinkText="zum Login"
        secondaryLink="/profil/login"
      />
      <p v-else-if="loading.recipes" style="text-align: center;opacity: 0.75; font-size:0.8em;">
        Sammlung wird geladen
      </p>
      <div
        v-else-if="userLoggedIn && favoriteRecipes.length > 0 && typeof favoriteRecipes === 'object'"
        class="card-wrapper"
      >
        <p style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{ `${favoriteRecipes.length} Rezept${favoriteRecipes.length > 1 ? 'e' : ''} gespeichert` }}
        </p>
        <RecipeCard
          v-for="(recipe, index) in favoriteRecipes"
          :key="index"
          :recipe-object="recipe"
          :is-favorite="userFavoriteRecipeIds ? userFavoriteRecipeIds.includes(parseInt(recipe.id)) : false"
        ></RecipeCard>
      </div>
      <PlaceholderIndicator
        v-else
        image="/img/onboarding/eatable-kochen.svg"
        title="Keine Rezepte gefunden"
        text="Du hast noch keine Rezepte zu deiner Sammlung hinzugefügt."
        linkText="Rezepte finden!"
        link="/kochen/rezepte"
      />
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonProgressBar } from '@ionic/vue';
import PlaceholderIndicator from '@/components/PlaceholderIndicator.vue';
import RecipeCard from '@/components/cards/RecipeCard.vue';
import store from '@/store';
// import { createGesture } from '@ionic/vue';
import router from '@/router';
import axios from 'axios';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'SammlungRezepte',
  components: {
    PlaceholderIndicator,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonProgressBar,
    RecipeCard,
  },
  data() {
    return {
      favoriteRecipes: [],
      loading: {
        recipes: true,
      },
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
    userFavoriteRecipeIds: () => {
      return store.getters.getUserFavoriteRecipes;
    },
  },
  watch: {
    userFavoriteRecipeIds: {
      handler(newIdList) {
        if (newIdList && JSON.parse(newIdList).length > 0) {
          this.fetchRecipeData(JSON.parse(newIdList));
        } else {
          this.favoriteRecipes = [];
          this.loading.recipes = false;
        }
      },
      immediate: true,
    },
  },
  // mounted() {
  //   const gesture = createGesture({
  //     el: this.$el,
  //     direction: 'x',
  //     threshold: 15,
  //     gestureName: 'switch-collection-to-rezepte',
  //     onMove: ev => this.onMoveHandler(ev),
  //   });
  //   gesture.enable();
  // },
  methods: {
    onMoveHandler(ev: any) {
      if (ev.deltaX < -200) {
        router.push('/sammlung/shops');
      }
    },
    fetchRecipeData(favoriteRecipesIdArray: Array<number>) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'getRecipesByIds',
          recipesIds: favoriteRecipesIdArray,
        })
        .then(response => {
          if (response.data === false) throw 'wrong recipesIds provided';
          this.favoriteRecipes = response.data;
          this.loading.recipes = false;
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
});
</script>