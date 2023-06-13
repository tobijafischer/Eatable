<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="shop-single-header" collapse="fade">
        <ion-toolbar>
          <ion-buttons slot="start">
            <ion-back-button default-href="/einkaufen/shops" color="light" text="zurück"> </ion-back-button>
          </ion-buttons>
          <ion-buttons slot="end">
            <ion-button class="like-heart" @click="favoriteShopToggle($event)" :data-recipeid="singleShopData.id">
              <ion-icon
                v-if="userFavoriteShopIds && userFavoriteShopIds.includes(parseInt(singleShopData.id))"
                class="o-heart-2"
                fill="solid"
                slot="icon-only"
              ></ion-icon>
              <ion-icon v-else class="o-heart-1" fill="solid" slot="icon-only"></ion-icon>
            </ion-button>
            <!-- <ion-button color="light" @click="shareTest(singleShopData.title, `${this.$router.fullPath}`)">
              <ion-icon class="o-radar-1" fill="solid" slot="start"></ion-icon>
            </ion-button> -->
          </ion-buttons>
        </ion-toolbar>
        <h2>{{ webShareError }}</h2>
        <ion-img
          v-if="singleShopData.main_image"
          :src="`${tblVars.baseCdnUrl}/img/shop/${singleShopData.main_image}`"
        ></ion-img>
        <h1>{{ singleShopData.title }}</h1>
      </div>
      <div id="shop-single-content">
        <h3 v-if="singleShopData.description">Beschreibung</h3>
        <p v-if="singleShopData.description">{{ singleShopData.description }}</p>
        <h3 v-if="singleShopData.street && singleShopData.city">Adresse</h3>
        <address v-if="singleShopData.street || singleShopData.city">
          {{ singleShopData.title }}<br />
          {{ singleShopData.street }}<br />
          {{ singleShopData.zip }} {{ singleShopData.city }}<br />
        </address>
        <h3 v-if="singleShopData.website">Webseite</h3>
        <a v-if="singleShopData.website" @click="openInAppBrowser(singleShopData.website)">{{
          singleShopData.website
        }}</a>
        <h3 v-if="singleShopData.gallery">Galerie</h3>
        <div v-if="typeof galleryImages === 'object' && galleryImages.length > 0" id="gallery-scroller">
          <div class="inner-gallery-wrapper" width="max-content">
            <!-- <ion-img
              v-for="image in galleryImages"
              :key="image"
              :src="`${tblVars.baseCdnUrl}/img/shop/${image}`"
            ></ion-img> -->
            <ion-img
              v-for="image in galleryImages"
              :key="image"
              :src="
                `https://res.cloudinary.com/fc-wiesendangen/image/fetch/h_200,w_auto/f_auto/${tblVars.baseCdnUrl}/img/shop/${image}`
              "
            ></ion-img>
          </div>
        </div>
      </div>
    </ion-content>
    <ion-footer v-if="shopMapsLink">
      <ion-toolbar>
        <ion-button expand="block" fill="solid" @click="openInAppBrowser(shopMapsLink)">
          Karte öffnen
        </ion-button>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  alertController,
  IonPage,
  IonContent,
  IonImg,
  IonToolbar,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonFooter,
} from '@ionic/vue';
import axios from 'axios';
import store from '@/store';
import { getCookie } from '@/utils/methods/cookieMethods';
import { validateUser, setUserInfo } from '@/utils/methods/userMethods';
import { SingleShop } from '@/types/shopTypes';
import { openInAppBrowser } from '@/utils/methods/capacitorMethods';
import { openToastOptions } from '@/utils/ionCustomHelpers';
import { ToastColor } from '@/types/ionicTypes';
import { tblVars } from '@/variables/environmentVariables';

