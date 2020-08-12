<template>
    <!-- 신청서 아님 -->
    <advanced-card v-if="fieldName =='비밀번호'">
        <named-field fieldName="비밀번호" :autocomplete="autocomplete" @modelChange="onModelChange" :required="required" :formKey="newPassword? '새비밀번호':'비밀번호'" :label="newPassword? '새 비밀번호':'비밀번호'"></named-field>
        <named-field v-if="passwordConfirm" fieldName="비밀번호확인" :syncValue.sync="modelValue" autocomplete="current-password" :required="required" formKey="no"></named-field>
    </advanced-card>
    <v-layout v-else wrap>
        <v-flex v-if="fieldName =='근무일자'" xs12>
            <advanced-card :label="get.label" card>
                <v-layout>
                    <v-flex>
                    </v-flex>
                    <named-field :value="prevalues? prevalues['근무일자_근무일자_달'] : ''" fieldName="근무일자_근무일자_달" :required="required"></named-field>
                    <v-flex>
                        <v-container fluid>
                            <v-layout wrap>
                                <v-flex xs1 v-for="i in 31" :key="i">
                                    <span>
                                        <div class="ml-1">{{i}}</div>
                                        <named-field :value="prevalues? prevalues['근무일자_일#' + i] : ''" :formKey="'근무일자_일#'+i" fieldName="근무일자_일" :label="' '">
                                        </named-field>
                                    </span>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-flex>
                </v-layout>
            </advanced-card>
        </v-flex>
        <template v-else-if="fieldName =='취득신고내외국인'">
            <v-flex xs4>
                <advanced-card label="내국인인가요?" card>
                    <v-radio-group v-model="personState">
                        <v-radio value="내국인" label="예"> </v-radio>
                        <v-radio value="외국인" label="아니요"> </v-radio>
                    </v-radio-group>
                </advanced-card>
            </v-flex>
            <template v-if="personState=='내국인'">
                <v-flex xs4>
                    <advanced-card :label="'주민등록번호'" card>
                        <NamedField :value="prevalues? prevalues['주민등록번호'] : ''" fieldName="주민등록번호" required></NamedField>
                    </advanced-card>
                </v-flex>
            </template>
            <template v-if="personState=='외국인'">

                <v-flex xs4>
                    <advanced-card :label="'외국인등록번호'" card>
                        <NamedField :value="prevalues? prevalues['외국인등록번호'] : ''" fieldName="외국인등록번호" required></NamedField>
                    </advanced-card>
                </v-flex>
                <v-flex xs4>
                    <advanced-card :label="'체류코드'" card>
                        <NamedField :value="prevalues? prevalues['체류코드'] : ''" fieldName="체류코드" required></NamedField>
                    </advanced-card>
                </v-flex>
                <v-flex xs4>
                    <advanced-card :label="'국가코드'" card>
                        <NamedField :value="prevalues? prevalues['국가코드'] : ''" fieldName="국가코드" required></NamedField>
                    </advanced-card>
                </v-flex>
            </template>
        </template>
        <template v-else-if="fieldName =='상실신고내외국인'">
            <v-flex xs4>
                <advanced-card label="내국인인가요?" card>
                    <v-radio-group v-model="personState">
                        <v-radio value="내국인" label="예"> </v-radio>
                        <v-radio value="외국인" label="아니요"> </v-radio>
                    </v-radio-group>
                </advanced-card>
            </v-flex>

            <v-flex xs4 v-if="personState == '내국인'">
                <NamedField :value="prevalues? prevalues['주민등록번호'] : ''" fieldName="주민등록번호" card required></NamedField>
            </v-flex>
            <v-flex xs4 v-else-if="personState == '외국인'">
                <NamedField :value="prevalues? prevalues['외국인등록번호'] : ''" fieldName="외국인등록번호" card required></NamedField>
            </v-flex>
        </template>

        <template v-else-if="fieldName == '피부양자'">
            <v-flex xs4>
                <named-field card fieldName="피부양자_피부양자파일" :required="required"></named-field>
            </v-flex>
            <v-flex xs4>
                <advanced-card label="피부양자가 몇 명인가요?" card help="드래그로 피부양자 수를 조절 할 수 있습니다.">
                    <v-slider min="0" max="10" v-model="dependentCount" always-dirty thumb-label="always" color="primary" :tick-labels="ticksLabels" ticks="always"></v-slider>
                </advanced-card>
            </v-flex>
            <v-flex xs4 v-for="i in dependentCount" :key="i">
                <advanced-card label="피부양자" card>
                    <named-field :value="prevalues? prevalues[`피부양자${i}#성명`] : ''" fieldName="성명" :formKey="`피부양자${i}#성명`" :required="true"></named-field>
                    <named-field :value="prevalues? prevalues[`피부양자${i}#주민등록번호`] : ''" fieldName="주민등록번호" :formKey="`피부양자${i}#주민등록번호`" :required="true"></named-field>
                    <named-field :value="prevalues? prevalues[`피부양자${i}#피부양자종류`] : ''" fieldName="피부양자종류" :formKey="`피부양자${i}#피부양자종류`" :required="true"></named-field>
                </advanced-card>
            </v-flex>
        </template>
        <template v-else-if="fieldName == '상실_퇴직전4개월평균임금'" sublabel="근로소득원천징수부 또는 급여대장 지급총액을 입력해주세요.">
            <advanced-card label="퇴직전4개월평균임금" card>
                <v-text-field type="number" label="1개월전 평균임금" v-model="average_pay.one" suffix="원"></v-text-field>
                <v-text-field type="number" label="2개월전 평균임금" v-model="average_pay.two" suffix="원"></v-text-field>
                <v-text-field type="number" label="3개월전 평균임금" v-model="average_pay.three" suffix="원"></v-text-field>
                <v-text-field type="number" label="4개월전 평균임금" v-model="average_pay.four" suffix="원"></v-text-field>
                <named-field type="number" fieldName="상실_퇴직전4개월평균임금" :value="((+average_pay.one + +average_pay.two + +average_pay.three + +average_pay.four) / 4) + ''"></named-field>
            </advanced-card>
        </template>
        <template v-else-if="fieldName =='사업장해지_신고종류와해지사유'">
            <v-flex xs4>
                <named-field :value="prevalues? prevalues['신고종류'] : ''" fieldName="사업장해지_신고종류" card @modelChange="onModelChange" :required="required"></named-field>
            </v-flex>
            <v-flex xs4 v-if="modelValue == '사무대행기관 수임해지'">
                <named-field :value="prevalues? prevalues['해지사유'] : ''" fieldName="사업장해지_해지사유" card :required="required"></named-field>
            </v-flex>
        </template>

    </v-layout>

