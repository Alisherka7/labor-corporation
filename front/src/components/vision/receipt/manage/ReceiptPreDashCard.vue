<template>
  <receipt-dash-card :items="preitems"></receipt-dash-card>
</template>

<script>
import ReceiptDashCard from "@/components/lib/ReceiptDashCard";
export default {
  name: "ReceiptPreDashCard",
  props:['items'],

  components: {
    ReceiptDashCard,
  },
  computed: {
    preitems() {
      // var receiptLengths = this.$store.getters.receiptLengths;
      // console.log(this.items);
      var lengths = {};
      // lengths.apply = this.items.filter(item=>item['상태'] == 'apply').length;
      // lengths.refuse = this.items.filter(item=>item['상태'] == 'refuse').length;
      // lengths.cancel = this.items.filter(item=>item['상태'] == 'cancel').length;
      // lengths.accept = this.items.filter(item=>item['상태'] == 'accept').length;

      var objReceipt = Object.values(this.$store.state.all.receipts);
      lengths.apply = objReceipt.filter(receipt => receipt.meta.state == 'apply' ? true : false).length;
      lengths.refuse = objReceipt.filter(receipt => receipt.meta.state == 'refuse' ? true : false).length;
      lengths.cancel = objReceipt.filter(receipt => receipt.meta.state == 'cancel' ? true : false).length;
      lengths.accept = objReceipt.filter(receipt => receipt.meta.state == 'accept' ? true : false).length;
      lengths.sum = +lengths.apply + +lengths.refuse + +lengths.cancel + +lengths.accept;
      return [
        {
          title: "신청",
          description: "this is for applycation",
          subtitle: lengths.apply,
          icon: "favorite",
          color: "purple lighten-1"
        },
        {
          title: "보류",
          description: "this is for refuse",
          subtitle: lengths.refuse,
          icon: "wb_cloudy",
          color: "indigo lighten-1"
        },
        {
          title: "완료",
          description: "this is for compelete",
          subtitle: lengths.accept,
          icon: "wb_sunny",
          color: "success"
        },
        {
          title: "취소",
          description: "this is for cancel",
          subtitle: lengths.cancel,
          icon: "brightness_2",
          color: "secondary lighten-1"
        }
      ];
    }
  }
};
</script>
