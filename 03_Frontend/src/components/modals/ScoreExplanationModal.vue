<template>
  <ion-header>
    <ion-toolbar>
      <ion-title>Über unsere Scores</ion-title>
      <ion-buttons slot="end" style="--padding-end: 0;">
        <ion-button @click="dismissCurrentModal()">
          <ion-icon class="o-close-1" style="font-size: 48px;line-height: 0.8em;" />
        </ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>
  <ion-content>
    <h2>Heading 2</h2>
    <p>
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad quo repudiandae odit! At ad reiciendis culpa, fugiat
      cumque explicabo aliquid odio iste fugit nihil officiis beatae asperiores ducimus qui placeat veritatis debitis,
      sapiente quo doloribus neque nemo non recusandae. Quo nam asperiores ad facilis ipsam, perspiciatis nesciunt,
      culpa dicta neque qui, eum ducimus sed totam quae id a. Sint velit impedit ipsa laboriosam quam nisi, minus beatae
      incidunt ea, autem iste commodi quibusdam libero! Fugit magni porro odio similique modi necessitatibus, numquam
      aliquid amet quibusdam quaerat repellat tenetur vel exercitationem quisquam a nulla neque vitae labore eveniet
      tempore? Et, porro!
    </p>
    <p id="fragen-dazu">
      Du hast Fragen zu unseren Scores oder der App? Dann <a href="mailto:hello@eatable.ch">melde dich bei uns</a>
    </p>
  </ion-content>
</template>
<script lang="ts">
import { defineComponent } from 'vue';
import {
  modalController,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonButtons,
  IonButton,
  IonIcon,
  IonContent,
} from '@ionic/vue';

export default defineComponent({
  name: 'ScoreExplanationModal',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonButtons,
    IonButton,
    IonIcon,
    IonContent,
  },
  data() {
    return {
      test: 'test',
    };
  },
  methods: {
    async dismissCurrentModal() {
      const modal = await modalController.dismiss();
      return modal;
    },
  },
});
</script>
<style scoped>
ion-content {
  --padding-top: 24px;
  --padding-bottom: 24px;
  --padding-start: 16px;
  --padding-end: 16px;
}
p#fragen-dazu {
  font-size: 12px;
  text-align: center;
  max-width: 300px;
  margin: 48px auto 12px auto;
  line-height: 1.7em;
  opacity: 0.75;
}
</style>