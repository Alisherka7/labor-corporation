<template>
  <div>
      <receipt-manage-box auth="admin"></receipt-manage-box>
  </div>
</template>

<script>
import ReceiptManageBox from "@/components/vision/receipt/manage/ReceiptManageBox";
export default {
  name: "ReceiptManage",
  mounted(){
    window.scrollTo(0, 0);

  },
  components: {
    ReceiptManageBox,
  }
};
</script>
