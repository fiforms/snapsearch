#!/bin/bash

source "$(dirname "$0")/parsename_functions.sh"
file="$1"

license=$(get_copyright "$file" license)
attribution=$(get_copyright "$file" attribution)

echo "License: $license"
echo "Attribution: $attribution"
