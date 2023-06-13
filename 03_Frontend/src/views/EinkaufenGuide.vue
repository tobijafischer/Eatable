<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Guide</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Guide</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-progress-bar v-if="loading.ingredients" type="indeterminate"></ion-progress-bar>
      <div class="card-wrapper">
        <p v-if="loading.ingredients" style="text-align: center;opacity: 0.75; font-size:0.8em;">
          Zutaten werden geladen
        </p>
        <p v-else-if="ingredientEntriesSorted.length < 1" style="text-align: center;">
          Keine Zutaten gefunden...
        </p>
        <p v-else style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{ `${ingredientEntriesSorted.length} Zutat${ingredientEntriesSorted.length > 1 ? 'en' : ''} gefunden` }}
        </p>

        <IngredientCard
          v-for="(ingredient, index) in ingredientEntriesSorted.slice(0, numberOfDisplayedIngredients)"
          :key="index"
          :ingredient-object="ingredient"
        ></IngredientCard>

        <ion-infinite-scroll
          @ionInfinite="loadMoreIngredients(25, $event)"
          threshold="250px"
          id="infinite-scroll"
          :disabled="infiniteScrollFinished"
        >
          <ion-infinite-scroll-content loading-spinner="bubbles" loading-text="Zutaten werden geladen...">
          </ion-infinite-scroll-content>
        </ion-infinite-scroll>
      </div>
    </ion-content>
    <ion-footer class="search-bar">
      <ion-toolbar>
        <ion-item>
          <ion-label v-if="false" position="floating">Nach Zutat suchen</ion-label>
          <ion-input placeholder="Nach Zutat suchen" v-model.lazy="ingredientSearchTerm"></ion-input>
          <ion-icon class="o-search-1" slot="end"></ion-icon>
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
  IonProgressBar,
  IonFooter,
  IonItem,
  IonLabel,
  IonInput,
  IonIcon,
} from '@ionic/vue';
// import EinkaufenTabs from '@/views/EinkaufenTabs.vue';
import IngredientCard from '@/components/cards/IngredientCard.vue';
import axios from 'axios';
import { SingleIngredient } from '@/types/ingredientTypes';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'EinkaufenGuide',
  components: {
    IonPage,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    IngredientCard,
    IonProgressBar,
    IonFooter,
    IonItem,
    IonLabel,
    IonInput,
    IonIcon,
  },
  data() {
    return {
      tblVars,
      infiniteScrollFinished: false,
      ingredientEntries: [] as Array<SingleIngredient>,
      ingredientEntriesSorted: [] as Array<SingleIngredient>,
      numberOfDisplayedIngredients: 20,
      directLinkIngredient: null as number | null,
      ingredientSearchTerm: '',
      loading: {
        ingredients: true,
      },
    };
  },
  mounted() {
    this.getIngredientsData('10000');
    if (this.$route.query.s) {
      if (typeof this.$route.query.s === 'string') {
        this.ingredientSearchTerm = this.$route.query.s;
      }
    }
  },
  watch: {
    ingredientEntries() {
      this.sortIngredientEntries(this.ingredientEntries);
      // console.log(this.ingredientEntriesSorted);
    },
    ingredientSearchTerm() {
      // router.push({ query: { s: value }})
      this.sortIngredientEntries(this.ingredientEntries);
    },
  },
  methods: {
    getIngredientsData(quantity = '16', offset = '0') {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `${offset},${quantity}`,
          orderby: 'title',
          searchTable: 'ingredients',
          searchColumn: 'title',
          sort: 'ASC',
          term: '',
        })
        .then(response => {
          this.ingredientEntries = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },
    loadMoreIngredients(quantity = 25, event: any) {
      this.numberOfDisplayedIngredients += quantity;

      if (this.numberOfDisplayedIngredients > this.ingredientEntries.length) {
        this.numberOfDisplayedIngredients = this.ingredientEntries.length;
        this.infiniteScrollFinished = true; // no more infinite loading happening
      } else {
        const infiniteScroll: HTMLElement | null = document.getElementById('infinite-scroll');
        if (infiniteScroll) {
          event.target.complete(); // infinite scroll finished, ready for new triggering
        }
      }
    },
    sortIngredientEntries(initialIngredientArray: Array<SingleIngredient>) {
      const newIngredientArray = initialIngredientArray.sort((a, b) => {
        if (!a.co2_16fu_ohne_flug || a.co2_16fu_ohne_flug === 0) return 1;
        if (!b.co2_16fu_ohne_flug || b.co2_16fu_ohne_flug === 0) return -1;

        return a.co2_16fu_ohne_flug - b.co2_16fu_ohne_flug;
      });

      this.ingredientEntriesSorted = newIngredientArray.filter(ingredient => {
        if (ingredient.title.toLowerCase().includes(this.ingredientSearchTerm.toLowerCase())) {
          return true;
        }
      });
      this.loading.ingredients = false;
    },
  },
});
</script>