<template>
    <v-card flat class="divider3" :class="[type == 'dialog'?'mb-0' : 'mb-4']">
        <v-card-title>
            <div>
                <div class="font-weight-bold">{{title}}</div>
                <div class="grey--text">{{subtitle}}</div>
            </div>

        </v-card-title>
        <v-card-text class="py-0 ">
            <slot></slot>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
  name: "Divider3",
  props: {
    type: { type: String, default: "" },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" }
  }
};
</script>

<style>
</style>
