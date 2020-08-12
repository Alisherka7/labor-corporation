<template>
  <v-list-tile :class="{'highlight--background': get.isnow && item.type != 'group'}" @click="onClick">
    <v-list-tile-avatar>
      <v-icon :class="[get.color]">{{item.icon}}</v-icon>
    </v-list-tile-avatar>
    <v-list-tile-content>
      <span :class="[get.color]"> {{item.title}}</span>
    </v-list-tile-content>
    <v-icon v-if="item.aftericon" :class="[get.color]">{{item.aftericon}}</v-icon>
  </v-list-tile>
</template>

<script>
export default {
  name: "MenuTile",
  /**
   * to
   * icon
   * title
   *
   */
  props: ["item"],
  data() {
    return {};
  },
  methods: {
    onClick() {
      this.item.to ? this.$router.push(this.item.to) : false;
      this.item.href ? window.open(this.item.href): false;
    }
  },
  computed: {
    get() {
      var r = {};
      var isnow = false;
      if (this.item.type == "group") {
        for (var i in this.item.children) {
          var child = this.item.children[i];
          if (this.$route.path == child.to) {
            isnow = true;
            break;
          }
        }
      } else {
        if (this.$route.path == this.item.to) {
          isnow = true;
        }
      }
      r.color = isnow ? "white--text" : "grey--text";
      r.isnow = isnow;
      return r;
    }
  }
};
</script>

<style scoped>
.highlight--background {
  background-color: rgba(255, 255, 255, 0.1) !important;
}
</style>
