<template>
  <ion-header :data-klimaRanking="klimaRanking">
    <ion-toolbar>
      <ion-title>{{ ingredient.title }}</ion-title>
      <ion-buttons slot="end" style="--padding-end: 0;">
        <ion-button @click="dismissCurrentModal()">
          <!-- <ion-icon :icon="closeOutline" /> -->
          <ion-icon class="o-close-1" style="font-size: 48px;line-height: 0.8em;" />
        </ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>
  <ion-content class="ion-padding" :data-klimaRanking="klimaRanking">
    <ion-img
      v-if="ingredient.main_image !== 'ingredient-default.jpg'"
      :src="`${tblVars.baseCdnUrl}/img/ingredient/${ingredient.main_image}`"
    ></ion-img>
    <div id="zutaten-infos">
      <h2 v-if="ingredient.description || ingredient.nutrition_value">Allgemeine Informationen</h2>
      <p v-if="ingredient.description">{{ ingredient.description }}</p>
      <p v-if="ingredient.nutrition_value">
        Nutrition: <strong>{{ ingredient.nutrition_value }}</strong>
      </p>
      <h2 v-if="ingredient.co2_16fu_ohne_flug > 0 || ingredient.g_co2_100g_ohne_flug > 0">
        Nachhaltigkeit
      </h2>
      <p v-if="ingredient.co2_16fu_ohne_flug > 0" style="line-height: 1.5em;">
        Emissionen:
        <strong>{{ ingredient.co2_16fu_ohne_flug }}</strong
        ><span style="font-size: 0.75em;"> g CO2 / 1.6 Food Unit</span>
      </p>
      <p
        v-if="ingredient.co2_16fu_ohne_flug > 0 && klimaPercent"
        style="font-size:12px; line-height: 1.5em; opacity: 0.75;"
      >
        Diese Zutat produziert
        <strong>{{ Math.abs(klimaPercent) }}% {{ klimaPercent > 0 ? 'mehr' : 'weniger' }} CO2</strong> als eine
        durchschnittliche Zutat. Es wird von einem Flugzeug-freien Transport ausgegangen.
      </p>
      <p
        v-if="ingredient.g_co2_100g_ohne_flug && ingredient.g_co2_100g_ohne_flug !== '0'"
        style="font-size: 0.75em;opacity: 0.75;"
      >
        Für 100g {{ ingredient.title }} werden ca. {{ Math.round(ingredient.g_co2_100g_ohne_flug) }}g CO2 benötigt.
      </p>
      <h2>Saisonalität</h2>
      <div v-if="ingredientSeason">
        <div style="overflow-x: auto;">
          <table>
            <tbody>
              <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mai</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Aug</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Dez</th>
              </tr>
              <tr>
                <td
                  :class="ingredientSeason.january > 0 ? `in-season-${ingredientSeason.january}` : 'out-of-season'"
                ></td>
                <td
                  :class="ingredientSeason.february > 0 ? `in-season-${ingredientSeason.february}` : 'out-of-season'"
                ></td>
                <td :class="ingredientSeason.march > 0 ? `in-season-${ingredientSeason.march}` : 'out-of-season'"></td>
                <td :class="ingredientSeason.april > 0 ? `in-season-${ingredientSeason.april}` : 'out-of-season'"></td>
                <td :class="ingredientSeason.may > 0 ? `in-season-${ingredientSeason.may}` : 'out-of-season'"></td>
                <td :class="ingredientSeason.june > 0 ? `in-season-${ingredientSeason.june}` : 'out-of-season'"></td>
                <td :class="ingredientSeason.july > 0 ? `in-season-${ingredientSeason.july}` : 'out-of-season'"></td>
                <td
                  :class="ingredientSeason.august > 0 ? `in-season-${ingredientSeason.august}` : 'out-of-season'"
                ></td>
                <td
                  :class="ingredientSeason.september > 0 ? `in-season-${ingredientSeason.september}` : 'out-of-season'"
                ></td>
                <td
                  :class="ingredientSeason.october > 0 ? `in-season-${ingredientSeason.october}` : 'out-of-season'"
                ></td>
                <td
                  :class="ingredientSeason.november > 0 ? `in-season-${ingredientSeason.november}` : 'out-of-season'"
                ></td>
                <td
                  :class="ingredientSeason.december > 0 ? `in-season-${ingredientSeason.december}` : 'out-of-season'"
                ></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <p v-else style="margin-bottom: 0; line-height: 1.5em;">
        Saisonalität ist bei {{ ingredient.title }} irrelevant<br /><span style="font-size: 0.75em;opacity: 0.75;">
          (oder uns fehlen zur Zeit noch zuverlässige Daten)</span
        >
      </p>
    </div>
    <p id="fehler-melden">
      Du hast einen Fehler in unseren Daten entdeckt? Dann melde dich <a href="mailto:hello@eatable.ch">hier</a>
    </p>
  </ion-content>
