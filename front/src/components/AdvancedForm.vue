<template>
  <div>
    <v-form ref="form">
      <slot></slot>
    </v-form>
  </div>
</template>

<script>
export default {
  name: "AdvancedForm",
  methods: {
    get() {
      let fields_detail = this.getfields();
      let fields = {};
      for (var i in fields_detail) {
        var field = fields_detail[i];
        if (field.type != "file-field") {
          fields[field.key] = field.value;
        }
      }
      let result = {
        validated: this.validate(),
        fields: fields,
        fields_detail: fields_detail
      };

      return result;
    },
    validate() {
      return this.$refs.form.validate();
    },
    /*
        v-switch
        v-text-field
        v-radio-group
        v-autocomplete
        v-textarea
        v-date-picker
        // (v-slider)
        file-field ( text-field를 file-field 에서 구현한다. )
    */
    getfields() {
      let fields = {};
      $(this.$refs.form.$el)
        .find(".file-field")
        .each((index, el) => {
          let result = {
            key: "",
            type: "file-field",
            value: ""
          };
          result.key = $(el).attr("form-key");
          result.value = $(el)[0].files;

          if (result.key) fields[result.key] = result;
        });

      for (let input of this.$refs.form.inputs) {
        let tag = input.$vnode.tag;

        let result = {
          key: "",
          type: "",
          value: ""
        };
        if (tag.indexOf("v-autocomplete") != -1) {
          result.type = "v-autocomplete";
          result.key = $(input.$el)
            .find("input")
            .attr("form-key");

          result.value = $(input.$el)
            .find("input")
            .val();
        } else if (tag.indexOf("v-radio-group") != -1) {
          result.type = "v-radio-group";
          result.key = $(input.$el).attr("form-key");

          $(input.$el)
            .find("input")
            .each((i, e) => {
              if ($(e).attr("aria-checked") == "true") {
                result.value = $(e).attr("aria-label");
              }
            });
        } else if (tag.indexOf("v-text-field") != -1) {
          var $el = $(input.$el);
          result.type = "v-text-field";
          result.key = $el.find("input").attr("form-key");
          result.value = $el.find("input").val();

          if ($el.hasClass("masked")) {
            result.value = result.value.replace(/ /g, "");
          }
        } else if (tag.indexOf("v-textarea") != -1) {
          result.type = "v-textarea";
          result.key = $(input.$el)
            .find("textarea")
            .attr("form-key");
          result.value = $(input.$el)
            .find("textarea")
            .val();
        } else if (tag.indexOf("v-switch") != -1) {
          result.type = "v-switch";
          result.key = $(input.$el)
            .find("input")
            .attr("form-key");
          result.value = $(input.$el)
            .find("input")
            .attr("aria-checked");
        } else if (tag.indexOf("v-checkbox") != -1) {
          result.type = "v-checkbox";
          result.key = $(input.$el)
            .find("input")
            .attr("form-key");
          result.value = $(input.$el)
            .find("input")
            .attr("aria-checked");
        }
        if (result.key) fields[result.key] = result;
      }
      return fields;
    }
  }
};
</script>

