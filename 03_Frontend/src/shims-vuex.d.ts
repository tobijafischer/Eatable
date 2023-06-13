import { Store } from '@/store';
import { Storage } from '@ionic/storage';


declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $store: Store;
    $eatableStorage: Storage;
  }
}