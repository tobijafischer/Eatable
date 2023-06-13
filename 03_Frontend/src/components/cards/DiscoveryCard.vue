<template>
  <ion-card>
    <img
      v-if="discoveryObject.main_image !== 'discovery-default.jpg'"
      :src="`${tblVars.baseCdnUrl}/img/discovery/${discoveryObject.main_image}`"
    />
    <ion-card-header>
      <ion-card-title>{{ discoveryObject.title }}</ion-card-title>
    </ion-card-header>

    <ion-card-content
      v-html="discoveryObject.content.replace('../img/', `${tblVars.baseCdnUrl}/img/`)"
    ></ion-card-content>
    <ion-button expand="block" fill="solid" @click="openInAppBrowser(discoveryObject.external_url)"
      >Mehr erfahren</ion-button
    >
  </ion-card>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonButton } from '@ionic/vue';
import { openInAppBrowser } from '@/utils/methods/capacitorMethods';
import { tblVars } from '@/variables/environmentVariables';

interface SingleDiscovery {
  id: string;
  title: string;
  slug: string;
  main_image: string | null;
  content: string;
  author: string;
  external_url: string | null;
  creation_date: string;
  active: string;
}

export default defineComponent({
  name: 'DiscoveryCard',
  components: {
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardContent,
    IonButton,
  },
  props: {
    discoveryObject: {
      type: Object as () => SingleDiscovery,
    },
  },
  data() {
    return {
      tblVars,
    };
  },
  methods: {
    openInAppBrowser,
  },
});
</script>

<style scoped>
ion-card img {
  width: 100%;
}
ion-card ion-button {
  margin: 16px 0 0 0;
  --border-radius: 0;
}
</style>