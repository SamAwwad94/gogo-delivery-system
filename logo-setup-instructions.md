# Logo and Image Management System Setup Instructions

## Overview

This document provides detailed instructions for setting up and managing the logo and image system for your delivery application. The system allows you to easily update the main logo, dark mode logo, and favicon through an admin interface.

## System Features

- Dedicated logo management section in the admin settings
- Support for different logo types (main logo, dark logo, favicon)
- Centralized helper for easy display across the application
- Cache busting to ensure logo updates are immediately visible
- Fallback to default logos when custom ones aren't set

## Prerequisites

Before proceeding, please prepare the following:

- **Logo Image Files**:
  - Main logo (light version): 200-250px width, PNG format with transparency
  - Dark logo: 200-250px width, PNG format with transparency
  - Favicon: 16x16px, 32x32px, or 64x64px, ICO or PNG format

## Implementation Steps

### 1. Directory Structure

The system uses a dedicated directory for storing logo files:

```
public/images/logos/
```

This directory has already been created with proper permissions.

### 2. Database Configuration

The system uses the existing `settings` table with the following structure:

- `id`: Primary key
- `type`: Category of setting (e.g., 'general')
- `key`: Unique identifier for the setting
- `value`: Actual setting value (in this case, the logo file path)

Default settings have been added for:

- `site_logo`: Main website logo
- `site_dark_logo`: Dark mode logo
- `site_favicon`: Website favicon

### 3. Admin Interface

A dedicated logo management interface is accessible from the admin settings section:

1. Log in to your admin dashboard
2. Navigate to Settings
3. Click on "Logo Management" in the sidebar
4. Use the form to upload and manage your logos

### 4. Customizing Logos

To update your logos:

1. Access the Logo Management section as described above
2. For each logo type:
   - View the current logo preview
   - Click "Choose File" to select a new image
   - Observe the recommended dimensions
3. Click "Update Logos" to save changes
4. The changes will be immediately visible throughout the application

### 5. Technical Details

The implementation includes:

- A `LogoHelper` class for centralized logo management
- Integration with existing layout files
- Cache busting with timestamp parameters
- Default fallback logos

### 6. Troubleshooting

If you encounter issues with logo display:

1. Check that you've uploaded properly sized images in the correct format
2. Verify permissions on the `public/images/logos` directory
3. Clear your browser cache to ensure you're seeing the latest images
4. Check browser console for any loading errors

## Support

If you need additional assistance with the logo management system, please contact the development team.
