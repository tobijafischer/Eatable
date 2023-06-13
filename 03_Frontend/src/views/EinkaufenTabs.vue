<template>
  <ion-page>
    <ion-content>
      <ion-tabs>
        <ion-router-outlet></ion-router-outlet>
        <ion-tab-bar slot="bottom">
          <ion-tab-button tab="guide" href="/einkaufen/guide">
            <!-- <ion-icon :icon="radioOutline" /> -->
            <ion-icon class="o-guide-1" />
            <ion-label>zum Guide</ion-label>
          </ion-tab-button>

          <ion-tab-button tab="shops" href="/einkaufen/shops">
            <!-- <ion-icon :icon="restaurantOutline" /> -->
            <ion-icon class="o-store-1" />
            <ion-label>Shops entdecken</ion-label>
          </ion-tab-button>
        </ion-tab-bar>
      </ion-tabs>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonTabs, IonTabBar, IonTabButton, IonLabel, IonIcon, IonPage, IonContent, IonRouterOutlet } from '@ionic/vue';
import { personAddOutline, radioOutline, restaurantOutline } from 'ionicons/icons';

export default defineComponent({
  name: 'EinkaufenTabs',
  components: {
    IonLabel,
    IonTabBar,
    IonContent,
    IonTabs,
    IonTabButton,
    IonIcon,
    IonPage,
    IonRouterOutlet,
  },
  data() {
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