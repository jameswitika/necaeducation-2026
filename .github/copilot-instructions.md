# NECA Education - AI Coding Agent Instructions

## Project Overview
WordPress site for NECA Education with WooCommerce and custom JobReady integration. Runs on XAMPP (Windows) with MySQL database `necaeducation`.

## Architecture

### Core Stack
- **CMS**: WordPress (parent theme: "pro", child theme: "necaeducation")
- **E-commerce**: WooCommerce with custom checkout flow
- **Forms**: Gravity Forms (GFAPI) - form submissions trigger JobReady API integration
- **Custom Plugins**: 
  - `job-ready/` - JobReady RTO API integration (courses, dates, employers, enrolments)
  - `neca-reporting/` - Admin reporting with PhpSpreadsheet exports

### Data Flow Pattern
1. User submits Gravity Form → 2. Form entry triggers hooks → 3. JobReadyForm class processes → 4. API call to JobReady RTO → 5. Creates WP custom post types (job_ready_courses, job_ready_dates)

## Critical Custom Components

### JobReady Plugin (`wp-content/plugins/job-ready/`)
- **API Configuration**: Edit `includes/settings.php` for form IDs and environment (LIVE/STAGING)
  - `JR_API_SERVER` - Toggle between staging/production
  - Form constants (e.g., `CPD_FORM_ID`, `UEE30820_APPLICATION_FORM`)
- **Class Pattern**: Dual-class system - data models (e.g., `JRACourse`) + Operations classes (e.g., `JRACourseOperations`) for API/DB interactions
- **Sync System**: Daily cron job `neca_job_ready_daily_sync()` syncs courses/dates from JobReady API
- **Custom Post Types**: 
  - `job_ready_courses` - Training courses from API
  - `job_ready_dates` - Course schedules/sessions
  - `job_ready_employers` - Employer organizations

### NECA Theme (`wp-content/themes/necaeducation/`)
- **Parent Theme**: Extends "pro" theme (X Theme framework)
- **Breadcrumbs**: Uses Yoast SEO breadcrumbs, not X Theme default
- **WooCommerce Customizations**: Cart URLs redirect to `/training-with-us` instead of shop
- **Custom Sidebars**: Home sidebar registered via `functions.php`

## Development Workflows

### Running Locally
```powershell
# XAMPP must be running (Apache + MySQL)
# Access: http://localhost/necaeducation.com.au
# Admin: /wp-admin/
```

### Testing/Debugging Scripts (`test/` directory)
- **Reprocessing**: `app-reprocess-*.php` - Re-submit form entries to JobReady API
- **PDF Generation**: `*-create-pdf.php` - Generate course enrollment PDFs
- **Sync Testing**: `test-sync.php`, `employer-sync.php` - Manual API sync triggers
- **Pattern**: Include `wp-load.php`, use GFAPI for form entries, call JobReady classes

Example test script pattern:
```php
include("../wp-load.php");
$entry = GFAPI::get_entry($entry_id);
$form = new JobReadyForm();
// Process entry...
```

### Important Configuration
- **WP Cron**: Disabled (`DISABLE_WP_CRON = true`) - use external cron
- **File Editing**: Disabled in admin (`DISALLOW_FILE_EDIT`)
- **SSL**: Forced for admin (`FORCE_SSL_ADMIN`)
- **Database Prefix**: `neca_`
- **Debug Mode**: Various `*_DEBUG_MODE` constants in `job-ready/includes/settings.php`

## Code Conventions

### Naming Patterns
- **JobReady Classes**: Prefix `JRA` (API classes) or `JobReady` (WP classes)
- **Form Constants**: `{COURSE}_FORM_ID` or `{COURSE}_APPLICATION_FORM`
- **Custom Functions**: Prefix `neca_` or `job_ready_` for theme/plugin functions
- **Meta Keys**: Format `jrc_*` (courses), `jrd_*` (dates), `jre_*` (employers)

### WordPress Hooks
- Form submissions hook into `gform_after_submission_{form_id}`
- Sync functions use `wp_schedule_event` for daily jobs
- Custom columns use `manage_{post_type}_posts_columns` pattern

### API Integration
```php
// JobReady API uses Basic Auth + XML
global $jr_api_headers;
// Classes follow: instantiate → set properties → submit()
$course = new JRACourse();
$course->setCourseNumber($value);
$result = JRACourseOperations::submit($course);
```

## Key Files to Reference

- `wp-config.php` - Database, security, cron settings
- `wp-content/plugins/job-ready/job-ready.php` - Plugin bootstrap, CPT registration
- `wp-content/plugins/job-ready/includes/settings.php` - Form IDs, API endpoints
- `wp-content/plugins/job-ready/includes/sync.php` - Sync logic
- `wp-content/themes/necaeducation/functions.php` - Theme customizations
- `test/` directory - Debugging/reprocessing utilities

## Common Tasks

**Add new Gravity Form integration**:
1. Define form ID constant in `job-ready/includes/settings.php`
2. Add form processing logic to `JobReadyForm` class
3. Map form fields to JobReady API classes

**Debug sync issues**:
1. Enable debug mode for specific course in `settings.php`
2. Run test script from `test/` directory
3. Check `wp-content/debug.log` and `test/php_errorlog`

**Modify course display**:
- Template overrides in `wp-content/themes/necaeducation/woocommerce/`
- Custom post type templates use `job-ready/templates/`

## External Dependencies

- **JobReady RTO API**: External training management system (XML-based)
- **WooCommerce**: Products linked to course enrollment
- **Gravity Forms**: Front-end data collection
- **ACF Pro**: Advanced Custom Fields for course metadata
- **PhpSpreadsheet**: Reporting exports (via Composer in neca-reporting plugin)
