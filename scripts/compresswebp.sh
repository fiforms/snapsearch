#!/bin/bash
if [[ -e ".convert_tmp.$extension" ]]; then
	exit 1;
fi

if [[ "$1" =~ (.*)\.([^\.]+)$ ]]; then
    filename="${BASH_REMATCH[1]}"
    extension="${BASH_REMATCH[2]}"
    mv "$1" ".convert_tmp.$extension"
    #convert ".convert_tmp.$extension" -quality 99 "$filename.webp"
    cwebp -q 90 ".convert_tmp.$extension" -o "$filename.webp"
    if [[ -e "$filename.webp" ]]; then
	    rm ".convert_tmp.$extension"
    fi
fi
