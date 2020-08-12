<template>
  <div>
    <divider1 title="신청서">
      <divider3 >
        <!-- title="온라인 신청서" subtitle="양식별로 선택하여 신청하세요" -->
        <ReceiptApplyBox></ReceiptApplyBox>
      </divider3>
    </divider1>
    <divider1 title="서식">
      <divider3 title="서식게시판" subtitle="여기에서 서식을 받아가세요">
        <FormatTable></FormatTable>
      </divider3>
    </divider1>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import FormatTable from "@/components/vision/format/FormatTable";
import ReceiptApplyBox from "@/components/vision/receipt/ReceiptApplyBox";

export default {
  name: "Receipt",
  mounted(){
    window.scrollTo(0, 0);

  },
  components: {
    FormatTable,
    ReceiptApplyBox,
    Divider1,
    Divider3
  },
  data() {
    return {
      selected: -1,
      receiptformModal: false
    };
  },
  methods: {
    onSelect(i) {
      this.selected = i;
      this.receiptformModal = !this.receiptformModal;
    },
    onModalClose() {
      this.receiptformModal = !this.receiptformModal;
      // location.reload();
    }
  }
};
</script>


