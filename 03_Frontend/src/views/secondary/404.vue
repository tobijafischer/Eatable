<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Seite nicht gefunden</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Seite nicht gefunden</ion-title>
        </ion-toolbar>
      </ion-header>
      <PlaceholderIndicator
        title="404"
        :text="
          `${searchSubject.name} ${
            redirectPath ? '(' + redirectPath + ') ' : ''
          }wurde nicht gefunden. Mit etwas Glück findest du ${searchSubject.pronoun} auf einem anderen Weg...`
        "
        :linkText="primaryLink.text"
        :link="primaryLink.url"
        :secondaryLinkText="userLoggedIn ? 'zum Profil' : 'zum Login'"
        secondaryLink="/profil"
        image="/img/illustrations/eatable-404.png"
      />
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PlaceholderIndicator from '@/components/PlaceholderIndicator.vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/vue';
import store from '@/store';

export default defineComponent({
  name: '404',
  components: {
    PlaceholderIndicator,
    IonPage,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
  },
  data() {
    return {
      redirectPath: '',
      searchSubject: {
        name: 'Diese Seite',
        pronoun: 'sie',
      },
      primaryLink: {
        text: 'Zur Startseite',
        url: '/entdecken',
      },
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
  },
  mounted() {
    if (this.$route.query.source) {
      if (this.$route.query.source === 'recipe') {
        this.searchSubject.name = 'Dieses Rezept';
        this.searchSubject.pronoun = 'es';
        this.primaryLink.text = 'Alle Rezepte ansehen';
        this.primaryLink.url = '/kochen/rezepte';
      } else if (this.$route.query.source === 'shop') {
        this.searchSubject.name = 'Dieser Shop';
        this.searchSubject.pronoun = 'ihn';
        this.primaryLink.text = 'Alle Shops ansehen';
        this.primaryLink.url = '/einkaufen/shops';
      } else {
        this.searchSubject.name = 'Diese Seite';
        this.searchSubject.pronoun = 'sie';
        this.primaryLink.text = 'Zur Startseite';
        this.primaryLink.url = '/entdecken';
      }
    } else if (this.$route.redirectedFrom) {
      this.redirectPath = this.$route.redirectedFrom.fullPath;
    }
  },
});
</script>