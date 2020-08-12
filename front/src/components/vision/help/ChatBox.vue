<template>
  <v-card :height="this.$store.state.windowSize.y/1.37" class="chatbox">
    <v-toolbar class="toolbar" flat>
      <v-speed-dial v-model="fab" direction="right" :open-on-hover="true" :transition="'slide-y-reverse-transition'">
        <v-btn slot="activator" v-model="fab" fab flat color="white" :loading="loading">
          <v-icon large color="black">chat</v-icon>
          <v-icon large>close</v-icon>
        </v-btn>
        <v-btn dark color="green" @click="updateChat" :loading="loading">
          <v-icon>refresh</v-icon>
        </v-btn>
        <v-btn v-if="ones.id =='admin'" dark color="red" @click="clearChat" :loading="loading">
          <v-icon>delete</v-icon>
        </v-btn>
      </v-speed-dial>
      <v-toolbar-title v-if="!fab">
        <span v-if="ones.id == 'admin'">{{opponent.id}}님과의 채팅</span>
        <span v-else>1:1 문의</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-btn flat fab @click="$store.state.chat.state = false">
          <v-icon>clear</v-icon>
        </v-btn>
      </v-toolbar-items>
    </v-toolbar>
    <div class="hr"></div>

    <chat :chars="chars" :talks="talks" @talk="onTalk" ref="chat"></chat>
  </v-card>
</template>

<script>
import Chat from "@/components/Chat";
export default {
  name: "ChatBox",
  components: {
    Chat
  },
  props: ["load"],
  data() {
    return {
      loading: false,
      adminAvatarSrc: "",
      avatarSrc: "",
      fab: false,
      chars: {},
      talks: [],
      ones: {},
      opponent: {}
    };
  },
  mounted() {
    this.updateAdminAvatar();
    this.updateAvatarSrc();
  },
  methods: {
    updateAdminAvatar() {
      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "get_user_avatar",
          bag: {
            id: "admin"
          }
        },
        success: result => {
          this.adminAvatarSrc = require("@/assets/avatars/1/avatar (" +
            (Number.parseInt(result) + 1) +
            ").svg");
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "아바타 업데이트에 실패했습니다."
          });
        }
      });
    },
    updateAvatarSrc() {
      this.avatarSrc = this.getAvatarSrc(this.$store.state.all.id);
    },
    getAvatarSrc(id) {
      var userAvatarIndex = this.$store.state.all.users[id]["메타"]["avatar"];
      var index = userAvatarIndex ? userAvatarIndex : 1;
      return require("@/assets/avatars/1/avatar (" +
        (Number.parseInt(index) + 1) +
        ").svg");
    },
    onTalk(talkData) {
      this.$store.dispatch("post", {
        data: {
          type: "chat",
          name: "add",
          bag: {
            room: this.$store.state.chat.room,
            talk: talkData.talk,
            chars: this.chars
          }
        },
        success: res => {
          this.updateChat();
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "문의 추가에 실패했습니다."
          });
        }
      });
    },
    clearChat() {
      this.loading = true;
      this.$store.dispatch("post", {
        data: {
          type: "chat",
          name: "clear",
          bag: {
            room: this.$store.state.chat.room
          }
        },
        success: res => {
          this.updateChat();
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "문의채팅 초기화에 실패했습니다."
          });
        }
      });
    },
    updateChat() {
      this.loading = true;
      /**
       * CHARS 업데이트
       */
      var user = this.$store.getters.user;
      if (user["아이디"] == "admin") {
        this.ones.id = "admin";
        this.opponent.id = this.$store.state.chat.room;
        this.opponent.src = this.getAvatarSrc(this.opponent.id);
      } else {
        this.ones.id = this.$store.state.chat.room;
        this.opponent.id = "admin";
        this.opponent.src = this.adminAvatarSrc;
      }

      this.chars[this.opponent.id] = {
        id: this.opponent.id,
        self: false,
        src: this.opponent.src
      };
      this.chars[this.ones.id] = {
        id: this.ones.id,
        self: true
      };
      /** PROPS의 ROOM으로 업데이트 */
      this.$store.dispatch("post", {
        data: {
          type: "chat",
          name: "get",
          bag: {
            room: this.$store.state.chat.room
          }
        },
        success: result => {
          this.loading = false;
          this.talks = result;
          this.$refs.chat.SetScrollToFloor();
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "채팅을 가져오는데에 실패했습니다."
          });
        }
      });
    }
  },
  watch: {
    load(v) {
      if (v) {
        this.updateChat();
      }
    }
  }
};
</script>


<style scoped>
.toolbar {
  background-color: #a0c0d7 !important;
}
.hr {
  border: 0.5px solid rgba(0, 0, 0, 0.2);
}
.chatbox {
  border: 2px solid dimgray;
  border-radius: 5px;
}
</style>
