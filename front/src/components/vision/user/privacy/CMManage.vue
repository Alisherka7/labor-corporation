<template>
  <div>
    <v-layout>
      <v-spacer></v-spacer>
      <v-flex xs3>
        <v-text-field prepend-icon="search" label="검색" v-model="search"></v-text-field>
      </v-flex>
    </v-layout>
    <v-data-table :search="search" rows-per-page-text="페이지별 갯수" :headers="headers" :items="items" :rows-per-page-items="[15, 30, 50, 100]">
      <template slot="no-data">
        <v-alert :value="true" color="warning" icon="warning">
          관리회사 데이터가 존재하지 않습니다.
        </v-alert>
      </template>
      <template slot="items" slot-scope="props">
        <td v-for="(v, i) in props.item" :key="i">
          <named-text :name="i" :value="v"></named-text>
        </td>
        <td>
          <v-btn icon @click="removeCM(props.item)" :loading="loading">
            <v-icon>delete</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>

    <v-dialog width="800px" v-model="dialog">
      <v-btn color="primary" slot="activator">추가</v-btn>
      <divider3 v-if="dialog" title="관리회사 추가" subtitle="관리회사를 추가 할 수 있습니다." type="dialog">
        <advanced-form ref="form">
          <v-layout>
            <span v-for="(v,i) in headers" :key="i">
              <named-field v-if="v.value != `actions`" :fieldName="v.value" required></named-field>
            </span>
          </v-layout>
        </advanced-form>
        <v-layout>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="onAddClick" :loading="loading">추가</v-btn>
        </v-layout>
      </divider3>
    </v-dialog>
  </div>
</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import AdvancedForm from "@/components/AdvancedForm";
import NamedField from "@/components/NamedField";
import NamedText from "@/components/NamedText";
export default {
  name: "CMManage",
  components: {
    AdvancedForm,
    NamedField,
    Divider1,
    Divider3,
    NamedText
  },
  data() {
    return {
      search: "",
      loading: false,
      dialog: false,
      headers: [
        { text: "회사명", value: "회사명" },
        { text: "사업자등록번호", value: "사업자등록번호" },
        { text: "팩스번호", value: "회사팩스번호" },
        { text: "전화번호", value: "회사전화번호" },
        { text: "행동", value: "actions", sortable: false }
      ],
      items: []
    };
  },
  mounted() {
    this.updateItemsData();
  },
  methods: {
    updateItemsData() {
      let users = this.$store.state.all.users;
      let id = this.$store.state.all.id;
      let user = users[id];
      let tempItems = [];
      for (var cmName in user["관리회사"]) {
        var cmDatas = user["관리회사"][cmName];
        var item = {};
        for (var cmDataKey in cmDatas) {
          var cmDataValue = cmDatas[cmDataKey];
          item[cmDataKey] = cmDataValue;
        }
        tempItems.push(item);
      }

      this.items = tempItems;
    },
    UpdateItems() {
      this.$store.commit("update", {
        type: "users",
        name: "get",
        success: () => {
          this.updateItemsData();
        }
      });
    },
    removeCM(item) {
      this.loading = true;

      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "remove_cm",
          bag: {
            company_name: item["회사명"]
          }
        },
        success: res => {
          this.UpdateItems();
          this.loading = false;
        },
        error: () => {
          this.loading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "관리회사를 삭제하지 못했습니다."
          });
        }
      });
    },
    onAddClick() {
      var result = this.$refs.form.get();
      if (!result.validated) {
        this.$store.commit("snackbar", {
          pre: "err_form"
        });
        return;
      }
      this.loading = true;

      this.$store.dispatch("post", {
        data: {
          type: "user",
          name: "add_cm",
          bag: {
            datas: result.fields
          }
        },
        success: res => {
          this.loading = false;
          this.dialog = false;
          this.UpdateItems();
        },
        error: result => {
          this.loading = false;
          if (result == "exist") {
            this.$store.commit("snackbar", {
              type: "error",
              text: "이미 존재하는 회사명입니다."
            });
          }
        }
      });
    }
  }
};
</script>