export default defineComponent({
  components: {
    IonPage,
    IonContent,
    IonImg,
    IonToolbar,
    IonButtons,
    IonButton,
    IonBackButton,
    IonIcon,
    IonFooter,
  },
  data() {
    return {
      currentSlug: this.$route.params.slug,
      singleShopData: {} as SingleShop,
      singleShopGallery: '',
      galleryImages: [] as Array<string>,
      isFavorite: false,
      tblVars,
      webShareError: '',
    };
  },
  computed: {
    userLoggedIn: () => {
      return store.getters.getLoginState;
    },
    userFavoriteShopIds: () => {
      return store.getters.getUserFavoriteShops;
    },
    shopMapsLink(): string | null {
      if (this.singleShopData.street && this.singleShopData.city && this.singleShopData.zip) {
        return `https://www.google.ch/maps/search/${encodeURIComponent(
          this.singleShopData.street + ', ' + this.singleShopData.zip + ' ' + this.singleShopData.city
        )}/`;
      } else if (this.singleShopData.location_lat && this.singleShopData.location_lon) {
        return `https://www.google.ch/maps/search/${encodeURIComponent(
          this.singleShopData.location_lat + ',' + this.singleShopData.location_lon
        )}/`;
      } else if (this.singleShopData.title) {
        return `https://www.google.ch/maps/search/${encodeURIComponent(this.singleShopData.title)}/`;
      } else {
        return null;
      }
    },
  },
  mounted() {
    this.fetchShop(this.currentSlug);
  },
  methods: {
    openInAppBrowser,
    // async shareTest(title: string, url: string) {
    //   // navigator.clipboard.write(data).then(
    //   //   function () {
    //   //   console.log('success');
    //   //   },
    //   //   function () {
    //   //   console.error('fail');
    //   //   }
    //   // );
    //   const shareData = {
    //     text: 'eatable – Nachhaltiger Shop',
    //     text: title,
    //     url: url,
    //   };

    //   try {
    //     navigator.share(shareData);
    //     console.log(shareData);
    //   } catch(err) {
    //     console.log('Error: ' + err);
    //     this.webShareError = 'Error: ' + err;
    //   }
    // },
    fetchShop(slug: string | string[]) {
      axios
        .post(`${tblVars.baseApiUrl}/index.php`, {
          action: 'getBySlug',
          slug: slug,
          table: 'shops',
        })
        .then(response => {
          if (response.data && typeof response.data !== 'string') {
            if (response.data.gallery !== null) {
              this.galleryImages = response.data.gallery.match(/gallery_eatable-farms_[0-9]+.[a-z]+/gm);
            }
            this.singleShopData = response.data;
          } else if (typeof slug === 'string' && !isNaN(parseInt(slug))) {
            // adding support for shop id as slugs
            axios
              .post(`${tblVars.baseApiUrl}/index.php`, {
                action: 'getSingleShop',
                shopId: slug,
              })
              .then(response => {
                if (response.data && typeof response.data !== 'string') {
                  if (response.data.gallery !== null) {
                    this.galleryImages = response.data.gallery.match(/gallery_eatable-farms_[0-9]+.[a-z]+/gm);
                  }
                  this.singleShopData = response.data;
                } else {
                  this.$router.push('/404?source=shop');
                }
              })
              .catch(error => {
                console.error(error);
              });
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    favoriteShopToggle(event: any) {
      event.stopPropagation();
      if (!this.userLoggedIn) {
        this.userLoginPrompt();
      }
      const sessionToken = getCookie('sessionToken');
      if (!sessionToken) {
        console.log(sessionToken);
        return;
      }

      validateUser(sessionToken).then(userId => {
        let ajaxAction;
        let toastTitle = 'Zur Sammlung hinzugefügt';
        let toastColor: ToastColor = 'success';
        if (this.userFavoriteShopIds.includes(parseInt(this.singleShopData.id))) {
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
            favoriteShop: this.singleShopData.id,
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
ion-toolbar {
  --background: linear-gradient(180deg, rgba(32, 32, 32, 0.75), rgba(32, 32, 32, 0));
  --ion-color-base: transparent !important;
}
ion-button.like-heart {
  --ion-toolbar-color: var(--ion-color-tertiary, var(--ion-color-light));
  /* background-color: transparent;
  visibility: visible;
  --background: none;
  --box-shadow: none;
  --background-activated: none;
  --background-hover: none;
  --ion-toolbar-color: transparent;
  --color-activated: var(--ion-color-light);
  color: var(--ion-color-tertiary, var(--ion-color-light)); */
}
h3 {
  font-size: 18px;
  font-weight: 700;
  margin-top: 36px;
}
a {
  cursor: pointer;
  text-decoration: underline;
}
#shop-single-header {
  min-height: 240px;
  position: relative;
}
#shop-single-header::after {
  content: '';
  background: linear-gradient(0deg, rgba(32, 32, 32, 0.75), rgba(32, 32, 32, 0));
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 48%;
  z-index: -1;
}
#shop-single-header ion-img {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1;
}
#shop-single-header h1 {
  position: absolute;
  bottom: 0;
  left: 0;
  margin: 0;
  padding: 12px 24px;
  color: var(--ion-card-background, var(--ion-color-light));
}

#shop-single-content {
  padding: 0px 16px 24px 16px;
}
#shop-single-content p {
  line-height: 1.4em;
}
div#gallery-scroller {
  overflow-x: auto;
}
div#gallery-scroller .inner-gallery-wrapper {
  display: flex;
  width: max-content;
}
div#gallery-scroller .inner-gallery-wrapper ion-img {
  height: 200px;
  width: max-content;
  margin-right: 8px;
}

/* external link */
ion-footer ion-toolbar {
  --background: var(--ion-background-color);
  --border-color: var(--ion-color-secondary);
  box-shadow: 0px 0px 20px 20px var(--ion-background-color);
}
</style>
<style>
.eatable-toast {
  text-align: center;
  justify-content: center;
}
</style>