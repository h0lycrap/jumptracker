<template>
  <v-card dark>
    <v-card-title class="d-flex align-end">
      <h2>Map List</h2>
      <v-spacer></v-spacer>
      <v-text-field
        v-model="search"
        label="Search"
        single-line
        hide-details
        append-icon="mdi-magnify"
      ></v-text-field>
    </v-card-title>
    <v-data-table 
      :items="filteredMap" 
      :headers="headers" 
      :search="search" 
      :loading="mapsLoading" 
      loading-text="Loading... Please wait"
      :footer-props="{
        'items-per-page-options': [10, 20, 30, 40, 50]
      }"
      :items-per-page="20"
      :page.sync="page"
      hide-default-footer
      @page-count="pageCount = $event"
      :single-expand="singleExpand"
      :expanded.sync="expanded"
      :show-expand="false"
      @click:row="rowClicked"
      dark>
      <template v-slot:item.difficulty="{ item }">
        <div v-if="item.difficulty === 0"> -
        </div>
        <div v-else>
          <v-chip :color="getColor(item.difficulty)" @click="setDifficultyFilter(item.difficulty)" dark>{{ item.difficulty }}</v-chip>
        </div>
      </template>
      <template v-slot:item.status="{ item }">
        <v-fab-transition hide-on-leave>
          <v-progress-circular :value="getJumpProgress(item)" color="white" rotate="270" v-if="getJumpProgress(item) != 100 && getJumpProgress(item) != 0"></v-progress-circular>
        </v-fab-transition>
        <v-scale-transition hide-on-leave>
              <v-icon v-if="getJumpProgress(item) == 100" color="success">mdi-check-bold</v-icon>
        </v-scale-transition>
      </template>
      <template v-slot:item.name="{ item }">
        <div class="font-weight-bold">{{item.name}}</div>
        <v-btn x-small rounded v-if="item.multipath" color="#b134eb" class="black--text" @click="setFilter(1)">multipath</v-btn>
        <v-btn x-small rounded v-if="item.icy" color="#90cffc" class="black--text" @click="setFilter(2)">icy</v-btn>
        <v-btn x-small rounded v-if="item.team" color="#f7f74d" class="black--text" @click="setFilter(3)">team</v-btn>
        <v-btn x-small rounded v-if="item.deathrun" color="#6ff74d" class="black--text" @click="setFilter(4)">deathrun</v-btn>
        <v-btn x-small rounded v-if="item.stamina" color="#969696" class="black--text" @click="setFilter(5)">inf. stamina</v-btn>
        <v-btn x-small rounded v-if="item.walljump" color="#000000" @click="setFilter(6)">inf. walljump</v-btn>
      </template>
      <template v-slot:expanded-item="{ headers, item }">
        <td :colspan="headers.length" class="table_expansion_container" padding="0px">
          <div class="d-flex align-start table_expansion">
            <v-img :src="require('@/assets/'+item.thumbnail)" :lazy-src="require('@/assets/no_image.jpg')" max-width="200px" height="150px" class="thumbnail">
              <template v-slot:placeholder>
                <v-row
                  class="fill-height ma-0"
                  align="center"
                  justify="center"
                >
                  <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                </v-row>
              </template>
            </v-img>
            <div class="d-flex flex-column">
              <h4>Jumps:</h4>
              <div class="d-flex flex-wrap align-start">
                <div v-for="n in jump.totalJumps" >
                  <v-btn class="mx-2 jumpbutton" fab dark small :color="isJumpCompleted(item, n) ? 'green' : '#272727' " @click="completeJump(item, n)">
                    {{n}}
                  </v-btn>
                </div>
              </div>
              <h4 v-if="jump.leets.length">L33ts:</h4>
              <div class="d-flex flex-wrap align-start">
                <div v-for="n in jump.leets">
                  <v-btn class="mx-2 jumpbutton" fab dark small :color="isLeetCompleted(item, n) ? 'orange' : '#272727' " @click="completeLeet(item, n)">
                    {{n}}
                  </v-btn>
                </div>
              </div>
              <h4 v-if="jump.secrets">Secrets:</h4>
              <div class="d-flex flex-wrap align-start">
                <div v-for="n in jump.secrets">
                  <v-btn class="mx-2 jumpbutton" fab dark small :color="isSecretCompleted(item, n) ? 'pink' : '#272727' " @click="completeSecret(item, n)">
                    {{n}}
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
        </td>
      </template>
    </v-data-table>
    <v-pagination v-model="page" :length="pageCount"></v-pagination>
  </v-card>
