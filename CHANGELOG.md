# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Search statistics tracking: Automatically track which search terms customers use
- Click-through rate analysis in admin statistics page
- Statistics admin page showing:
  - Search term frequency
  - Click counts per search term
  - Click-through rate percentage
  - Last searched timestamp
  - Sales channel breakdown
- SearchStatisticLogger service for tracking search behavior
- Database migration for search statistics table

### Changed
- SuggestPageLoadedSubscriber now logs all search queries automatically

## [0.0.3] - 2025-01-14

### Fixed
- **MariaDB compatibility**: Removed foreign key constraints for category and cms_page tables due to versioning issues
- Added indexes for category_id and cms_page_id to maintain query performance
- Merged all migrations into single migration file for clean fresh installations

## [0.0.1] - 2025-01-13

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
