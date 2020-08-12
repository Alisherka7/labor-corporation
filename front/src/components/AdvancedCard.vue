<template>
  <div class="maindiv">
    <slot v-if="!card"></slot>
    <v-card v-else height="100%" color="">
      <v-card-title class="pa-0 " v-if="!nohead">
        <v-subheader>
          <span class="subheading font-weight-bold">{{label}}</span>
          <pre>   </pre>

          <v-tooltip top>
            <span>{{help}}</span>
            <v-icon v-if="help" slot="activator" class="pointer--cursor">help</v-icon>
          </v-tooltip>
          <v-btn icon v-if="link" small :to="link">
            <v-icon>view_list</v-icon>
          </v-btn>
        </v-subheader>
      </v-card-title>
      <v-subheader v-if="sublabel" class="mx-4 sublabel">
        <span >*</span> {{sublabel}}
      </v-subheader>
      <v-card-text class="pl-4 py-0">
        <slot></slot>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
export default {
  name: "AdvancedCard",
  props: {
    label: {
      default: "",
      type: String
    },
    help: {
      default: "",
      type: String
    },
    link: {
      default: "",
      type: String
    },
    nohead: {
      default: false,
      type: Boolean
    },
    card: {
      default: false,
      type: Boolean
    },
    sublabel: {
      default: "",
      type: String
    }
  }
};
</script>

<style scoped>
.sublabel {
  color: rgba(220, 0, 0, 0.5);
}
.maindiv {
  height: 100%;
}
.pointer--cursor:hover {
  cursor: pointer;
}
</style>
