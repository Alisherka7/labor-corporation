<template>
  <div>
    <v-jumbotron dark :src="require('@/assets/sacramento-header.png')" height="180">
      <v-container fill-height>
        <v-layout align-center>
          <v-flex text-xs-center>
            <div class="display-1 font-weight-light">
              {{$store.getters.receipt.name? $store.getters.receipt.name + ' 신청서': '아래의 신청서를 선택하세요'}}
            </div>
            <div class="subheading font-weight-light">
              {{$store.getters.receipt.englishName?$store.getters.receipt.englishName: 'Please select the application below'}}
            </div>
          </v-flex>
        </v-layout>
      </v-container>
    </v-jumbotron>

    <v-stepper v-model="step" vertical class="elevation-0">
      <v-stepper-step :complete="step > 1" step="1">
        작성 준비
      </v-stepper-step>

      <v-stepper-content step='1'>
        <ReceiptSelectBox @selected="updateFormatFiles()"></ReceiptSelectBox>
        <divider3  v-if="$store.getters.receipt.doc">
          <!-- title="알림" -->
          <span class="doc subheading font-weight-bold" v-html="$store.getters.receipt.doc"></span>
        </divider3>
        <divider3 v-if="formatfiles.length != 0" title="서식" subtitle="필요한 서식이 있나요?" >
          <v-btn outline v-for="(file,i) in formatfiles" :key="i" color="primary" @click="onFormatClick(file.basename)">
            <v-icon dark class="mr-1">link</v-icon>
            <span>{{file.filename}}</span>
          </v-btn>
        </divider3>
        <divider3 title="관리회사 선택" >
          <!-- subtitle="신청할 회사를 선택하세요" -->
          <v-layout>
            <v-flex xs3>
              <advanced-form ref="addform">
                <v-autocomplete v-model="cmModel" prepend-icon="accessibility_new" form-key="관리회사" :items="CMItems"></v-autocomplete>
              </advanced-form>
            </v-flex>
            <v-btn outline color="primary" @click="$router.push('/home/privacy')">새로운 관리회사 추가하기</v-btn>
          </v-layout>
        </divider3>

        <v-btn color="primary" @click="onReady">
          <span class="subheading">신청서 작성</span>
        </v-btn>
      </v-stepper-content>
      <v-stepper-step :complete="step > 2" step="2">신청서 작성</v-stepper-step>
      <v-stepper-content step="2">
        <v-card class="" flat>
          <!-- <div class="text-xs-center mb-5">
            <div class="display-1 font-weight-light mb-3">
              {{$store.getters.receipt.name}}
            </div>
            <v-divider></v-divider>
          </div> -->
          <advanced-form ref="form">
            <Receipts :name="$store.getters.receipt.name" :step="step"> </Receipts>
          </advanced-form>
        </v-card>
        <v-btn color="primary" @click="formHandler($refs.form.get())" :loading="loading">
          <span>작성완료</span>
        </v-btn>
        <v-btn color="secondary" flat @click="step--" :loading="loading">
          <span>취소</span>
        </v-btn>
      </v-stepper-content>

      <v-stepper-step :complete="step > 3" step="3">완료</v-stepper-step>

      <v-stepper-content step="3">
        <NavigateCard noaction>
          <v-card flat class="step_card">
            <v-card-title>
              <SuccessAnimation :show="successAnimation"></SuccessAnimation>
            </v-card-title>
            <v-card-text>
              <div class="headline text-xs-center ml-3">
                <div>신청서 접수가 완료되었습니다!</div>
                <v-btn color="primary" large style="width:20%" @click="step = 1">완료</v-btn>
              </div>
            </v-card-text>
          </v-card>
        </NavigateCard>
      </v-stepper-content>
    </v-stepper>
  </div>
</template>

