<template>
  <div>
    <div v-for="(item, i) in items" :key="i" class="ma-3">
      <v-layout>
        <v-flex xs6>
          <span class="desc_key subheading">{{item.key}}</span>
        </v-flex>
        <v-flex xs6>
          <div v-if="type == 'render'">
            <div>
              <v-menu :open-on-hover="item.value.toString().length > strLimit && !item.file" :open-on-click="false" offset-y>
                <span slot="activator">
                  <span>
                    <template v-if="item.file">
                      <v-btn outline color="primary" dark label v-for="(v, i) in item.value" :key="i" @click="$store.dispatch('FileDownload', $store.state.uploadsurl.receipt + basename(v))">{{ToDot(basename(v), 20)}}</v-btn>
                    </template>
                    <template v-else>
                      <div class="pointer desc_value" @click="Copy(item.value.toString())">
                        <named-text :name="item.key" :value="ToDot(item.value.toString(), strLimit)"></named-text>
                      </div>
                    </template>
                  </span>
                </span>
                <v-card >
                  <v-card-text>
                    <named-text :name="item.key" :value="ToDot(item.value.toString(), 1000)"></named-text>
                  </v-card-text>
                </v-card>
              </v-menu>
            </div>
          </div>
          <div v-else-if="type =='modify'">
            <slot :name="item.key"></slot>
          </div>
        </v-flex>
      </v-layout>
    </div>

  </div>
</template>

<script>
import NamedText from "@/components/NamedText";
export default {
  name: "FieldsDescriptor",
  components: {
    NamedText
  },
  props: {
    type: {
      type: String,
      default: "render"
    },
    items: {
      type: Array
    }
  },
  data() {
    return {
      strLimit: 100
    };
  },
  methods: {
    basename(path) {
      return path.replace(/\\/g, "/").replace(/.*\//, "");
    },
    ToDot(str, len) {
      if (str.length > len) {
        str = str.substr(0, len) + "...";
        str = str.replace(/,/g, ", ");
      }
      return str;
    },
    Copy(str) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(str).select();
      document.execCommand("copy");
      $temp.remove();
      this.$store.commit("snackbar", {
        type: "success",
        text: "클립보드에 복사되었습니다."
      });
    }
  }
};
</script>

<style scoped>
.desc_key {
  color: grey;
}
.desc_value {
  font-size: 17px;
  color: rgba(0, 0, 255, 0.7);
}
</style>
