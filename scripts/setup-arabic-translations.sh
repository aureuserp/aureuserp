#!/bin/bash

# Script to set up Arabic translations for all AureusERP plugins
# Run from project root: bash scripts/setup-arabic-translations.sh

echo "Setting up Arabic translations for AureusERP plugins..."

# Find all plugin lang/en directories and create ar copies
for plugin_lang in plugins/webkul/*/resources/lang/en; do
    if [ -d "$plugin_lang" ]; then
        ar_dir="${plugin_lang%/en}/ar"
        plugin_name=$(echo "$plugin_lang" | sed 's|plugins/webkul/\([^/]*\)/.*|\1|')
        
        if [ ! -d "$ar_dir" ]; then
            cp -r "$plugin_lang" "$ar_dir"
            echo "✓ Created Arabic translations for: $plugin_name"
        else
            echo "○ Arabic translations already exist for: $plugin_name"
        fi
    fi
done

echo ""
echo "Arabic translation structure created!"
echo "Now you need to translate the content in each ar/ folder."
echo ""
echo "Plugin translation locations:"
find plugins/webkul/*/resources/lang/ar -maxdepth 0 2>/dev/null | while read dir; do
    echo "  - $dir"
done
