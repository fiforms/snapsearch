<template>
  <div class="container" tabindex="0" @keydown.esc="clearHit">
	  <div class="parametercontainer">
	    <div class="homelink">

	      <a href="https://snapshots.vrbm.org">
	      <img src="/arrow.png" />
	      Virtual Bible <br />Snapshot Project
	      </a>
	    </div>
	    <div class="searchbox">
	    <input v-model="searchterm" id="searchinput" placeholder="Search..." /><br />
	    
	    <label>
	      <input name="sortradio" type="radio" v-model="sort" value="path">
	      Folder Order
	    </label>

	    <label>
	      <input name="sortradio" type="radio" v-model="sort" value="date">
	      Newest First
	    </label>

	    <label>
	      <input name="sortradio" type="radio" v-model="sort" value="random" @click="reSort">
	      Random
	    </label>&nbsp;&nbsp;&nbsp;&nbsp;
	    <label>
	      <input name="showall" type="checkbox" v-model="showall"> Show All Hits
	    </label>
	    <label>
	      <input name="filterxx" type="checkbox" v-model="filterxx"> Filter Offensive Images
	    </label>
	    <label>
		    &nbsp;&nbsp;&nbsp;
		    <select name="typefilter" v-model="typefilter">
			    <option value="">{Filter by Type}</option>
			    <option v-for="type in typelist">{{type}}</option>
		    </select>
	    </label> &nbsp;
	    <a href="https://content.vrbm.org/browse/" style="color: #999999;">Browse Thumbnails</a>
	    </div>
	    <a href="https://snapshots.vrbm.org/"><img src="/searchicon.png" class="logo" /></a>
	  </div>
	  <div class="searchcontainer">
		  <div class="searchtile" v-for="row in searchresults" @click="setHit(row)">
			  <img class="searchthumb" v-bind:src="row.src + '/' + row.letter + '/' + row.md5 + '.webp'" @click="setHit(row)" />
		  </div>
	  </div>
          <div class="overlay" v-if="hit" @click="clearHit()">&nbsp;</div>
	  <div class="hitdetails" v-if="hit">
	      <div class="heading">Details
	      <span class="close" @click="clearHit()">X</span>
	      </div>
	      <div class="innerbody">
	                    <img v-bind:src="hit.src + '/' + hit.letter + '/' + hit.md5 + '.768.webp'" /><br />
			    <table>
				    <tr><th>Filename</th><td>{{hit.filename}}</td></tr>
				    <tr><td colspan="2">{{hit.desc}}</td></tr>
				    <tr><th>Type</th><td>{{hit.arttype}}</td></tr>
				    <tr><th>Resolution</th><td>{{hit.width}}x{{hit.height}}</td></tr>
				    <tr><th>License</th><td>{{hit.license}}</td></tr>
				    <tr><th>Attribution</th><td>{{hit.attribution}}</td></tr>
				    <tr><th>Folder</th><td>{{hit.dir}}</td></tr>
				    <tr><td colspan="2">
			    <a :href="hit.medurl" v-if="hit.medurl" target="_blank">Download Medium</a><br />
			    <a :href="hit.largeurl" target="_blank">Download Large</a><br />
			    <a v-if="hit.sourceurl" :href="hit.sourceurl" target="_blank">Image Source</a><br />
			    <a :href="hit.meddirlink" v-if="hit.meddirlink" target="_blank">Browse Folder (Medium Resolution)</a><br />
			    <a :href="hit.bigdirlink" target="_blank">Browse Folder (High Resolution)</a><br />
					    </td></tr></table>
	      </div>
	  </div>
		  

  </div>
</template>

