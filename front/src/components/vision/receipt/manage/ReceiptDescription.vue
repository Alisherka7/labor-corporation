<template>
  <div>
    <divider1 :title="this.modalItem.receipt_meta.index + ' - ' +this.modalItem.receipt_meta.receipt_name">
      <divider3 title="관리회사" subtitle="관리회사 정보입니다">
        <v-layout wrap>
          <v-flex xs6>
            <fields-descriptor :items="getCMItems.front">
            </fields-descriptor>
          </v-flex>
          <v-flex xs6>
            <fields-descriptor :items="getCMItems.back">
            </fields-descriptor>
          </v-flex>
        </v-layout>
      </divider3>
      <v-divider></v-divider>
      <divider3 title="신청서" subtitle="신청서 정보입니다">
        <v-layout wrap>
          <v-flex xs6>
            <fields-descriptor :items="getReceiptItems.front">
            </fields-descriptor>
          </v-flex>
          <v-flex xs6>
            <fields-descriptor :items="getReceiptItems.back">
            </fields-descriptor>
          </v-flex>
        </v-layout>
      </divider3>
      <v-divider></v-divider>

      <divider3 title="신청자" subtitle="신청자 정보입니다">
        <v-layout wrap>
          <v-flex xs6>
            <fields-descriptor :items="getUserItems.front">
            </fields-descriptor>
          </v-flex>
          <v-flex xs6>
            <fields-descriptor :items="getUserItems.back">
            </fields-descriptor>
          </v-flex>
        </v-layout>
      </divider3>
      <v-divider></v-divider>
      <divider3 title="신청서" subtitle="신청서 메타정보입니다">
        <v-layout wrap>
          <v-flex xs6>
            <fields-descriptor :items="getMetaItems.front">
            </fields-descriptor>
          </v-flex>
          <v-flex xs6>
            <fields-descriptor :items="getMetaItems.back">
            </fields-descriptor>
          </v-flex>
        </v-layout>
      </divider3>
    </divider1>
  </div>
</template>

<script>
import FieldsDescriptor from "@/components/FieldsDescriptor";
import Divider1 from "@/components/lib/divider/Divider1";
import Divider3 from "@/components/lib/divider/Divider3";
export default {
  name: "ReceiptDescription",

  components: {
    FieldsDescriptor,
    Divider3,
    Divider1
  },
  computed: {
    getReceiptItems() {
      var fixedReceiptData = [];
      var tempReceiptData = {};

      /**
       * EX)
       * 피부양자1:{
       * 이름: '김이름',
       * 주민등록번호: '1234511234512',
       * },
       * 신고사유:{
       * 폐업/도산: ture,
       * 휴업: false,
       * }
       * */

      for (var key in this.modalItem.receipt_data) {
        var value = this.modalItem.receipt_data[key];
        var sharpIndex = key.indexOf("#");
        if (sharpIndex != -1) {
          var front = key.substr(0, sharpIndex);
          var back = key.substr(sharpIndex + 1);
          
          
          
          if (front == "신고사유" || front == "근무일자_일") {
            if (!(front in tempReceiptData)) tempReceiptData[front] = {};
            tempReceiptData[front][back] = value;
          } else {
            tempReceiptData[key] = value;
          }
        } else {
          tempReceiptData[key] = value;
        }
      }

      for (var key in tempReceiptData) {
        var item = {};
        var value = tempReceiptData[key];

        item.key = key;
        item.value = value;

        if (this.inArray(key, this.modalItem.receipt_meta.file_fields) != -1) {
          item.file = true;
          

        } else if (typeof value == "object") {
          if (key == "신고사유" || key == "근무일자_일") {
            var newValue = "";
            for (var comKey in value) {
              var comValue = value[comKey];
              if (comValue == "true") {
                newValue += comKey + ", ";
              }
              // newValue += "[" + comKey + ": " + comValue + "]\n";
            }
            newValue = newValue.substring(newValue, newValue.length - 2);
            item.value = newValue;
          }
        }

        if(key == '직종코드' || key == '국가코드'){
          item.value = item.value.replace(/[0-9]/g, '');
          item.value = item.value.trim();
        }else if (key == '체류코드'){
          var lastIndex = item.value.lastIndexOf(' ');
          item.value = item.value.substr(lastIndex);

        }
        fixedReceiptData.push(item);
      }
      return {
        front: fixedReceiptData.slice(0, fixedReceiptData.length / 2 + 1),
        back: fixedReceiptData.slice(fixedReceiptData.length / 2 + 1)
      };
    },
    getCMItems() {
      var manageCompany = this.modalItem.receipt_meta["manage_company"];
      var newArray = this.sliceObject(manageCompany);
      // console.log(manageCompany);
      return {
        front: newArray.front,
        back: newArray.back,
        exists: manageCompany.length ? true : false
      };
    },
    getUserItems() {
      var newObj = {};
      var sub = ["메타", "관리회사", "비밀번호"];
      for (var key in this.modalItem.user) {
        if (sub.indexOf(key) != -1) continue;
        else newObj[key] = this.modalItem.user[key].toString();

        
        if(key == '직종코드' ){
          newObj[key] = newObj[key].replace(/[0-9]/g, '');
          newObj[key] = newObj[key].trim();
        }
      }
      return this.sliceObject(newObj);
    },
    getMetaItems() {
      var newObj = {};
      for (var key in this.modalItem.receipt_meta) {
        var value = this.modalItem.receipt_meta[key];
        if (key == "receipt_name") {
          newObj["신청서"] = value;
        } else if (key == "apply_time") {
          newObj["신청 시간"] = this.getTimeFormat(1, value);
        } else if (key == "id") {
          newObj["신청자 아이디"] = value;
        } else if (key == "index") {
          newObj["신청번호"] = value;
        }
      }
      return this.sliceObject(newObj);
    }
  },
  props: ["modalItem"],
  methods: {
    inArray(finder, array) {
      return $.inArray(finder, array);
    },
    basename(path) {
      return path.replace(/\\/g, "/").replace(/.*\//, "");
    },
    sliceObject(object) {
      var items = [];
      for (var key in object) {
        var value = object[key];

        items.push({
          key: key,
          value: value
        });
      }
      var front = items.slice(0, items.length / 2 + 1);
      var back = items.slice(items.length / 2 + 1);

      return {
        front: front,
        back: back,
        items: items
      };
    },
    getTimeFormat(type, time) {
      var date = new Date(time * 1000);
      var mm = date.getMonth() + 1;
      var dd = date.getDate();
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var seconds = date.getSeconds();
      var ymd = [
        date.getFullYear(),
        (mm > 9 ? "" : "0") + mm,
        (dd > 9 ? "" : "0") + dd
      ].join("-");

      var hms = [
        (hours > 9 ? "" : "0") + hours,
        (minutes > 9 ? "" : "0") + minutes,
        (seconds > 9 ? "" : "0") + seconds
      ].join(":");

      switch (type) {
        case 1:
          return ymd + " " + hms;
        case 2:
          return ymd;
      }
    }
  }
};
</script>

<style>
</style>