</template>

<script>
export default {
  name: "MapTable",

  methods: {
    async read() {
      window.axios.get('/api/maps').then(resp => {this.maps = resp.data; this.mapsLoading = false; this.getUserProgress();});
    },

    getColor (difficulty) {
      if (difficulty >= 90) return 'black'
      else if (difficulty >= 70) return 'red'
      else if (difficulty >= 50) return 'orange'
      else if (difficulty >= 30) return 'green'
      else return 'blue'
    },

    parseJump (jumps){
      jumps = jumps.replace(/\'/g, "\"")
      var jsonJump = JSON.parse(jumps)
      return jsonJump
    },

    rowClicked(value) {
      var jsonJump = this.parseJump(value.jumps)
      //this.isExpanded = !this.isExpanded
      if (!(this.jump.displayed==value.id)){
        this.jump.displayed = value.id
        this.jump.totalJumps = jsonJump.jumps
        this.jump.leets = jsonJump.leets
        this.jump.secrets = jsonJump.secrets
        this.expanded = [value]
      }
      else{
        this.jump.displayed = 0
        this.expanded = []
      } 
    },

    completeJump(jump, number){
      if(this.loggedIn){
        if (this.isJumpCompleted(jump, number)){
          for(let i = 0; i<this.jumpsCompleted[jump.id].jumps.length; i++){
            if(i>=number-1){
              if(this.isJumpCompleted(jump, i+1)){
                this.jumpsCompleted[jump.id].jumps[i] = false
                this.jumpsCompleted[jump.id].jumpsCompleted--
              }
            }
          }
        }
        else{
          for(let i = 0; i<this.jumpsCompleted[jump.id].jumps.length; i++){
            if(i<=number-1){
              if(!this.isJumpCompleted(jump, i+1)){
                this.jumpsCompleted[jump.id].jumps[i] = true
                this.jumpsCompleted[jump.id].jumpsCompleted++
              }
            }
          }
        }
        jump.jumps = jump.jumps.replace(/\'/g, "\"")
        var jsonJump = JSON.parse(jump.jumps)
        this.jumpsCompleted[jump.id].progress = parseInt(100*this.jumpsCompleted[jump.id].jumpsCompleted/jsonJump.jumps)
        jump.status = this.jumpsCompleted[jump.id].progress

        this.updateProgress(jump.id)
        this.$forceUpdate()

        
      }
      else{
        this.$router.push({name: 'login'})
      }

    },

    isJumpCompleted(jump, number){
      return this.jumpsCompleted[jump.id].jumps[number-1]
    },


    completeLeet(jump, number){
      if(this.loggedIn){
      
        if (this.jumpsCompleted[jump.id].leets[number-1]){
          this.jumpsCompleted[jump.id].leets[number-1] = false
          this.jumpsCompleted[jump.id].leetsCompleted--
        }
        else{
          this.jumpsCompleted[jump.id].leets[number-1] = true
          this.jumpsCompleted[jump.id].leetsCompleted++
        }
        this.updateProgress(jump.id)
        this.$forceUpdate()
        
      }
      else{
        this.$router.push({name: 'login'})
      }
    },

    isLeetCompleted(jump, number){
      return this.jumpsCompleted[jump.id].leets[number-1]
    },

    completeSecret(jump, number){
      if(this.loggedIn){
      
        if (this.jumpsCompleted[jump.id].secrets[number-1]){
          this.jumpsCompleted[jump.id].secrets[number-1] = false
          this.jumpsCompleted[jump.id].secretsCompleted--
        }
        else{
          this.jumpsCompleted[jump.id].secrets[number-1] = true
          this.jumpsCompleted[jump.id].secretsCompleted++
        }

        this.updateProgress(jump.id)
        this.$forceUpdate()
      }
      else{
        this.$router.push({name: 'login'})
      }
    },

    isSecretCompleted(jump, number){
      return this.jumpsCompleted[jump.id].secrets[number-1]
    },

    getJumpProgress(jump){
      return this.jumpsCompleted[jump.id].progress
    },

    getUserProgress(){
      if (!this.jumpsCompleted.length>0){
        for(let i = 0; i < this.maps.length; i++){
          this.maps[i].jumps = this.maps[i].jumps.replace(/\'/g, "\"")
          var jsonJumps = JSON.parse(this.maps[i].jumps)
          var jumpArray = []
          var leetArray = []
          var secretArray = []
          for(let i = 0; i < jsonJumps.jumps; i++){
            jumpArray.push(false)
          }
          for(let i = 0; i < jsonJumps.leets.length; i++){
            leetArray.push(false)
          }
          for(let i = 0; i < jsonJumps.secrets; i++){
            secretArray.push(false)
          }
          this.jumpsCompleted[this.maps[i].id]={
            name: this.maps[i].id,
            jumps: jumpArray,
            jumpsCompleted: 0,
            leets: leetArray,
            leetsCompleted: 0,
            secrets: secretArray,
            secretsCompleted: 0,
            progress: 0,
          }
        }
      }
    },

    setFilter(filter){
      if (this.activeFilter != filter){
        this.activeFilter = filter
      }
      else{
        this.activeFilter = 0
      }
    },

    setDifficultyFilter(difficulty){
      var filter 
      if (difficulty >= 90) filter = 7
      else if (difficulty >= 70) filter = 8
      else if (difficulty >= 50) filter = 9
      else if (difficulty >= 30) filter = 10
      else filter = 11
      if (this.activeFilter != filter){
        this.activeFilter = filter
      }
      else{
        this.activeFilter = 0
      }
    },

    filterJumps (item) {
      if(this.activeFilter == 1){
        return item.multipath == 1
      }
      else if(this.activeFilter == 2){
        return item.icy == 1
      }
      else if(this.activeFilter == 3){
        return item.team == 1
      }
      else if(this.activeFilter == 4){
        return item.deathrun == 1
      }
      else if(this.activeFilter == 5){
        return item.stamina == 1
      }
      else if(this.activeFilter == 6){
        return item.walljump == 1
      }
      else if(this.activeFilter == 7){
        return item.difficulty >= 90
      }
      else if(this.activeFilter == 8){
        return item.difficulty >= 70 && item.difficulty < 90
      }
      else if(this.activeFilter == 9){
        return item.difficulty >= 50 && item.difficulty < 70
      }
      else if(this.activeFilter == 10){
        return item.difficulty >= 30 && item.difficulty < 50
      }
      else if(this.activeFilter == 11){
        return item.difficulty > 0 && item.difficulty < 30
      }
      else{
        return true
      }
    },

    updateProgress(mapid) {
        var data = {progress: this.jumpsCompleted[mapid], id: this.$store.state.auth.user.id, mapid: 'map_'+mapid.toString()}
        console.log(data)
        this.$store.dispatch('auth/updateProgress', data)
    },

    sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },

    async setUserProgress(){
      await this.sleep(2000);
      if(this.loggedIn){
        for (let i=1 ; i<=this.maps.length ; i++){
          if (this.$store.state.auth.user['map_'+i.toString()]){
            if(typeof this.$store.state.auth.user['map_'+i.toString()] == 'object'){
              console.log(this.$store.state.auth.user['map_'+i.toString()])
              this.jumpsCompleted[i] = this.$store.state.auth.user['map_'+i.toString()]
            }
            else{ 
              this.jumpsCompleted[i] = JSON.parse(this.$store.state.auth.user['map_'+i.toString()])
            }
          }
          else{
            console.log('coucouc')
          }
        }
        console.log(this.jumpsCompleted)
      }
      this.$forceUpdate()
    },
  },

  computed: {
    filteredMap(){
      return this.maps.filter((item) => {
        return this.filterJumps(item)
      })
    },

    loggedIn(){
      return this.$store.state.auth.status.loggedIn
    }
  },

  created(){
    this.read()
    this.setUserProgress()
  },

 

  data() {
    return {
      maps: [],
      mapsLoading: true,
      page: 1,
      pageCount: 0,
      singleExpand: true,
      expanded: [],
      //isExpanded: false, 
      search: "",
      jump: {
        displayed: 0,
        totalJumps: 0,
        leets: [],
        secrets: 0,
      },
      jumpsCompleted: [],
      buttonClicked: 0,
      activeFilter: 0,
      headers: [
        {
          text: "Map Name",
          align: "start",
          sortable: true,
          value: "name"
        },
        {
          text: "Mapper",
          align: "start",
          sortable: true,
          value: "author"
        },
        {
          text: "Difficulty",
          align: "center",
          sortable: true,
          value: "difficulty"
        },
        { 
          text: "Progress", 
          align: "center",
          sortable: true,
          value: "status" 
        }
      ]
    }
  }
}
</script>

<style scoped>
  .table_expansion_container{
    padding:0px !important;
  }
  .table_expansion{
    background-color: #545454;
    padding:15px;
  }
  .thumbnail{
    margin-right:15px;
  }

  .jumpbutton{
    margin-bottom: 8px;
  }
</style>
