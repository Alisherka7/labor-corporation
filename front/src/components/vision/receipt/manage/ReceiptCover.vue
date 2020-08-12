<template>
  <div>
    <v-container grid-list-xl>
      <v-layout wrap>
        <v-layout>
          <v-flex xs4>
            <v-autocomplete :items="corpAutocomplete" label="공단 선택" v-model="cover.corpName" @change="corpSelect"></v-autocomplete>
          </v-flex>
        </v-layout>
        <v-layout>
          <v-flex>
            <v-text-field label="공단 팩스번호" v-model="cover.corpFax"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="공단 전화번호" v-model="cover.corpContact"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="담당자 이름" v-model="cover.managerFullName"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="담당자 전화번호" v-model="cover.managerContact"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="페이지 수" v-model="cover.pageCount"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="사업장명" v-model="cover.companyName"></v-text-field>
          </v-flex>
          <v-flex>
            <v-text-field label="신고서명" v-model="cover.formName"></v-text-field>
          </v-flex>
        </v-layout>
      </v-layout>
    </v-container>
    <v-btn color="primary" @click="createCover" :loading="loading">표지 생성하기</v-btn>

  </div>
</template>

<script>
export default {
  name: "ReceiptCover",
  components: {},
  data() {
    return {
      loading: false,

      cover: {
        corpName: "",
        corpFax: "",
        corpContact: "",
        managerFullName: "",
        managerContact: "",
        pageCount: "",
        companyName: "",
        formName: ""
      },
      corps: {},
      corpAutocomplete: []
    };
  },
  methods: {
    corpSelect(i) {
      this.cover.corpContact = this.corps[i].call;
      this.cover.corpFax = this.corps[i].fax;
      this.cover.pageCount = 0;
    },
    createCover() {
      this.loading = true;

      this.$store.dispatch("post", {
        data: {
          type: "receipt",
          name: "cover",
          bag: this.cover
        },
        success: result => {
          this.loading = false;
          var path = result;
          this.$store.dispatch(
            "FileDownload",
            this.$store.state.uploadsurl.dummy +
              path.replace(/\\/g, "/").replace(/.*\//, "")
          );
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "표지를 생성하는데에 실패했습니다."
          });
        }
      });
    }
  },
  mounted() {
    //   this.corpAutocomplete = [{text:'test',value:'testvalue'},{text:'test2',value:'te2stvalue'},];
    this.$store.dispatch("post", {
      data: {
        type: "data",
        name: "get",
        bag: {
          name: "corporations"
        }
      },
      success: result => {
        var result = result;
        for (var index in result) {
          var value = result[index];
          if (!this.corps.hasOwnProperty(value.num)) this.corps[value.num] = {};
          switch (value.eng) {
            case "C":
              this.corps[value.num].name = value.data;
              break;

            case "D":
              this.corps[value.num].fax = value.data;
              break;

            case "E":
              this.corps[value.num].call = value.data;
              break;
          }
        }

        var temp = [];
        for (var i in this.corps) {
          temp.push({
            text: this.corps[i].name,
            value: i
          });
        }
        this.corpAutocomplete = temp;
      },
      error: () => {
        this.$store.commit("snackbar", {
          type: "error",
          text: "공단 정보를 가져오는데에 실패했습니다."
        });
      }
    });
  }
};
</script>

