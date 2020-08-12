<template>
    <v-card flat>
        <v-container>
            <v-layout>
                <v-flex xs4>
                    <v-card flat>
                        <v-card-text>
                            <div class="text-xs-center">
                                <v-icon x-large color="black">phone_android</v-icon>
                                <div class="subheading">{{call}}</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs4>
                    <v-card flat>

                        <v-card-text>
                            <div class="text-xs-center">
                                <v-icon x-large color="black">location_on</v-icon>
                                <div>{{location}}</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs4>
                    <v-card flat>

                        <v-card-text>
                            <div class="text-xs-center">
                                <v-icon x-large color="black">mail</v-icon>
                                <div>{{email}}</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
            <div class="ma-1">
                <v-card>
                    <v-card-text>
                        <iframe :src="googlemap" width="100%" height="600" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </v-card-text>
                </v-card>
            </div>
        </v-container>
    </v-card>
</template>
<script>
export default {
  name: "ContactBox",
  props: ["call", "location", "email", "googlemap"]
};
</script>
