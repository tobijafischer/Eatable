<template>
  <div class="empty-state-placeholder">
    <ion-img v-if="image" :src="image"></ion-img>
    <h2>{{ title }}</h2>
    <p>{{ text }}</p>
    <ion-button class="main-btn" v-if="link" :routerLink="link">{{ linkText }}</ion-button
    ><br />
    <ion-button v-if="secondaryLink && secondaryLinkText" fill="clear" :routerLink="secondaryLink">{{
      secondaryLinkText
    }}</ion-button>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonButton, IonImg } from '@ionic/vue';

export default defineComponent({
  name: 'PlaceholderIndicator',
  props: {
    image: {
      default: null,
    },
    title: {
      type: String,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
    linkText: {
      type: String,
      default: 'Los!',
    },
    link: String,
    secondaryLinkText: String,
    secondaryLink: String,
  },
  components: { IonButton, IonImg },
});
</script>

<style scoped>
ion-img {
  width: 80%;
  max-width: 640px;
  margin: auto;
  padding-bottom: 24px;
}
.empty-state-placeholder {
  text-align: center;
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  padding: 16px;
}
.empty-state-placeholder h2 {
  font-weight: 700;
  font-size: 24px;
  line-height: 32px;
}
.empty-state-placeholder p {
  font-size: 16px;
  line-height: 24px;
  margin: 0;
}
ion-button {
  text-transform: initial;
}
ion-button.main-btn {
  margin-top: 24px;
}
</style>