// This Class allows to use HTTP requests with autentication
export default class AdminService {
  isAdmin() {
    return (sessionStorage.length > 0);
  }
  getName() {
    return sessionStorage.getItem('username');
  }

  getPassword() {
    return sessionStorage.getItem('password');
  }
  getCredentials() {
    return `user=${this.getName()}&password=${this.getPassword()}`;
  }

  // Is used when a http request need an authentication
  adminFetch(url, callbackSuccess, callbackError = null, fetchOption = null) {
    if (!fetchOption) {
      fetchOption = { mode: 'cors' };
    }
    fetch(
      `${url}?${this.getCredentials()}`,
      fetchOption
    )
    .then(response => {
      if (response.status === 200) {
        return response.json();
      } else {
        throw response.text();
      }
    })
    .then(json => {
      callbackSuccess(json);
    })
    .catch(exception => {
      if (callbackError) {
        callbackError(exception);
      } else {
        exception.then(ex => console.log(ex));
      }
    });
  }
}
