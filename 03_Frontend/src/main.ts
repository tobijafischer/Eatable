import { createApp } from 'vue'
import App from '@/App.vue'
import router from '@/router';
import store from "@/store";
import storagePlugin from '@/utils/plugins/storagePlugin';
import { isPlatform } from '@ionic/vue';

import { IonicVue } from '@ionic/vue';

/* Core CSS required for Ionic components to work properly */
import '@ionic/vue/css/core.css';

/* Basic CSS for apps built with Ionic */
import '@ionic/vue/css/normalize.css';
import '@ionic/vue/css/structure.css';
import '@ionic/vue/css/typography.css';

/* Optional CSS utils that can be commented out */
// import '@ionic/vue/css/padding.css';
// import '@ionic/vue/css/float-elements.css';
// import '@ionic/vue/css/text-alignment.css';
// import '@ionic/vue/css/text-transformation.css';
// import '@ionic/vue/css/flex-utils.css';
// import '@ionic/vue/css/display.css';

/* Theme variables */
import './variables/variables.css';

/* Custom Global CSS */
import './style/app.css';
import './style/iconfont.css';

const app = createApp(App)
  .use(store)
  .use(IonicVue, {
    mode: isPlatform('android') ? 'md' : 'ios',
  })
  .use(router)
  .use(storagePlugin);
  
// app.provide('eatableStorage', new Storage({
//   name: 'eatable_db',
//   driverOrder: [CordovaSQLiteDriver._driver, Drivers.IndexedDB, Drivers.LocalStorage]
// }))

  // app.config.globalProperties.$eatableStorage = new Storage({
  //   name: 'eatable_db',
  //   driverOrder: [CordovaSQLiteDriver._driver, Drivers.IndexedDB, Drivers.LocalStorage]
  // });;

router.isReady().then(() => {
  app.mount('#app');
});