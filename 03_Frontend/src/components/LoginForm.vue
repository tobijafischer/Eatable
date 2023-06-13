<template>
  <div>
    <form v-on:submit.prevent="submit" ref="form">
      <ion-item>
        <ion-label position="floating" for="email">E-Mail</ion-label>
        <ion-input type="email" name="email" autocomplete="email" required v-on:keyup="submitKeyWatcher"></ion-input>
      </ion-item>
      <ion-item>
        <ion-label position="floating" for="password">Passwort</ion-label>
        <ion-input
          type="password"
          name="password"
          autocomplete="current-password"
          minlength="6"
          required
          v-on:keyup="submitKeyWatcher"
        ></ion-input>
      </ion-item>
      <ion-button id="ionic-submit" expand="block" fill="solid" size="default" strong="true" type="submit"
        >Einloggen</ion-button
      >
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonItem, IonLabel, IonInput, IonButton } from '@ionic/vue';
import axios from 'axios';
import store from '@/store';
import getRandomToken from '@/utils/randomToken';
import { setCookie } from '@/utils/methods/cookieMethods';
import { setUserInfo } from '@/utils/methods/userMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'LoginForm',
  components: {
    IonItem,
    IonLabel,
    IonInput,
    IonButton,
  },
  methods: {
    submit: function(e: Event) {
      const formData = new FormData(e.currentTarget as HTMLFormElement);
      const inputEmail = formData.get('email') as string;
      const inputPassword = formData.get('password') as string;
      const authToken = getRandomToken();
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          headers: {
            authorization: authToken,
          },
          action: 'login',
          email: inputEmail,
          password: inputPassword,
        })
        .then(response => {
          if (typeof response.data !== 'number') {
            throw response.data;
          } else {
            const userId = response.data;
            store.dispatch('initiateUserLogin', {
              userId: userId,
              authToken: authToken,
            });
            setCookie('sessionToken', authToken, 1);
            setUserInfo(authToken, userId);
            this.$router.push('/profil');
          }
        })
        .catch(e => {
          console.error(e);
        });
    },
    submitKeyWatcher: function(e: KeyboardEvent) {
      if (e.key == 'Enter' && e !== null) {
        const submitButton = document.getElementById('ionic-submit');
        if (submitButton) {
          submitButton.click();
        }
      }
    },
  },
});
</script>

<style scoped>
form {
  width: 90%;
  max-width: 720px;
  margin: auto;
  margin-top: 15vh;
}
</style>