#!/bin/bash

# Test ProcessSteps GraphQL Queries and Mutations

echo "=== Testing ProcessSteps GraphQL API ==="
echo ""

# 1. Query all process steps
echo "1. Querying all process steps..."
curl -s -X POST http://localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { processSteps { id title short_description step_number icon icon_color slug order is_active items { id title icon order } } }"
  }' | jq '.'

echo ""
echo "----------------------------------------"
echo ""

# 2. Query active process steps only
echo "2. Querying active process steps only..."
curl -s -X POST http://localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { processSteps(is_active: true) { id title step_number is_active } }"
  }' | jq '.'

echo ""
echo "----------------------------------------"
echo ""

# 3. Query single process step by ID
echo "3. Querying single process step (ID: 1)..."
curl -s -X POST http://localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { processStep(id: 1) { id title description short_description step_number icon icon_color slug items { id title description icon order } } }"
  }' | jq '.'

echo ""
echo "----------------------------------------"
echo ""

echo "=== Public Query Tests Complete ==="
echo ""
echo "Note: Mutation tests require authentication token."
echo "To test mutations, you need to:"
echo "1. Login and get a token"
echo "2. Use the token in Authorization header"
echo "3. Test create/update/delete mutations"
