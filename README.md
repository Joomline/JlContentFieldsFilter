# JL Content Fields Filter

A Joomla extension for filtering articles by custom fields. Supports field types: text, radio, list, checkboxes, and calendar (date range filtering).

**Official page:** [RU](https://joomline.ru/rasshirenija/moduli/jlcontentfieldsfilter.html) - [EN](http://joomline.org/extensions/modules-for-joomla/jlcontentfieldsfilter.html)

## Joomla 6 Compatibility

Version 4.0.0 is fully compatible with **Joomla 6** and has been completely refactored to meet modern standards:

### Module and Plugin
- Fully rewritten with PSR-4 namespaces and Joomla 6 architecture
- All deprecated Joomla API methods replaced with modern equivalents
- Tested with PHP 8.2+ and Joomla 6.0
- Complete compatibility with Joomla 4.x and 5.x maintained

### Component
- Updated to use modern Joomla API (`getDatabase()` instead of deprecated `getDbo()`)
- Admin interface rebuilt with Vue.js 2 and Axios for dynamic filtering
- All SQL injection vulnerabilities fixed
- Full support for SEO meta tags configuration per filter combination

### System Plugin
- Enhanced filtering for com_content, com_contact, and com_tags
- Improved performance with optimized database queries
- Security hardening: all user inputs properly sanitized

## Features

### Filtering Support
The filter works with:
- **Articles** (com_content) - full support for all field types
- **Contacts** (com_contact) - filter contacts by custom fields
- **Tags** (com_tags) - filter entities by tag with field filtering

### Tag Component Filtering (com_tags)
Currently, only Joomla articles are filtered in the tag entity list. Filtering entities from **different components** by their field values is not supported.

### Supported Field Types
- Text fields (exact match and LIKE search)
- Radio buttons
- Select lists
- Checkboxes
- Calendar fields (date range filtering with FROM/TO)

### SEO Features
- Configure unique meta title, description, and keywords for each filter combination
- Automatic hash-based filter identification
- Publish/unpublish control for filter combinations

## Installation

Download the latest release from the [Releases page](https://github.com/joomline/JlContentFieldsFilter/releases) and install all three packages:

1. **Module:** `mod_jlcontentfieldsfilter` - frontend filtering interface
2. **Plugin:** `plg_system_jlcontentfieldsfilter` - system plugin (must be published!)
3. **Component:** `com_jlcontentfieldsfilter` - admin component for SEO configuration

After installation:
1. Publish the system plugin
2. Publish and configure the module
3. Assign the module to desired positions

## Requirements

- **Joomla:** 6.0+ (also compatible with 4.x and 5.x)
- **PHP:** 8.1+
- **Database:** MySQL 5.7+ / MariaDB 10.3+

## Testing

Tested on:
- Joomla 6.0 with PHP 8.2+
- Joomla 5.x with PHP 8.1+
- Joomla 4.x with PHP 8.0+

## Development

### Building from Source

To build the extension packages from source, use Phing:

```bash
# Install Phing globally
composer global require phing/phing

# Build all packages
phing

# Built packages will be in _dist/ folder:
# - com_jlcontentfieldsfilter.zip (component)
# - mod_jlcontentfieldsfilter.zip (module)
# - plg_system_jlcontentfieldsfilter.zip (plugin)
```

### Version 4.0.0 Changes

**Security Fixes:**
- Fixed 5 SQL injection vulnerabilities in filter processing
- Sanitized all user inputs with proper escaping
- Fixed operator precedence bug in filter string creation

**Joomla 6 Compatibility:**
- Migrated to PSR-4 namespaces
- Replaced all deprecated API calls (`getDbo()` → `getDatabase()`)
- Fixed PHP 8.2+ dynamic property deprecation warnings
- Updated all database queries to use modern QueryInterface

**Bug Fixes:**
- Fixed admin component inability to save new filter records
- Added missing `delete()` method in ItemModel
- Fixed undefined `$db` variable in ContactModel and TagModel
- Fixed Apply button not working in admin filter interface

## Statistics

![GitHub all releases](https://img.shields.io/github/downloads/joomline/JlContentFieldsFilter/total?style=for-the-badge&color=blue)  ![GitHub release (latest by SemVer)](https://img.shields.io/github/downloads/Joomline/JlContentFieldsFilter/latest/total?style=for-the-badge&color=blue)

## Credits

Developed by [Joomline](https://joomline.ru/)

## License

GNU General Public License version 2 or later
