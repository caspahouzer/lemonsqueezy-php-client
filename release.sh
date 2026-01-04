#!/bin/bash
set -e

# Get the latest version from the changelog
VERSION=$(grep -m 1 '## \[' CHANGELOG.md | sed -e 's/## \[//' -e 's/\].*//')

if [ -z "$VERSION" ]; then
    echo "Could not find version in CHANGELOG.md"
    exit 1
fi

echo "Creating release for version $VERSION..."

# Get the changelog for the latest version
CHANGELOG_ENTRY=$(awk "/## \\[$VERSION\\]/{f=1;next} /## \\[/ && f{f=0} f" CHANGELOG.md)

# Create a new git tag
git tag -a "v$VERSION" -m "Version $VERSION"

# Create a new GitHub release
gh release create "v$VERSION" --notes "$CHANGELOG_ENTRY" --title "Version $VERSION"

echo "Release v$VERSION created successfully."
