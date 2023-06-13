<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Vorrat & Filter</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Vorrat & Filter</ion-title>
        </ion-toolbar>
      </ion-header>
      <div class="eatable-content-wrapper">
        <h3>Vorrat</h3>
        <p style="font-size:12px; line-height: 1.5em; opacity: 0.75; margin: 24px 0 48px 0;">
          Bald wird es bei eatable die Möglichkeit geben, vorhandene Zutaten zu speichern. So kann man verhindern, dass
          Lebensmittel vergessen gehen und die Rezeptsuche lässt sich automatisch auf den eigenen Vorrat abstimmen.
        </p>

        <h3>Filter</h3>
        <ion-item>
          <ion-label>Kategorie</ion-label>
          <ion-select
            v-if="categoryFilterActive"
            interface="alert"
            multiple
            ok-text="filtern"
            cancel-text="abbrechen"
            placeholder="auswählen"
            v-model="activeRecipeCategoryIds"
          >
            <ion-select-option
              v-for="category in activeRecipeCategoryData"
              :key="category.id"
              :value="category.id"
              :selected="activeRecipeCategoryIds.includes(JSON.stringify(category.id)) ? true : null"
              >{{ category.title }}</ion-select-option
            >
          </ion-select>
        </ion-item>
        <ion-item>
          <ion-label>Ernährung</ion-label>
          <ion-select
            v-if="tagFilterActive"
            interface="alert"
            multiple
            ok-text="filtern"
            cancel-text="abbrechen"
            placeholder="auswählen"
            v-model="activeRecipeTagIds"
          >
            <ion-select-option
              v-for="tag in activeRecipeTagData"
              :key="tag.id"
              :value="tag.id"
              :selected="activeRecipeTagIds.includes(JSON.stringify(tag.id)) ? true : null"
              >{{ tag.title }}</ion-select-option
            >
          </ion-select>
        </ion-item>

        <!-- <h3>Partner</h3>
        <p v-for="partner in activeRecipePartnerData" :key="partner.id">{{ partner.title }}</p> -->

        <PlaceholderIndicator
          v-if="false"
          title="Diese Seite ist in Erstellung"
          text="Hier entsteht ein Ort, wo man sich selbst an zu verwendende Zutaten erinnern kann (und eine Art Foodwaste Tagebuch hat)."
          linkText="Rezepte finden"
          link="/kochen/rezepte"
        />
      </div>
    </ion-content>
    <ion-footer>
      <ion-toolbar>
        <ion-button expand="block" fill="solid" routerLink="/kochen/rezepte">Rezepte finden</ion-button>
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
  IonFooter,
  IonButton,
  IonItem,
  IonLabel,
  IonSelect,
  IonSelectOption,
} from '@ionic/vue';
import PlaceholderIndicator from '@/components/PlaceholderIndicator.vue';
import { SingleRecipePartner, SingleRecipeTag, SingleRecipeCategory } from '@/types/TaxonomyTypes';
import axios from 'axios';
import store from '@/store';
import { RecipeFilter } from '@/types/recipeTypes';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'KochenVorrat',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonButton,
    IonFooter,
    IonItem,
    IonLabel,
    IonSelect,
    IonSelectOption,
    PlaceholderIndicator,
  },
  data() {
    return {
      // activeRecipePartnerIds: [1],
      activeRecipePartnerData: [] as Array<SingleRecipePartner>,
      tagFilterActive: false,
      activeRecipeTagIds: [] as Array<string>,
      activeRecipeTagData: [] as Array<SingleRecipeTag>,
      categoryFilterActive: false,
      activeRecipeCategoryIds: [] as Array<string>,
      activeRecipeCategoryData: [] as Array<SingleRecipeCategory>,
      activeRecipeFilters: {
        ingredientStock: {
          filterInclude: false,
          ingredients: [393, 351, 49],
        },
        categories: [],
        tags: [],
      } as RecipeFilter,
    };
  },
  watch: {
    activeRecipeTagIds: {
      handler(newVal) {
        const numberArray = newVal.map((id: string) => parseInt(id));
        this.activeRecipeFilters.tags = numberArray;
      },
    },
    activeRecipeCategoryIds: {
      handler(newVal) {
        const numberArray = newVal.map((id: string) => parseInt(id));
        this.activeRecipeFilters.categories = numberArray;
      },
    },
    activeRecipeFilters: {
      handler(newVal) {
        this.$eatableStorage.set('recipeFilter', JSON.stringify(newVal));
        store.dispatch('setFilters', newVal);
      },
      deep: true,
    },
  },
  mounted() {
    this.getRecipeCategories();
    this.getRecipeTags();

    this.$eatableStorage
      .get('recipeFilter')
      .then(response => {
        if (!response) return;

        const recipeFilterObject = JSON.parse(response);

        this.activeRecipeCategoryIds = recipeFilterObject.categories.map((id: number) => id.toString());
        this.activeRecipeTagIds = recipeFilterObject.tags.map((id: number) => id.toString());
      })
      .catch(error => {
        console.error(error);
      });
  },
  methods: {
    getRecipeTags() {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `0,1000000`,
          orderby: 'title',
          searchTable: 'tags',
          searchColumn: 'id',
          sort: 'ASC',
          term: '',
        })
        .then(response => {
          this.activeRecipeTagData = response.data;
          this.tagFilterActive = true;
        })
        .catch(error => {
          console.error(error);
        });
    },
    getRecipeCategories() {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `0,1000000`,
          orderby: 'title',
          searchTable: 'categories',
          searchColumn: 'id',
          sort: 'ASC',
          term: '',
        })
        .then(response => {
          this.activeRecipeCategoryData = response.data;
          this.categoryFilterActive = true;
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
});
</script>
<style scoped>
.eatable-content-wrapper {
  padding: 24px 16px;
}
ion-footer ion-toolbar {
  --background: var(--ion-background-color);
  --border-color: transparent;
  box-shadow: 0px 0px 20px 20px var(--ion-background-color);
}
ion-item {
  --padding-start: 0;
}
</style>