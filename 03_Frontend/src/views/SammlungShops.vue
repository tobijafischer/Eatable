<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Sammlung Shops</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Sammlung Shops</ion-title>
        </ion-toolbar>
      </ion-header>

      <ion-progress-bar v-if="loading.shops && userLoggedIn" type="indeterminate"></ion-progress-bar>
      <PlaceholderIndicator
        v-if="!userLoggedIn"
        image="/img/onboarding/eatable-einkaufen.svg"
        title="Ready zum sammeln?"
        text="Erstelle ein eatable Konto oder logge dich ein, um Shops zu speichern."
        linkText="Konto erstellen!"
        link="/profil/registrierung"
        secondaryLinkText="zum Login"
        secondaryLink="/profil/login"
      />
      <p v-else-if="loading.shops" style="text-align: center;opacity: 0.75; font-size:0.8em;">
        Sammlung wird geladen
      </p>
      <div
        v-else-if="userLoggedIn && userFavoriteShopIds && favoriteShops.length > 0 && typeof favoriteShops === 'object'"
        class="card-wrapper"
      >
        <p style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{ `${favoriteShops.length} Shop${favoriteShops.length > 1 ? 's' : ''} gespeichert` }}
        </p>
        <ShopCard
          v-for="(shop, index) in favoriteShops"
          :key="index"
          :shop-object="shop"
          :is-favorite="userFavoriteShopIds ? userFavoriteShopIds.includes(parseInt(shop.id)) : false"
        ></ShopCard>
      </div>
      <PlaceholderIndicator
        v-else
        image="/img/onboarding/eatable-einkaufen.svg"
        title="Keine Shops gefunden"
        text="Du hast noch keine Shops zu deiner Sammlung hinzugefügt."
        linkText="Shops entdecken!"
        link="/einkaufen/shops"
      />
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonProgressBar } from '@ionic/vue';
import PlaceholderIndicator from '@/components/PlaceholderIndicator.vue';
import store from '@/store';
// import { createGesture } from '@ionic/vue';
import router from '@/router';
import axios from 'axios';
import ShopCard from '@/components/cards/ShopCard.vue';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'SammlungShops',
  components: {
    PlaceholderIndicator,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonProgressBar,
    ShopCard,
  },
  data() {
    return {
      favoriteShops: [],
      loading: {
        shops: true,
      },
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
    userFavoriteShopIds: () => {
      return store.getters.getUserFavoriteShops;
    },
  },
  watch: {
    userFavoriteShopIds: {
      handler(newIdList) {
        if (newIdList && JSON.parse(newIdList).length > 0) {
          this.fetchShopData(JSON.parse(newIdList));
        } else {
          this.favoriteShops = [];
          this.loading.shops = false;
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
      if (ev.deltaX > 200) {
        router.push('/sammlung/rezepte');
      }
    },
    fetchShopData(favoriteShopsIdArray: Array<number>) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'getShopsByIds',
          shopsIds: favoriteShopsIdArray,
        })
        .then(response => {
          this.favoriteShops = response.data;
          this.loading.shops = false;
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
});
</script>