<template>
  <div>
    <advanced-form ref="form">
      <fields-descriptor :type="type" :items="items" @icon_click="type = 'modify'">
        <div v-for="(item,i) in items" :key="i" :slot="item.key">
          <slot :name="item.key"></slot>
        </div>
      </fields-descriptor>
    </advanced-form>
    <div>
      <div v-if="type =='render'" >
        <v-btn @click="type = 'modify'" :loading="loading" color="primary" >수정하기</v-btn>
      </div>
      <div v-else-if="type=='modify'">
        <div class="mx-3">
          <v-layout>
            <v-btn @click="type ='render'" :loading="loading" icon>
              <v-icon>chevron_left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn @click="onModify" :loading="loading" color="primary">수정완료</v-btn>
          </v-layout>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FieldsDescriptor from "@/components/FieldsDescriptor";
import AdvancedForm from "@/components/AdvancedForm";
export default {
  name: "ModifiyerBox",
  components: {
    FieldsDescriptor,
    AdvancedForm
  },
  props: {
    items: {
      type: Array
    }
  },
  data() {
    return {
      loading: false,
      type: "render"
    };
  },

  methods: {
    onModify() {
      var result = this.$refs.form.get();
      if (!result.validated) {
        this.$store.commit("snackbar", {
          pre: "err_form"
        });
        return;
      }
      this.loading = true;

      this.$emit("modify", result);
    },
    onModifyComplete() {
      this.loading = false;
      this.type = "render";
      this.$store.commit("snackbar", {
        type: "success",
        text: "수정이 완료되었습니다."
      });
    },
    onModifyFail() {
      this.loading = false;
    }
  },
  watch: {
    type() {
      this.$emit("state", this.type);
    }
  }
};
</script>
