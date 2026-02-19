require('./bootstrap');

window.Vue = require('vue');

Vue.component('statistic-card', require('./components/statisticCard.vue'));

const app = new Vue({
    el: '#app',
});