<script>
import AdvancedForm from "@/components/AdvancedForm";
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import ReceiptSelectBox from "@/components/vision/receipt/ReceiptSelectBox";
import SuccessAnimation from "@/components/animation/SuccessAnimation";
import NavigateCard from "@/components/NavigateCard";
import Receipts from "@/components/vision/receipt/Receipts";
import AdvancedCard from "@/components/AdvancedCard";
export default {
  name: "ReceiptApplyBox",
  components: {
    Divider1,
    Divider3,
    ReceiptSelectBox,
    NavigateCard,
    SuccessAnimation,
    Receipts,
    AdvancedCard,
    AdvancedForm
  },
  data() {
    return {
      loading: false,
      step: this.$store.state.reapply.valid ? 2 : 1,
      progressValue: 0,
      progressIndeterminate: false,
      successAnimation: true,
      stepsFields: [],
      formatfiles: [],
      testCount: 0,
      cmModel: ""
    };
  },
  computed: {
    CMItems() {
      var items = [];
      var user = this.$store.state.all.users[this.$store.state.all.id];
      items.push(user["회사명"]);
      for (var cmkey in user["관리회사"]) {
        items.push(cmkey);
      }
      this.cmModel = user["회사명"];
      return items;
    }
  },
  watch: {
    step() {
      if (this.step == 3) {
        this.successAnimation = false;
        setTimeout(() => {
          this.successAnimation = true;
        });
      }
    }
  },
  mounted() {
    if (this.$store.state.reapply.valid) {
      var reapply_receipt = this.$store.state.all.receipts[
        this.$store.state.reapply.index
      ];
      this.$store.state.receipt.selected = reapply_receipt.meta.receipt_name;
    }

    this.updateFormatFilesData();
  },
  methods: {
    onReady() {
      if (this.$store.state.receipt.selected) {
        this.step++;
      } else {
        this.$store.commit("snackbar", {
          text: "신청서가 선택되지 않았습니다.",
          type: "warning"
        });
        return;
      }
    },
    updateFormatFilesData() {
      let formatfiles = [];
      for (var i in this.$store.state.all.formats) {
        var v = this.$store.state.all.formats[i];
        if (v.effect["파일연동"] == this.$store.getters.receipt.name) {
          for (var j in v.files) {
            var path = v.files[j];
            var basename = path.replace(/\\/g, "/").replace(/.*\//, "");
            var filename = "";
            if (j in v.effect["파일명"]) {
              filename = v.effect["파일명"][j];
            }
            var filename = formatfiles.push({
              basename: basename,
              filename: filename
            });
          }
        }
      }
      this.formatfiles = formatfiles;
    },
    updateFormatFiles() {
      this.$store.commit("update", {
        type: "formats",
        name: "get",
        success: result => {
          this.updateFormatFilesData();
        }
      });
    },
    formHandler(result) {
      var formResult = result;
      var manageCompanyName = this.$refs.addform.get()["fields"]["관리회사"];
      if (!result.validated) {
        this.$store.commit("snackbar", {
          pre: "err_form"
        });
        return;
      }

      var user = this.$store.state.all.users[this.$store.state.all.id];
      this.loading = true;
      var addReceipt = (result, fileFields) => {
        this.$store.dispatch("post", {
          data: {
            type: "receipt",
            name: "add",
            bag: {
              data: result.fields,
              receipt_name: this.$store.getters.receipt.name,
              file_fields: fileFields ? fileFields : [""],
              manage_company: user["관리회사"][manageCompanyName]
                ? user["관리회사"][manageCompanyName]
                : {
                    회사명: user["회사명"],
                    사업자등록번호: user["사업자등록번호"],
                    회사팩스번호: user["회사팩스번호"],
                    회사전화번호: user["회사전화번호"]
                  }
            }
          },
          success: result => {
            this.$store.commit("update", {
              type: "receipts",
              name: "get",
              bag: {
                index: result.index
              },
              success: res => {
                if (false) {
                  this.testCount++;
                  if (this.testCount < 3000) {
                    this.formHandler(formResult);
                  }
                  // console.log(this.testCount);
                } else {
                  this.$store.commit("snackbar", {
                    type: "success",
                    text: "신청서 접수가 완료되었습니다."
                  });
                  this.$store.commit("update", {
                    type: "users",
                    name: "get"
                  });
                  this.loading = false;
                  this.step++;
                }
              }
            });
          },
          error: result => {
            this.loading = false;
            this.$store.commit("snackbar", {
              type: "error",
              text: "신청서 접수에 실패했습니다."
            });
          }
        });
      };

      // for(var i=0; i<3000; i++){
      //   addReceipt(formResult);
      // }

      switch (this.step) {
        case 1:
          this.step++;
          break;
        case 2:
          let ajaxurl = this.$store.state.ajaxurl;
          let data = {};
          // 1. send files
          var fileFieldExists = false;
          var formData = new FormData();
          for (var i in result.fields_detail) {
            var detail = result.fields_detail[i];
            if (detail.type == "file-field") {
              var count = 0;
              for (var j in detail.value) {
                var file = detail.value[j];
                formData.append(detail.key + "#" + count++, file);
                fileFieldExists = true;
              }
            }
          }
          if (!fileFieldExists) {
            addReceipt(result);
          } else {
            $.ajax({
              url: ajaxurl,
              contentType: "multipart/form-data",
              type: "POST",
              data: formData,
              dataType: "json",
              mimeType: "multipart/form-data",
              cache: false,
              contentType: false,
              processData: false,
              success: res => {
                if (!res.success) {
                  console.error(res);
                  return;
                }
                let fileFields = {};
                var temp = {};
                for (var key in res.result) {
                  var path = res.result[key];
                  var formKey = key.substr(0, key.indexOf("#"));
                  if (!temp.hasOwnProperty(formKey)) {
                    temp[formKey] = [];
                  }
                  temp[formKey].push(path);
                  fileFields[formKey] = true;
                }

                for (var tempKey in temp) {
                  var tempValue = temp[tempKey];
                  result.fields[tempKey] = tempValue;
                }
                addReceipt(result, Object.keys(fileFields));
              }
            });
          }
          break;
        default:
          break;
      }
    },
    onFormatClick(link) {
      FileDownload(this.$store.state.uploadsurl.format + link);
    }
  }
};

function FileDownload(filePath) {
  var a = document.createElement("A");

  a.href = filePath;
  a.download = filePath.substr(filePath.lastIndexOf("/") + 1);
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}
</script>

<style scoped>
.doc {
  color: #ef9a9a;
}
/* .step_card {
  border: 8px solid rgba(0, 0, 0, 0.1);
} */
</style>