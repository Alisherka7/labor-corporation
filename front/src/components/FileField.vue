<template>
  <span>
    <v-btn outline :color="required && !changed ? 'error': 'primary'" :dark="dark" @click="onBtnClick">
      {{baseLabel || label}}
      <v-icon right dark>cloud_upload</v-icon>
      <input type="file" @change="onFileChange" v-show="false" :multiple="multiple" class="file-field" :form-key="formKey"/>
    </v-btn>
  </span>
</template>

<script>
export default {
  name: "FileField",
  props: {
    dark: { type: Boolean, default: true },
    label: { type: String, default: "파일 선택" },
    multiple: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    formKey: {type: String, default: ''}
  },
  data() {
    return {
      baseLabel: "",
      changed: false
    };
  },

  methods: {
    onFileChange(e) {
      this.changed = true;
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      if (this.multiple) {
        this.baseLabel = "파일" + files.length + "개 선택됨";
      } else {
        this.baseLabel = files[0].name;
      }
      this.$emit("change", e);
    },
    onBtnClick() {
      this.$el.getElementsByTagName("input")[0].click();
    }
  },
};
</script>

