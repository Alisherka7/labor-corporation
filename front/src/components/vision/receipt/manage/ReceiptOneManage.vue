<template>
  <div class="white">
    <div>
      <!-- 탭 -->
      <v-tabs icons-and-text dark centered color="cyan" v-model="tab">
        <v-tabs-slider color="cyan" v-show="false"></v-tabs-slider>
        <v-tab @click="move(getMoveIndex().before)">
          <v-icon dark>arrow_back_ios</v-icon>
        </v-tab>
        <v-tab @click="step = 'receipt'">
          <span>신청서</span>
          <v-icon>home</v-icon>
        </v-tab>
        <template v-if="auth == 'admin'">
          <!-- <v-tab dark large class="ml-3" @click="step = 'user'">
            <span>신청자</span>
            <v-icon>description</v-icon>
          </v-tab> -->
          <v-tab dark large class="ml-3" @click="step = 'refuse'">
            <span>보류 메세지 수정</span>
            <v-icon>description</v-icon>
          </v-tab>
          <v-tab dark large class="ml-3" @click="step = 'cover'">
            <span>표지 & 엑셀 생성</span>
            <v-icon>class</v-icon>
          </v-tab>
        </template>
        <template v-else-if="auth == 'user'">

          <v-tab dark large class="ml-1" @click="step = 'reapply_cancel'">
            <span>재신청 & 신청취소</span>
            <v-icon>alarm_on</v-icon>
          </v-tab>

        </template>
        <v-tab @click="move(getMoveIndex().after)">
          <v-icon dark>arrow_forward_ios</v-icon>
        </v-tab>
        <v-tabs-items>
        </v-tabs-items>
      </v-tabs>
    </div>
    <!-- 카드 -->
    <v-card :height="this.$store.state.windowSize.y/1.37" class="container" v-if="modalItem.exists">
      <v-card-text>
        <div>
          <!-- 홈 -->
          <template v-if="step =='receipt'">
            <v-alert :value="true" type="warning" class="ma-0" v-if="modalItem.receipt_meta && modalItem.receipt_meta.message != ''">
              <div class="subheading">
                <span>{{modalItem.receipt_meta ? modalItem.receipt_meta.message: ''}}</span>
              </div>
            </v-alert>
            <receipt-description :modalItem="modalItem"></receipt-description>
          </template>

          <!-- 표지 -->
          <template v-else-if="step == 'cover'">
            <divider3 title="엑셀 생성하기" subtitle="현재 신청서로 엑셀을 생성합니다.">
              <v-btn @click="excelDownload()" color="primary" :loading="loading">엑셀 생성하기</v-btn>
            </divider3>
            <divider3 title="표지 생성하기" subtitle="표지를 생성합니다.">
              <receipt-cover></receipt-cover>
            </divider3>

          </template>
          <!-- 보류메세지 -->
          <receipt-refuse-message v-else-if="step == 'refuse'" @saved="onRefuseMessageSaved" :index="modalItem.receipt_meta.index"></receipt-refuse-message>
          <template v-else-if="step == 'reapply_cancel'">
            <divider3 title="재신청 & 신청취소">
              <v-layout>
                <v-flex xs6>
                  <div @click="doReapply">
                    <v-card class="mr-2 onlypointer" @mouseover="reapplyColor = 'green lighten-1'" @mouseout="reapplyColor = 'green'" :color="reapplyColor" dark>
                      <v-card-title primary-title>
                        <div>
                          <h3 class="headline mb-2">재신청</h3>
                          <div> 재신청을 하면 기존의 신청서가 취소됩니다 <br> 재신청하려면 클릭하세요 </div>
                        </div>
                      </v-card-title>

                      <v-card-actions>
                        <v-btn flat color="white" large :loading="loading">재신청하기</v-btn>
                      </v-card-actions>
                    </v-card>
                  </div>
                </v-flex>
                <v-flex xs6>
                  <div @click="doCancel()">
                    <v-card class="ml-2 onlypointer" @mouseover="cancelColor = 'blue-grey darken-1'" @mouseout="cancelColor = 'blue-grey darken-2'" :color="cancelColor" dark>
                      <v-card-title primary-title>
                        <div>
                          <h3 class="headline mb-2">신청취소</h3>
                          <div> 신청서를 취소해도 재신청을 할 수 있습니다. <br> 신청취소하려면 클릭하세요 </div>
                        </div>
                      </v-card-title>

                      <v-card-actions>
                        <v-btn flat color="white" large :loading="loading">신청취소하기</v-btn>
                      </v-card-actions>
                    </v-card>
                  </div>
                </v-flex>
              </v-layout>
            </divider3>
          </template>
        </div>
      </v-card-text>
    </v-card>

    <!-- 네비 -->
    <v-bottom-nav :active.sync="bottomNav" :color="color" :value="true">
      <v-btn dark v-for="(item,i) in tabStateItems" :key="i" @click="onTabClick($event, item)">
        <span>{{item.text}}</span>
        <v-icon>{{item.icon}}</v-icon>
      </v-btn>
    </v-bottom-nav>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";

import ReceiptCover from "@/components/vision/receipt/manage/ReceiptCover";
import ReceiptDescription from "@/components/vision/receipt/manage/ReceiptDescription";
import ReceiptRefuseMessage from "@/components/vision/receipt/manage/ReceiptRefuseMessage";

