<template>
  <div>
    <v-layout>
      <v-spacer></v-spacer>
      <v-flex xs3>
        <v-text-field prepend-icon="search" label="검색" v-model="search"></v-text-field>
      </v-flex>
    </v-layout>
    <v-data-table :pagination.sync="pagination" :search="search" rows-per-page-text="페이지별 갯수" :headers="headers" :items="filteredItems" :rows-per-page-items="[30, 70, 100, 250]">
      <template slot="items" slot-scope="props">
        <td v-for="(v, i) in props.item" :key="i">
          <template v-if="i == 'readcount'">
            <v-btn v-if="props.item['아이디'] != 'admin'" icon @click="onChatClick(props.item['아이디'])">
              <v-badge v-if="v != 0 && chatClicked.indexOf(props.item['아이디']) == -1" right color="error" overlap>
                <span slot="badge">{{v}}</span>
                <v-icon large color="yellow lighten-2">chat_bubble</v-icon>
              </v-badge>
              <v-icon v-else large color="yellow lighten-2">chat_bubble</v-icon>
            </v-btn>
          </template>
          <v-avatar v-else-if="i == 'avatar'" size="35">
            <img :src="v" />
          </v-avatar>
          <template v-else>
            <v-menu :open-on-click="false" :open-on-hover="v.toString().length > 20" offset-y>
              <span slot="activator">
                <div class="pointer" @click="Copy(v.toString())">
                  <named-text :name="i" :value="ToDot(v.toString(), 20)"></named-text>
                </div>
              </span>
              <v-card width="500">
                <v-card-text>
                  <named-text :name="i" :value="ToDot(v.toString(), 1000)"></named-text>
                </v-card-text>
              </v-card>
            </v-menu>
          </template>
        </td>
        <td>
          <v-btn icon @click="deleteUser(props.item)" :loading="deleteLoading" v-if="props.item['아이디'] != 'admin'">
            <v-icon>delete</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import NamedText from "@/components/NamedText";
export default {
  name: "UserManageTable",
  components: {
    NamedText
  },
  data() {
    return {
      search: "",
      deleteLoading: false,
      dialog: false,
      headers: [],
      norender: ["관리회사", "비밀번호확인", "메타"],
      chatClicked: [],
      pagination: {
        sortBy: "readcount",
        descending: true
      }
    };
  },
  computed: {
    filteredItems() {
      var tempItems = [];
      var users = this.$store.state.all.users;
      for (var id in users) {
        var user = users[id];
        var tempItem = {};
        var readcount = 0;
        if (users["admin"]["메타"]["rooms"].hasOwnProperty(id)) {
          readcount = users["admin"]["메타"]["rooms"][id];
        }
        tempItem["readcount"] = readcount;
        tempItem["avatar"] = require("@/assets/avatars/1/avatar (" +
          (Number.parseInt(users[id]["메타"]["avatar"]) + 1) +
          ").svg");
        for (var key in user) {
          var value = user[key];
          if (this.norender.indexOf(key) != -1) continue;

          tempItem[key] = value;
        }

        tempItems.push(tempItem);
      }
      return tempItems;
    }
  },
  mounted() {
    this.updateItemsData();
  },
  methods: {
    Copy(str) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(str).select();
      document.execCommand("copy");
      $temp.remove();
      this.$store.commit("snackbar", {
        type: "success",
        text: "클립보드에 복사되었습니다."
      });
    },
    onChatClick(id) {
      this.$store.commit("chat", id);
      this.chatClicked.push(id);
    },
    updateItemsData() {
      let users = this.$store.state.all.users;
      // let tempItems = {};
      var tempHeaders = [];

      tempHeaders.push({
        text: "알림",
        value: "chat",
        sortable: false
      });
      tempHeaders.push({
        text: "아바타",
        value: "avatar",
        sortable: false
      });
      for (var key in users["admin"]) {
        if (this.norender.indexOf(key) != -1) continue;
        tempHeaders.push({
          text: key,
          value: key
        });
      }
      tempHeaders.push({
        text: "행동",
        value: "actions",
        sortable: false
      });
      this.headers = tempHeaders;
    },
    UpdateItems() {
      this.$store.commit("update", {
        type: "users",
        name: "get_all",
        success: result => {
          this.updateItemsData();
        }
      });
    },
    deleteUser(item) {
      this.deleteLoading = true;
      this.dialog = !this.dialog;

      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "remove",
          bag: {
            아이디: item["아이디"]
          }
        },
        success: res => {
          this.UpdateItems();
          this.deleteLoading = false;
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "유저를 삭제하지 못했습니다."
          });
        }
      });
    },
    ToDot(str, len) {
      if (str.length > len) {
        str = str.replace(/,/g, ", ");
        return str.substr(0, len) + "...";
      }
      return str;
    }
  }
};
</script>

