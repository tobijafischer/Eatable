<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Login</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Login</ion-title>
        </ion-toolbar>
      </ion-header>
      <LoginForm />
      <!-- <p style="text-align:center;margin-top: 25px;">Noch kein Konto?</p> <a href="/profil/registrierung" style="text-decoration: underline;">Registrieren</a> -->
      <p style="text-align:center;margin-top: 25px;">
        Noch kein Konto?
        <a href="/profil/registrierung" style="text-decoration: underline;">Registrieren</a>
      </p>
      <!-- <ion-item style="text-align: center;" href="/profil/registrierung">
        <ion-label>Registrieren</ion-label>
      </ion-item> -->
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/vue';
// import { restaurantOutline, storefrontOutline, logOutOutline, closeOutline, peopleOutline, starOutline, logoInstagram, documentTextOutline, chevronForwardOutline } from 'ionicons/icons';
import LoginForm from '@/components/LoginForm.vue';
import store from '@/store';
// import router from '@/router';

export default defineComponent({
  name: 'Login',
  components: { LoginForm, IonHeader, IonToolbar, IonTitle, IonContent, IonPage },
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