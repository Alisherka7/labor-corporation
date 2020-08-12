<template>
  <div>
    <modifiyer-box :items="filteredItems" @state="onStateChange" @modify="onModify" ref="modifybox">
      <named-field v-for="(item, i) in filteredItems" :key="i" :fieldName="item.key" :value="item.value" :slot="item.key"></named-field>
    </modifiyer-box>
  </div>
</template>

<script>
import ModifiyerBox from "@/components/ModifiyerBox";
import NamedField from "@/components/NamedField";
export default {
  name: "PrivacyModify",
  components: {
    NamedField,
    ModifiyerBox
  },
  data() {
    return {
      items: [],
      state: "render",
      noRender: ["비밀번호", "비밀번호확인", "관리회사", "신청서", "메타"],
      noModify: [
        "비밀번호",
        "비밀번호확인",
        "관리회사",
        "신청서",
        "아이디",
        "메타"
      ]
    };
  },
  computed: {
    filteredItems() {
      var tempItems = this.items.filter(v => {
        if (this.state == "render") {
          if (this.noRender.indexOf(v.key) != -1) return false;
          else return true;
        } else if (this.state == "modify") {
          if (this.noModify.indexOf(v.key) != -1) return false;
          else return true;
        }
      });

      for (var i in tempItems) {
        var v = tempItems[i];
        if (this.noModify.indexOf(v.key) == -1) {
          tempItems[i].icon = "";
        } else {
          tempItems[i].icon = "";
        }
      }
      return tempItems;
    }
  },
  methods: {
    updateItems() {
      this.$store.commit("update", {
        type: "users",
        name: "get",
        success: result => {
          this.updateItemsData();
        }
      });
    },
    updateItemsData() {
      var result = this.$store.state.all.users[this.$store.state.all.id];
      var tempItems = [];
      for (var key in result) {
        var value = result[key];
        tempItems.push({
          key: key,
          value: value
        });
      }
      this.items = tempItems;
    },
    onModify(result) {
      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "modify_privacy",
          bag: {
            privacys: result.fields
          }
        },
        success: result => {
          this.updateItems();
          this.$refs.modifybox.onModifyComplete();
        },
        error: () => {
          this.$store.commit("snackbar", {
            type: "error",
            text: "개인정보를 변경하지 못했습니다"
          });
        }
      });
    },
    onStateChange(state) {
      this.state = state;
    }
  },
  mounted() {
    this.updateItemsData();
  }
};
</script>

