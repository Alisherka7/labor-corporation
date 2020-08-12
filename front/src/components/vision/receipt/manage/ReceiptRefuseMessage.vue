<template>
  <div>
    <divider3 title="보류 메세지" subtitle="보류 메세지 작성하기">
      <v-textarea v-model="message" label="보류 메세지"></v-textarea>
      <v-btn color="primary" @click="onSave" :loading="loading">SAVE</v-btn>
    </divider3>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
export default {
  name: "ReceiptRefuseMessage",
  components: {
    Divider1,
    Divider3
  },
  props: ["index"],
  data() {
    return {
      message: "",
      loading: false
    };
  },
  methods: {
    onSave() {
      this.loading = true;
      let index = this.index;
      let ajaxurl = this.$store.state.ajaxurl;
      this.$store.dispatch("post", {
        data: {
          type: "receipt",
          name: "set_message",
          bag: {
            index: index,
            message: this.message
          }
        },
        success: result => {
          this.$store.commit("update", {
            type: "receipts",
            name: "get",
            bag: {
              index: index
            },
            success: result => {
              this.$emit("saved");
              this.loading = false;
            }
          });
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "보류 메세지를 셋팅하지 못했습니다."
          });
        }
      });
    }
  }
};
</script>

<style>
</style>
