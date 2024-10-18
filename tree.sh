#!/bin/sh

# Function to print tree structure to output.md
print_tree() {
    local indent="$2"
    local dir="$1"

    # List directories in the given directory
    for entry in "$dir"/*; do
        if [ -d "$entry" ]; then
            # If it's a directory, print it and recursively call the function
            echo "${indent}- 📁 **$(basename "$entry")/**" >> output.md
            print_tree "$entry" "   $indent"
        fi
    done

    # List up to 2 files in the current directory
    file_count=0
    for entry in "$dir"/*; do
        if [ -f "$entry" ] && [ $file_count -lt 2 ]; then
            echo "${indent}- 📄 $(basename "$entry")" >> output.md
            file_count=$((file_count+1))
        fi
    done
}

# Get the directory to print, default to current directory if not provided
start_dir=${1:-.}

# Start writing the tree structure to output.md
echo "# Directory structure of: $start_dir" > output.md
print_tree "$start_dir"

echo "Tree structure written to output.md"
