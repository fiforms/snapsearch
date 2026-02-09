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
	    <div class="top-toolbar">
		    <input v-model="searchterm" id="searchinput" placeholder="Search..." />
		    <button class="mobile-menu-button" type="button" @click.stop="toggleMobileFilters" aria-haspopup="true" aria-controls="mobilefilters" :aria-expanded="showMobileFilters">
			    <span class="mobile-menu-icon" aria-hidden="true"></span>
			    Filters
		    </button>
		    <div class="filter-row" id="mobilefilters" :class="{ 'mobile-open': showMobileFilters }" @click.stop>
			    <label class="typefilter">
				    <select name="typefilter" v-model="typefilter">
					    <option value="">{Filter by Type}</option>
					    <option v-for="type in typelist">{{type}}</option>
				    </select>
			    </label>
			    <label class="collectionfilter">
				    <select name="collectionfilter" v-model="collectionfilter">
					    <option value="">{Collection}</option>
					    <option v-for="collection in collections" :value="collection.id">{{collection.label}}</option>
				    </select>
			    </label>
		    </div>
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
			    <a href="https://snapshots.vrbm.org/" class="mobile-logo-link"><img src="/searchicon.png" class="logo mobile-logo" /></a>
		    </div>
		    <div class="folder-toolbar" v-if="collectionfilter" @click.stop>
		    <div class="folder-path">
			    <button class="folder-button" type="button" v-if="collectionPath.length" @click="goToParentFolder">Up</button>
			    <span class="folder-current" v-if="collectionPath.length">{{collectionPath.join(' / ')}}</span>
			    <span class="folder-current" v-else>All Folders</span>
		    </div>
		    <div class="folder-list">
			    <button class="folder-button" type="button" v-for="folder in currentFolderOptions" :key="folder" @click="enterFolder(folder)">{{folder}}</button>
		    </div>
	    </div>
	    </div>
	    <a href="https://snapshots.vrbm.org/" class="desktop-logo-link"><img src="/searchicon.png" class="logo" /></a>
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
	      <div class="lightbox-nav">
		      <button class="nav-button" type="button" @click.stop="showPrevHit" aria-label="Previous result">
			      <span aria-hidden="true">&lt;</span>
		      </button>
		      <button class="nav-button" type="button" @click.stop="showNextHit" aria-label="Next result">
			      <span aria-hidden="true">&gt;</span>
		      </button>
	      </div>
	      <div class="lightboxmedia">
		      <div v-show="!isVideoReady" class="video-thumb" @click="maybePlayVideo">
	                  <img v-bind:src="hit.src + '/' + hit.letter + '/' + hit.md5 + '.768.webp'" />
			  <div v-if="hit.ftype == 'video'" class="play-overlay" @click.stop="playVideo">▶</div>
		      </div>
		      <video v-if="hit.ftype == 'video' && isPlaying" v-show="isVideoReady" v-bind:src="hit.medurl" controls autoplay class="video-player" @loadeddata="onVideoLoaded"></video>
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
	sort: "date",
	autoSort: true,
	showall: false,
	filterxx: true,
	typefilter: "",
	collectionfilter: "",
	collectionPath: [],
	collections: [
		{ id: "videos", label: "Video Collection" },
		{ id: "thumbs", label: "Main Collection" },
		{ id: "illustrations", label: "Illustration Collection" },
	],
	collectionTree: {},
	typelist: [],
	isPlaying: false,
	isVideoReady: false,
	showSettings: false,
	showMobileFilters: false,
	handleKeydown: null,
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
	    collectionfilter(newCollection) {
		    this.collectionPath = [];
		    this.updateSearch();
		    this.updateUrl();
	    },
    },
	methods: {
	getRowPath(row) {
		return row.dir || "";
	},
	getPathSegments(path) {
		if(!path) {
			return [];
		}
		return path.split("/").filter(segment => segment.length > 0);
	},
	addToCollectionTree(collectionId, path) {
		if(!collectionId) {
			return;
		}
		if(!this.collectionTree[collectionId]) {
			this.collectionTree[collectionId] = { children: {} };
		}
		const segments = this.getPathSegments(path);
		if(segments.length === 0) {
			return;
		}
		let node = this.collectionTree[collectionId];
		for(let i = 0; i < segments.length; i++) {
			const segment = segments[i];
			if(!node.children[segment]) {
				node.children[segment] = { children: {} };
			}
			node = node.children[segment];
		}
	},
	getCurrentFolderNode() {
		if(!this.collectionfilter || !this.collectionTree[this.collectionfilter]) {
			return null;
		}
		let node = this.collectionTree[this.collectionfilter];
		for(let i = 0; i < this.collectionPath.length; i++) {
			const segment = this.collectionPath[i];
			if(!node.children || !node.children[segment]) {
				return null;
			}
			node = node.children[segment];
		}
		return node;
	},
	matchesCollectionFilter(row) {
		if(!this.collectionfilter) {
			return true;
		}
		if(row.src !== this.collectionfilter) {
			return false;
		}
		if(this.collectionPath.length === 0) {
			return true;
		}
		const segments = this.getPathSegments(this.getRowPath(row));
		if(segments.length < this.collectionPath.length) {
			return false;
		}
		for(let i = 0; i < this.collectionPath.length; i++) {
			if(segments[i] !== this.collectionPath[i]) {
				return false;
			}
		}
		return true;
	},
	enterFolder(folder) {
		this.collectionPath.push(folder);
		this.updateSearch();
		this.updateUrl();
	},
	goToParentFolder() {
		if(this.collectionPath.length === 0) {
			return;
		}
		this.collectionPath.pop();
		this.updateSearch();
		this.updateUrl();
	},
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
	    if(this.searchterm.length < 3 && this.showall) {
	        this.showall = false;
	        this.$forceUpdate();
	    }
	    if(this.snapshots.length > 0) {
		    for(let i = 0; i < this.snapshots.length; i++) {
			    let terms = this.searchterm.toLowerCase().split(" ")
			    if(this.match(terms,i)) {
				    let row=this.snapshots[i];
				    if((this.typefilter == "" || row.arttype == this.typefilter) && this.matchesCollectionFilter(row)) {
				        row.letter = row.md5.substring(0,1);
				        res.push(row);
				        count++
				    }
			    }
		    }
			res.sort(this.sortCompare);
			if(res.length <= 100 || this.showall) {
				this.searchresults = res;
				return 0;
			}
			let trimmed = new Array();
			for(let i = 0; i < 100; i++) {
				trimmed.push(res[i]);
			}
		    this.searchresults = trimmed;
	    }
	},
	setHit(row) {
	    this.hit = row;
	    this.isPlaying = false;
	    this.isVideoReady = false;
	},
	getHitIndex() {
	    if(!this.hit || !this.searchresults || this.searchresults.length === 0) {
		    return -1;
	    }
	    let directIndex = this.searchresults.indexOf(this.hit);
	    if(directIndex >= 0) {
		    return directIndex;
	    }
	    return this.searchresults.findIndex(row => row.md5 === this.hit.md5 && row.src === this.hit.src);
	},
	showPrevHit() {
	    if(!this.hit || !this.searchresults || this.searchresults.length === 0) {
		    return;
	    }
	    let index = this.getHitIndex();
	    if(index === -1) {
		    return;
	    }
	    let prevIndex = index - 1;
	    if(prevIndex < 0) {
		    prevIndex = this.searchresults.length - 1;
	    }
	    this.setHit(this.searchresults[prevIndex]);
	},
	showNextHit() {
	    if(!this.hit || !this.searchresults || this.searchresults.length === 0) {
		    return;
	    }
	    let index = this.getHitIndex();
	    if(index === -1) {
		    return;
	    }
	    let nextIndex = index + 1;
	    if(nextIndex >= this.searchresults.length) {
		    nextIndex = 0;
	    }
	    this.setHit(this.searchresults[nextIndex]);
	},
	maybePlayVideo() {
	    if(this.hit && this.hit.ftype == "video") {
		    this.playVideo();
	    }
	},
	playVideo() {
	    if(!this.hit || this.hit.ftype != "video") {
		    return;
	    }
	    this.isVideoReady = false;
	    this.isPlaying = true;
	},
	onVideoLoaded() {
	    this.isVideoReady = true;
	},
	clearHit() {
	    this.hit = null;
	    this.isPlaying = false;
	    this.isVideoReady = false;
	},
	toggleSettings() {
	    if(!this.showSettings) {
		    this.showMobileFilters = false;
	    }
	    this.showSettings = !this.showSettings;
	},
	toggleMobileFilters() {
	    if(!this.showMobileFilters) {
		    this.showSettings = false;
	    }
	    this.showMobileFilters = !this.showMobileFilters;
	},
	closeSettings() {
	    if(this.showSettings) {
		    this.showSettings = false;
	    }
	    if(this.showMobileFilters) {
		    this.showMobileFilters = false;
	    }
	},
	reSort() {
		if(this.searchresults && this.searchresults.length > 0) {
		    this.searchresults.sort(this.sortCompare)
		}
	},
	sortCompare(a,b) {
	    if(this.sort == "path") {
	        const dirA = (a.dir || "");
	        const dirB = (b.dir || "");
	        if(dirA !== dirB) {
	            return dirA < dirB ? -1 : 1;
	        }
	        const fileA = (a.filename || "");
	        const fileB = (b.filename || "");
	        if(fileA === fileB) {
	            return 0;
	        }
	        return fileA < fileB ? -1 : 1;
	    }
	    else if(this.sort == "date") {
	        const dateA = (a.date || "");
	        const dateB = (b.date || "");
	        if(dateA === dateB) {
	            return 0;
	        }
	        if(!dateA) {
	            return 1;
	        }
	        if(!dateB) {
	            return -1;
	        }
	        return dateA > dateB ? -1 : 1;
	    }
	    else {
	        let num = Math.random() - 0.5
	        return num
	    }
	},
        updateUrl() {
          // Update the URL with the current search parameter
          const baseUrl = window.location.href.split('#')[0];
          const params = new URLSearchParams();
          if(this.searchterm) {
            params.set("q", this.searchterm);
          }
          params.set("sort", this.sort);
          if(this.typefilter) {
            params.set("type", this.typefilter);
          }
          if(this.collectionfilter) {
            params.set("collection", this.collectionfilter);
          }
          if(this.collectionPath.length > 0) {
            params.set("folder", this.collectionPath.join("/"));
          }
          window.location.href = `${baseUrl}#${params.toString()}`;
        },
        addArtType: function(newType) {
	    if(!this.typelist.includes(newType)) {
		    this.typelist.push(newType);
		    this.typelist.sort();
	    }
        },
    },
    computed: {
	    currentFolderOptions() {
		    const node = this.getCurrentFolderNode();
		    if(!node || !node.children) {
			    return [];
		    }
		    return Object.keys(node.children).sort((a,b) => a.localeCompare(b));
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
	  this.autoSort = false;
        }
	const typeFromUrl = urlParams.get('type');
	if(typeFromUrl) {
	  this.typefilter = typeFromUrl;
	}
	const collectionFromUrl = urlParams.get('collection');
	if(collectionFromUrl) {
	  this.collectionfilter = collectionFromUrl;
	}
	const folderFromUrl = urlParams.get('folder');
	if(folderFromUrl) {
	  this.collectionPath = this.getPathSegments(folderFromUrl);
	}
        axios
          .get('/videos/snapshots.json')
          .then(res => {
			if(!res || !res.data || !Array.isArray(res.data) || res.data.length < 1) {
				return 1;
			}
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "videos";
		    this.addArtType(res.data[i].arttype);
		    this.addToCollectionTree(res.data[i].src, this.getRowPath(res.data[i]));
	    }
            this.snapshots = this.snapshots.concat(res.data);
			this.updateSearch();
            document.getElementById('searchinput').focus();
          })
        axios
          .get('/thumbs/snapshots.json')
          .then(res => {
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "thumbs";
		    this.addArtType(res.data[i].arttype);
		    this.addToCollectionTree(res.data[i].src, this.getRowPath(res.data[i]));
	    }
            this.snapshots = this.snapshots.concat(res.data);
			this.updateSearch();
            document.getElementById('searchinput').focus();
          })
        axios
          .get('/illustrations/snapshots.json')
          .then(res => {
            for(let i = 0; i < res.data.length; i++) {
		    res.data[i].src = "illustrations";
		    this.addArtType(res.data[i].arttype);
		    this.addToCollectionTree(res.data[i].src, this.getRowPath(res.data[i]));
	    }
            this.snapshots = this.snapshots.concat(res.data);
            this.updateSearch();
            document.getElementById('searchinput').focus();
          })
    },
    mounted: function() {
	    this.handleKeydown = (event) => {
		    if(!this.hit) {
			    return;
		    }
		    if(event.key === "ArrowLeft") {
			    event.preventDefault();
			    this.showPrevHit();
		    } else if(event.key === "ArrowRight") {
			    event.preventDefault();
			    this.showNextHit();
		    } else if(event.key === "Escape") {
			    event.preventDefault();
			    this.clearHit();
		    }
	    };
	    window.addEventListener("keydown", this.handleKeydown);
    },
    beforeUnmount: function() {
	    if(this.handleKeydown) {
		    window.removeEventListener("keydown", this.handleKeydown);
	    }
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
	        flex: 1 1 22rem;
	        min-width: 14rem;
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
		overflow: visible;
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
	.lightbox-nav {
		position: absolute;
		top: 50%;
		left: 0;
		right: 0;
		display: flex;
		justify-content: space-between;
		pointer-events: none;
		transform: translateY(-50%);
		padding: 0 0.5em;
		z-index: 2;
	}
	.nav-button {
		pointer-events: auto;
		border: 1px solid rgba(0, 0, 0, 0.2);
		background: rgba(255, 255, 255, 0.95);
		color: #111111;
		font-size: 2rem;
		line-height: 1;
		width: 2.2rem;
		height: 2.2rem;
		border-radius: 50%;
		cursor: pointer;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
	}
	.nav-button:hover {
		background: rgba(255, 255, 255, 1);
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
	.filter-row {
	    display: flex;
	    align-items: center;
	    gap: 0.75em;
	    flex-wrap: nowrap;
	}
	.mobile-menu-button {
	    display: none;
	    align-items: center;
	    gap: 0.45em;
	    border: 1px solid rgba(255, 255, 255, 0.12);
	    border-radius: 10px;
	    padding: 0.35rem 0.55rem;
	    background: rgba(18, 26, 36, 0.9);
	    color: #cccccc;
	    font-size: 0.85rem;
	    cursor: pointer;
	}
	.mobile-menu-icon {
	    display: inline-block;
	    width: 14px;
	    height: 10px;
	    background-image:
	      linear-gradient(#cccccc, #cccccc),
	      linear-gradient(#cccccc, #cccccc),
	      linear-gradient(#cccccc, #cccccc);
	    background-size: 100% 2px;
	    background-position: 0 0, 0 4px, 0 8px;
	    background-repeat: no-repeat;
	}
	.top-toolbar {
	    display: flex;
	    align-items: center;
	    gap: 0.75em;
	    width: 100%;
	    flex-wrap: nowrap;
	}
	.settingswrap {
	    margin-left: auto;
	}
	.mobile-logo-link {
	    display: none;
	    align-items: center;
	    line-height: 1;
	}
	.folder-toolbar {
	    margin-top: 0.5em;
	    display: flex;
	    flex-direction: column;
	    gap: 0.4em;
	    width: 100%;
	    clear: both;
	}
	.folder-path {
	    display: flex;
	    align-items: center;
	    gap: 0.5em;
	    flex-wrap: wrap;
	}
	.folder-list {
	    display: flex;
	    flex-wrap: wrap;
	    gap: 0.4em;
	}
	.folder-button {
	    background-color: #2d2d66;
	    color: #eeeeee;
	    border: 1px solid #444455;
	    border-radius: 0.4em;
	    padding: 0.3em 0.6em;
	    cursor: pointer;
	    font-size: 0.9em;
	}
	.folder-button:hover {
	    background-color: #3a3a77;
	}
	.folder-current {
	    font-size: 0.9em;
	    color: #dddddd;
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

	@media (max-width: 760px) {
	  html,
	  body,
	  #app,
	  .container {
	    max-width: 100%;
	    overflow-x: hidden;
	  }
	  .searchbox {
	    padding-right: 0.25em;
	  }
	  #searchinput {
	    min-width: 0;
	  }
	  .top-toolbar {
	    position: relative;
	    flex-wrap: wrap;
	    gap: 0.5em;
	  }
	  .mobile-menu-button {
	    display: inline-flex;
	  }
	  .filter-row {
	    display: none;
	    position: absolute;
	    top: calc(100% + 0.4em);
	    left: 0;
	    right: 0;
	    z-index: 140;
	    padding: 0.55em;
	    border-radius: 10px;
	    border: 1px solid rgba(255, 255, 255, 0.12);
	    background: rgba(18, 26, 36, 0.98);
	    flex-direction: column;
	    align-items: stretch;
	    gap: 0.5em;
	  }
	  .filter-row.mobile-open {
	    display: flex;
	  }
	  .filter-row label,
	  .filter-row select {
	    width: 100%;
	  }
	  .settingswrap {
	    margin-left: 0;
	    position: static;
	  }
	  .settingsmenu {
	    left: 0;
	    right: 0;
	    top: calc(100% + 0.4em);
	    min-width: 0;
	  }
	  .logo {
	    position: static;
	    width: 2.8em;
	    margin-left: 0;
	  }
	  .desktop-logo-link {
	    display: none;
	  }
	  .mobile-logo-link {
	    display: inline-flex;
	  }
	  .mobile-logo {
	    width: 2.2em;
	  }
	  .searchtile {
	    margin: 0;
	    width: 100%;
	    max-width: 260px;
	    height: auto;
	    aspect-ratio: 1 / 1;
	  }
	  .searchtile img {
	    position: static;
	    transform: none;
	    width: 100%;
	    height: 100%;
	    object-fit: cover;
	  }
	}
</style>
