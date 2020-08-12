<template>
  <v-container class="container">
    <v-layout justify-center wrap fill-height align-center>
      <LoginBox :logo="logo" @register="register_dialog = !register_dialog" @help="helpModal = !helpModal"></LoginBox>
    </v-layout>
    <v-dialog v-model="register_dialog" max-width="800px">
      <RegisterBox @complete="register_dialog = !register_dialog"></RegisterBox>
    </v-dialog>
    
    <v-dialog v-model="helpModal" max-width="800px">
      <divider3 type="dialog" title= "고객지원" subtitle="언제든지 연락주세요">
      <ContactBox :call="call" :location="location" :email="email" :googlemap="googlemap"></ContactBox>
      </divider3>
    </v-dialog>
  </v-container>

</template>

<script>
import Divider3 from "@/components/lib/divider/Divider3";
import ContactBox from "@/components/vision/help/ContactBox";
import LoginBox from "@/components/vision/user/LoginBox";
import RegisterBox from "@/components/vision/user/RegisterBox";
export default {
  name: "Login",
  components: {
    LoginBox,
    RegisterBox,
    ContactBox,
    Divider3
  },

  data: function() {
    return {
      register_dialog: false,
      helpModal: false,
      logo: require("@/assets/logo/icon.png"),
      call: "010 3333 4444",
      location: "서울특별시 서대문구 통일로 87",
      email: "dahuin000@gmail.com",
      googlemap:
        "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.67363755065!2d126.96632751520715!3d37.56275227979869!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca289a6c9cd7b%3A0xbbdf63b00c68cc56!2z64yA7ZWc66-86rWtIOyEnOyauO2KueuzhOyLnCDshJzrjIDrrLjqtawg66-46re864-ZIO2GteydvOuhnCA4Nw!5e0!3m2!1sko!2sus!4v1532681823779"
    };
  },
  mounted() {
    var url = require("@/assets/background_mountain.png");
    document.getElementById("app").style.background = "url(" + url + ")";
    document.getElementById("app").style.backgroundSize = "cover";
    window.scrollTo(0, 0);
  }
};
</script>


