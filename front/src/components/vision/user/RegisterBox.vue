<template>
  <v-flex>
    <v-stepper alt-labels v-model="step">
      <div class="text-xs-center mt-5 mb-2" v-if="step != 4">
        <div class="headline font-weight-light">
          노무법인 회원가입
        </div>
        <div class="subheading font-weight-light">
          Welcome to visit Laber Corporation
        </div>
      </div>

      <v-stepper-header class="elevation-0" v-if="step != 4">
        <v-stepper-step step="1" :complete="step > 1">기본정보</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="2" :complete="step >2">회사정보</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="3" :complete="step > 3">담당자정보</v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step step="4" :complete="step > 4">회원가입 완료</v-stepper-step>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content step="1">
          <NavigateCard @result="formHandler" :nextbtnLoading="nextbtnLoading">
            <named-field fieldName="아이디" required prependIcon="person"></named-field>
            <NamedComplexField fieldName="비밀번호" required passwordConfirm></NamedComplexField>
          </NavigateCard>
        </v-stepper-content>
        <v-stepper-content step="2">
          <NavigateCard @result="formHandler" @before="step-=1">
            <NamedField fieldName="회사명" required></NamedField>
            <NamedField fieldName="사업자등록번호" required></NamedField>
            <NamedField fieldName="회사팩스번호"></NamedField>
            <NamedField fieldName="회사전화번호" required></NamedField>
            <NamedField fieldName="직종코드" required></NamedField>
          </NavigateCard>
        </v-stepper-content>
        <v-stepper-content step="3">
          <NavigateCard @result="formHandler" @before="step-=1" :nextbtnLoading="nextbtnLoading">
            <NamedField fieldName="담당자성명" required></NamedField>
            <NamedField fieldName="담당자전화번호" required></NamedField>
          </NavigateCard>
        </v-stepper-content>
        <v-stepper-content step="4">
          <div class="pointer" @click="$emit('complete')">
            <img :src="require('@/assets/balloons.gif')" width="100%" />
            <v-btn class="headline" large color="primary" dark block>환영합니다</v-btn>
          </div>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </v-flex>
</template>

<script>
import NavigateCard from "@/components/NavigateCard";
import NamedField from "@/components/NamedField";
import NamedComplexField from "@/components/NamedComplexField";
export default {
  name: "RegisterBox",
  data() {
    return {
      step: 1,
      fields: {},
      nextbtnLoading: false
    };
  },
  components: {
    NavigateCard,
    NamedField,
    NamedComplexField
  },
  methods: {
    formHandler(result) {
      let ajaxurl = this.$store.state.ajaxurl;
      let data = {};
      if (result.validated) {
        for (let key in result.fields) {
          this.fields[key] = result.fields[key];
        }
        switch (this.step) {
          case 1:
            this.nextbtnLoading = true;
            this.$store.dispatch("post", {
              data: {
                type: "user",
                name: "id_check",
                bag: {
                  아이디: result.fields["아이디"]
                }
              },
              success: () => {
                this.nextbtnLoading = false;
                this.step++;
              },
              error: () => {
                this.nextbtnLoading = false;
                this.$store.commit("snackbar", {
                  type: "error",
                  text: "이미 존재하는 아이디입니다."
                });
              }
            });
            break;
          case 2:
            this.step++;
            break;
          case 3:
            this.nextbtnLoading = true;
            this.$store.dispatch("post", {
              data: {
                type: "user",
                name: "add",
                bag: this.fields
              },
              success: () => {
                this.step++;
                this.nextbtnLoading = false;
              },
              error: () => {
                this.nextbtnLoading = false;
              }
            });

            break;
          case 4:
            break;
        }
      }
    }
  }
};
</script>
