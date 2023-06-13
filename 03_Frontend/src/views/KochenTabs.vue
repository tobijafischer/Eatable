<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">
        <ion-tab-button tab="vorrat" href="/kochen/vorrat">
          <ion-icon class="o-fridge-1" />
          <ion-label>Vorrat & Filter</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="rezepte" href="/kochen/rezepte">
          <ion-icon class="o-invoice-1" />
          <ion-label>Rezepte finden</ion-label>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonTabs, IonTabBar, IonTabButton, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import { personAddOutline, radioOutline, restaurantOutline } from 'ionicons/icons';

export default defineComponent({
  name: 'KochenTabs',
  components: {
    IonLabel,
    IonTabBar,
    IonTabs,
    IonTabButton,
    IonIcon,
    IonPage,
    IonRouterOutlet,
  },
  setup() {
    return {
      personAddOutline,
      radioOutline,
      restaurantOutline,
    };
  },
});
</script>
<style scoped>
ion-tab-bar {
  --ion-tab-bar-color: var(--ion-color-primary-tint);
  --ion-safe-area-bottom: 0;
}
ion-icon {
  font-size: 18px;
  padding-bottom: 3px;
}
ion-tab-button.tab-selected ion-label {
  font-weight: 600;
  color: var(--ion-color-primary);
}
</style>