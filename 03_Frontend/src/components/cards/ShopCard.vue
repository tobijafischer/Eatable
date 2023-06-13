<template>
  <ion-card v-if="shopObject" :routerLink="`/einkaufen/shops/${shopObject.slug}`">
    <ion-button class="like-heart" @click="favoriteShopToggle($event)" :data-recipeid="shopObject.id">
      <ion-icon v-if="isFavorite" class="o-heart-2" fill="solid" slot="icon-only"></ion-icon>
      <ion-icon v-else class="o-heart-1" fill="solid" slot="icon-only"></ion-icon>
    </ion-button>
    <ion-img
      :src="
        `${tblVars.baseCdnUrl}/img/shop/${
          shopObject.main_image.length > 4 ? shopObject.main_image : 'shop-default.jpg'
        }`
      "
    ></ion-img>
    <ion-card-header>
      <ion-card-subtitle v-if="showDistance && shopObject.distanceIndicator">{{
        shopObject.distanceIndicator
      }}</ion-card-subtitle>
      <ion-card-title v-text="shopObject.title"></ion-card-title>
    </ion-card-header>

    <ion-card-content>
      {{ shopObject.street }}
      <br />
      {{ shopObject.zip }} {{ shopObject.city }}
    </ion-card-content>
  </ion-card>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  alertController,
  IonCard,
  IonCardHeader,
  IonCardSubtitle,
  IonCardTitle,
  IonCardContent,
  IonButton,
  IonIcon,
  IonImg,
} from '@ionic/vue';
import axios from 'axios';
import store from '@/store';
import { getCookie } from '@/utils/methods/cookieMethods';
import { validateUser, setUserInfo } from '@/utils/methods/userMethods';
import { SingleShop } from '@/types/shopTypes';
import { openToastOptions } from '@/utils/ionCustomHelpers';
import { ToastColor } from '@/types/ionicTypes';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'ShopCard',
  components: {
    IonCard,
    IonCardHeader,
    IonCardSubtitle,
    IonCardTitle,
    IonCardContent,
    IonButton,
    IonIcon,
    IonImg,
  },
  props: {
    shopObject: {
      type: Object as () => SingleShop,
    },
    isFavorite: {
      type: Boolean,
      default: false,
    },
    showDistance: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      tblVars,
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
  },
  methods: {
    favoriteShopToggle(event: any) {
      event.stopPropagation();
      if (!this.userLoggedIn) {
        this.userLoginPrompt();
      }
      const targetId = event.currentTarget.dataset.recipeid;
      const sessionToken = getCookie('sessionToken');
      if (!sessionToken) return;

      validateUser(sessionToken).then(userId => {
        let ajaxAction;
        let toastTitle = 'Zur Sammlung hinzugefügt';
        let toastColor: ToastColor = 'success';
        if (this.isFavorite) {
          ajaxAction = 'removeUserFavoriteShop';
          toastTitle = 'Aus deiner Sammlung entfernt';
          toastColor = 'tertiary';
        } else {
          ajaxAction = 'addUserFavoriteShop';
        }
        axios
          .post(`${tblVars.baseApiUrl}/index.php`, {
            headers: {
              authorization: sessionToken,
            },
            action: ajaxAction,
            userId: userId,
            favoriteShop: targetId,
          })
          .then(() => {
            setUserInfo(sessionToken, userId);
            openToastOptions(toastTitle, toastColor, 2000, 'top');
          })
          .catch(error => {
            console.error(error);
          });
      });
    },
    async userLoginPrompt() {
      const alert = await alertController.create({
        header: 'Es freut uns, dass dir dieser Shop gefällt!',
        message: 'Melde dich an, um ihn in deiner Sammlung zu speichern...',
        buttons: [
          {
            text: 'Konto erstellen',
            role: 'register',
            handler: () => this.$router.push('/profil/registrierung'),
          },
          {
            text: 'zum Login',
            role: 'login',
            handler: () => this.$router.push('/profil/login'),
          },
          {
            text: 'abbrechen',
            role: 'cancel',
          },
        ],
      });
      return alert.present();
    },
  },
});
</script>
<style scoped>
/* Heart Icon */
ion-card ion-button.like-heart {
  position: absolute;
  z-index: 1;
  top: 0;
  right: 0;
  background-color: transparent;
  visibility: visible;
  --background: none;
  --box-shadow: none;
  --background-activated: transparent;
  --background-hover: transparent;
  /* --ripple-color: transparent; */
  --color-activated: var(--ion-color-light);
  color: var(--ion-card-background, var(--ion-color-light));
}
ion-card ion-img {
  width: 100%;
  height: 175px;
  object-fit: cover;
}
ion-card ion-button[expand='block'] {
  margin: 16px 0 0 0;
  --border-radius: 0;
}
</style>
<style>
.eatable-toast {
  text-align: center;
  justify-content: center;
}
</style>