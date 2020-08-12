<template>
    <v-container grid-list-md>
        <v-layout wrap>
            <v-flex  v-for="(v, i) in items" :key="i" @click="$emit('selected', i)">
                <img class="img" :src="v" :width="width"/>
            </v-flex>
        </v-layout>
    </v-container>

</template>

<script>
export default {
  name: "ImageContainer",
    props:['items', 'width']

}
</script>

<style scoped>
    .img:hover{
        cursor: pointer;
        opacity: 0.8;
        border: 0.5px solid rgba(255,0,0,0.1);
    }
</style>
