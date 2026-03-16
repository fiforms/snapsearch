#!/bin/bash
if [[ "$1" =~ (.*)\.([^\.]+)$ ]]; then
    filename="${BASH_REMATCH[1]}"
    extension="${BASH_REMATCH[2]}"
    convert "$1" -define webp:lossless=true "$filename.webp"
    if [[ -e "$filename.webp" ]]; then
	    rm "$1"
    fi
fi
