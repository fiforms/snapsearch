<template>
  <div class="container" tabindex="0" @keydown.esc="clearHit" @click="closeSettings">
	  <div class="parametercontainer">
	    <div class="homelink">

	      <a href="https://snapshots.vrbm.org">
	      <img src="/arrow.png" />
	      Virtual Bible <br />Snapshot Project
	      </a>
	    </div>
	    <div class="searchbox">
	    <input v-model="searchterm" id="searchinput" placeholder="Search..." /><br />
	    <label class="typefilter">
		    <select name="typefilter" v-model="typefilter">
			    <option value="">{Filter by Type}</option>
			    <option v-for="type in typelist">{{type}}</option>
		    </select>
	    </label>
	    <div class="settingswrap" @click.stop>
		    <button class="settingsbutton" type="button" @click="toggleSettings" aria-haspopup="true" :aria-expanded="showSettings">
			    <svg viewBox="0 0 24 24" aria-hidden="true">
				    <path d="M19.14 12.94a7.97 7.97 0 0 0 .05-.94 7.97 7.97 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.6 7.6 0 0 0-1.63-.94l-.36-2.54a.5.5 0 0 0-.5-.42h-3.84a.5.5 0 0 0-.5.42l-.36 2.54c-.58.23-1.12.53-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.7 8.84a.5.5 0 0 0 .12.64l2.03 1.58a7.97 7.97 0 0 0-.05.94c0 .32.02.63.05.94L2.82 14.52a.5.5 0 0 0-.12.64l1.92 3.32a.5.5 0 0 0 .6.22l2.39-.96c.51.41 1.05.72 1.63.94l.36 2.54a.5.5 0 0 0 .5.42h3.84a.5.5 0 0 0 .5-.42l.36-2.54c.58-.23 1.12-.53 1.63-.94l2.39.96a.5.5 0 0 0 .6-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.58ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
			    </svg>
		    </button>
		    <div class="settingsmenu" v-if="showSettings">
			    <div class="settingsgroup">
				    <span class="settingstitle">Sort</span>
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
				    </label>
			    </div>
			    <div class="settingsgroup">
				    <span class="settingstitle">Filters</span>
				    <label>
				      <input name="showall" type="checkbox" v-model="showall"> Show All Hits
				    </label>
				    <label>
				      <input name="filterxx" type="checkbox" v-model="filterxx"> Filter Offensive Images
				    </label>
			    </div>
			    <a class="browse-link" href="https://content.vrbm.org/browse/">Browse Thumbnails</a>
		    </div>
	    </div>
	    </div>
	    <a href="https://snapshots.vrbm.org/"><img src="/searchicon.png" class="logo" /></a>
	  </div>
	  <div class="searchcontainer">
		  <div class="searchtile video-thumb" v-for="row in searchresults" @click="setHit(row)">
		      <a :title="row.filename + ' ' + row.desc">
			  <img class="searchthumb" v-bind:src="row.src + '/' + row.letter + '/' + row.md5 + '.webp'" @click="setHit(row)" />
		          <div v-if="row.ftype == 'video'" class="videoicon-overlay">▶</div>
		      </a>
		  </div>
	  </div>
          <div class="overlay" v-if="hit" @click="clearHit()">&nbsp;</div>
	  <div class="hitdetails" v-if="hit">
	      <button class="close lightbox-close" type="button" @click="clearHit()">X</button>
	      <div class="lightboxmedia">
		      <div v-if="!isPlaying" class="video-thumb" @click="playVideo">
	                  <img v-bind:src="hit.src + '/' + hit.letter + '/' + hit.md5 + '.768.webp'" />
			  <div v-if="hit.ftype == 'video'" class="play-overlay" @click="playVideo">▶</div>
		      </div>
		      <video v-if="isPlaying" v-bind:src="hit.medurl" controls autoplay class="video-player"></video>
	      </div>
	      <div class="innerbody">
			    <table class="mediainfo">
				<thead><tr><td colspan="2">
					<a :href="hit.medurl" v-if="hit.medurl" target="_blank"><button>Download Medium</button></a>&nbsp;
					<a v-if="hit.largeurl" :href="hit.largeurl" target="_blank"><button>Download Large</button></a>&nbsp;
					<a v-if="hit.sourceurl" :href="hit.sourceurl" target="_blank"><button>Source</button></a>&nbsp;
					<a :href="hit.meddirlink" v-if="hit.meddirlink" target="_blank"><button>Browse <span v-if="hit.bigdirlink">(Medium)</span></button></a>&nbsp;
					<a v-if="hit.bigdirlink" :href="hit.bigdirlink" target="_blank"><button>Browse (High Res)</button></a>
				</td></tr>
				</thead>
				<tbody>
				    <tr><th>Filename</th><td class="data">{{hit.filename}}</td></tr>
				    <tr><td colspan="2" class="twocolumn">{{hit.desc}}</td></tr>
				    <tr><th>Type</th><td class="data">{{hit.arttype}}</td></tr>
				    <tr><th>Resolution</th><td class="data">{{hit.width}}x{{hit.height}}</td></tr>
				    <tr><th>License</th><td class="data">{{hit.license}}</td></tr>
				    <tr><th>Attribution</th><td class="data">{{hit.attribution}}</td></tr>
				    <tr><th>Folder</th><td class="data">{{hit.dir}}</td></tr>
				    </tbody></table>
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
	isPlaying: false,
	showSettings: false,
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
	playVideo() {
	    this.isPlaying = true;
	},
	clearHit() {
	    this.hit = null;
	    this.isPlaying = false;
	},
	toggleSettings() {
	    this.showSettings = !this.showSettings;
	},
	closeSettings() {
	    if(this.showSettings) {
		    this.showSettings = false;
	    }
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
          .get('/videos/snapshots.json')
          .then(res => {
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "videos";
		    this.addArtType(res.data[i].arttype);
	    }
            this.snapshots = this.snapshots.concat(res.data);
            this.reSort();
            document.getElementById('searchinput').focus();
          })
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
	.videoicon-overlay {
	  position: absolute;
	  top: 50%;
	  left: 50%;
	  transform: translate(-50%, -50%);
	  font-size: 40px;
	  color: white;
	  text-shadow: 0 0 10px black;
	  opacity: 0.5;
	}
	.play-overlay {
	  position: absolute;
	  top: 50%;
	  left: 50%;
	  transform: translate(-50%, -50%);
	  font-size: 56px;
	  color: white;
	  text-shadow: 0 0 10px black;
	  pointer-events: none;
	  cursor: pointer;
	}
	.video-player {
	  max-width: 100%;
	  height: auto;
	}
	.video-thumb {
	  position: relative;
	  display: inline-block;
	}
	.video-thumb img {
	  display: block;
	}
</style>
