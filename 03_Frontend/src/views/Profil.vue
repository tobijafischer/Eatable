<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Profil</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">Profil</ion-title>
        </ion-toolbar>
      </ion-header>
      <div v-if="loginState">
        <!-- <ion-thumbnail slot="start">
            <ion-avatar v-if="currentUserInfo.userAvatar">
              <img :src="currentUserInfo.userAvatar">
            </ion-avatar>
          </ion-thumbnail> -->
        <div style="padding: 16px 16px 0 16px">
          <h1>
            Hallo
            <span style="font-style: italic; font-weight: 700">{{ currentUserInfo.userName }}</span>
          </h1>
          <h2>Schön bist du ein Teil von eatable!</h2>
          <p class="eatable-light-text">{{ currentUserInfo.userEmail }}</p>
        </div>
        <ion-item-group>
          <ion-item-divider>
            <ion-label>Sammlungen</ion-label>
          </ion-item-divider>

          <ion-item href="/sammlung/rezepte">
            <!-- <ion-icon :icon="restaurantOutline" slot="start"></ion-icon> -->
            <ion-icon class="o-invoice-1" slot="start"></ion-icon>
            <ion-label>Meine Rezepte</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <ion-item href="/sammlung/shops">
            <!-- <ion-icon :icon="storefrontOutline" slot="start"></ion-icon> -->
            <ion-icon class="o-store-1" slot="start"></ion-icon>
            <ion-label>Meine Shops</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
        </ion-item-group>

        <ion-item-group>
          <ion-item-divider>
            <ion-label>Konto</ion-label>
          </ion-item-divider>

          <ion-item button @click="logoutUser()">
            <!-- <ion-icon :icon="logOutOutline" slot="start"></ion-icon> -->
            <ion-icon class="o-export-1" slot="start"></ion-icon>
            <ion-label>Ausloggen</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <ion-item button @click="openInAppBrowser(`${tblVars.baseAdminUrl}/passwort-vergessen/`)">
            <!-- <ion-icon class="o-export-1" slot="start"></ion-icon> -->
            <ion-icon :icon="helpOutline" slot="start"></ion-icon>
            <ion-label>Passwort vergessen</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <ion-item
            style="color: var(--ion-color-danger); opacity: 0.6"
            :href="
              `mailto:hello@eatable.ch?subject=Bitte%20l%C3%B6scht%20mein%20eatable%20Konto%20&body=Ich%20erbitte%20die%20L%C3%B6schung%20meines%20eatable%20Kontos%20mit%20der%20folgenden%20E-Mail%20Adresse%3A%20${encodeURIComponent(
                currentUserInfo.userEmail
              )}.%0D%0A%0D%0AMit%20freundlichen%20Gr%C3%BCsse%0D%0A%0D%0A${encodeURIComponent(
                currentUserInfo.userName
              )}`
            "
          >
            <!-- <ion-icon :icon="closeOutline" slot="start" style="color:var(--ion-color-danger);opacity:.6;"></ion-icon> -->
            <ion-icon class="o-exit-1" slot="start"></ion-icon>
            <ion-label>Konto schliessen</ion-label>
            <ion-icon
              v-show="checkAndroid"
              :icon="chevronForwardOutline"
              slot="end"
              style="color: var(--ion-color-danger); opacity: 0.6"
            ></ion-icon>
          </ion-item>
        </ion-item-group>

        <ion-item-group>
          <ion-item-divider>
            <ion-label>Über uns</ion-label>
          </ion-item-divider>

          <ion-item href="https://eatable.ch/mitgliedschaft/" target="_blank">
            <ion-icon :icon="peopleOutline" slot="start"></ion-icon>
            <ion-label>Werde Mitglied bei eatable</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <ion-item href="https://www.instagram.com/eatable.ch/" target="_blank">
            <ion-icon :icon="logoInstagram" slot="start"></ion-icon>
            <ion-label>Folge uns auf Instagram</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <!-- <ion-item v-show="!checkAndroid" href="https://www.apple.com/chde/app-store/" target="_blank">
            <ion-icon :icon="starOutline" slot="start"></ion-icon>
            <ion-label>Bewerte uns im App Store</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item> -->
          <ion-item
            v-show="checkAndroid"
            href="https://play.google.com/store/apps/details?id=eatable.app"
            target="_blank"
          >
            <ion-icon :icon="starOutline" slot="start"></ion-icon>
            <ion-label>Bewerte uns im Play Store</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
          <ion-item href="https://eatable.ch/impressum/" target="_blank">
            <ion-icon :icon="documentTextOutline" slot="start"></ion-icon>
            <ion-label>Impressum & ABG's</ion-label>
            <ion-icon v-show="checkAndroid" :icon="chevronForwardOutline" slot="end"></ion-icon>
          </ion-item>
        </ion-item-group>
      </div>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  isPlatform,
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonIcon,
  IonItem,
  IonLabel,
  IonItemGroup,
  IonItemDivider,
} from '@ionic/vue';
import {
  restaurantOutline,
  storefrontOutline,
  logOutOutline,
  closeOutline,
  peopleOutline,
  starOutline,
  logoInstagram,
  documentTextOutline,
  chevronForwardOutline,
  helpOutline,
} from 'ionicons/icons';
import store from '@/store';
import { logoutUser, deleteUser } from '@/utils/methods/userMethods';
import { openInAppBrowser } from '@/utils/methods/capacitorMethods';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  name: 'Profil',
  components: {
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonPage,
    IonIcon,
    IonItem,
    IonLabel,
    IonItemGroup,
    IonItemDivider,
  },
  data() {
    return {
      restaurantOutline,
      storefrontOutline,
      logOutOutline,
      closeOutline,
      peopleOutline,
      starOutline,
      logoInstagram,
      documentTextOutline,
      chevronForwardOutline,
      helpOutline,
      checkAndroid: isPlatform('android'),
      checkIos: isPlatform('ios'),
      tblVars,
    };
  },
  methods: {
    logoutUser,
    deleteUser,
    openInAppBrowser,
  },
  computed: {
    loginState: () => {
      return store.getters.getLoginState;
    },
    currentUserInfo() {
      return store.getters.getUserInfo;
    },
  },
});
</script>
<style scoped>
h2 {
  font-size: 16px;
  margin-top: 0;
}
p.eatable-light-text {
  color: var(--ion-color-step-400);
}
ion-item-divider {
  background: transparent;
}

/* Icons */
ion-icon {
  text-align: center;
  color: var(--ion-color-primary);
}
ion-item ion-icon.o-export-1 {
  font-size: 23px;
  padding-left: 3px;
}

/* ios styles */
ion-item-divider.ios {
  padding-top: 16px;
}
ion-content ion-item ion-label.ios {
  margin: 12px 0;
}
</style>