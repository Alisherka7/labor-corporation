<template>
  <v-flex xs8 sm6 md5 lg4 xl3>
    <v-card>
      <v-container>
        <div>
          <div class="mb-5">
            <img :src="logo" width="100%" />
          </div>
    
          <div>
            <AdvancedForm ref="form">
              <NamedField fieldName="아이디" autocomplete="username" required @enter="onLogin"></NamedField>
              <NamedField fieldName="비밀번호" autocomplete="current-password" required @enter="onLogin"></NamedField>
            </AdvancedForm>
          </div>
          <div xs12 class="mb-2">
            <v-btn block color="primary" large @click="onLogin()" :loading="loginLoading">로그인</v-btn>
            <v-btn block color="success" large @click="$emit('register')">회원가입</v-btn>
            <v-btn block color="warning" large @click="$emit('help')" flat>고객지원</v-btn>
          </div>
        </div>
      </v-container>

    </v-card>
  </v-flex>

</template>

<script>
import NamedField from "@/components/NamedField";
import AdvancedForm from "@/components/AdvancedForm";
export default {
  name: "LoginBox",
  props: ["logo"],
  data: function() {
    return {
      hidePassword: true,
      loginLoading: false
    };
  },
  components: {
    NamedField,
    AdvancedForm
  },
  methods: {
    onLogin() {
      let formget = this.$refs.form.get();
      let ajaxurl = this.$store.state.ajaxurl;
      if (formget.validated) {

        this.loginLoading = true;
        this.$store.dispatch("post", {
          data: {
            type: "user",
            name: "login",
            bag: formget.fields
          },
          success: () => {
            this.loginLoading = false;
            if (formget.fields["아이디"] == "admin") {
              this.$router.push("/home/receipt_manage");
            } else {
              this.$router.push("/home/receipt_history");
            }
          },
          error: result => {
            this.loginLoading = false;
            if (result == "password") {
              this.$store.commit("snackbar", {
                type: "warning",
                text: "비밀번호가 일치하지 않습니다."
              });
            } else if (result == "id") {
              this.$store.commit("snackbar", {
                type: "warning",
                text: "아이디가 존재하지 않습니다."
              });
            }
          }
        });
      }
    }
  }
};
</script>

