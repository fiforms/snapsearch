<template>
  <div class="container">
	  <h3> Snapshot Search: <input v-model="searchterm" /></h3>
	  <div class="searchcontainer">
		  <div class="searchtile" v-for="row in searchresults">
			  <img class="searchthumb" v-bind:src="'thumbs/' + row.letter + '/' + row.md5 + '.webp'" @click="setHit(row)" />
		  </div>
	  </div>
	  <div class="hitdetails" v-if="hit">
	      <span class="close" @click="clearHit()">X</span>
	                    <img v-bind:src="'thumbs/' + hit.letter + '/' + hit.md5 + '.768.webp'" /><br />
			    {{hit.filename}}<br />
			    {{hit.desc}}<br />
			    {{hit.dir}}<br />
			    <a :href="hit.medlink" target="_blank">Download Medium</a><br />
			    <a :href="hit.biglink" target="_blank">Download Large</a><br />
			    <a v-if="hit.sourceurl" :href="hit.sourceurl" target="_blank">Image Source</a><br />
			    <a :href="hit.meddirlink" target="_blank">Browse Folder (Medium Resolution)</a><br />
			    <a :href="hit.bigdirlink" target="_blank">Browse Folder (High Resolution)</a><br />
	  </div>
		  

  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'Snapshots',
    data() {
      return {
        snapshots: null,
	searchterm: "",
	searchresults: null,
	hit: null,
      };
    },
    watch: {
	    searchterm(searchterm) {
		    this.updateSearch();
	    }
    },
    methods: {
        match (terms,i) {
            let found = 0
            for(let j = 0; j < terms.length; j++) {
                let term = terms[j]
                if((this.snapshots[i].dir && this.snapshots[i].dir.toLowerCase().includes(term)) || 
                    (this.snapshots[i].filename && this.snapshots[i].filename.toLowerCase().includes(term)) ||
                    (this.snapshots[i].desc && this.snapshots[i].desc.toLowerCase().includes(term))) {
                    found++;
                }
            }
            return (found == terms.length)
        },
        updateSearch (event) {
	    var res = new Array();
	    var count = 0
	    for(let i = 0; i < this.snapshots.length; i++) {
		    let terms = this.searchterm.toLowerCase().split(" ")
		    if(this.match(terms,i)) {
			    let row=this.snapshots[i];
			    row.letter = row.md5.substring(0,1);
			    res.push(row);
			    count++
		    }
		    if(count >= 100) {
			break;
		    }
	    }
		this.searchresults = res;
	   
	},
	setHit(row) {
	    this.hit = row;
	    		    this.hit.medlink = "https://cloud.fiforms.org/dan_cloud/index.php/s/tSkY7gainasSTpD/download?path=" +
			        encodeURIComponent("/" + row.dir) +
			        "&files=" +
			        encodeURIComponent(row.filename.replace(/\.png$/,".jpg").replace(/\.webp$/,".jpg"));
			    this.hit.biglink = "https://cloud.fiforms.org/dan_cloud/index.php/s/TrjZLnLdf7QHcBY/download?path=" +
			        encodeURIComponent("/" + row.dir) +
			        "&files=" +
			        encodeURIComponent(row.filename);
			    this.hit.meddirlink = "https://cloud.fiforms.org/dan_cloud/index.php/s/tSkY7gainasSTpD?path=" + 
			        encodeURIComponent("/" + row.dir)
			    this.hit.bigdirlink = "https://cloud.fiforms.org/dan_cloud/index.php/s/TrjZLnLdf7QHcBY?path=" + 
			        encodeURIComponent("/" + row.dir)
	},
	clearHit() {
	    this.hit = null;
	},
    },
    created: function() {
      axios
        .get('/thumbs/snapshots.json')
        .then(res => {
          this.snapshots = res.data;
        })
    }
  }
</script>

<style>
  h3 {
    margin-bottom: 5%;
  }
  .searchcontainer
  {
      background-color: #ddddff;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between; /* Adjust as needed */
      align-content: space-between; /* Adjust as needed */
      width: 900px;
  }
  .searchtile
  {
      width: 276px;
      height: 276px;
      margin: 5px; /* Adjust as needed */
      box-sizing: border-box;
  }
    .hitdetails {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }
    /* Add a close button if needed */
    .close {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
    }
</style>
