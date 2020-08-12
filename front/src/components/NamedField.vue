<template>
  <advanced-card :card="get.card" :label="get.label" :help="get.help" :sublabel="get.sublabel" v-if="get.fieldType != 'void'">
    <v-text-field v-if="get.fieldType == 'v-text-field'" @keyup.enter="$emit('enter',$event)" :prefix="get.prefix" :suffix="get.suffix" :hint="get.hint" :persistent-hint="get.persistent_hint" :form-key="get.formKey" :type="get.type" :autocomplete="autocomplete" :label="get.label" :prepend-icon="get.icon" :rules="get.rules" :mask="get.mask" v-model="modelValue" :class="{'masked':get.mask}">
    </v-text-field>
    <v-autocomplete v-else-if="get.fieldType == 'v-autocomplete'" :form-key="get.formKey" :prepend-icon="get.icon" :items="get.items" :label="get.label" v-model="modelValue" :rules="get.rules">
      <template slot="item" slot-scope="data">
        <span>{{data.item}}</span>
      </template>
    </v-autocomplete>
    <v-radio-group v-else-if="get.fieldType == 'v-radio-group'" :form-key="get.formKey" :prepend-icon="get.icon" :label="get.label" v-model="modelValue" :rules="get.rules">
      <v-radio v-for="(item, i) in get.items" :key="i" :value="item" :label="item"></v-radio>
    </v-radio-group>
    <template v-else-if="get.fieldType == 'v-date-picker'">

          <!-- <v-btn flat color="primary" @click="menu_date = false">X</v-btn> -->
        <v-menu ref="menu1" :close-on-content-click="false" v-model="menu_date" :nudge-right="40" lazy transition="scale-transition" offset-y >
          <v-text-field slot="activator" v-model="modelValue" label="시작날짜" prepend-icon="event" readonly></v-text-field>
          <!-- <v-date-picker v-model="filters.date.start" no-title scrollable>
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="menu_date = false">Cancel</v-btn>
              <v-btn flat color="primary" @click="$refs.menu1.save(filters.date.start);">OK</v-btn>
          </v-date-picker> -->
        <v-date-picker class="named-date-picker" :form-key="get.formKey" :label="get.label" :prepend-icon="get.icon" :type="get.type" v-model="modelValue" :rules="get.rules">
        </v-date-picker>

        </v-menu>
      <v-text-field :form-key="get.formKey" :autocomplete="autocomplete" :value="modelValue" v-show="false"></v-text-field>
    </template>
    <v-textarea :solo="get.solo" v-else-if="get.fieldType == 'v-textarea'" :form-key="get.formKey" :label="get.label" :prepend-icon="get.icon" v-model="modelValue" :rules="get.rules" :auto-grow="get.autogrow" :rows="get.rows?get.rows: 4"></v-textarea>
    <v-checkbox v-else-if="get.fieldType == 'v-checkbox'" :form-key="get.formKey" :label="get.label" :prepend-icon="get.icon" v-model="modelValue" :rules="get.rules"></v-checkbox>
    <template v-else-if="get.fieldType == 'file-field'">
      <file-field :formKey="get.formKey" :multiple="get.multiple" :prepend-icon="get.icon" @change="onChange" :required="required"></file-field>
      <v-text-field :rules="get.rules" v-model="changed" readonly v-show="false">
      </v-text-field>
    </template>
    <v-switch v-else-if="get.fieldType == 'v-switch'" :form-key="get.formKey" :label="get.label" :prepend-icon="get.icon" v-model="modelValue"></v-switch>
    <div v-else-if="get.fieldType == 'void'"></div>
    <div v-else>ERROR: {{get}}</div>
  </advanced-card>
</template>

<script>
import AdvancedCard from "@/components/AdvancedCard";
import FileField from "@/components/FileField";

