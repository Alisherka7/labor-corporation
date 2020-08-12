<template>
  <v-container grid-list-md>
    <v-layout>
      <v-flex xs3 v-for="(receipt, i) in $store.state.receipt.receipts" :key="i" @click="onSelect(receipt.name)" class="box">
        <v-card class="receipt_card" :class="{'selected':$store.getters.receipt.name == receipt.name}">
          <div class="text-xs-center category mt-1" :class="['category_'+ receipt.category]">{{receipt.category}}</div>
          <div class="text-xs-center mt-3">
            <img :src="receipt.src" width="60%" height="60%" />
          </div>
          <v-card-text class="text-xs-center subheading font-weight-bold">{{receipt.name}}</v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  name: "ReceiptSelect",
  data() {
    return {};
  },
  methods: {
    onSelect(name) {
      this.$store.state.receipt.selected = name;
      this.$emit("selected");
    }
  }
};
</script>

<style scoped>
/* .category_피보험자관리{
  color: blueviolet;
}

.category_사업장관리{
  color: chocolate;
}
.category_기타관리{
  color: indigo;
} */
.category{
  opacity: 0.5;
}
.receipt_card {
  height: 100%;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border: 2px solid white;
  border-radius: 5%;
}
.receipt_card:hover {
  opacity: 0.5;
  cursor: pointer;
  border: 2px solid white;
  border-radius: 5%;
  /* background-color: cornsilk; */
}
.selected {
  /* background-color: rgba(255, 255, 0, 0.3) !important; */
  border: 2px solid rgba(200, 0, 0, 0.5) !important;
  border-radius: 5%;
}
</style>

