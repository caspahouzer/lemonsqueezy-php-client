#!/bin/bash
# LemonSqueezy Batch Operations Test Runner

# Get the directory where this script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "Working directory: $(pwd)"
echo ""

# Check if .env.local exists
if [ ! -f .env.local ]; then
    echo "❌ Error: .env.local file not found in $SCRIPT_DIR"
    echo ""
    echo "Please create an .env.local file in the LemonSqueezy directory:"
    echo "  echo 'LEMONSQUEEZY_API_KEY=your_api_key_here' > .env.local"
    exit 1
fi

# Load API key from .env.local
if ! grep -q "LEMONSQUEEZY_API_KEY" .env.local; then
    echo "❌ Error: LEMONSQUEEZY_API_KEY not found in .env.local"
    exit 1
fi

# Export the API key
export $(grep LEMONSQUEEZY_API_KEY .env.local | sed 's/ //g')

# Verify API key is set and not empty
if [ -z "$LEMONSQUEEZY_API_KEY" ]; then
    echo "❌ Error: LEMONSQUEEZY_API_KEY is empty"
    exit 1
fi

echo "✓ API Key loaded successfully"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Running LemonSqueezy Batch Operations Real API Tests..."
echo ""

# Run the batch operations integration tests
./vendor/bin/phpunit tests/Integration/BatchOperationsRealApiTest.php

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✓ Batch test run complete!"
