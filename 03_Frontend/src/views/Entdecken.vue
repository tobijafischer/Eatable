<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Entdecken</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Entdecken</ion-title>
        </ion-toolbar>
      </ion-header>
      <ion-progress-bar v-if="loading.discoveries" type="indeterminate"></ion-progress-bar>
      <div class="card-wrapper">
        <p v-if="loading.discoveries" style="text-align: center;opacity: 0.75; font-size:0.8em;">
          Beiträge werden geladen
        </p>
        <p v-else-if="discoveryEntriesSorted.length < 1" style="text-align: center;">
          Keine Beiträge gefunden...
        </p>
        <p v-else style="text-align: center;opacity: 0.75; font-size:0.8em;">
          {{
            `${discoveryEntriesSorted.length} ${discoveryEntriesSorted.length > 1 ? 'Beiträge' : 'Beitrag'} gefunden`
          }}
        </p>
        <DiscoveryCard
          v-for="(discovery, index) in discoveryEntriesSorted.slice(0, numberOfDisplayedDiscoveries)"
          :key="index"
          :discovery-object="discovery"
        >
        </DiscoveryCard>

        <ion-infinite-scroll
          @ionInfinite="loadMoreDiscoveries(10, $event)"
          threshold="100px"
          id="infinite-scroll"
          :disabled="infiniteScrollFinished"
        >
          <ion-infinite-scroll-content loading-spinner="bubbles" loading-text="Mehr Inspiration ist unterwegs...">
          </ion-infinite-scroll-content>
        </ion-infinite-scroll>
      </div>
    </ion-content>
    <ion-footer class="search-bar">
      <ion-toolbar>
        <ion-item>
          <ion-label v-if="false" position="floating">Beiträge finden</ion-label>
          <ion-input placeholder="Beiträge finden" v-model.lazy="discoverySearchTerm"></ion-input>
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
  IonItem,
  IonLabel,
  IonInfiniteScroll,
  IonInfiniteScrollContent,
  IonFooter,
  IonInput,
  IonProgressBar,
  IonIcon,
} from '@ionic/vue';
import DiscoveryCard from '@/components/cards/DiscoveryCard.vue';
import store from '@/store';
import axios from 'axios';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'Entdecken',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonItem,
    IonLabel,
    IonPage,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    IonFooter,
    IonInput,
    IonProgressBar,
    IonIcon,
    DiscoveryCard,
  },
  data() {
    return {
      infiniteScrollFinished: false,
      discoveryEntries: [],
      discoveryEntriesSorted: [] as Array<any>,
      discoverySearchTerm: '',
      numberOfDisplayedDiscoveries: 20,
      loading: {
        discoveries: true,
      },
    };
  },
  mounted() {
    this.getDiscoveryData();
  },
  watch: {
    discoverySearchTerm() {
      this.filterDiscoveryEntries(this.discoveryEntries);
    },
  },
  computed: {
    loggedIn() {
      return store.getters.getLoginState;
    },
  },
  methods: {
    getDiscoveryData(quantity = '1000', offset = '0') {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'bigSearch',
          limit: `${offset},${quantity}`,
          orderby: 'creation_date',
          searchTable: 'discoveries',
          searchColumn: 'title',
          sort: 'DESC',
          term: '',
        })
        .then(response => {
          this.discoveryEntries = response.data;
          this.loading.discoveries = false;
          this.filterDiscoveryEntries(this.discoveryEntries);
        })
        .catch(error => {
          console.error(error);
        });
    },
    loadMoreDiscoveries(quantity = 20, event: any) {
      this.numberOfDisplayedDiscoveries += quantity;

      if (this.numberOfDisplayedDiscoveries > this.discoveryEntries.length) {
        this.numberOfDisplayedDiscoveries = this.discoveryEntries.length;
        this.infiniteScrollFinished = true; // no more infinite loading happening
      } else {
        const infiniteScroll: any = document.getElementById('infinite-scroll');
        if (infiniteScroll) {
          event.target.complete(); // infinite scroll finished, ready for new triggering
        }
      }
    },
    filterDiscoveryEntries(initialDiscoveryArray: Array<any>) {
      if (this.discoverySearchTerm === '') {
        this.discoveryEntriesSorted = this.discoveryEntries;
      } else {
        this.discoveryEntriesSorted = initialDiscoveryArray.filter(discovery => {
          const term = this.discoverySearchTerm.toLowerCase();
          if (discovery.title.toLowerCase().includes(term) || discovery.content.toLowerCase().includes(term)) {
            return true;
          }
        });
      }
    },
  },
});
</script>