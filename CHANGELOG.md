# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.10] - 2025-10-26

### Fixed
- Reverted to sw-text-field for teaserText for better compatibility with Shopware 6.6.0+

## [0.0.9] - 2025-10-26

### Changed
- Replaced CmsPage references with LandingPageEntity for correct entity handling
- Updated all PHP entity classes, definitions, and templates to use landing_page instead of cms_page
- Renamed database column from cms_page_id to landing_page_id
- Updated admin component translations and labels for consistency

## [0.0.8] - 2025-10-23

### Changed
- Removed exact match logic in favor of priority-based sorting for all prefix matches

## [0.0.7] - 2025-10-21

### Fixed
- With multiple matches only one target was shown

## [0.0.6] - 2025-10-21

### Fixed
- Limit search terms to sales channels

## [0.0.5] - 2025-10-20

### Fixed
- v-model for teaserText

## [0.0.4] - 2025-10-20

### Added
- Optional display image for search targets

### Fixed
- Creation of search targets now only possible in system default language

## [0.0.3] - 2025-10-14

### Fixed
- **MariaDB compatibility**: Removed foreign key constraints for category and cms_page tables due to versioning issues
- Added indexes for category_id and cms_page_id to maintain query performance
- Merged all migrations into single migration file for clean fresh installations

## [0.0.1] - 2025-10-13

### Added
- Initial plugin release
- Core functionality: Display categories and landing pages in search suggestions
- Two-tier search matching (exact match and prefix match)
- Translatable title and teaser text fields
- Language-specific search terms
- Sales channel specific configuration
- Priority-based sorting
- Active/inactive toggle for search terms
- Admin UI for managing search targets and terms
- Language switcher support in Admin
- Storefront template for displaying search suggestions
- Database migrations for entity structure
- Comprehensive README documentation (English and German)
- MIT License file
- .gitignore file for plugin development
- Complete unit test suite for SearchTargetLoader and SuggestPageLoadedSubscriber
- Smart search logic with automatic prefix matching
- Shortest term length prioritization
- Support for both Shopware 6.6 and 6.7
- Fully translatable Admin interface (English and German)
- Bootstrap-based storefront styling
