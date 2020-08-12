<template>
    <span>{{randomMessage}}</span>
</template>

<script>
export default {
  name: "RandomMessageCreater",
  computed: {
    randomMessage() {
      var getRandomInt = max => {
        return Math.floor(Math.random() * Math.floor(max));
      };
      var srcArray = this.src.split(".");
      var randomIndex = getRandomInt(srcArray.length - 1);
      
      var randomMessage =  srcArray[randomIndex];
      randomMessage = randomMessage.replace(/'/g, '');
      randomMessage= randomMessage.replace(/"/g, '');
      randomMessage= randomMessage.replace(/\[/g, '');
      randomMessage= randomMessage.replace(/\]/g, '');
      randomMessage= randomMessage.replace(/\(/g, '');
      randomMessage= randomMessage.replace(/\)/g, '');
      randomMessage= randomMessage.replace(/“/g, '');
      randomMessage= randomMessage.replace(/”/g, '');
      
      
      return randomMessage;
    }
  },
  data() {
    return {
      src: ``
    };
  }
};
</script>

<style>
</style>
