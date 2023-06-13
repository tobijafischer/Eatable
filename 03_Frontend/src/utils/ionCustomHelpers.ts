import { toastController } from "@ionic/core";
import { ToastColor } from '@/types/ionicTypes';

export async function openLoginToast(message: string, duration = 2000) {
    const toast = await toastController
      .create({
        message: message,
        duration: duration
      })
    return toast.present();
}

export async function openToastOptions(title: string, color: ToastColor, duration: number, position: 'top' | 'bottom' | 'middle' | undefined, cssClass = 'eatable-toast') {
    const toast = await toastController
    .create({
        header: title,
        position: position,
        duration: duration,
        color: color,
        cssClass: cssClass,
      })
    await toast.present();
}

// export function scrollToTop(selector = 'ion-content') {
//   const el = document.querySelector(selector);
//   if ( el ) {
//     el.scrollToTop();
//   }
// }