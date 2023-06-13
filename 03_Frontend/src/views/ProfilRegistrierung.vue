<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Registrierung</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Registrierung</ion-title>
        </ion-toolbar>
      </ion-header>
      <RegistrationForm />
      <p style="text-align:center;margin-top: 25px;">
        Hast du schon ein Konto?
        <!-- <a @click="() => router.push('/profil/login')" style="text-decoration: underline;">Anmelden</a> -->
        <!-- <ion-router-link href="/profil/login">Anmelden</ion-router-link> -->
        <a href="/profil/login" style="text-decoration: underline;">Login</a>
        <!-- <router-link to="/profil/login" style="text-decoration: underline;">Login</router-link> -->
      </p>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/vue';
import RegistrationForm from '@/components/RegistrationForm.vue';
import store from '@/store';
// import router from '@/router';

export default defineComponent({
  name: 'Registrierung',
  components: { RegistrationForm, IonHeader, IonToolbar, IonTitle, IonContent, IonPage },
  computed: {
    loginState: () => {
      return store.getters.getLoginState;
    },
    currentUserInfo() {
      return store.getters.getUserInfo;
    },
  },
  // data() {
  //   return {
  //     router,
  //   };
  // },
});
</script>
<style scoped>
img.avatar {
  max-width: 100px;
  max-height: 100px;
}
</style>