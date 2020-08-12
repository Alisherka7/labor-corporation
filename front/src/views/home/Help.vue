<template>
  <div>
    <divider1 title="고객지원">
      <divider3 title="문의하기">
        <v-jumbotron :gradient="gradient" :src="require('@/assets/support2.jpg')" height="700px">
          <v-container fill-height>
            <v-layout align-center>
              <v-flex text-xs-center>
                <div class="white--text display-2 text-xs-center font-weight-thin mb-3">어떤 문제가 있나요?</div>
                <div class="text-xs-center">
                  <v-btn color="yellow lighten-2" large @click="onHelpClick">1:1 문의하기</v-btn>
                </div>
              </v-flex>
            </v-layout>
          </v-container>

          <!-- <HelpApplyBox></HelpApplyBox> -->
        </v-jumbotron>
      </divider3>
      <v-divider></v-divider>
      <divider3 title="찾아오시는길">
        <div class="text-xs-center">
        </div>
        <ContactBox :call="call" :location="location" :email="email" :googlemap="googlemap"></ContactBox>
      </divider3>
    </divider1>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import ContactBox from "@/components/vision/help/ContactBox";
// import HelpApplyBox from "@/components/vision/HelpApplyBox";
export default {
  name: "Help",
  mounted(){
    window.scrollTo(0, 0);

  },
  data() {
    return {
      gradient: "to top right, rgba(0,0,0, 0.8), rgba(255,255,255, .5)",
      call: "010 2680 2370",
      location: "서울특별시 서대문구 통일로 87",
      email: "agan397@gmail.com",
      googlemap:
        "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.67363755065!2d126.96632751520715!3d37.56275227979869!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca289a6c9cd7b%3A0xbbdf63b00c68cc56!2z64yA7ZWc66-86rWtIOyEnOyauO2KueuzhOyLnCDshJzrjIDrrLjqtawg66-46re864-ZIO2GteydvOuhnCA4Nw!5e0!3m2!1sko!2sus!4v1532681823779"
    };
  },
  components: {
    ContactBox,
    Divider1,
    Divider3
    // HelpApplyBox
  },
  methods: {
    onHelpClick() {
      if (this.$store.state.all.id == "admin") {
        this.$store.commit("snackbar", {
          type: "info",
          text: "관리자는 문의할 수 없습니다.",
        });
      } else this.$store.commit("chat", this.$store.state.all.id);
    }
  }
};
</script>
