#!/bin/bash

for i in *.webp; do
    if [[ ! -e "$i" ]]; then
        echo "No Files Found"
        exit 1
    fi

    filename="$i"
    if [[ "$i" =~ (.*)\.upsc\..*$ ]]; then
        filename="${BASH_REMATCH[1]}"
    else
        if [[ "$i" =~ (.*)\.webp$ ]]; then
            filename="${BASH_REMATCH[1]}"
        fi
    fi

    original="original/$filename.webp"
    if [[ ! -e "$original" ]]; then
        original="original/$filename.jpg"
    fi

    if [[ -e "$original" ]]; then
        # 1. Try EXIF ImageDescription
        original_desc=$(exiftool -s -s -s -ImageDescription "$original")

        # 2. If empty, try XMP description
        if [[ -z "$original_desc" ]]; then
            original_desc=$(exiftool -s -s -s -XMP-dc:Description "$original")
        fi

        if [[ -n "$original_desc" ]]; then
            echo "Original description: $original_desc"

            # Check if destination already has any description
            dest_desc_exif=$(exiftool -s -s -s -ImageDescription "$i")
            dest_desc_xmp=$(exiftool -s -s -s -XMP-dc:Description "$i")

            if [[ -n "$dest_desc_exif" || -n "$dest_desc_xmp" ]]; then
                if [[ "$1" = "--overwrite" ]]; then
                    echo "Overwriting destination XMP description"
                    exiftool -overwrite_original -XMP-dc:Description="$original_desc" "$i"
                else
                    echo "Destination already has a description. Use --overwrite to replace."
                fi
            else
                echo "Copying description -> destination XMP"
                exiftool -overwrite_original -XMP-dc:Description="$original_desc" "$i"
            fi
        fi
    fi
done

