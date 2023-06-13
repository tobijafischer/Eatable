<template>
  <div>
    <form v-on:submit.prevent="submit" ref="form">
      <ion-item>
        <ion-label position="floating" for="username">Benutzername</ion-label>
        <ion-input type="text" name="username" required v-on:keyup="submitKeyWatcher"></ion-input>
      </ion-item>
      <ion-item>
        <ion-label position="floating" for="firstname"
          >Vorname <span style="font-size:0.8em;">(optional)</span></ion-label
        >
        <ion-input type="text" name="firstname" v-on:keyup="submitKeyWatcher"></ion-input>
      </ion-item>
      <ion-item>
        <ion-label position="floating" for="lastname"
          >Nachname <span style="font-size:0.8em;">(optional)</span></ion-label
        >
        <ion-input type="text" name="lastname" v-on:keyup="submitKeyWatcher"></ion-input>
      </ion-item>
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
      <ul v-if="formError.length" class="submit-error">
        <li v-for="error in formError" :key="error">{{ error }}</li>
      </ul>
      <ion-button
        style="margin-top: 25px;"
        id="ionic-submit"
        expand="block"
        fill="solid"
        size="default"
        strong="true"
        type="submit"
        >Registrieren</ion-button
      >
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonItem, IonLabel, IonInput, IonButton } from '@ionic/vue';
import axios from 'axios';
import store from '@/store';
import router from '@/router';
import getRandomToken from '@/utils/randomToken';
import { setCookie } from '@/utils/methods/cookieMethods';
import { setUserInfo } from '@/utils/methods/userMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'RegistrationForm',
  components: {
    IonItem,
    IonLabel,
    IonInput,
    IonButton,
  },
  data() {
    return {
      formError: [],
    };
  },
  methods: {
    submit: function(e: Event) {
      const formData = new FormData(e.currentTarget as HTMLFormElement);
      const inputEmail = formData.get('email');
      const inputPassword = formData.get('password');
      const inputUsername = formData.get('username');
      const inputFirstname = formData.get('firstname');
      const inputLastname = formData.get('lastname');
      const authToken = getRandomToken();
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          headers: {
            authorization: authToken,
          },
          action: 'register',
          email: inputEmail,
          password: inputPassword,
          username: inputUsername,
          firstname: inputFirstname,
          lastname: inputLastname,
        })
        .then(response => {
          if (!response.data.success || typeof response.data.userId !== 'number') {
            this.formError = response.data.errors;
            for (let i = 0; i < response.data.errors.length; i++) {
              console.error(response.data.errors[i]);
            }
          } else {
            const userId = response.data.userId;
            store.dispatch('initiateUserLogin', {
              userId: userId,
              authToken: authToken,
            });
            setCookie('sessionToken', authToken, 1);
            setUserInfo(authToken, userId);
            router.push('/profil');
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
ul.submit-error {
  color: tomato;
  list-style: none;
  padding-left: 20px;
}
</style>