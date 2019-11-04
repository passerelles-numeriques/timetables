// Import the 3rd party libraries
window.Popper = require('popper.js').default;
window.moment = require('moment');
require('chart.js');
window.$ = window.jQuery = require('jquery');
require('bootstrap');

// Define and launch the Vue application
import Vue from 'vue';
import App from './App';
import Router from 'vue-router';
import { buildRouter } from './router.js';
import PublicService from './services/PublicService.js';

// Parse the config passed by the server
const configElement = document.getElementById('configTag');
const config = JSON.parse(configElement.innerHTML);
// Attach objects be used in views without mixin
Vue.prototype.baseURL = config.baseURL;
Vue.prototype.publicService = new PublicService(config.baseURL);

Vue.config.productionTip = false;
Vue.use(Router);

// Instance of Vue
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router: buildRouter(config.pathURL),
  components: { App },
  template: '<App/>'
});
