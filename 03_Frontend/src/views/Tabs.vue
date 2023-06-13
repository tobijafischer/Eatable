<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">
        <ion-tab-button tab="entdecken" href="/entdecken">
          <!-- <ion-icon :icon="radioOutline" /> -->
          <!-- <span data-icon="&#xfb44;"></span> -->
          <ion-icon class="o-radar-1" />
          <ion-label>Entdecken</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="kochen" href="/kochen">
          <!-- <ion-icon :icon="restaurantOutline" /> -->
          <!-- <span data-icon="&#xfb30;"></span> -->
          <ion-icon class="o-cooking-pot-1" />
          <ion-label>Kochen</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="einkaufen" href="/einkaufen">
          <!-- <ion-icon :icon="storefrontOutline" /> -->
          <ion-icon class="o-basket-1" />
          <ion-label>Einkaufen</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="sammlung" href="/sammlung">
          <!-- <ion-icon :icon="heartOutline" /> -->
          <ion-icon class="o-heart-1" />
          <ion-label>Sammlung</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="profil" href="/profil">
          <!-- <ion-icon v-if="loggedInComputed" :icon="personOutline" />
          <ion-icon v-else :icon="personAddOutline" /> -->
          <ion-icon v-if="loggedInComputed" class="o-user-1" />
          <ion-icon v-else class="o-detective-1" />
          <ion-label>Profil</ion-label>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script lang="ts">
import { computed, defineComponent } from 'vue';
import { IonTabBar, IonTabButton, IonTabs, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import {
  heartOutline,
  personAddOutline,
  personOutline,
  radioOutline,
  restaurantOutline,
  storefrontOutline,
} from 'ionicons/icons';
import store from '@/store';

export default defineComponent({
  name: 'Tabs',
  components: { IonLabel, IonTabs, IonTabBar, IonTabButton, IonIcon, IonPage, IonRouterOutlet },
  setup() {
    const loggedInComputed = computed(() => {
      return store.getters.getLoginState;
    });

    return {
      heartOutline,
      personAddOutline,
      personOutline,
      radioOutline,
      restaurantOutline,
      storefrontOutline,
      loggedInComputed,
    };
  },
  computed: {
    currentUserInfo() {
      return store.getters.getUserInfo;
    },
  },
});
</script>

<style scoped>
ion-tab-bar {
  --ion-tab-bar-color: var(--ion-color-primary-tint);
}
ion-icon {
  font-size: 18px;
  padding-bottom: 3px;
}
ion-tab-button.tab-selected {
  pointer-events: none;
}
ion-tab-button.tab-selected ion-label {
  font-weight: 600;
  color: var(--ion-color-primary);
}
@media screen and (max-width: 411px) {
  ion-tab-button ion-label {
    font-size: 10px;
    padding-top: 3px;
  }
}
</style>