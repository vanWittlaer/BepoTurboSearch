# Turbo Search Suggests

Enhance your Shopware search experience by displaying categories and landing pages prominently in the search suggestions, making it easier for customers to find what they're looking for.

## What does this plugin do?

When customers use your shop's search function, they typically only see product suggestions. With **Turbo Search Suggests**, you can also show relevant **categories** and **landing pages** at the top of the search dropdown.

### Example

Your customer types "shi" into the search bar:
- **Without this plugin**: Only shows product results
- **With this plugin**: Shows your "Shirts" category prominently at the top, followed by product suggestions

This helps customers navigate directly to the right category or landing page, improving their shopping experience and increasing conversion rates.

## Features

- Display categories in search suggestions
- Display landing pages in search suggestions
- Fully translatable titles and teaser texts
- Smart search matching (exact match and prefix match)
- Priority-based sorting (show most important results first)
- Sales channel specific configuration
- Language-specific search terms
- Active/inactive toggle for individual search terms
- Easy-to-use administration interface

## Installation

### Via Shopware Extension Store

1. Open your Shopware Administration
2. Navigate to **Extensions → My extensions**
3. Search for "Turbo Search Suggests"
4. Click **Install** and then **Activate**

### Via Shopware Administration (Manual Upload)

1. Download the plugin ZIP file
2. Open your Shopware Administration
3. Navigate to **Extensions → My extensions**
4. Click **Upload extension**
5. Select the downloaded ZIP file
6. Click **Install** and then **Activate**

### Via Composer

```bash
composer require bepo/turbo-suggest
bin/console plugin:refresh
bin/console plugin:install --activate BepoTurboSuggest
```

## How to Use

### 1. Access the Plugin

In your Shopware Administration, navigate to:
**Marketing → Turbo Search Suggests**

### 2. Create a Search Target

Click **"Add new target"** and configure:

- **Title** (optional): Override the category or landing page name shown in suggestions
- **Teaser Text** (optional): Add a short description below the title
- **Category OR Landing Page**: Select either a category or a landing page (not both)
- **Sales Channel**: Choose which sales channel this target applies to
- **Priority**: Higher numbers appear first in search results (e.g., 100 appears before 50)

### 3. Add Search Terms

After saving your target, switch to the **"Search Terms"** tab:

1. Click **"Add search term"**
2. Enter the search term (e.g., "shirts", "mens", "socks")
3. Select the language
4. Toggle **"Active"** on/off
5. Save

**Tip**: Use complete words as search terms (e.g., "shirts", "t-shirts") - the plugin automatically matches partial inputs like "sh", "shi", or "shirt" against them. Only add multiple terms for different word variations.

### 4. Language Support

Both the target (title and teaser text) and search terms support multiple languages:

- Use the **language switcher** in the top bar to add translations
- Create search terms for each language your shop supports
- Translations are automatically displayed based on the customer's selected language

## How Search Matching Works

The plugin uses a smart two-tier matching system:

1. **Exact Match (Priority 1)**: If a search term exactly matches the customer's input, that target is shown
2. **Prefix Match (Priority 2)**: If no exact match exists, the plugin looks for terms that start with the customer's input

When multiple targets match:
- Targets with the **shortest matching term** are preferred
- Results are sorted by **priority** (highest first)

## Examples

### Example 1: Category Suggestion

**Target Configuration:**
- Category: "Men's Clothing"
- Priority: 100
- Search Terms: "men", "mens", "herren" (German)

**Result**: When customers type "men", they see "Men's Clothing" at the top of suggestions.

### Example 2: Landing Page Promotion

**Target Configuration:**
- Landing Page: "Summer Sale 2025"
- Title: "☀️ Summer Sale"
- Teaser Text: "Up to 50% off on selected items"
- Priority: 200
- Search Terms: "summer", "sale", "sommer" (German)

**Result**: When customers type "sum", they see your prominent summer sale landing page with the custom title and teaser.

### Example 3: Multiple Categories

**Target 1:**
- Category: "Women's Shirts"
- Priority: 80
- Search Terms: "shirt"

**Target 2:**
- Category: "Men's Shirts"
- Priority: 70
- Search Terms: "shirt"

**Result**: When customers type "shirt", both categories appear, with "Women's Shirts" first (higher priority).

## Best Practices

1. **Use meaningful priorities**: Reserve high numbers (100+) for your most important categories or promotions
2. **Use complete words as search terms**: The plugin matches prefixes automatically, so "shirts" will match "sh", "shi", "shirt", etc. Only add separate terms for different word variations (e.g., "shirts" and "t-shirts")
3. **Keep teaser texts short**: One sentence is usually enough
4. **Test in multiple languages**: Make sure each language has appropriate search terms
5. **Use the active toggle**: Temporarily disable search terms for seasonal campaigns without deleting them

## Requirements

- Shopware 6.6.0 or higher

## Support

For questions, feature requests, or bug reports, please contact:
- Website: https://www.poensgen.de
- Support: https://www.poensgen.de/support

## License

This plugin is licensed under the MIT License. See LICENSE file for details.

## Credits

Developed by Benny Poensgen
Copyright (c) 2025 B. Poensgen IT-Dienstleistungen
