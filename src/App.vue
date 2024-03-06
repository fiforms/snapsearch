<template>
  <div id="app">
    <Snapshots />
  </div>
</template>

<script>
import Snapshots from './components/Snapshots.vue'

export default {
  name: 'app',
  components: {
    Snapshots
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
