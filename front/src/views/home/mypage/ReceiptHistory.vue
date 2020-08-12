<template>
  <div>
    <receipt-manage-box auth="user"></receipt-manage-box>
  </div>
</template>

<script>
import ReceiptManageBox from "@/components/vision/receipt/manage/ReceiptManageBox";
export default {
  mounted(){
    window.scrollTo(0, 0);

  },
  name: "ReceiptHistory",
  components:{
    ReceiptManageBox
  }
}
</script>
