import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import store from '@/store';
import { eraseCookie, getCookie } from '@/utils/methods/cookieMethods';
import { validateUser, setUserInfo } from '@/utils/methods/userMethods';
import Tabs from '@/views/Tabs.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/entdecken'
  },
  {
    path: '/',
    component: Tabs,
    children: [
      {
        path: '',
        redirect: 'entdecken'
      },
      {
        path: 'entdecken',
        component: () => import('@/views/Entdecken.vue')
      },
      {
        path: 'kochen',
        component: () => import('@/views/KochenTabs.vue'),
        children: [
          {
            path: '',
            redirect: '/kochen/vorrat'
          },
          {
            path: 'vorrat',
            component: () => import('@/views/KochenVorrat.vue'),
          },
          {
            path: 'rezepte',
            component: () => import('@/views/KochenRezepte.vue'),
          }
        ]
      },
      {
        path: 'kochen/rezepte/:slug',
        component: () => import('@/views/_KochenRezepteSingle.vue'),
      },
      {
        path: 'einkaufen',
        component: () => import('@/views/EinkaufenTabs.vue'),
        children: [
          {
            path: '',
            redirect: '/einkaufen/guide',
          },
          {
            path: 'guide',
            component: () => import('@/views/EinkaufenGuide.vue'),
          },
          {
            path: 'shops',
            component: () => import('@/views/EinkaufenShopFinder.vue')
          },
        ]
      },
      {
        path: 'einkaufen/shops/:slug',
        component: () => import('@/views/_EinkaufenShopSingle.vue'),
      },
      {
        path: '/sammlung',
        meta: {
          requiresAuth: false
        },
        component: () => import('@/views/SammlungTabs.vue'),
        children: [
          {
            path: '',
            redirect: '/sammlung/rezepte'
          },
          {
            name: 'SammlungRezepte',
            path: 'rezepte',
            component: () => import('@/views/SammlungRezepte.vue')
          },
          {
            name: 'SammlungShops',
            path: 'shops',
            component: () => import('@/views/SammlungShops.vue')
          }
        ]
      },
      {
        name: 'Profil',
        path: '/profil',
        meta: {
          requiresAuth: true
        },
        component: () => import('@/views/Profil.vue')
      },
      {
        name: 'Login',
        path: '/profil/login',
        meta: {
          hideForAuth: true
        },
        component: () => import('@/views/ProfilLogin.vue'),
      },
      {
        name: 'Registrierung',
        path: '/profil/registrierung',
        meta: {
          hideForAuth: true
        },
        component: () => import('@/views/ProfilRegistrierung.vue'),
      },
      {
        path: '/404',
        name: '404',
        component: () => import('@/views/secondary/404.vue'),
      },
    ]
  },
  {
    name: 'Onboarding',
    path: '/onboarding',
    component: () => import('@/views/secondary/UserOnboarding.vue'),
  },
  {
    path: '/:catchAll(.*)', redirect:'404'
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  const sessionToken = getCookie('sessionToken');
  if ( store.getters.getLoginState === null && sessionToken) {
    // initial page load of probably authenticated user
    if (sessionToken) {
      validateUser(sessionToken)
      .then(userId => {
        // user is validated, start auth
        store.dispatch('initiateUserLogin', {
          userId: userId,
          authToken: sessionToken
        });
        setUserInfo(sessionToken, userId);

        // manage redirections
        if ( to.matched.some(record => record.meta.hideForAuth) ) {
          next({ name: 'Profil' });
        } else {
          next();
        }
      })
      .catch( () => {
        eraseCookie('sessionToken');
        // console.error(e); // invalid session token
        if (to.matched.some(record => record.meta.requiresAuth)) {
          next({ name: 'Login' });
        } else {
          next();
        }
      })
    }
  } else {
    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires auth, check if logged in
      // if not, redirect to login page.
      if (!store.getters.getLoginState) {
        next({ name: 'Login' });
      } else {
        next(); // authenticated (go wherever you were going)
      }
    } else if ( to.matched.some(record => record.meta.hideForAuth) ) {
      if (store.getters.getLoginState) {
        next({ name: 'Profil' }); // already authenticated (go to profile)
      } else {
        next(); // not yet authenticated (go wherever you were going)
      }
    } else {
      next(); // neutral page (go wherever you were going)
    }
  }
});

export default router
