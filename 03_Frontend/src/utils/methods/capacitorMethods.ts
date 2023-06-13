import { Browser } from '@capacitor/browser';

export const openInAppBrowser = async (urlToOpen: string): Promise<void> => {
  await Browser.open({
    url: urlToOpen
  })
}