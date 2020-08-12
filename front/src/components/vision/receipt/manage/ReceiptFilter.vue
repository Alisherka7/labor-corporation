<template>
    <!-- 필터 -->
    <v-layout>
        <v-flex>
            <v-layout wrap>
                <v-flex>
                    <v-checkbox label="신청" v-model="filters.state.apply" @change="onFilterChange"></v-checkbox>
                </v-flex>
                <v-flex>
                    <v-checkbox label="보류" v-model="filters.state.refuse" @change="onFilterChange"></v-checkbox>
                </v-flex>
                <v-flex>
                    <v-checkbox label="취소" v-model="filters.state.cancel" @change="onFilterChange"></v-checkbox>
                </v-flex>
                <v-flex>
                    <v-checkbox label="완료" v-model="filters.state.accept" @change="onFilterChange"></v-checkbox>
                </v-flex>
                <v-flex>
                    <v-menu ref="menu1" :close-on-content-click="false" v-model="menu.start" :nudge-right="40" :return-value.sync="filters.date.start" lazy transition="scale-transition" offset-y full-width min-width="290px">
                        <v-text-field @change="onFilterChange" slot="activator" v-model="filters.date.start" label="시작날짜" prepend-icon="event" readonly></v-text-field>
                        <v-date-picker v-model="filters.date.start" no-title scrollable>
                            <v-spacer></v-spacer>
                            <v-btn flat color="primary" @click="menu.start = false">Cancel</v-btn>
                            <v-btn flat color="primary" @click="$refs.menu1.save(filters.date.start); onFilterChange()">OK</v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-flex>
                <v-flex>
                    <v-menu ref="menu2" :close-on-content-click="false" v-model="menu.end" :nudge-right="40" :return-value.sync="filters.date.end" lazy transition="scale-transition" offset-y full-width min-width="290px">
                        <v-text-field slot="activator" v-model="filters.date.end" label="종료날짜" prepend-icon="event" readonly></v-text-field>
                        <v-date-picker v-model="filters.date.end" no-title scrollable>
                            <v-spacer></v-spacer>
                            <v-btn flat color="primary" @click="menu.end = false">Cancel</v-btn>
                            <v-btn flat color="primary" @click="$refs.menu2.save(filters.date.end); onFilterChange()">OK</v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-flex>
                <v-flex>
                    <v-text-field label="검색" v-model="filters.search" prepend-icon="search"></v-text-field>
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
  name: "ReceiptFilter",
  props: ["auth"],
  methods: {
    onFilterChange() {
      this.$emit("change", this.filters);
    }
  },
  data() {
    return {
      filters: {
        date: {
          start: "",
          end: ""
        },
        state: {
          apply: true,
          refuse: this.auth == 'admin'? false:true,
          cancel: this.auth == 'admin'? false:true,
          accept: this.auth == 'admin'? false:true
        },
        search: ""
      },
      menu: {
        start: false,
        end: false
      }
    };
  },
  mounted() {
    this.onFilterChange();
  },
};
</script>

<style>
</style>
