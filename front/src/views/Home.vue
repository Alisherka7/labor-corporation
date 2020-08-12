<template>
  <div v-if="getOK.length >= getOKCount">
    <!-- 툴바 -->
    <v-toolbar dark app flat dense class="toolbar">
      <v-toolbar-title>
        <span class="subheading">
          노무법인
        </span>
      </v-toolbar-title>
      <!-- <v-avatar>
          <img :src="require('@/assets/logo/1.gif')">
        </v-avatar> -->

      <!-- {{RouterPathToName($route.path)}} -->
    </v-toolbar>

    <!-- 네비 -->
    <v-navigation-drawer app :mini-variant="mini" permanent width="280" dark class="nav">
      <v-layout v-if="!mini">
        <v-btn icon class="ml-2" @click="$router.push('/home/privacy')">
          <v-icon color="grey">settings</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn icon class="mr-2" @click="mini = !mini">
          <v-icon color="grey">menu</v-icon>
        </v-btn>
      </v-layout>
      <div v-else>
        <div class="text-xs-center mt-3">
          <v-btn icon class="mr-2" @click="mini = !mini">
            <v-icon color="grey">menu</v-icon>
          </v-btn>
        </div>
      </div>
      <!-- 아바타 -->
      <div class="text-xs-center " :class="[mini?'mt-3 mb-1':'mt-4 mb-2']">
        <div :class="{'nav_avatar_border':!mini}">
          <v-avatar @click="avatarModal = !avatarModal" :size="mini?'40':'110'" color="white" class="nav_avatar">
            <img :src='avatarSrc'>
          </v-avatar>
        </div>
        <template v-if="!mini">
          <div class="title font-weight-light mt-4 white--text">
            <v-avatar size="18" v-if="$store.state.all.id == 'admin'"><img :src="require('@/assets/crown.svg')"></v-avatar>
            {{$store.getters.user['담당자성명']}}
          </div>
          <div class="body-1 grey--text mt-2">{{$store.getters.user['회사명']}}</div>
        </template>
      </div>

      <!-- <div class="text-xs-center">
        <v-btn icon @click="mini = !mini" large v-if="mini">
          <v-icon large>{{mini?`chevron_right` : `chevron_left`}}</v-icon>
        </v-btn>
      </div> -->
      <v-list>
        <v-divider></v-divider>
        <v-list-group v-for="(item, i) in newNavItems" :key="i" v-if="item.type == 'group' && (item.auth != 'admin' || $store.getters.isAdmin)">
          <menu-tile :item="item" slot="activator"></menu-tile>
          <menu-tile :item="children_item" v-for="(children_item, j) in item.children" :key="j"></menu-tile>
        </v-list-group>
        <menu-tile :item="item" v-for="(item, i) in newNavItems" :key="i" v-if="item.type == 'link'"></menu-tile>
      </v-list>
      <v-divider></v-divider>
      <v-list>
        <menu-tile :item="item" v-for="(item, i) in newNavItems" :key="i" v-if="item.type == 'href' && !mini"></menu-tile>
      </v-list>

    </v-navigation-drawer>
    <!-- 컨텐츠 -->
    <v-content class="content">
      <div class="mx-3">
        <!-- 공지사항 -->
        <v-alert v-if="$store.state.all.settings.notice" type="success" :value="true" dismissible>{{$store.state.all.settings.notice}}</v-alert>
        <router-view/>
        <v-footer>
          <v-spacer></v-spacer>
          <div>노무법인 &copy; {{ new Date().getFullYear() }}</div>
        </v-footer>
      </div>
    </v-content>

    <!-- 아바타 모달 -->
    <v-dialog v-model="avatarModal" width="750">
      <v-card>
        <divider3 type="dialog" title="아바타" subtitle="프로필로 사용할 아바타를 선택하세요">
          <image-container :items="imageContainerItmes" width="90" @selected="onAvataSelected"></image-container>
        </divider3>
      </v-card>
    </v-dialog>
    <!-- 채팅 버튼 -->
    <!-- :style="{'zoom': readCount?'1.5':'1'}" -->
    <v-btn :style="{'zoom': readCount?'1.2':'1.2'}" large absolute bottom right class="mb-5 mr-3" fixed flat icon dark color="yellow lighten-2" @click="onChatButtonDown">
      <v-icon x-large v-if="!readCount || clicked">chat_bubble</v-icon>
      <v-badge v-else overlap right color="error">
        <span slot="badge">{{readCount}}</span>
        <v-icon x-large>chat_bubble</v-icon>
      </v-badge>
    </v-btn>
    <!-- 채팅 다이얼로그 -->
    <v-dialog v-model="$store.state.chat.state" width="500px">
      <chat-box :load="$store.state.chat.state" class="chatbox"></chat-box>
    </v-dialog>

  </div>
  <div v-else style="height:100%">
    <v-container fill-height>
      <v-layout align-center class="mb-5">
        <v-flex text-xs-center>
          <div class="display-1"> 잠시만 기다려 주세요</div>
          <img :src="require('@/assets/giphy.gif')">
          <v-progress-linear :value="getOK.length/getOKCount*100" color="orange lighten-3" buffer-value="100" height="30"></v-progress-linear>
          <random-message-creater></random-message-creater>
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>

