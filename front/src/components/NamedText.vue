<template>
  <span>
    {{fixedValue}}
  </span>
</template>

<script>
export default {
  name: "NamedText",
  props: {
    name: {
      type: String,
      required: true
    },
    value: {
      required: true
    }
  },
  data() {
    return {
      masktype: ""
    };
  },
  mounted() {
    let nameds = {
      // call: ["회사전화번호", "담당자전화번호"],
      call: ["담당자전화번호"],
      // fax: ["회사팩스번호"],
      companyRegisterNumber: ["사업자등록번호"],
      regidentRegisterNumber: ["주민등록번호", "외국인등록번호"]
    };

    /**  어떤 마스크인지 찾는다.  */
    for (var masktype in nameds) {
      for (var i in nameds[masktype]) {
        if (nameds[masktype][i] == this.name) {
          this.masktype = masktype;
          break;
        }
      }
    }
  },
  computed: {
    fixedValue() {
      if (!this.masktype) return this.value;

      let masks = this.$store.state.masks;
      var mask = masks[this.masktype];
      var r = "";
      var valueArray = this.value.split("").reverse();
      for (var i in mask) {
        var sharp = mask[i];
        if (sharp == "#") {
          var character = valueArray.pop();
          if (character) r += character;
          else break;
        } else {
          r += " ";
        }
      }
      return r;
    }
  }
};
</script>

<style>
</style>
