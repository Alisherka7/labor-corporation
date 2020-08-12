<template>
  <div style="height:100%">
    <div class="room">
      <div v-for="(talk, i) in talks" :key=" i">
        <div v-if="isself(talk.char)" class="text-xs-right talk_container talk_container_self">
          <v-card class="talk talk_self">
            {{talk.talk}}
          </v-card>
        </div>
        <div v-else class="talk_container talk_container_oppo">
          <v-layout>
            <v-flex>
              <v-avatar class="talk_oppo_avatar" color="white">
                <img :src="chars[talk.char].src?chars[talk.char].src:'https://avatars0.githubusercontent.com/u/9064066?v=4&s=460'" />
              </v-avatar>
            </v-flex>
            <v-flex xs12>
              <div class="talk_oppo_name">
                {{talk.char}}
              </div>
              <v-card class="talk talk_oppo">
                {{talk.talk}}
              </v-card>
            </v-flex>
          </v-layout>
        </div>
      </div>
    </div>
    <v-divider></v-divider>
    <div class="mouse ml-2">
      <v-layout>
        <v-textarea @keyup.shift.enter="Talk" rows="4" v-model="sound" hint="shift + enter = 보내기" placeholder="보낼 메세지를 입력하세요"></v-textarea>
        <v-btn @click="Talk()" icon>
          <v-icon>send</v-icon>
        </v-btn>
      </v-layout>
    </div>
  </div>
</template>

<script>
export default {
  name: "Chat",
  props: {
    chars: {
      type: Object
      // id를 키값으로 한다.
      // src
      // self(Boolean)
    },
    talks: {
      type: Array
      // content
      // id
    }
  },
  data() {
    return {
      sound: ""
    };
  },
  computed: {
    getSelfID() {
      for (var id in this.chars) {
        let char = this.chars[id];
        if (char.self) return id;
      }
    }
  },
  methods: {
    isself(id) {
      return this.chars[id].self;
    },
    Get(self) {
      var r = {};
      if (self) {
        r.color = "primary";
        r.dark = true;
      } else {
        r.dark = false;
        r.color = "white";
      }
      return r;
    },
    Talk() {
      this.sound = this.sound.trim();
      if (this.sound.length == 0) return;
      let talkData = {
        id: this.getSelfID,
        talk: this.sound
      };
      this.sound = "";
      this.$emit("talk", talkData);
    },
    SetScrollToFloor() {
      setTimeout(() => {
        this.$el.children[0].scrollTop = this.$el.children[0].scrollHeight;
      }, 10);
    }
  }
};
</script>

<style scoped>
.talk_container_oppo {
  margin-top: 15px;
  margin-left: 4%;
  margin-right: 15%;
}

.talk_container_self {
  margin-top: 15px;
  margin-left: 15%;
  margin-right: 4%;
}
.talk_oppo_name {
  margin-left: 10px;
  font-size: 19px;
}

.talk_oppo_avatar {
}
.talk {
  border-radius: 3px;
  display: inline-block;
  font-size: 18px;
  font-weight: lighter;
  padding: 6px;
}
.talk_self {
  display: inline-block;
  background-color: #ffe900 !important;
}
.talk_oppo {
  display: inline-block;
  margin-left: 10px;
  background-color: #fbfdff !important;
}

.mouse {
  /* height: 200px; */
  /* height: 40%; */
}
.room {
  overflow-y: scroll;
  overflow-x: hidden;
  width: 100%;
  height: calc(100% - 195px);
  background-color: #a0c0d7 !important;
  padding-top: 8px;
  padding-bottom: 8px;
}
</style>