</template>
<script lang="ts">
import {
  modalController,
  IonContent,
  IonHeader,
  IonTitle,
  IonToolbar,
  IonButton,
  IonIcon,
  IonButtons,
  IonImg,
} from '@ionic/vue';
import { defineComponent } from 'vue';
import { SingleIngredient, IngredientSeason } from '@/types/ingredientTypes';
import axios from 'axios';
import { calculateKlimaPercent } from '@/utils/methods/scoreMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'IngredientModal',
  components: {
    IonContent,
    IonHeader,
    IonTitle,
    IonToolbar,
    IonButton,
    IonIcon,
    IonButtons,
    IonImg,
  },
  props: {
    ingredient: {
      type: Object as () => SingleIngredient,
      required: true,
    },
    klimaRanking: {
      type: String,
    },
  },
  data() {
    return {
      ingredientSeason: null as IngredientSeason | null,
      tblVars,
    };
  },
  computed: {
    klimaPercent(): number | null {
      if (!this.ingredient) return null;
      const co2 = this.ingredient.co2_16fu_ohne_flug;
      if (!co2 || co2 === 0) return null;
      return calculateKlimaPercent(co2);
    },
  },
  mounted() {
    this.checkSeasonality(this.ingredient.id);
  },
  methods: {
    async dismissCurrentModal() {
      const modal = await modalController.dismiss();
      return modal;
    },
    checkSeasonality(ingredientId: string | number) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `0,10`,
          orderby: 'ingredient_id',
          searchTable: 'ingredients_season',
          searchColumn: 'ingredient_id',
          sort: 'ASC',
          term: ingredientId,
        })
        .then(response => {
          if (typeof response.data === 'object' && response.data.length > 0) {
            response.data.forEach((ingredientResponse: IngredientSeason) => {
              if (ingredientResponse.ingredient_id === this.ingredient.id) {
                this.ingredientSeason = ingredientResponse;
              }
            });
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    // async openExplanationModal() {
    //   const modal = await modalController.create({
    //     component: ScoreExplanationModal,
    //     cssClass: 'explanation-modal',
    //     swipeToClose: true,
    //   });
    //   return modal.present();
    // },
  },
});
</script>
<style scoped>
ion-header[data-klimaRanking='A'],
ion-content[data-klimaRanking='A'] {
  --klimascore-background: var(--klimascore-a-bg);
}
ion-header[data-klimaRanking='B'],
ion-content[data-klimaRanking='B'] {
  --klimascore-background: var(--klimascore-b-bg);
}
ion-header[data-klimaRanking='C'],
ion-content[data-klimaRanking='C'] {
  --klimascore-background: var(--klimascore-c-bg);
}
ion-header[data-klimaRanking='D'],
ion-content[data-klimaRanking='D'] {
  --klimascore-background: var(--klimascore-d-bg);
}
ion-header[data-klimaRanking='E'],
ion-content[data-klimaRanking='E'] {
  --klimascore-background: var(--klimascore-e-bg);
}
ion-toolbar {
  --ion-toolbar-background: var(--klimascore-background, var(--ion-color-step-50));
  --border-color: var(--ion-color-primary);
}
ion-content {
  padding: 24px;
  --background: linear-gradient(180deg, var(--klimascore-background), var(--ion-color-tertiary) 50%);
}
div#zutaten-infos {
  padding: 24px;
}
div#zutaten-infos h2:not(:first-of-type) {
  margin-top: 36px;
}
ion-img {
  max-width: 120px;
  margin: 24px auto;
}
p#fehler-melden {
  font-size: 12px;
  text-align: center;
  max-width: 300px;
  margin: 48px auto 12px auto;
  line-height: 1.7em;
  opacity: 0.75;
}
th {
  padding: 0.5em 0.75em;
}
td {
  background-color: var(--klimascore-b-bg, var(--ion-color-tertiary));
  padding: 1em 0.75em;
}
td.in-season-1 {
  background-color: var(--klimascore-a-bg);
}
td.in-season-2 {
  background-color: #c4e3b5;
}
</style>