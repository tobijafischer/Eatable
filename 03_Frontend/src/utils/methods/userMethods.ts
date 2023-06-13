import axios from 'axios';
import store from '@/store';
import router from '@/router';
import { SingleUser } from '@/types/userTypes';
import { tblVars } from '@/variables/environmentVariables';

export function resetUserInfo() {
  store.dispatch('setUserInfo', {});
}

export function setUserInfo(authToken: string, userId: number) {
  axios.post(`${tblVars.baseApiUrl}/index.php`, {
    headers: {
      authorization: authToken
    },
    action: 'getUser',
    userId: userId
  })
  .then(response => {
    const currentUser: SingleUser = response.data;
    store.dispatch('setUserInfo', currentUser);
  })
  .catch( error => {
    console.error(error);
  });
}

export function validateUser(authToken: string): Promise<number> {
  const validationPromise: Promise<number> = new Promise( (resolve, reject) => {
    axios.post(`${tblVars.baseApiUrl}/index.php`, {
      headers: {
        authorization: authToken
      },
      action: 'userValidate',
    })
    .then(response => {
      if ( typeof response.data !== 'number' ) {
        reject(response.data);
      } else {
        resolve(response.data);
      }
    })
    .catch(error => {
      reject(error);
    });
  });
  return validationPromise;
}

export function logoutUser() {
  axios.post(`${tblVars.baseApiUrl}/index.php`, {
    headers: {
      authorization: '123456789'
    },
    action: 'deleteToken',
  })
  .then( response => {
    console.log(response);
  })
  .catch( error => {
    console.error(error);
  });
  store.dispatch('initiateUserLogout');
  router.push('/profil/login');
}

export function deleteUser() {
  window.alert('The function to delete your account is not yet supported');
}