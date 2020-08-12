<template>
    <v-card class="mb-3" flat>
        <v-card-text>
            <AdvancedForm ref="form">
                <slot></slot>
            </AdvancedForm>
        </v-card-text>
        <v-card-actions v-if="!noaction">
            <v-layout>
                <v-btn large icon @click="$emit('before')">
                    <v-icon>navigate_before</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn large color="primary" @click="submit" :loading="nextbtnLoading">
                    <span class="subheading">다음</span>
                    <v-icon>navigate_next</v-icon>
                </v-btn>
            </v-layout>
        </v-card-actions>
    </v-card>
</template>

<script>
import AdvancedForm from "@/components/AdvancedForm";
export default {
  name: "NavigateCard",
  components: {
    AdvancedForm
  },
  props: {
    noaction: {
      type: Boolean,
      default: false,
    },
      nextbtnLoading: {
          type: Boolean,
          default: false, 
      }

  },
  methods: {
      submit(){
          this.$emit('result', this.$refs.form.get());
      }
  }
};
</script>
 