<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Shop-Finder</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Shop-Finder</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-progress-bar v-if="loading.shops" type="indeterminate"></ion-progress-bar>
      <div class="card-wrapper">
        <p v-if="loading.shops" style="text-align: center;opacity: 0.75; font-size:0.8em;">Shops werden geladen</p>
        <p v-else-if="shopEntriesSorted.length < 1">
          Keine passenden Shops gefunden...
        </p>
        <p v-else style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{ `${shopEntriesSorted.length} Shop${shopEntriesSorted.length > 1 ? 's' : ''} gefunden` }}
        </p>
        <p v-if="searchPlaceName" style="margin: 0; text-align: center;opacity: 0.75; font-size:0.8em;">
          <em>{{ searchPlaceName }}</em>
          <a @click="resetSearchFilter()" style="cursor: pointer;"> (Filter zurücksetzen)</a>
        </p>
        <ShopCard
          v-for="(shop, index) in shopEntriesSorted.slice(0, numberOfDisplayedShops)"
          :key="index"
          :show-distance="searchPlaceName ? true : false"
          :shop-object="shop"
          :is-favorite="userFavoriteShopIds ? userFavoriteShopIds.includes(parseInt(shop.id)) : false"
        ></ShopCard>
        <ion-infinite-scroll
          @ionInfinite="loadMoreShops(10, $event)"
          threshold="100px"
          id="infinite-scroll"
          :disabled="infiniteScrollFinished"
        >
          <ion-infinite-scroll-content loading-spinner="bubbles" loading-text="Mehr Shops werden geladen...">
          </ion-infinite-scroll-content>
        </ion-infinite-scroll>
        <h6 v-if="infiniteScrollFinished">Alle Shops wurden geladen</h6>
      </div>
    </ion-content>
    <ion-footer class="search-bar">
      <ion-toolbar>
        <ion-item>
          <ion-label v-if="false" position="floating">Nach Ort/Adresse suchen</ion-label>
          <ion-input
            :placeholder="searchPlaceName ? searchPlaceName : 'Nach Ort/Adresse suchen'"
            v-model.lazy="shopSearchTerm"
          ></ion-input>
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
import ShopCard from '@/components/cards/ShopCard.vue';
import store from '@/store';
import axios from 'axios';
import LatLon from 'geodesy/latlon-spherical.js';
import { SingleShop } from '@/types/shopTypes';
import { openToastOptions } from '@/utils/ionCustomHelpers';
import { shuffleArray } from '@/utils/methods/helpers';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'EinkaufenShopFinder',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    ShopCard,
    IonProgressBar,
    IonFooter,
    IonItem,
    IonLabel,
    IonInput,
    IonIcon,
  },
  data() {
    return {
      shopEntries: [] as Array<SingleShop>,
      shopEntriesSorted: [] as Array<SingleShop>,
      searchPlaceName: '',
      infiniteScrollFinished: false,
      numberOfDisplayedShops: 20,
      timeout: null as any,
      debouncedSearchTerm: '',
      loading: {
        shops: true,
      },
    };
  },
  computed: {
    userFavoriteShopIds: () => {
      return store.getters.getUserFavoriteShops;
    },
    shopSearchTerm: {
      get(): string {
        return this.debouncedSearchTerm;
      },
      set(val: string) {
        if (this.timeout) clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
          this.debouncedSearchTerm = val;
        }, 800);
      },
    },
  },
  watch: {
    debouncedSearchTerm(term) {
      this.sortShopEntries(this.shopEntries, term);
    },
    // shopEntries() {
    //   this.sortShopEntries(this.shopEntries);
    // },
  },
  mounted() {
    this.getshopsData('5000');
  },
  methods: {
    // openToastOptions,
    getshopsData(quantity = '20', offset = '0') {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'getShops',
          limit: `${offset},${quantity}`,
        })
        .then(response => {
          this.shopEntries = response.data;
          this.shopEntriesSorted = shuffleArray(response.data);
          this.loading.shops = false;
        })
        .catch(error => {
          console.error(error);
        });
    },
    loadMoreShops(quantity = 20, event: any) {
      this.numberOfDisplayedShops += quantity;

      if (this.numberOfDisplayedShops > this.shopEntries.length) {
        this.numberOfDisplayedShops = this.shopEntries.length;
        this.infiniteScrollFinished = true;
      } else {
        const infiniteScroll = document.getElementById('infinite-scroll');
        if (infiniteScroll) {
          event.target.complete();
        }
      }
    },
    sortShopEntries(initialShopArray: Array<any>, mapsQuery = '') {
      if (mapsQuery === '') {
        this.shopEntriesSorted =
          this.shopEntriesSorted.length < 1 ? shuffleArray(this.shopEntries) : this.shopEntriesSorted;
        // this.searchPlaceName = '';
        this.loading.shops = false;
        return;
      }
      const maxDistance = 1000000000;
      axios
        .get(
          `https://api.mapbox.com/geocoding/v5/mapbox.places/${mapsQuery}.json?country=CH&language=de&access_token=${tblVars.mapboxDemoOnlyToken}`
        )
        .then(response => {
          if (response.data.features.length < 1 || response.data.features[0].relevance < 0.5) {
            this.shopEntriesSorted =
              this.shopEntriesSorted.length < 1 ? shuffleArray(this.shopEntries) : this.shopEntriesSorted;
            this.loading.shops = false;

            openToastOptions(`Keine Ort mit dem Namen «${mapsQuery}» gefunden`, 'warning', 3200, 'top');
            return false;
          }
          this.searchPlaceName = response.data.features[0].place_name;
          const queryLong = response.data.features[0].center[0];
          const queryLat = response.data.features[0].center[1];

          const pQuery = new LatLon(queryLat, queryLong);

          const newShopEntries = initialShopArray
            .map((shop: any) => {
              if (shop.location_lat && shop.location_lon) {
                const shopLatLon = new LatLon(shop.location_lat, shop.location_lon);
                shop.latlon = shopLatLon;
                shop.currentDistance = pQuery.distanceTo(shopLatLon);
              } else {
                shop.currentDistance = maxDistance;
              }
              return shop;
            })
            .sort((a: any, b: any): number => {
              return a.currentDistance - b.currentDistance;
            });

          return newShopEntries;
        })
        .then(sortedEntries => {
          if (!sortedEntries) return;
          const newShopArray = sortedEntries.map(entry => {
            let distanceIndicator;
            if (entry.currentDistance === maxDistance) {
              distanceIndicator = 'Entfernung unbekannt';
            } else if (entry.currentDistance < 1000) {
              const distanceInMeter = Math.floor(entry.currentDistance);
              distanceIndicator = distanceInMeter.toString() + 'm entfernt';
            } else {
              const distanceInKilometer = Math.floor(entry.currentDistance / 1000);
              distanceIndicator = distanceInKilometer.toString() + 'km entfernt';
            }
            entry.distanceIndicator = distanceIndicator;

            return entry;
          });
          this.shopEntriesSorted = newShopArray;
          this.loading.shops = false;
        });
    },
    resetSearchFilter() {
      this.shopEntriesSorted = shuffleArray(this.shopEntries);
      this.debouncedSearchTerm = '';
      this.searchPlaceName = '';
    },
  },
});
</script>