<template>
  <v-app v-resize="onResize">
    <router-view/>
    <v-snackbar vertical v-model="$store.state.snackbar.on" :color="$store.getters.snackbar.color" :timeout="3000">
      <v-icon dark>{{$store.getters.snackbar.icon}}</v-icon>
      <div class="text-xs-center">

        {{ $store.state.snackbar.text }}
      </div>
    </v-snackbar>

  </v-app>
</template>

<script>
export default {
  name: "App",
  data() {
    return {
      panel: 1
    };
  },
  methods: {
    onResize() {
      this.$store.commit("updateWindowSize");
    }
  }
};
</script>

<style>
.full-height {
  height: 100%;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
.pointer:hover {
  cursor: pointer;
  background-color: rgba(0, 0, 0, 0.1) !important;
}
.onlypointer:hover {
  cursor: pointer;
}
.rotate:hover {
  animation: anirotate 1s infinite;
  cursor: pointer;
}
@keyframes anirotate {
  0% {
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>