<script>
  import axios from 'axios';
  
  export default {
    name: 'Snapshots',
    data() {
      return {
        snapshots: [],
	searchterm: "",
	searchresults: null,
	hit: null,
	sort: "default",
	showall: false,
	filterxx: true,
	typefilter: "",
	typelist: [],
      };
    },
    watch: {
	    searchterm(newSearchterm) {
		    this.updateSearch();
		    this.updateUrl();
	    },
	    sort(newSort) {
	            this.updateUrl();
	            this.reSort();
	    },
	    showall(newShow) {
	            this.updateSearch();
	    },
	    filterxx(newFilter) {
	            this.updateSearch();
	    },
	    typefilter(newType) {
		    this.updateSearch();
	            this.updateUrl();
	    },
    },
    methods: {
        match (terms,i) {
            let found = 0
	    if(this.snapshots[i].xx == "XX" && this.filterxx) {
		return 0;
	    }
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
	    if(this.searchterm.length >= 3 && this.sort == "default") {
		    this.sort = "path";
	    }
	    if(this.searchterm.length < 3 && this.showall) {
	        this.showall = false;
	        this.$forceUpdate();
	    }
	    if(this.snapshots.length > 0) {
		    for(let i = 0; i < this.snapshots.length; i++) {
			    let terms = this.searchterm.toLowerCase().split(" ")
			    if(this.match(terms,i)) {
				    let row=this.snapshots[i];
				    if(this.typefilter == "" || row.arttype == this.typefilter) {
				        row.letter = row.md5.substring(0,1);
				        res.push(row);
				        count++
				    }
			    }
			    if(count >= 100 && !this.showall) {
				break;
			    }
		    }
		    this.searchresults = res;
	    }
	},
	setHit(row) {
	    this.hit = row;
	},
	clearHit() {
	    this.hit = null;
	},
	reSort() {
	    this.snapshots.sort(this.sortCompare)
	    this.updateSearch()
	},
	sortCompare(a,b) {
	    if(this.sort == "path") {
	        if(a.dir == b.dir) {
	            return a.filename < b.filename ? -1 : 1
	        }
	        else {
	            return a.dir < b.dir ? -1 : 1
	        }
	    }
	    else if(this.sort == "date") {
	        return a.date > b.date ? -1 : 1
	    }
	    else {
	        let num = Math.random() - 0.5
	        return num
	    }
	},
        updateUrl() {
          // Update the URL with the current search parameter
          const baseUrl = window.location.href.split('#')[0];
          window.location.href = `${baseUrl}#q=${this.searchterm}&sort=${this.sort}`;
        },
        addArtType: function(newType) {
	    if(!this.typelist.includes(newType)) {
		    this.typelist.push(newType);
		    this.typelist.sort();
	    }
        },
    },
    created: function() {
        // Initialize search based on URL parameter
        const urlParams = new URLSearchParams(window.location.hash.slice(1));
        const searchFromUrl = urlParams.get('q');
        if (searchFromUrl) {
          this.searchterm = searchFromUrl;
        }
        const sortFromUrl = urlParams.get('sort');
        if (sortFromUrl) {
          this.sort = sortFromUrl;
        }
        axios
          .get('/thumbs/snapshots.json')
          .then(res => {
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "thumbs";
		    this.addArtType(res.data[i].arttype);
	    }
            this.snapshots = this.snapshots.concat(res.data);
            this.reSort();
            document.getElementById('searchinput').focus();
          })
        axios
          .get('/illustrations/snapshots.json')
          .then(res => {
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "illustrations";
		    this.addArtType(res.data[i].arttype);
	    }
            this.snapshots = this.snapshots.concat(res.data);
            this.reSort();
            document.getElementById('searchinput').focus();
          })
    },
  }
</script>

<style>
	.parametercontainer {
		display: block;
		background-color: #222255;
		width: 100%;
		min-height: 4em;
		color: #cccccc;
		padding: 0px;
	}
	.searchbox {
	        padding: 1em;
	        padding-right: 5em;
	}
	.searchcontainer
	{
		background-color: #ddddff;
		display: flex;
		flex-wrap: wrap;
		width: 100%;
		padding: 2em;
	}
	#searchinput {
	        width: 50vw;
	}
	.logo
	{
	       position: absolute;
	       top: 2px;
	       right: 2px;
	       width: 3.5em;
	}
	.searchtile
	{

		width: 276px;
		height: 276px;
		margin: 5px; /* Adjust as needed */
		box-sizing: border-box;
		cursor: pointer;
		background-color: #333333;
	}
	.searchtile:hover
	{
		background-color: #666666;
	}
	.searchtile img
	{
		position: relative;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	.hitdetails {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		padding: 0px;
		background-color: #fff;
		border: 1px solid #ccc;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		z-index: 1000;
		max-width: 90vw;
	}
	.hitdetails .heading {
		width: 100%;
		background-color: #000000;
		height: 2em;
		color: #ffffff;
		font-weight: bold;
		padding: 0.5em;
	}
	.hitdetails .innerbody {
		max-height: 90vh; /* Set the maximum height as a percentage of the viewport height */
		overflow-y: auto; /* Enable vertical scrollbar when content exceeds the max height */
		overflow-x: clip;
		max-width: 60vh;

	}
	.hitdetails .innerbody img {
	        width: 100%;
		max-width: 100%;
	}
	
	/* Add a close button */
	.close {
		position: absolute;
		top: 5px;
		right: 5px;
		cursor: pointer;
		font-weight: bold;
		color: #999999;
	}
	
	.overlay {
		display: block;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5); /* Semi-transparent grey background */
		z-index: 999; /* Z-index lower than modal to ensure it's behind */
	}
	.homelink {
	    float: left;
	    width: 15em;
	    padding-right: 15px;
	    padding: 6px;
	    font-size: 9.5pt;
	}
	.homelink a {
	    color: #cccccc;
	    text-decoration: none;
	}
	.homelink img {
	    width: 2em;
	    float: left;
	    margin-right: 1em;
	}
</style>