export default {
  name: "NamedField",
  components: {
    FileField,
    AdvancedCard
  },
  props: {
    fieldName: { type: String },
    formKey: {
      type: String,
      default: ""
    },
    label: {
      type: String,
      default: ""
    },
    card: { type: Boolean },
    autocomplete: {
      type: String,
      default: "off"
    },
    required: {
      type: Boolean,
      default: false
    },
    syncValue: {
      default: ""
    },
    value: {
      type: String,
      default: ""
    },
    suffix: {
      type: String,
      default: ""
    }
  },
  methods: {
    onChange(e) {
      this.$emit("change", e);
      this.changed = true;
    }
  },
  computed: {
    cSyncValue() {
      return this.syncValue;
    },
    get() {
      /** 변수선언 */
      let valid = this.$store.state.valid;
      let kinds = this.$store.state.kinds;
      let masks = this.$store.state.masks;

      let r = {};
      let rules = [];

      /** 전처리 */

      r.fieldName = this.fieldName;
      r.card = this.card;
      r.formKey = this.fieldName;
      this.modelValue = this.value;

      if (r.fieldName.indexOf("_") != -1) {
        let transName = r.fieldName.substr(r.fieldName.indexOf("_") + 1);
        r.label = transName;
        r.formKey = transName;
      }

      if (this.required) {
        rules.push(v => {
          return valid.required(v);
        });
      }

      if (this.formKey) {
        if (this.formKey == "no") {
          r.formKey = "";
        } else {
          r.formKey = this.formKey;
        }
      }

      /** 시작 */
      /**
       * 모든 아이콘 추가 O
       * 길이룰은 일반적으로 통합하여 추가 O
       * 글자제한 추가 O
       * 마스크 추가 O
       * 앞, 뒤에 붙는 '원' 같은 것 넣기 (suffix, prefix) O
       * 라벨추가 O
       */
      switch (this.fieldName) {
        /** v-text-field */
        case "아이디":
          rules.push(v => valid.min(v, 1));
          rules.push(v => valid.max(v, 15));
          // rules.push(v => valid.filter(v, ["english", "number"]));
          r.icon = "person";
          r.fieldType = "v-text-field";
          break;
        case "비밀번호":
          r.fieldType = "v-text-field";
          rules.push(v => valid.min(v, 4), v => valid.max(v, 15));
          r.icon = "lock_open";
          r.type = "password";
          break;
        case "비밀번호확인":
          r.fieldType = "v-text-field";
          r.icon = "lock";
          r.type = "password";
          rules.push(v => this.cSyncValue == v || "비밀번호가 같지 않습니다");
          r.label = "비밀번호 확인";
          break;
        case "성명":
          r.icon = "face";
          r.fieldType = "v-text-field";
          break;
        case "영문성명":
          r.icon = "face";
          r.fieldType = "v-text-field";
          r.label = "영문 성명";
          break;
        case "주민등록번호":
          r.icon = "work";
          r.fieldType = "v-text-field";
          rules.push(v => valid.min(v, 13), v => valid.max(v, 13));
          rules.push(v => valid.filter(v, ["number"]));
          r.mask = masks.regidentRegisterNumber;
          break;
        case "외국인등록번호":
          r.icon = "work";
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          rules.push(v => valid.min(v, 13), v => valid.max(v, 13));
          r.mask = masks.regidentRegisterNumber;
          break;
        case "소속회사":
          r.icon = "location_city";
          r.fieldType = "v-text-field";
          r.label = "소속 회사";
          break;
        case "월평균보수":
          r.icon = "money";
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.suffix = "원";
          r.label = "월 평균 보수";
          r.sublabel = "식대 등 비과세 제외";
          break;
        case "주간근로시간":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "access_time";
          r.suffix = "시간";
          break;
        case "주소정근로시간":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "access_time";
          r.suffix = "시간";
          r.label = "주소정 근로시간";
          break;
        case "월급여":
          r.icon = "money";
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.suffix = "원";
          r.label = "월 급여";
          break;
        case "근로일수":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "calendar_today";
          r.suffix = "일";
          r.label = "근로일수";
          break;
        case "보수지급기초일수":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "calendar_today";
          r.suffix = "일";
          r.label = "보수지급 기초일수";
          break;
        case "일평균근로시간":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "access_time";
          r.suffix = "시간";
          r.label = "일평균 근로시간";
          break;
        case "보수총액":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "money";
          r.suffix = "원";
          break;
        case "해당년도임금총액":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "money";
          r.suffix = "원";
          r.label = "해당 연도 임금총액";
          break;
        case "전년도임금총액":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "money";
          r.suffix = "원";
          r.label = "전년도 임금총액";
          break;
        case "임금총액":
          r.fieldType = "v-text-field";
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "money";
          r.suffix = "원";
          break;
        case "회사명":
          r.fieldType = "v-text-field";
          r.icon = "domain";
          break;
        case "사업자등록번호":
          r.fieldType = "v-text-field";
          r.icon = "bubble_chart";
          r.mask = masks.companyRegisterNumber;
          rules.push(v => valid.min(v, 10), v => valid.max(v, 10));
          rules.push(v => valid.filter(v, ["number"]));
          break;
        case "회사전화번호":
          r.fieldType = "v-text-field";
          rules.push(v => valid.max(v, 11));
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "call";
          // r.mask = masks.call;
          r.label = "회사 전화번호";
          break;
        case "회사팩스번호":
          r.fieldType = "v-text-field";
          rules.push(v => valid.max(v, 11));
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "local_printshop";
          // r.mask = masks.fax;
          r.label = "회사 팩스번호";
          break;
        case "담당자성명":
          r.fieldType = "v-text-field";
          r.icon = "person";
          r.label = "담당자 성명";
          break;
        case "담당자전화번호":
          r.fieldType = "v-text-field";
          rules.push(v => valid.max(v, 11));
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "call";
          r.mask = masks.call;
          r.label = "담당자 전화번호";
          break;
        case "상실_퇴직전4개월평균임금":
          r.fieldType = "v-text-field";
          r.label = "퇴직 전 4개월 평균임금";
          // r.sublabel = ;
          // rules.push(v => valid.filter(v, ["number"]));
          r.icon = "money";
          r.suffix = "원";
          break;

        /** RADIO GROUP */
        case "사업장해지_신고사유":
          r.fieldType = "v-radio-group";
          r.items = kinds.reason;
          r.label = "사업장해지 신고사유";
          break;

        case "사업장변경_변경사항":
          r.fieldType = "v-radio-group";
          r.items = kinds.change;
          r.label = "변경사항";
          break;
        case "사업장위탁_두루누리지원금여부":
          r.fieldType = "v-radio-group";
          r.items = kinds.yesNo;
          r.help =
            "10명 미만의 사업장에서 근로자 월 급여가 140만원 미만인 경우 신청하면 근로자와 사업장에 고용보험과 국민연금 보험료 일부를 지원해주는 제도입니다.";
          r.label = "두루누리지원금여부";
          break;
        case "상실_퇴사사유":
          r.fieldType = "v-radio-group";
          r.items = kinds.out;
          r.label = "퇴사사유";
          break;
        case "기타_신고내용":
          r.fieldType = "v-radio-group";
          r.items = kinds.another;
          r.label = "신고내용";
          break;
        case "고용형태":
          r.fieldType = "v-radio-group";
          r.items = kinds.employmentType;
          break;
        case "보험구분":
          r.fieldType = "v-radio-group";
          r.items = kinds.insuranceDivision;
          break;
        case "피부양자종류":
          r.fieldType = "v-radio-group";
          r.items = kinds.dependents;
          break;
        case "사업장해지_신고종류":
          r.fieldType = "v-radio-group";
          r.items = kinds.terminate;
          r.label = "신고종류";
          break;

        /**AUTOCOMPLETE */
        case "직종코드":
          r.fieldType = "v-autocomplete";
          r.items = kinds.occupations;
          r.icon = "code";
          break;
        case "체류코드":
          r.fieldType = "v-autocomplete";
          r.items = kinds.status_of_sojourns;
          r.icon = "code";
          break;
        case "국가코드":
          r.fieldType = "v-autocomplete";
          r.items = kinds.countrys;
          r.icon = "code";
          break;

        /**checkbox */
        case "근무일자_일":
          r.fieldType = "v-checkbox";
          break;

        /**datePICKER */
        case "사업장해지_해지일자":
          r.fieldType = "v-date-picker";
          break;
        case "지급월":
          r.fieldType = "v-date-picker";
          r.type = "month";
          break;
        case "근무일자_근무일자_달":
          r.fieldType = "v-date-picker";
          r.type = "month";
          break;
        case "퇴사일":
          r.fieldType = "v-date-picker";
          break;
        case "입사일":
          r.fieldType = "v-date-picker";
          break;
        case "월급여변경일자":
          r.fieldType = "v-date-picker";
          break;
        /** FILE */
        case "상실_첨부파일":
          r.fieldType = "file-field";
          r.multiple = true;
          break;
        case "기타_첨부파일":
          r.fieldType = "file-field";
          r.multiple = true;
          r.sublabel =
            "휴직신고와 일용직신고, 일자리안정자금 및 외국인임의가입 신청시 반드시 첨부파일을 첨부해주세요.";
          break;
        case "사업장변경_증빙서류업로드":
          r.fieldType = "file-field";
          r.label = "증빙서류 업로드";
          r.multiple = true;
          break;
        case "사업장해지_폐업증명서파일첨부":
          r.fieldType = "file-field";
          r.multiple = true;
          break;
        case "사업장위탁_위탁서파일첨부":
          r.fieldType = "file-field";
          r.multiple = true;
          break;
        case "사업장위탁_사업자등록증파일첨부":
          r.fieldType = "file-field";
          r.multiple = true;
          break;

        case "피부양자_피부양자파일":
          r.fieldType = "file-field";
          r.multiple = true;
          r.label = "피부양자 파일 등록";
          r.help = "피부양자 등록시 반드시 관련 서류를 첨부해주세요.";
          break;
        /** TEXTAREA */

        case "사업장위탁_사업장정보입력":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          break;
        case "사업장변경_상세내용":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          r.label = "상세내용";
          break;
        case "메모":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          break;

        case "취득신고외국인_메모":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          r.sublabel =
            "피부양자 등록이 필요할 경우 가족관계를 증명하는 서류를 팩스로 보내주세요.";
          break;
        case "사업장해지_해지사유":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          break;
        case "고객지원_문의":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          r.label = "어떤 문제가 있나요?";
          break;

        case "상실_구체적퇴사사유":
          r.icon = "note";
          r.solo = true;
          r.fieldType = "v-textarea";
          r.sublabel = "실업급여 신청시 작성해주세요.";
          break;

        case "서식_제목":
          r.fieldType = "v-text-field";
          r.label = "제목";
          r.icon = "title";
          break;
        case "서식_파일명":
          r.fieldType = "v-text-field";
          r.label = "파일명";
          r.icon = "title";
          r.hint = "띄어쓰기로 구분하세요";
          break;
        case "서식_파일연동":
          r.fieldType = "v-autocomplete";
          r.label = "연동";
          r.items = kinds.receipts;
          r.icon = "code";
          break;

        case "서식_내용":
          r.solo = true;
          r.fieldType = "v-textarea";
          r.label = "내용";
          r.icon = "note";
          break;
        case "서식_첨부파일":
          r.fieldType = "file-field";
          r.multiple = true;
          break;

        case "설정_공지사항메세지":
          r.solo = true;
          r.fieldType = "v-textarea";
          r.autogrow = true;
          r.rows = "1";
          r.icon = "note";
          break;
        case "설정_신청서업데이트간격":
          r.fieldType = "v-text-field";
          r.icon = "access_time";
          r.suffix = "초";
          rules.push(v => valid.filter(v, ["number"]));
          rules.push(v => valid.size_min(v, 10), v => valid.size_max(v, 7777));
          break;

        case "설정_보류완료파일자동삭제":
          r.fieldType = "v-switch";
          break;
        case "설정_파일최대크기":
          rules.push(v => valid.filter(v, ["number"]));
          r.icon = "pan_tool";
          r.fieldType = "v-text-field";
          r.suffix = "MB";
          break;
        case "void":
          r.fieldType = "void";
          break;

          break;
        default:
          console.error("NO FIELD NAME: " + r.fieldName);
          break;
      }

      if (rules.length == 0) {
        if (r.fieldType == "v-text-field") {
          rules.push(v => valid.max(v, 300));
        } else if (r.fileField == "v-textarea") {
          rules.push(v => valid.max(v, 2000));
        }
      }

      /** 후처리 */
      if (!r.label) r.label = r.fieldName;
      r.rules = rules;

      if (this.suffix) {
        r.suffix = this.suffix;
      }
      if (
        (r.fieldType == "v-radio-group" || r.fieldType == "v-autocomplete") &&
        this.value == ""
      ) {
        this.modelValue = r.items[0];
      }

      if (r.fieldType == "v-checkbox" || r.fieldType == "v-switch") {
        if (this.value == "false") {
          this.modelValue = false;
        } else if (this.value == "true") {
          this.modelValue = true;
        }
      }

      if (r.fieldType == "v-date-picker" && this.value == "") {
        var date = new Date();
        var mm = date.getMonth() + 1;
        var dd = date.getDate();
        this.modelValue = [
          date.getFullYear(),
          (mm > 9 ? "" : "0") + mm,
          (dd > 9 ? "" : "0") + dd
        ].join("-");
      }

      if (this.label) {
        r.label = this.label;
      }

      /** 종료 */
      return r;
    }
  },
  data() {
    return {
      modelValue: "",
      changed: false,
      menu_date: false
    };
  },
  watch: {
    modelValue() {
      this.$emit("modelChange", this.modelValue);
    }
  }
};
</script>

<style scoped>
.named-date-picker {
  zoom: 0.9;
}
</style>
