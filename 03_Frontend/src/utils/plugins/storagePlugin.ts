import {App} from 'vue'
import { Storage, Drivers } from "@ionic/storage";
import * as CordovaSQLiteDriver from 'localforage-cordovasqlitedriver';

export default {
  install: async (app: App) => {
    const storage = new Storage({
      name: 'eatable_db',
      driverOrder: [CordovaSQLiteDriver._driver, Drivers.IndexedDB, Drivers.LocalStorage]
    });

    storage.defineDriver(CordovaSQLiteDriver);
    const storageInstance = await storage.create()

    app.config.globalProperties.$globalStringVar = 'iteresting'
    app.config.globalProperties.$eatableStorage = storageInstance
    app.config.globalProperties.$store.$eatableStorage = storageInstance // This one is only needed if you want to access the ionic storage instance in your VUEX store actions
  }
}