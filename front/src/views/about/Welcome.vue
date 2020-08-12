<template>
  <div class="intro">
    <v-layout fill-height align-center>
      <v-flex x12>
        <div class="text-xs-center intro-write">
          <div class="display-4 white--font mb-5">Labor Corporation</div>
          <div class="title white--font mb-5">노무법인</div>
          <v-btn dark color="primary mt-3" class="toLogin" depressed to="login">지금 시작하기</v-btn>
        </div>
      </v-flex>
    </v-layout>
  </div>
</template>


<script>
export default {
  name: "Welcome",
  mounted() {
    document.getElementById("app").style.background = "";
    window.scrollTo(0, 0);
  },
};
</script>

<style scoped>
.intro {
  background: url("../../assets/background_mountain.png");
  background-size: cover;
  height: 1000px;
}
.white--font {
  color: white;
}
.toLogin {
  height: 50px;
  font-size: 25px;
}
.intro-write {
  margin-top: 5%;
}
</style>

