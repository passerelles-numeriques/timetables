<template>
  <div id="app">
    <Header class="no-print" :active="active" />
    <Loader class="no-print" :loading="showLoader" />
    <ErrorAlert class="no-print" :message="errorMessage" />
    <router-view class="main-page" 
      @changeActive="setActive" 
      @loader="setLoader" 
      @showErrorMessage="setErrorMessage" 
      :key="$route.fullPath">
    </router-view>
  </div>
</template>

<script>
import Header from './components/Header';
import Loader from './components/Loader';
import ErrorAlert from './components/ErrorAlert';

export default {
  name: 'App',
  data() {
    return {
      active: 'Teachers',
      showLoader: false,
      errorMessage: null
    };
  },
  components: {
    Header,
    Loader,
    ErrorAlert
  },
  methods: {
    setActive: function(active) {
      this.active = active;
    },
    setLoader: function(loading) {
      this.showLoader = loading;
    },
    setErrorMessage: function(message) {
      this.errorMessage = message;
    }
  }
};
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
.icon-button::before {
  padding-right: 2px;
}
html, body {
  height: 100%;
}
.main-page {
  padding-top: 5px;
}

@media print {
  @page {
    size: A4 landscape;
    margin: 25mm 25mm 25mm 25mm;
  }
  .no-print {
    display: none;
  }
}
</style>