<script>
import RandomMessageCreater from "@/components/RandomMessageCreater";
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import ChatBox from "@/components/vision/help/ChatBox";
import MenuTile from "@/components/MenuTile";
import ImageContainer from "@/components/ImageContainer";
export default {
  name: "Home",
  components: {
    Divider1,
    Divider3,
    ChatBox,
    MenuTile,
    ImageContainer,
    RandomMessageCreater
  },
  data() {
    return {
      // notice: "",
      avatarSrc: "",
      avatarModal: false,
      clicked: false,
      mini: false,
      getOK: [],
      getOKCount: 100,
      chars: {},
      navState: true,
      navImgUrl: require("@/assets/logo/4.png"),
      newNavItems: [
        {
          
          type: "group",
          icon: "home",
          title: "홈",
          children: [
            {
              type: "link",
              icon: "domain",
              to: "/home/receipt_history",
              title: "신청내역"
            },
            {
              type: "link",
              icon: "person",
              to: "/home/privacy",
              title: "개인정보관리"
            }
          ]
        },
        {
          type: "group",
          icon: "person_add",
          title: "관리자",
          auth: "admin",
          children: [
            {
              type: "link",
              icon: "location_city",
              to: "/home/receipt_manage",
              title: "신청서 관리"
            },
            {
              type: "link",
              icon: "person_add",
              to: "/home/user_manage",
              title: "회원 관리"
            },
            {
              type: "link",
              icon: "settings",
              to: "/home/config",
              title: "설정"
            }
          ]
        },
        {
          type: "link",
          icon: "description",
          title: "온라인 신청서",
          to: "/home/receipt"
        },
        {
          type: "link",
          icon: "help",
          title: "고객지원",
          to: "/home/help"
        },
        {
          type: "link",
          icon: "replay",
          title: "로그아웃",
          to: "/about/welcome"
        },
        {
          type: "href",
          icon: "layers",
          aftericon: "public",
          title: "국민건강보험공단",
          href: "http://www.nhis.or.kr/retrieveHomeMain.xx"
        },
        {
          type: "href",
          icon: "terrain",
          aftericon: "public",
          title: "4대사회보험 정보연계센터",
          href: "http://www.4insure.or.kr/ins4/ptl/Main.do"
        }
        // {
        //   type: "link",
        //   icon: "person",
        //   title: "로그아웃",
        //   to: "/about/welcome"
        // }
      ]
    };
  },
  computed: {
    readCount() {
      var user = this.$store.state.all.users[this.$store.state.all.id];
      var count = 0;
      for (var roomid in user["메타"]["rooms"]) {
        count += user["메타"]["rooms"][roomid];
      }
      return count;
    },
    imageContainerItmes() {
      var items = [];
      for (var i = 1; i <= 36; i++) {
        items.push(require("@/assets/avatars/1/avatar (" + i + ").svg"));
      }
      return items;
    }
  },
  mounted() {
    document.getElementById(
      "app"
    ).style.background = this.$store.state.options.home_background_color.code;
    this.$store.commit("update", {
      type: "users",
      name: "get",
      success: result => {
        this.getOK.push("update_users_get");
        this.updateAvatar();
        if (this.$store.state.all.id == "admin") {
          this.getOKCount = 6;

          this.$store.dispatch("post", {
            data: {
              type: "receipt",
              name: "remove_accept_cancel_files"
            },
            success: result => {
              this.getOK.push("receipt_remove_accept_cancel_files");
            },
            error: () => {
              this.$store.commit("snackbar", {
                type: "error",
                text: "완료되거나 취소된 신청서의 파일을 삭제하지 못했습니다."
              });
            }
          });

          this.$store.commit("update", {
            type: "users",
            name: "get_all",
            success: result => {
              this.getOK.push("update_users_get_all");
            }
          });
          this.$store.commit("update", {
            type: "receipts",
            name: "get_all",
            success: result => {
              this.getOK.push("update_receipts_get_all");
            }
          });

          this.$store.commit("update", {
            type: "settings",
            success: result => {
              this.getOK.push("update_settings");
              // this.notice = result["notice"];
            }
          });
        } else {
          this.getOKCount = 3;
          this.$store.commit("update", {
            type: "receipts",
            name: "get_users",
            success: result => {
              this.getOK.push("update_receipts_get_users");
            }
          });
        }
      }
    });
    this.$store.commit("update", {
      type: "formats",
      name: "get",
      success: () => {
        this.getOK.push("update_formats_get");
      }
    });
  },
  methods: {
    updateAvatar() {
      var index = this.$store.state.all.users[this.$store.state.all.id]["메타"][
        "avatar"
      ]
        ? this.$store.state.all.users[this.$store.state.all.id]["메타"][
            "avatar"
          ]
        : 1;
      this.avatarSrc = require("@/assets/avatars/1/avatar (" +
        (Number.parseInt(index) + 1) +
        ").svg");
    },
    RouterTo(to) {
      this.$router.push(to);
    },
    IsNowPath(path) {
      return this.$route.path == path;
    },
    GetTileTextColorByPath(path) {
      if (this.$route.path == path) {
        return "white--text";
      } else {
        return "grey--text";
      }
    },
    RouterPathToName(path) {
      return {
        "/home/mypage": "홈",
        "/home/receipt": "온라인 신청서",
        "/home/help": "고객지원"
      }[path];
    },
    onChatButtonDown() {
      if (this.$store.getters.isAdmin) {
        this.$router.push("/home/user_manage");
      } else {
        this.$store.commit("chat", this.$store.state.all.id);
      }
      this.clicked = true;
    },
    onAvataSelected(index) {
      this.avatarModal = !this.avatarModal;
      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "set_meta",
          bag: {
            name: "avatar",
            value: index
          }
        },
        success: result => {
          this.$store.commit("update", {
            type: "users",
            name: "get",
            success: result => {
              this.updateAvatar();
            }
          });
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "아바타를 변경하지 못했습니다."
          });
        }
      });
    }
  }
};
</script>


<style scoped>
.nowpath {
  background-color: rgba(255, 255, 255, 0.15) !important;
  /* background-color: rg !important; */
}
.nav {
  /* background-color: yellow ; */
  background-color: #2f353f !important;
}
.toolbar {
  background-color: black !important;
  opacity: 0.2;
}
.chatbox {
  overflow: hidden;
}
.nav_avatar:hover {
  cursor: pointer;
  opacity: 0.8;
  border: 0.5px solid rgba(255, 0, 0, 0.1);
}
.nav_avatar_border {
  display: inline-block;
  border-radius: 100%;
  padding: 10px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>
