<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <ion-slides
        :pager="true"
        :options="options"
        
        ref="slidesRef"
      >
        <ion-slide>
          <div>
            <ion-img src="/img/onboarding/eatable-einkaufen.svg"></ion-img>
            <h2>Umweltbewusst Einkaufen</h2>
            <p>Erhalte Informationen über die Saisonalität und Nachhaltigkeit von Zutaten. Finde Shops und Hofläden, wo du frisch und regional einkaufen kannst.</p>
            <!-- <p>Sehe auf einen Blick, welche Zutaten nachhaltig sind und gerade Saison haben. Und erfahre, wo du sie in deiner Nähe einkaufen kannst.</p> -->
            <ion-button expand="block" fill="solid" @click="slideToNext()">weiter</ion-button>
            <ion-button style="opacity: 0; visibility: hidden; pointer-events: none;" @click="slideToPrev()">zurück</ion-button>
          </div>
        </ion-slide>
        <ion-slide>
          <div>
            <ion-img src="/img/onboarding/eatable-kochen.svg"></ion-img>
            <h2>Nachhaltig Kochen</h2>
            <p>Finde nachhaltige und saisonale Rezepte für jede Gelegenheit. Behalte im Auge, welche Zutaten du noch zu Hause hast und finde heraus, was sich damit kochen lässt.</p>
            <!-- <p>Finde nachhaltige Rezepte, die saisonale und umweltschonende Zutaten verwenden. Oder erhalte Vorschläge basierend auf deinen Vorräten.</p> -->
            <ion-button expand="block" fill="solid" @click="slideToNext()">weiter</ion-button>
            <ion-button fill="clear" color="primary" @click="slideToPrev()">zurück</ion-button>
          </div>
        </ion-slide>
        <ion-slide>
          <div>
            <ion-img src="/img/onboarding/eatable-entdecken.svg"></ion-img>
            <h2>Möglichkeiten Entdecken</h2>
            <p>Es gibt immer mehr Initiativen und Möglichkeiten, um unsere Ernährung nachhaltiger zu machen. Mit eatable erfährst du wie.</p>
            <!-- <p>Mit eatable lassen sich neue Projekte und Initiativen zum Thema nachhaltige Ernährung entdecken.</p> -->
            <ion-button expand="block" fill="solid" @click="onboardingCompleted()">zur App</ion-button>
            <ion-button fill="clear" color="primary" @click="slideToPrev()">zurück</ion-button>
          </div>
        </ion-slide>
        <!-- <ion-slide>
          <h2>Loslegen</h2>
          <p>Das wars, wir wollen dich nicht länger aufhalten. Spring in die App und entdecke Leckeres und Nachhaltiges.</p>
          <ion-button>
            zur App
          </ion-button>
        </ion-slide> -->
      </ion-slides>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import {
  IonContent, 
  IonPage, 
  IonSlides,
  IonSlide,
  IonButton,
  IonImg
} from '@ionic/vue';
import { defineComponent, ref } from 'vue';
import router from '@/router';

export default defineComponent({
  name: 'Home',
  components: {
    IonContent,
    IonPage,
    IonSlides,
    IonSlide,
    IonButton,
    IonImg
  },
  setup() {
    const slidesRef = ref();
    const options = {
      keyboard: true,
      // navigation: {
      //   prevEl: '#swiper-prev',
      //   nextEl: '#swiper-next',
      // },
    }

    const slideToPrev = async () => {
      if (!slidesRef.value) return;
      await slidesRef.value.$el.slidePrev();
    }
    const slideToNext = async () => {
      if (!slidesRef.value) return;
      await slidesRef.value.$el.slideNext();
    }
    
    return {
      slideToPrev,
      slideToNext,
      slidesRef,
      options,
    }
  },
  methods: {
    onboardingCompleted() {
      this.$eatableStorage.set('onboarding', 'completed');
      const getPreviousUrl = router.options.history.state.back;
      if ( getPreviousUrl && typeof getPreviousUrl === 'string' ) {
        router.push(getPreviousUrl);
      } else {
        router.push('/');
      }
    }
  }
});
</script>

<style scoped>
  ion-slides {
    height: 100%;
    padding: 0;
    --bullet-background: rgb(var(--ion-color-primary-rgb), 0.5);
    --bullet-background-active: var(--ion-color-primary);
    --scroll-bar-background: var(--ion-color-light);
  }
  ion-slide {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    padding: 24px;
    overflow-y: auto;
  }
  ion-img {
    height:28vh;
    object-fit: contain;
    object-position:bottom;
    pointer-events: none;
  }
  h2 {
    margin-top: 4vh;
  }
  p {
    font-size: 14px;
    line-height: 1.6em;
    max-width: 480px;
    margin-bottom: auto auto 6vh auto;
  }
</style>
<style>
  .swiper-pagination.swiper-pagination-bullets {
    bottom: 4vh;
  }
</style>