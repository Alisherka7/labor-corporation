<template>
    <v-layout>
        <v-flex v-for="(item,i) in items" :key="i" class="ma-2" xs3>
            <v-card :color="item.color" dark class="pa-2">
                <v-card-text>
                    <v-layout>
                        <!-- 타이틀, 서브타이틀, 설명 -->
                        <v-flex>
                            <v-container grid-list-sm class="ma-0 pa-1">
                                <v-layout column align-content-space-around fill-height>
                                    <v-flex>
                                        <span class="subheading bold">
                                            {{item.title}}
                                        </span>
                                    </v-flex>
                                    <v-flex>
                                        <span class="display-1 bold"> {{item.subtitle}} </span>
                                        <v-icon small class="mb-1">arrow_upward</v-icon>
                                    </v-flex>
                                    <v-flex>
                                        <span class="body-2 bold">
                                            {{item.description}}
                                        </span>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-flex>
                        <!-- 아이콘 -->
                        <v-flex>
                            <v-layout align-center justify-end fill-height>
                                <v-icon dark x-large class="pa-3 icon_radius rotate" >{{item.icon}}</v-icon>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
  name: "ReceiptDashCard",
  props: {
    items: {}
  },
  data() {
    return {
    };
  },

};
</script>

<style scoped>
.icon_radius {
  border-radius: 100%;
  border: 2px solid rgba(255, 255, 255, 0.4);
}
