<template>
  <ion-page>
    <ion-content>
      <ion-router-outlet></ion-router-outlet>
    </ion-content>
    <ion-footer class="search-bar">
      <ion-segment @ionChange="segmentChanged($event)" value="guide">
        <ion-segment-button value="guide">
          <ion-label>Guide</ion-label>
        </ion-segment-button>
        <ion-segment-button value="shops">
          <ion-label>Shops entdecken</ion-label>
        </ion-segment-button>
      </ion-segment>
    </ion-footer>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonRouterOutlet, IonContent, IonFooter, IonLabel, IonSegment, IonSegmentButton } from '@ionic/vue';
import { personAddOutline, radioOutline, restaurantOutline } from 'ionicons/icons';
import router from '@/router';

export default defineComponent({
  name: 'einkaufenTabs',
  components: {
    IonLabel,
    IonContent,
    IonFooter,
    IonPage,
    IonRouterOutlet,
    IonSegment,
    IonSegmentButton,
  },
  data() {
    return {
      personAddOutline,
      radioOutline,
      restaurantOutline,
    };
  },
  methods: {
    segmentChanged(ev: CustomEvent) {
      if (ev.detail.value === 'shops') {
        router.push('/einkaufen/shops');
      } else {
        router.push('/einkaufen/guide');
      }
    },
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