#!/bin/bash

# Script to update all Filament Resources to extend BaseResource
# This applies global sorting: newest to oldest

echo "🔄 Updating Filament Resources to use BaseResource..."
echo "=================================================="

RESOURCES_DIR="app/Filament/Resources"
COUNT=0

# Find all *Resource.php files (not in subdirectories like Pages/)
for file in $(find "$RESOURCES_DIR" -maxdepth 1 -name "*Resource.php" -type f); do
    filename=$(basename "$file")
    
    # Skip BaseResource.php itself
    if [ "$filename" = "BaseResource.php" ]; then
        continue
    fi
    
    # Check if file extends Resource
    if grep -q "extends Resource" "$file"; then
        echo "✏️  Updating: $filename"
        
        # Replace the import statement
        sed -i 's/use Filament\\Resources\\Resource;/use App\\Filament\\Resources\\BaseResource;/g' "$file"
        
        # Replace extends Resource with extends BaseResource
        sed -i 's/\(class [A-Za-z0-9_]* \)extends Resource/\1extends BaseResource/g' "$file"
        
        COUNT=$((COUNT + 1))
    fi
done

echo "=================================================="
echo "✅ Updated $COUNT resources"
echo ""
echo "📋 Next steps:"
echo "   1. Review changes: git diff app/Filament/Resources/"
echo "   2. Test admin panel: Visit /admin and check any resource"
echo "   3. Commit changes: git add -A && git commit -m 'Apply global sorting to Filament resources'"
echo ""
echo "🎯 All resources now sort from newest to oldest by default!"
