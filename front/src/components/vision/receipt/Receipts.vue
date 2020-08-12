<template>
  <v-container>
    <template v-for="(v,i) in fields[name]">
      <named-complex-field required v-if="complexFieldsTop.indexOf(v) != -1" :fieldName="v" :key="i" card :prevalues="reapplyFixedReceipt">
      </named-complex-field>
    </template>
    <v-layout wrap fill-height>
      <template v-for="(v,i) in fields[name]">
        <v-flex xs4 v-if="complexFieldsTop.indexOf(v) == -1 && complexFieldsBottom.indexOf(v) == -1" :key="i">
          <named-field :required="noRequired.indexOf(v) == -1" :fieldName="v" card :value="reapplyFixedReceipt[GetTransName(v)]"></named-field>
        </v-flex>
      </template>
    </v-layout>
    <template v-for="(v,i) in fields[name]">
      <named-complex-field :required="noRequired.indexOf(v) == -1" v-if="complexFieldsBottom.indexOf(v) != -1" :fieldName="v" :key="i" card :prevalues="reapplyFixedReceipt">
      </named-complex-field>
    </template>
  </v-container>
</template>

<script>
import NamedField from "@/components/NamedField";
import NamedComplexField from "@/components/NamedComplexField";
export default {
  name: "Receipts",

  props: ["name", "step"],
  components: {
    NamedField,
    NamedComplexField
  },
  computed: {
    reapplyFixedReceipt() {
      var r = {};
      if (this.$store.state.reapply.valid) {
        // 테스트용으로 임시로 지움
        this.$store.state.reapply.valid = false;

        var receipt = this.$store.state.all.receipts[
          this.$store.state.reapply.index
        ];
        for (var key in receipt.data) {
          var value = receipt.data[key];
          // 파일필드 제외
          if (receipt.meta.file_fields.indexOf(key) != -1) {
            continue;
          }
          r[key] = value;
        }
      }
      return r;
    }
  },
  methods: {
    GetTransName(name) {
      return name.substr(name.indexOf("_") + 1);
    }
  },
  data() {
    return {
      fields: {
        /**내외국인슬롯제외 */
        "사업장 변경신고": [
          "사업장변경_증빙서류업로드",
          "사업장변경_변경사항",
          "사업장변경_상세내용"
        ],
        "폐업 및 수임해지": [
          "사업장해지_신고종류와해지사유",
          "사업장해지_신고사유",
          "사업장해지_폐업증명서파일첨부",
          "사업장해지_해지일자"
        ],
        "사업장 위탁신고": [
          "사업장위탁_위탁서파일첨부",
          "사업장위탁_사업자등록증파일첨부",
          "사업장위탁_두루누리지원금여부",
          "사업장위탁_사업장정보입력"
        ],
        "취득신고 내국인": [
          "성명",
          "주민등록번호",
          // "소속회사",
          "월평균보수",
          "주간근로시간",
          "직종코드",
          "피부양자",
          "고용형태",
          "입사일",
          "메모"
        ],
        "취득신고 외국인": [
          "성명",
          "영문성명",
          "외국인등록번호",
          // "소속회사",
          "월급여",
          "주소정근로시간",
          "고용형태",
          "입사일",
          "직종코드",
          "체류코드",
          "국가코드",
          "취득신고외국인_메모"
        ],
        "취득신고 일용직": [
          "성명",
          "취득신고내외국인",
          "보험구분",
          // "소속회사",
          "직종코드",
          "근무일자",
          "근로일수",
          "지급월",
          "보수지급기초일수",
          "일평균근로시간",
          "보수총액",
          "임금총액",
          "메모"
        ],
        상실신고: [
          // "소속회사",
          "성명",
          "고용형태",
          "퇴사일",
          "상실신고내외국인",
          "월급여",
          "해당년도임금총액",
          "전년도임금총액",
          "상실_퇴사사유",
          "상실_구체적퇴사사유",
          "상실_퇴직전4개월평균임금",
          "상실_첨부파일"
        ],
        기타신고: [
          
          "기타_신고내용",
          "기타_첨부파일",
          "void",
          "성명",
          "주민등록번호",
          "월급여",
          "월급여변경일자",
          
          "피부양자",
          "메모"
        ]
      },
      complexFieldsTop: [
        "사업장해지_신고종류와해지사유",
        "상실신고내외국인"

        // "사업장해지_신고사유",
      ],
      complexFieldsBottom: [
        "피부양자",
        "근무일자",
        "취득신고내외국인",
        "상실_퇴직전4개월평균임금"
        // "상실신고내외국인"
      ],
      noRequired: ["사업장위탁_사업장정보입력", "피부양자", "메모", "상실_구체적퇴사사유"]
    };
  }
};
</script>