</template>

<script>
import NamedField from "@/components/NamedField";
import AdvancedCard from "@/components/AdvancedCard";
export default {
  name: "NamedComplexField",
  components: {
    NamedField,
    AdvancedCard
  },
  props: {
    fieldName: {
      type: String
    },
    autocomplete: {
      type: String,
      default: "new-password"
    },
    required: {
      type: Boolean,
      default: false
    },
    passwordConfirm: {
      type: Boolean,
      default: false
    },
    prevalues: {},
    newPassword: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      average_pay: {
        one: 0,
        two: 0,
        three: 0,
        four: 0
      },
      personState: "내국인",
      dependentCount: 0,
      modelValue: ""
    };
  },
  methods: {
    onModelChange(modelValue) {
      this.modelValue = modelValue;
    }
  },
  mounted() {
    if (this.fieldName == "취득신고내외국인") {
      if ("외국인등록번호" in this.prevalues) {
        this.personState = "외국인";
      }
    } else if (this.fieldName == "상실신고내외국인") {
      if ("외국인등록번호" in this.prevalues) {
        this.personState = "외국인";
      }
    } else if (this.fieldName == "피부양자") {
      var tempDepCount = 0;
      for (var key in this.prevalues) {
        var depIndex = key.indexOf("피부양자");
        if (depIndex == -1) continue;
        var depNumber = key[4];
        if (tempDepCount < depNumber) tempDepCount = depNumber;
      }
      this.dependentCount = tempDepCount;
    }
  },
  computed: {
    ticksLabels() {
      var ticks = [];
      for (var i = 0; i < this.dependentCount; i++) {
        ticks.push(i);
      }
      return ticks;
    },
    get() {
      /** 변수선언 */
      let valid = this.$store.state.valid;
      let kinds = this.$store.state.kinds;
      let masks = this.$store.state.masks;

      let r = {};
      r.fieldName = this.fieldName;
      r.card = this.card;

      switch (this.fieldName) {
        case "사업장해지_신고사유":
          r.items = kinds.reason;
          break;
        case "근무일자":
          break;
        case "내외국인슬롯":
          break;
        case "비밀번호":
          break;
        case "사업장해지_신고종류와해지사유":
          break;
      }
      /** 후처리 */
      if (!r.label) r.label = r.fieldName;

      return r;
    }
  }
};
</script>
