<template>
  <div class="home d-flex justify-center">
    <MapTable class ="MapTable" />
  </div>
</template>

<script>
// @ is an alias to /src
import MapTable from "@/components/MapTable.vue";

export default {
  name: "Home",
  components: {
    MapTable
  },
  data() {
    return {};
  }
};
</script>

<style scoped>


  .MapTable{
    margin: 100px;
    width: 800px;
  }
</style>