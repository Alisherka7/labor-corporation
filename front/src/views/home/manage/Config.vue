<template>
  <divider1 title="설정">
    <divider3 title="설정" subtitle="여러가지 설정을 할 수 있습니다.">
      <v-container grid-list-lg>
        <advanced-form v-if="update" ref="form">
          <v-layout wrap>
            <v-flex xs4 v-for="(value,key) in settings" :key="key">
              <divider3 :title="value.label" :subtitle="value.desc">
                <template v-if="value.configType == 'nofield'">
                  <template v-if="key == 'delete_all_receipt'">
                    <v-card>
                      <v-card-text>
                        <v-layout>
                          <v-flex>
                            <div>
                              <v-avatar>
                                <v-icon large color="info">description</v-icon>
                              </v-avatar>
                              <span class="display-1">
                                {{receiptsLengths.sum}}
                              </span>
                            </div>
                            <template v-if="deleteStep == 'first'">
                              <v-btn color="error" flat small @click="deleteStep = 'second'">모든 신청서 데이터 삭제하기</v-btn>
                            </template>

                            <template v-else-if="deleteStep == 'second'">
                              <div>
                                <v-alert type="error" :value="true">삭제하면 복구할 수 없습니다. 삭제하시겠습니까?</v-alert>
                                <v-btn color="error" @click="deleteAllReceipt" :loading="loading">예</v-btn>
                                <v-btn color="error" flat @click="deleteStep='first'" :loading="loading">아니요</v-btn>
                              </div>
                            </template>
                          </v-flex>
                        </v-layout>
                      </v-card-text>
                    </v-card>
                  </template>
                </template>
                <template v-else>
                  <named-field :fieldName="value.type" :formKey="key" label=" " :value="value.value + ''" :suffix="value.suffix" :required="value.required"> </named-field>
                </template>
              </divider3>
            </v-flex>
          </v-layout>
        </advanced-form>
      </v-container>
      <div class="ma-5">
        <v-btn color="primary" @click="SaveSettings" :loading="loading">저장</v-btn>
        <v-btn @click="UpdateSettings" :loading="loading" flat color="primary">새로고침</v-btn>
      </div>
    </divider3>
  </divider1>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import AdvancedForm from "@/components/AdvancedForm";
import NamedField from "@/components/NamedField";
export default {
  name: "Config",
  components: {
    AdvancedForm,
    NamedField,
    Divider1,
    Divider3
  },
  data() {
    return {
      deleteStep: "first",
      loading: false,
      update: true,
      settings: {
        notice: {
          label: "공지사항 메세지",
          type: "설정_공지사항메세지",
          desc: "공지사항 메세지를 설정합니다.",
          required: false
        },

        receipt_manage_update_interval: {
          label: "신청서 업데이트 간격",
          type: "설정_신청서업데이트간격",
          desc:
            "신청서 관리 페이지에서 신청서를 업데이트하는 간격을 설정합니다."
        },
                file_max_size: {
          label: "파일 최대 크기",
          type: "설정_파일최대크기",
          desc: "올릴 수 있는 파일의 최대 크기를 설정합니다."
        },

        remove_accept_cancel_files: {
          label: "보류, 완료된 신청서 자동삭제",
          type: "설정_보류완료파일자동삭제",
          desc:
            "보류되거나 완료된 신청서를 재접속 할때마다 검사하여 자동으로 삭제합니다."
        },

        delete_all_receipt: {
          configType: "nofield",
          label: "모든 신청서 삭제",
          desc: "데이터베이스에 있는 모든 신청서를 삭제합니다."
        }
      }
    };
  },
  computed: {
    receiptsLengths() {
      var objReceipt = Object.values(this.$store.state.all.receipts);
      var lengths = {};
      lengths.apply = objReceipt.filter(
        receipt => (receipt.meta.state == "apply" ? true : false)
      ).length;
      lengths.refuse = objReceipt.filter(
        receipt => (receipt.meta.state == "refuse" ? true : false)
      ).length;
      lengths.cancel = objReceipt.filter(
        receipt => (receipt.meta.state == "cancel" ? true : false)
      ).length;
      lengths.accept = objReceipt.filter(
        receipt => (receipt.meta.state == "accept" ? true : false)
      ).length;
      lengths.sum =
        +lengths.apply + +lengths.refuse + +lengths.cancel + +lengths.accept;
      return lengths;
    }
  },
  mounted() {
    this.UpdateSettingData();
    window.scrollTo(0, 0);
  },
  methods: {
    deleteAllReceipt() {
      this.loading = true;
      this.$store.dispatch("post", {
        data: {
          type: "receipt",
          name: "remove_all_receipts"
        },
        success: () => {
          this.$store.commit("snackbar", {
            type: "success",
            text: "모든 신청서가 삭제되었습니다."
          });

          this.$store.commit("update", {
            type: "receipts",
            name: "remove_all"
          });
          this.$store.commit("update", {
            type: "users",
            name: "get_all"
          });
          this.deleteStep = "first";
          this.loading = false;
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "모든 신청서를 삭제하지 못했습니다."
          });
        }
      });
    },
    SaveSettings() {
      this.loading = true;
      let ajaxurl = this.$store.state.ajaxurl;
      var result = this.$refs.form.get();
      if (!result.validated) {
        this.$store.commit("snackbar", {
          pre: "err_form"
        });
        return;
      }
      for (var key in result.fields) {
        var value = result.fields[key];
        if (key == "remove_accept_cancel_files") {
          // if(value == false){}
          result.fields[key] = value == "true" ? 1 : 0;
        }
      }
      this.$store.dispatch("post", {
        data: {
          type: "setting",
          name: "set",
          bag: result.fields
        },
        success: () => {
          this.UpdateSettings();
          this.$store.commit("snackbar", {
            type: "success",
            text: "설정이 저장되었습니다."
          });
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "설정 변경에 실패했습니다."
          });
        }
      });
    },
    UpdateSettingData() {
      for (var key in this.$store.state.all.settings) {
        if (this.settings.hasOwnProperty(key)) {
          var value = this.$store.state.all.settings[key];
          if (key == "remove_accept_cancel_files") {
            value = value == "1" ? true : false;
          }
          this.settings[key].value = value;
          // console.log(this.settings[key].value);
        }
      }
      this.update = false;
      setTimeout(() => {
        this.update = true;
      });
    },
    UpdateSettings() {
      this.loading = true;
      this.$store.commit("update", {
        type: "settings",
        success: res => {
          this.UpdateSettingData();
          this.loading = false;
        }
      });
    }
  }
};
</script>
