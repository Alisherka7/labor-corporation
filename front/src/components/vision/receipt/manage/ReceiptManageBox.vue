<template>
  <div>
    <divider1 :title="auth=='admin'? '신청서 관리':'신청내역 조회'">
      
      <!-- 대쉬카드 -->
      <divider3 title="통계">
        <receipt-pre-dash-card :items="items"></receipt-pre-dash-card>
      </divider3>
      <!-- 필터 -->
      <divider3 title="신청내역">
        <receipt-filter :auth="auth" @change="onFilterChange"></receipt-filter>
        <!-- 테이블 -->
        <receipt-table @click="onReceiptDataClick" :items="items" :search="filters.search" :getTimeFormat="getTimeFormat"></receipt-table>
        
      </divider3>
      
    </divider1>
    <v-layout>
        <v-spacer></v-spacer>
          <div class="text-xs-right body-1 pt-2 pr-2">최근 업데이트: {{updatedTime}}</div>
          <v-progress-circular :indeterminate="loading" class="mr-2"></v-progress-circular>
      </v-layout>
    <!-- 다이얼로그 -->
    <v-dialog v-model="dialog" width="1200px">
      <receipt-one-manage class="overhidden" :auth="auth" @refuseMessageSaved="onRefuseMessageSaved" :modalItem="modalItem" @move="updateModalItem"></receipt-one-manage>
    </v-dialog>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import ReceiptPreDashCard from "@/components/vision/receipt/manage/ReceiptPreDashCard";
import ReceiptFilter from "@/components/vision/receipt/manage/ReceiptFilter";
import ReceiptTable from "@/components/vision/receipt/manage/ReceiptTable";
import ReceiptOneManage from "@/components/vision/receipt/manage/ReceiptOneManage";
export default {
  name: "ReceiptManageBox",
  props: ["auth"],
  components: {
    ReceiptFilter,
    ReceiptPreDashCard,
    ReceiptTable,
    ReceiptOneManage,
    Divider1,
    Divider3
  },
  data() {
    return {
      updatedTime: false,
      loading: false,
      items: [],
      modalItem: {},
      filters: {},

      message: "",
      dialog: false,
      intervalLoop: -1
    };
  },
  mounted() {
    this.updateItems();
    this.updatedTime = new Date().toLocaleString();

    this.intervalLoop = setInterval(() => {
      console.log("아이템이 업데이트 되었습니다.");
      this.$store.commit("update", {
        name: "get_all",
        type: "receipts",
        success: result => {
          this.updatedTime = new Date().toLocaleString();
          this.updateItems();
        }
      });
    }, this.$store.state.all.settings["receipt_manage_update_interval"] * 1000 || 60000);
  },
  destroyed() {
    clearInterval(this.intervalLoop);
  },
  watch: {
    dialog(on) {
      if (!on) {
        this.updateItems();
      }
    }
  },
  methods: {
    updateModalItem(index) {
      let receipts = this.$store.state.all.receipts;
      let users = this.$store.state.all.users;
      let user = false;
      if (receipts[index].meta.id in users) {
        user = users[receipts[index].meta.id];
      }
      let modalItem = {};
      if (!receipts.hasOwnProperty(index)) return false;
      modalItem.receipt_data = receipts[index].data;
      modalItem.receipt_meta = receipts[index].meta;
      modalItem.user = user;
      modalItem.exists = true;
      this.modalItem = modalItem;
      this.message = this.modalItem.receipt_meta.message;
      return true;
    },
    updateItems() {
      let items = [];

      let receipts = this.$store.state.all.receipts;
      let users = this.$store.state.all.users;
      for (let i in receipts) {
        let receipt = receipts[i];
        let item = {};
        let user = users[receipt.meta.id];
        // 필터링

        if (!this.filters.state[receipt.meta.state]) continue;
        var startDate = new Date(this.filters.date.start);
        var endDate = new Date(this.filters.date.end);
        var receiptDate = new Date(
          this.getTimeFormat(2, receipt.meta.apply_time)
        );
        if (startDate != "Invalid Date" && startDate > receiptDate) continue;
        if (endDate != "Invalid Date" && endDate < receiptDate) continue;

        if (
          this.auth != "admin" &&
          receipt.meta["id"] != this.$store.state.all.id
        ) {
          console.log(this.auth, receipt.meta["id"], this.$store.state.all.id);
          continue;
        }
        item["index"] = receipt.meta.index;
        item["상태"] = receipt.meta.state;
        item["신청서명"] = receipt.meta.receipt_name;
        item["신청시간"] = receipt.meta.apply_time;
        item["관리회사명"] = receipt.meta.manage_company
          ? receipt.meta.manage_company["회사명"]
          : "";
        item["회사명"] = user ? user["회사명"] : "";
        item["회사전화번호"] = user ? user["회사전화번호"] : "";
        item["담당자성명"] = user ? user["담당자성명"] : "";
        item["담당자전화번호"] = user ? user["담당자전화번호"] : "";
        item["신청번호"] = receipt.meta.index;
        items.push(item);
      }

      this.items = items;
    },
    onRefuseMessageSaved() {
      this.updateModalItem(this.modalItem.receipt_meta.index);
    },

    onReceiptDataClick(item) {
      this.dialog = !this.dialog;
      this.updateModalItem(item["신청번호"]);
    },
    onStateTabClick(state) {
      this.receipt["상태"] = state;
    },
    onFilterChange(filters) {
      this.filters = filters;
      this.updateItems();
    },

    getTimeFormat(type, time) {
      var date = new Date(time * 1000);
      var mm = date.getMonth() + 1;
      var dd = date.getDate();
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var seconds = date.getSeconds();
      var ymd = [
        date.getFullYear(),
        (mm > 9 ? "" : "0") + mm,
        (dd > 9 ? "" : "0") + dd
      ].join("-");

      var hms = [
        (hours > 9 ? "" : "0") + hours,
        (minutes > 9 ? "" : "0") + minutes,
        (seconds > 9 ? "" : "0") + seconds
      ].join(":");

      switch (type) {
        case 1:
          return ymd + " " + hms;
        case 2:
          return ymd;
      }
    }
  }
};
</script>
<style scoped>
.overhidden {
  overflow-y: hidden;
  overflow-x: hidden;
}
</style>



