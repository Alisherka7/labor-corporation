<template>
  <modifiyer-box :items="filteredItems" @state="onStateChange" @modify="onModify" ref="modifybox">
    <named-field fieldName="비밀번호" :required="true" formKey="비밀번호" label="비밀번호" slot="비밀번호"></named-field>
    <named-field fieldName="비밀번호" :required="true" formKey="새비밀번호" label="새비밀번호" @modelChange="onPasswordChange" slot="새비밀번호"></named-field>
    <named-field fieldName="비밀번호확인" :required="true" formKey="no" label="새비밀번호" :syncValue.sync="passwordValue" slot="새비밀번호확인"></named-field>
  </modifiyer-box>
</template>


<script>
import ModifiyerBox from "@/components/ModifiyerBox";
import NamedField from "@/components/NamedField";
export default {
  name: "PasswordModify",
  components: {
    NamedField,
    ModifiyerBox
  },
  data() {
    return {
      state: "render",
      passwordValue: "",
      password: ""
    };
  },
  computed: {
    filteredItems() {
      if (this.state == "render") {
        return [
          {
            key: "아이디",
            value: this.id + ""
          },
          {
            key: "비밀번호",
            value: "*".repeat(this.password.length),
            icon: ""
          }
        ];
      } else if (this.state == "modify") {
        return [
          {
            key: "비밀번호"
          },
          {
            key: "새비밀번호"
          },
          {
            key: "새비밀번호확인"
          }
        ];
      }
    }
  },
  methods: {
    updateItems() {
      this.$store.commit("update", {
        type: "users",
        name: "get",
        success: result => {
          this.updateItemsData();
        }
      });
    },
    updateItemsData() {
      var result = this.$store.state.all.users[this.$store.state.all.id];
      
      this.password = result["비밀번호"];
      this.id = result["아이디"];
    },
    onModify(result) {
      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "modify_password",
          bag: result.fields
        },
        success: result => {
          this.$refs.modifybox.onModifyComplete();
          this.updateItems();
        },
        error: result => {
          this.$refs.modifybox.onModifyFail();
          if (result == "unmatched") {
            this.$store.commit("snackbar", {
              type: "error",
              text: "기존 비밀번호가 맞지 않습니다."
            });
          }
        }
      });
    },
    onStateChange(state) {
      this.state = state;
    },
    onPasswordChange(passwordValue) {
      this.passwordValue = passwordValue;
    }
  },
  mounted() {
    this.updateItemsData();
  }
};
</script>