export default {
  name: "ReceiptOneManage",
  props: ["modalItem", "auth"],
  components: {
    Divider1,
    Divider3,
    ReceiptCover,
    ReceiptDescription,
    ReceiptRefuseMessage
  },
  data() {
    return {
      loading: false,
      cancelColor: "blue-grey darken-2",
      reapplyColor: "green",
      tab: 1,
      step: "receipt",
      tabStateItems: [
        { text: "신청", icon: "favorite", value: "apply" },
        { text: "보류", icon: "wb_cloudy", value: "refuse" },
        { text: "완료", icon: "wb_sunny", value: "accept" },
        { text: "취소", icon: "brightness_2", value: "cancel" }
      ],
      bottomNav: 0
    };
  },
  computed: {
    color() {
      switch (this.bottomNav) {
        case 0:
          return "primary";
        case 1:
          return "warning";
        case 2:
          return "success";
        case 3:
          return "secondary";
      }
    }
  },
  watch: {
    modalItem(v) {
      this.updateBottomNav();
    }
  },
  methods: {
    move(index) {
      var receipts = this.$store.state.all.receipts;
      if (!receipts.hasOwnProperty(index)) return false;
      this.$emit("move", index);
    },
    updateBottomNav() {
      this.bottomNav = { apply: 0, refuse: 1, accept: 2, cancel: 3 }[
        this.modalItem.receipt_meta.state
      ];
    },
    getMoveIndex() {
      let index = this.modalItem.receipt_meta.index;
      let r = {
        before: -1,
        after: -1
      };

      let next_stop = false;
      for (var receipt_index in this.$store.state.all.receipts) {
        if (next_stop) {
          r.after = receipt_index;
          break;
        } else if (receipt_index == index) {
          next_stop = true;
        } else {
          r.before = receipt_index;
        }
      }
      return r;
    },
    onTabClick(e, item) {
      var metas = this.modalItem.receipt_meta;
      if (item.value == "refuse" && this.auth == "admin") {
        this.step = "refuse";
      }
      var updateReceipt = index => {
        this.$store.commit("update", {
          type: "receipts",
          name: "get",
          bag: {
            index: index
          },
          success: result => {
            this.$emit("move", index);
          }
        });
      };
      let index = metas.index;
      switch (this.auth) {
        case "admin":
          let ajaxurl = this.$store.state.ajaxurl;
          let ajax_name = "state_" + item.value;

          this.$store.dispatch("post", {
            data: {
              type: "receipt",
              name: ajax_name,
              bag: {
                now_state: metas.state,
                index: index
              }
            },
            success: () => {
              if (metas.id != "admin") {
                var stateMessage =
                  metas.index +
                  "번 " +
                  metas.receipt_name +
                  " 신청서가 <" +
                  {
                    apply: "신청",
                    refuse: "보류",
                    accept: "완료",
                    cancel: "취소"
                  }[item.value] +
                  "> 되었습니다.";
                this.$store.dispatch("post", {
                  data: {
                    type: "chat",
                    name: "add",
                    bag: {
                      room: metas.id,
                      talk: stateMessage,
                      chars: {
                        admin: {
                          id: "admin",
                          self: true
                        },
                        [metas.id]: {
                          id: metas.id,
                          self: false
                        }
                      }
                    }
                  },
                  error: () => {
                    this.$store.commit("snackbar", {
                      type: "error",
                      text: "신청자에게 보내는 메세지 추가에 실패했습니다."
                    });
                  }
                });
              }

              updateReceipt(index);
            },
            error: () => {
              this.$store.commit("snackbar", {
                type: "error",
                text: "신청서 상태를 변경하는데에 실패했습니다."
              });
            }
          });

          break;
        case "user":
          updateReceipt(index);
          break;
      }
    },
    doCancel(proc) {
      this.loading = true;
      let index = this.modalItem.receipt_meta.index;
      this.$store.dispatch("post", {
        data: {
          type: "receipt",
          name: "state_cancel",
          bag: {
            index: index,
            now_state: this.modalItem.receipt_meta.state
          }
        },
        success: () => {
          this.$store.commit("update", {
            type: "receipts",
            name: "get",
            bag: {
              index: index
            },
            success: () => {
              this.loading = false;
              this.move(index);
              this.$store.commit("snackbar", {
                type: "success",
                text: "신청서가 성공적으로 취소되었습니다."
              });
              if (proc) proc(index);
            },
            error: () => {
              this.loading = false;
              this.$store.commit("snackbar", {
                type: "error",
                text: "신청서 정보를 가져오지 못했습니다."
              });
            }
          });
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "신청서 취소에 실패했습니다."
          });
        }
      });
    },
    doReapply() {
      this.doCancel(index => {
        this.$store.state.reapply.valid = true;
        this.$store.state.reapply.index = index;
        this.$router.push("/home/receipt");
      });
    },
    onRefuseMessageSaved() {
      this.step = "receipt";
      this.$emit("refuseMessageSaved");
    },
    excelDownload() {
      /**
       * 여기에서 추가 엑셀에 대한 정보를 바꾼다.
       */
      this.loading = true;
      var index = this.modalItem.receipt_meta.index;
      console.log(this.modalItem.receipt_data)
      this.$store.dispatch("post", {
        data: {
          type: "receipt",
          name: "excel",
          bag: {
            items: this.modalItem.receipt_data
          }
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
            text: "엑셀 다운로드에 실패했습니다."
          });
        }
      });
    },
    reapply() {}
  }
};
</script>

<style scoped>
.container {
  /* margin-left: 0px;
  margin-right: 0px; */
  /* padding: 0px; */
  overflow-y: scroll;
  overflow-x: hidden;
}
</style>
