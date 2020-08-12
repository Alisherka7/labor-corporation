<template>
    <div class="divider1">
        <div class="title font-weight-bold ma-4 ">
            <div>{{title}}</div>
        </div>
        <v-card class="mx-3 ">
            <slot></slot>
        </v-card>
    </div>
</template>

<script>
export default {
  name: "Divider1",
  props: {
    title: { type: String, default: "　" },
    subtitle: { type: String, default: "" }
  }
};
</script>

