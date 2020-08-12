<template>
  <v-data-table rows-per-page-text="페이지별 갯수" :search="search" :pagination.sync="pagination" :headers="receiptDataHeaders" :items="items" :rows-per-page-items="[30, 70, 100, 250]">
    <template slot="no-data">
      <v-alert :value="true" color="warning" icon="warning">
        신청서 데이터가 존재하지 않습니다.
      </v-alert>
    </template>
    <template slot="items" slot-scope="props">
      <td v-for="(v, i) in props.item" :key="i" @click="$emit('click', props.item)" class="piece" v-if="i !='신청번호'">
        <template v-if="i == '상태'">
          <v-chip label :color="getStateChipData(v).color" class="px-3">
              <v-avatar size="30">
              <v-icon dark>{{getStateChipData(v).icon}}</v-icon>
              </v-avatar>
            <span class="white--text">{{getStateChipData(v).text}}</span>
          </v-chip>
        </template>
        <template v-else-if="i =='신청시간'">{{getTimeFormat(1,v)}}</template>
        <template v-else><named-text :name="i" :value="v"></named-text> </template>
      </td>
    </template>
  </v-data-table>
</template>

<script>
import NamedText from "@/components/NamedText";
export default {
  name: "ReceiptTable",
  components:{
    NamedText,
  },
  props: ["search", "getTimeFormat", "items"],
  data() {
    return {
      pagination: {
        sortBy: "신청번호",
        descending: true
      },
      receiptDataHeaders: [
        { text: "신청번호", value: "index" },
        { text: "상태", value: "상태" },
        { text: "신청서명", value: "신청서명" },
        { text: "신청시간", value: "신청시간" },
        { text: "관리회사명", value: "관리회사명" },
        { text: "회사명", value: "회사명" },
        { text: "회사전화번호", value: "회사전화번호" },
        { text: "담당자성명", value: "담당자성명" },
        { text: "담당자전화번호", value: "담당자전화번호" }
        // { text: "신청번호", value: "신청번호" },
      ]
    };
  },
  methods: {
    getStateChipData(state) {
      var data = {};
      switch (state) {
        case "apply":
          data.color = "primary";
          data.text = "신청";
          data.icon = "local_airport";
          break;
        case "refuse":
          data.color = "warning";
          data.text = "보류";
          data.icon = "alarm";

          break;
        case "cancel":
          data.color = "secondary";
          data.text = "취소";
          data.icon = "airplanemode_inactive";

          break;
        case "accept":
          data.color = "success";
          data.text = "완료";
          data.icon = "done";

          break;
      }
      return data;
    }
  }
};
</script>

<style scoped>
.piece {
  cursor: pointer;
}
</style>