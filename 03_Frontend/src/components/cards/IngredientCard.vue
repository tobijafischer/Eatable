<template>
  <ion-item
    :data-id="ingredientObject.id"
    :data-klimaScore="ingredientObject.co2_16fu_ohne_flug"
    :data-klimaRanking="getKlimaScoreRanking(ingredientObject.co2_16fu_ohne_flug)"
    @click="openIngredientModal(ingredientObject)"
  >
    <h3 style="display: inline-block">{{ ingredientObject.title }}</h3>
    <p v-if="ingredientObject.co2_16fu_ohne_flug === 0" style="display: inline-block; margin: 8px 0 0 8px;">
      keine CO2 Daten...
    </p>
    <p v-else style="display: inline-block; margin: 8px 0 0 8px;">{{ ingredientObject.co2_16fu_ohne_flug }}g CO2</p>
    <h4 slot="end">{{ klimaPercent ? `${klimaPercent}%` : `?` }}</h4>
  </ion-item>
</template>
<script lang="ts">
import { defineComponent } from 'vue';
import { modalController, IonItem } from '@ionic/vue';
import IngredientModal from '@/components/modals/IngredientModal.vue';
import { SingleIngredient } from '@/types/ingredientTypes';
import { getKlimaScoreRanking, calculateKlimaPercent } from '@/utils/methods/scoreMethods';

export default defineComponent({
  name: 'IngredientCard',
  props: {
    ingredientObject: {
      type: Object as () => SingleIngredient,
      required: true,
    },
  },
  components: {
    IonItem,
  },
  computed: {
    klimaPercent() {
      const ingredient = this.ingredientObject;
      if (!ingredient) return null;
      const co2 = ingredient.co2_16fu_ohne_flug;
      if (co2 === null || co2 === 0) return null;
      return calculateKlimaPercent(co2);
    },
  },
  methods: {
    getKlimaScoreRanking,
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
  },
});
</script>
<style scoped>
ion-item[data-klimaRanking='A'] {
  --klimascore-background: var(--klimascore-a-bg);
}
ion-item[data-klimaRanking='B'] {
  --klimascore-background: var(--klimascore-b-bg);
}
ion-item[data-klimaRanking='C'] {
  --klimascore-background: var(--klimascore-c-bg);
}
ion-item[data-klimaRanking='D'] {
  --klimascore-background: var(--klimascore-d-bg);
}
ion-item[data-klimaRanking='E'] {
  --klimascore-background: var(--klimascore-e-bg);
}
ion-item {
  --inner-border-width: 0;
  --background: var(--ion-color-tertiary);
  --background: linear-gradient(
    90deg,
    var(--ion-color-tertiary) 50%,
    var(--klimascore-background, var(--ion-color-tertiary))
  );
  --border-radius: 8px;
  margin: 8px 16px;
  cursor: pointer;
}
ion-item h3 {
  font-size: 18px;
  line-height: 1.4em;
}
ion-item p {
  color: var(--ion-color-step-500);
  font-size: 14px;
}
</style>