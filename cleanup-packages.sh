#!/bin/bash

# Script to safely remove unused packages after modernization
# Run this script after testing that all replacements work correctly

echo "Package Cleanup Script"
echo "======================"
echo "This script will help you safely remove unused packages after modernization."
echo "Make sure you have tested all replacements before running this script."
echo ""

# Function to check if a package is used
check_usage() {
  package=$1
  echo "Checking usage of $package..."
  
  # Check in resources directory
  resources_usage=$(grep -r "$package" ./resources | wc -l)
  
  # Check in public directory
  public_usage=$(grep -r "$package" ./public | wc -l)
  
  # Check in package.json
  package_json_usage=$(grep -r "\"$package\"" ./package.json | wc -l)
  
  echo "Found $resources_usage references in resources directory"
  echo "Found $public_usage references in public directory"
  echo "Found $package_json_usage references in package.json"
  
  total=$((resources_usage + public_usage))
  
  if [ $total -eq 0 ]; then
    echo "✅ $package is not used in the codebase and can be safely removed."
    return 0
  else
    echo "⚠️ $package is still used in the codebase. Please replace all usages before removing."
    return 1
  fi
}

# Function to remove a package
remove_package() {
  package=$1
  
  echo ""
  echo "Removing $package..."
  npm uninstall $package
  
  if [ $? -eq 0 ]; then
    echo "✅ Successfully removed $package"
  else
    echo "❌ Failed to remove $package"
  fi
}

# List of packages to check and potentially remove
packages_to_check=(
  "vuex"
  "raphael"
  "xcharts"
  "lit-html"
  "lit-element"
  "vue-resource"
  "toastr"
  "tinymce"
  "select2"
  "moment"
)

echo "Checking packages that can be safely removed..."
echo ""

safe_to_remove=()

for package in "${packages_to_check[@]}"; do
  if check_usage "$package"; then
    safe_to_remove+=("$package")
  fi
  echo "-----------------------------------"
done

echo ""
echo "Packages safe to remove: ${#safe_to_remove[@]}"
if [ ${#safe_to_remove[@]} -gt 0 ]; then
  echo "The following packages can be safely removed:"
  for package in "${safe_to_remove[@]}"; do
    echo "- $package"
  done
  
  echo ""
  read -p "Do you want to remove these packages now? (y/n): " confirm
  
  if [ "$confirm" = "y" ]; then
    for package in "${safe_to_remove[@]}"; do
      remove_package "$package"
    done
    
    echo ""
    echo "Package cleanup complete!"
    echo "Run 'npm install' to update your node_modules directory."
  else
    echo "No packages were removed."
  fi
else
  echo "No packages are safe to remove yet. Please replace all usages first."
fi

echo ""
echo "Recommended replacements:"
echo "- moment → dayjs (already installed)"
echo "- toastr → vue-toastification (already installed)"
echo "- select2 → Naive UI Select (already installed)"
echo "- tinymce → Quill (already installed)"
echo ""
echo "After replacing all usages, run this script again to safely remove the old packages."
