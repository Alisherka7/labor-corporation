<template>
    <div>
        <v-jumbotron dark :src="require('@/assets/jumbo.jpeg')" gradient="to top right, rgba(0,0,0,0.5), rgba(255,255,255,0.5)">
            <v-container fill-height>
                <v-layout align-center>
                    <v-flex text-xs-center>
                        <div class="display-1">
                            {{$store.state.all.users[$store.state.all.id]['회사명']}}
                        </div>
                        <div class="subheading font-weight-light">
                            <named-text name="사업자등록번호" :value="$store.state.all.users[$store.state.all.id]['사업자등록번호']"></named-text>
                        </div>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-jumbotron>
        <v-layout>
            <v-flex>
                <divider1 title="개인정보">
                    <divider3 title="개인정보수정" subtitle="개인정보 수정 할 수 있습니다.">
                        <privacy-modify></privacy-modify>
                    </divider3>
                    <divider3 title="비밀번호수정" subtitle="비밀번호를 수정 할 수 있습니다.">
                        <password-modify></password-modify>
                    </divider3>
                    <divider3 title="로그아웃">
                        <v-btn @click="logout" color="red lighten-2" dark>logout</v-btn>
                    </divider3>
                </divider1>
            </v-flex>
            <v-flex>
                <divider1 title="회사 관리">
                    <divider3 title="관리회사" subtitle="관리하는 회사를 추가하고 제거 할 수 있습니다.">
                        <c-m-manage></c-m-manage>
                    </divider3>
                </divider1>

            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import PasswordModify from "@/components/vision/user/privacy/PasswordModify";
import PrivacyModify from "@/components/vision/user/privacy/PrivacyModify";
import CMManage from "@/components/vision/user/privacy/CMManage";
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import NamedText from "@/components/NamedText";
export default {
  name: "Privacy",
  components: {
    PasswordModify,
    PrivacyModify,
    CMManage,
    Divider1,
    Divider3,
    NamedText
  },
  methods: {
    logout() {
      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "logout",
          success: result => {
            this.$router.push("/about/welcome");
          }
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "로그아웃에 실패했습니다."
          });
        }
      });
    }
  },
  mounted() {
    window.scrollTo(0, 0);
  }
};
</script>

