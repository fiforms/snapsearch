#!/bin/bash
#
# Functions to parse filenames for URLs for the Virtual Bible Snapshot Project
# by Pastor Daniel McFeeters
# Licensed under CC0
#
#

function get_sourcelink() {
	link=""
	ftype="$3"
	dftype="$3"
        if [[ "$1" =~ ([^/]+)\.([^\.]+)$ ]]; then
            filename="${BASH_REMATCH[1]}"
            extension="${BASH_REMATCH[2]}"
            if [[ "$filename" =~ AME_([0-9]+) ]]; then
                result="${BASH_REMATCH[1]}"
	        link="https://media.adventistexchange.org/pages/view.php?ref=$result"
	    elif [[ "$filename" =~ PXHere.*[\-\ ]([0-9]+)\.(AIART\.)?CC0 ]]; then
                result="${BASH_REMATCH[1]}"
	        link="https://pxhere.com/en/photo/$result"
            elif [[ "$filename" =~ [Mm]id[Jj]ourney.*_([^_]+) ]]; then
                result="${BASH_REMATCH[1]}"
		if [[ "$result" =~ ([^\.]+?)\..* ]]; then
                    result="${BASH_REMATCH[1]}"
		fi
	        link="https://www.midjourney.com/jobs/$result"
	        ftype="AI Generated $dftype"
            elif [[ "$filename" =~ [Ww]ikimedia\ ([^ ]*) ]]; then
                result="${BASH_REMATCH[1]}"
	        link="https://commons.wikimedia.org/wiki/File:$result.$extension"
#           elif [[ "$filename" =~ [Ff]lickr[\ _]([a-zA-Z0-9\@\-]*)_([0-9]*)_.* ]]; then
            elif [[ "$filename" =~ [Ff]lickr[\ _]([A-Za-z0-9\@\._\-]+)_([0-9][0-9][0-9][0-9][0-9]+)_.* ]]; then
                flickruser="${BASH_REMATCH[1]}"
                flickrid="${BASH_REMATCH[2]}"
	        link="https://www.flickr.com/photos/$flickruser/$flickrid"
            elif [[ "$filename" =~ [Pp]ixabay\ ([^ ]*)\ .* ]]; then
                result="${BASH_REMATCH[1]}"
	        link="https://pixabay.com/photos/$result/"
            elif [[ "$filename" =~ ^Bing\ Image ]]; then
	        ftype="AI Generated $dftype"
	    fi
	fi
        if [[ "$1" =~ "DIA_MAP" ]]; then
		ftype="Diagrams & Maps"
        elif [[ "$1" =~ "DIGITALART" ]]; then
		ftype="Digital Art"
	elif [[ "$1" =~ "TRADART" ]]; then
		ftype="Traditional Art"
	elif [[ "$1" =~ "ChatGPT" ]]; then
		ftype="AI Generated $dftype"
	elif [[ "$1" =~ "Gemini" ]]; then
		ftype="AI Generated $dftype"
	elif [[ "$1" =~ [Dd][Aa][Ll][Ll]-?[Ee] ]]; then
		ftype="AI Generated $dftype"
	elif [[ "$1" =~ "AIART" ]]; then
		ftype="AI Generated $dftype"
	elif [[ "$1" =~ "AME-AI" ]]; then
		ftype="AI Generated $dftype"
	elif [[ "$1" =~ "BibleWorld" ]]; then
		ftype="AI Generated $dftype"
	fi
	if [[ "$2" = "type" ]]; then
		echo "$ftype"
	else
		echo "$link"
	fi
}

# Resolve an attribution token using a mapping file with lines:
#   jdoe:John Doe
# Supports comments (# ...) and whitespace around ':'.
function resolve_attribution() {
    local token="$1"
    local mapfile="${2:-name_mapping.txt}"

    # If nothing to resolve or mapping file missing, return original
    [[ -z "$token" || ! -f "$mapfile" ]] && { echo "$token"; return 0; }

    local line key val
    while IFS= read -r line || [[ -n "$line" ]]; do
        # Skip blank lines and comments
        [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]] && continue

        # Must contain a colon
        [[ "$line" != *:* ]] && continue

        key="${line%%:*}"
        val="${line#*:}"

        # Trim whitespace around key and value
        key="${key#"${key%%[![:space:]]*}"}"
        key="${key%"${key##*[![:space:]]}"}"
        val="${val#"${val%%[![:space:]]*}"}"
        val="${val%"${val##*[![:space:]]}"}"

        if [[ "$token" == "$key" ]]; then
            echo "$val"
            return 0
        fi
    done < "$mapfile"

    echo "$token"
}

function get_copyright() {
    local filename="$1"
    local mode="$2"
    local license=""
    local attribution=""
    local last=""
    local script_dir="$(dirname "${BASH_SOURCE[0]}")"  


    # Public Domain detection
    if [[ "$filename" =~ [\.\ ]PD[\.\ ] ]]; then
        license="Public Domain"
    fi

    # Creative Commons detection (e.g., CC-BY Jane Doe.jpg)
    if [[ "${filename}" =~ (CC[\-0][BYSANCD\-]*)[\ \.](.+)\.[A-Za-z0-9]+$ ]]; then
        license="${BASH_REMATCH[1]}"
        attribution="${BASH_REMATCH[2]}"
        
        # Strip out .4k. extension if needed
        if [[ "$attribution" =~ (.*)\.4k$ ]]; then
                attribution="${BASH_REMATCH[1]}"
        fi

        # Strip out .upsc. extension if needed
        if [[ "$attribution" =~ (.*)\.?upsc$ ]]; then
                attribution="${BASH_REMATCH[1]}"
        fi
    fi


    if [[ "$attribution" =~ upsc\. ]]; then
	attribution=
    fi
    
    # MidJourney fallback
    if [[ "$attribution" = "" && "$filename" =~ [Mm]id[Jj]ourney[\ _\.]([a-zA-Z0-9]+) ]]; then
            attribution="${BASH_REMATCH[1]}"
    fi


    # Final cleanup
    # attribution="${attribution%.[jJ][pP][gG]}"
    # attribution="${attribution%.[wW][eE][bB][pP]}"
    # attribution="${attribution%.[pP][nN][gG]}"
    # attribution="${attribution%.[mM][pP]4}"
    # attribution="${attribution%%.}"
    # attribution="${attribution%% }"

    # Map attribution usernames (exact match) to display names
    attribution="$(resolve_attribution "$attribution" "$script_dir/name_mapping.txt")"




    if [[ "$mode" == "license" ]]; then
        echo "$license"
    else
        echo "$attribution"
    fi
}

