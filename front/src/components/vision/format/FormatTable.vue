<template>
  <div>
    <v-card>
      <v-layout>
        <v-spacer></v-spacer>
        <v-flex xs3>
          <v-text-field prepend-icon="search" label="검색" v-model="search"></v-text-field>
        </v-flex>
      </v-layout>
      <v-data-table :search="search" rows-per-page-text="페이지별 갯수" :pagination.sync="pagination" :items="items" :headers="headers" :rows-per-page-items="[10, 20, 30, 50, 100]">
        <template slot="no-data">
          <v-alert :value="true" color="warning" icon="warning">
            포멧 데이터가 존재하지 않습니다.
          </v-alert>
        </template>
        <template slot="items" slot-scope="props">
          <td>{{props.item.index}}</td>
          <td>{{props.item.title}}</td>
          <td>{{props.item.body}}</td>
          <td>{{props.item.association}}</td>
          <td>
            <v-btn outline v-for="(file,i) in props.item.files" :key="i" color="primary" @click="onLinkClick(file.filename)">
              <v-icon dark class="mr-1">link</v-icon>
              <span>{{file.text}}</span>
            </v-btn>
            <!-- <v-chip v-for="(file,i) in props.item.files" :key="i" color="teal" @click="onLinkClick(file.filename)">
              <v-avatar icon class="chip">
                <v-icon dark>link</v-icon>
              </v-avatar>
              <span class="white--text chip">{{file.text}}</span>
            </v-chip> -->
          </td>
          <td v-if="$store.getters.isAdmin">
            <v-btn icon @click="onDeleteClick(props.item)" :loading="deleteLoading">
              <v-icon>delete</v-icon>
            </v-btn>
          </td>

        </template>
      </v-data-table>
      <div class="text-xs-left">
        <v-dialog width="800px" v-model="dialog">
          <v-btn color="primary" slot="activator" v-if="$store.getters.isAdmin">추가</v-btn>
          <divider3 v-if="dialog" type="dialog" title="서식 추가" subtitle="서식을 추가 할 수 있습니다.">
            <divider3 title="연동">
              <v-switch label="연동" v-model="association"></v-switch>
            </divider3>
            <advanced-form ref="form">
              <divider3 title="글작성">
                <v-layout>
                  <v-flex xs4>
                    <named-field fieldName="서식_제목" required></named-field>
                  </v-flex>
                </v-layout>
                <named-field fieldName="서식_내용" required></named-field>
              </divider3>
              <divider3 title="파일">
                <v-layout>
                  <named-field fieldName="서식_첨부파일" @change="FileChange" required></named-field>
                  <named-field fieldName="서식_파일명" required></named-field>
                  <named-field v-if="association" fieldName="서식_파일연동"></named-field>
                </v-layout>
              </divider3>
            </advanced-form>
            <v-layout>
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="onAddClick" :loading="addLoading">추가</v-btn>
            </v-layout>
          </divider3>
        </v-dialog>
      </div>
    </v-card>

  </div>

</template>

<script>
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
import AdvancedForm from "@/components/AdvancedForm";
import NamedField from "@/components/NamedField";
export default {
  name: "FormatTable",
  components: {
    AdvancedForm,
    NamedField,
    Divider1,
    Divider3
  },
  methods: {
    updateItems() {
      let items = [];
      let formats = this.$store.state.all.formats;
      for (var i in formats) {
        let format = formats[i];
        let files = [];
        for (var i in format["files"]) {
          var path = format["files"][i];
          files.push({
            text: format.effect["파일명"][i],
            filename: basename(path)
          });
        }
        items.push({
          index: format["index"],
          title: format["title"],
          body: format["body"],
          association: format.effect["파일연동"],
          files: files
        });
      }
      this.items = items;
    },
    onDeleteClick(item) {
      this.$store.dispatch("post", {
        data: {
          type: "format",
          name: "remove",
          bag: {
            index: item.index
          }
        },
        success: () => {
          this.formatsUpdate(() => {
            this.deleteLoading = false;
          });
        },
        error: () => {
          this.deleteLoading = false;
          this.$store.commit("snackbar", {
            type: "error",
            text: "삭제에 실패했습니다."
          });
        }
      });
    },
    formatsUpdate(proc) {
      this.$store.commit("update", {
        type: "formats",
        name: "get",
        success: result => {
          this.updateItems();
          if (proc) proc();
        }
      });
    },
    getRandomColor() {
      return this.$store.state.colors[
        Math.floor(Math.random() * this.$store.state.colors.length)
      ];
    },
    onLinkClick(link) {
      FileDownload(this.$store.state.uploadsurl.format + link);
    },
    FileChange(e) {
      this.formData = new FormData();
      for (var i in e.target.files) {
        this.formData.append("첨부파일#" + i, e.target.files[i]);
      }
    },
    onAddClick() {
      // if (this.formData == false) {
      //   alert("파일이 없습니다.");
      //   return;
      // }
      let data = {};
      let effect = {};
      let ajaxurl = this.$store.state.ajaxurl;
      let result = this.$refs.form.get();
      if (!result.validated) {
        this.$store.commit("snackbar", {
          pre: "err_form"
        });
        return;
      }
      let fields = result.fields;
      this.addLoading = true;
      $.ajax({
        url: ajaxurl, // url where upload the image
        contentType: "multipart/form-data",
        type: "POST",
        data: this.formData,
        dataType: "json",
        mimeType: "multipart/form-data",
        cache: false,
        contentType: false,
        processData: false,
        success: res => {
          if (res.success) {
            effect["파일명"] = fields["파일명"].split(" ");
            effect["파일연동"] = fields["파일연동"];

            var files = [];
            for (var key in res.result) {
              if (key.indexOf("첨부파일") != -1) {
                files.push(res.result[key]);
              }
            }

            this.$store.dispatch("post", {
              data: {
                type: "format",
                name: "add",
                bag: {
                  title: fields["제목"],
                  body: fields["내용"],
                  files: files,
                  effect: effect
                }
              },
              success: result => {
                this.dialog = false;
                this.formatsUpdate(() => {
                  this.addLoading = false;
                });
              },
              error: () => {
                this.addLoading = false;
                this.$store.commit("snackbar", {
                  type: "error",
                  text: "삭제에 실패했습니다."
                });
              }
            });
          }
        }
      });
    }
  },
  mounted() {
    this.headers = [
      { text: "신청번호", value: "index" },
      { text: "제목", value: "title", sortable: false },
      { text: "내용", value: "body", sortable: false },
      { text: "연동", value: "association" },
      { text: "첨부파일", value: "files", sortable: false }
    ];
    if (this.$store.getters.isAdmin)
      this.headers.push({ text: "행동", value: "actions", sortable: false });
    this.updateItems();
  },
  data() {
    return {
      search: "",
      addLoading: false,
      deleteLoading: false,
      association: true,
      dialog: false,
      formData: false,
      pagination: {
        sortBy: "index",
        descending: true
      },
      items: [],
      headers: []
    };
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

function basename(path) {
  return path.replace(/\\/g, "/").replace(/.*\//, "");
}

function dirname(path) {
  return path.replace(/\\/g, "/").replace(/\/[^\/]*$/, "");
}
</script>


<style scoped>
.chip:hover {
  cursor: pointer;
}
</style>

