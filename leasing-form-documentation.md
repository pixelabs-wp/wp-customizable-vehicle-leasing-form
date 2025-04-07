# Vehicle Leasing Form Plugin Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [Project Structure](#project-structure)
3. [Features](#features)
4. [Admin Interface](#admin-interface)
5. [Frontend Interface](#frontend-interface)
6. [WhatsApp Integration](#whatsapp-integration)
7. [Customization](#customization)
8. [Mobile Responsiveness](#mobile-responsiveness)
9. [Installation](#installation)
10. [Usage](#usage)
11. [Troubleshooting](#troubleshooting)

## Introduction

The Vehicle Leasing Form plugin is a comprehensive WordPress solution for vehicle leasing businesses. It allows administrators to create and manage vehicle listings with customizable subscription options, and provides users with an interactive interface to select their preferred leasing options and submit their interest via WhatsApp.

## Project Structure

The plugin follows a modular structure with clear separation of concerns:

```
leasing-form/
├── assets/
│   ├── css/           # Compiled CSS files
│   └── js/            # JavaScript files for frontend and admin
├── includes/          # PHP class files
│   ├── class-meta-boxes.php    # Handles admin meta boxes
│   ├── class-post-types.php    # Registers custom post types
│   └── class-templates.php     # Manages template rendering
├── src/               # Source files for development
│   └── input.css      # TailwindCSS source file
├── templates/         # Template files
│   ├── archive-vehicle.php     # Vehicle archive page template
│   ├── leasing-form.php        # Main leasing form template
│   └── single-vehicle.php      # Single vehicle display template
├── leasing-form.php   # Main plugin file
├── package.json       # NPM dependencies
└── tailwind.config.js # TailwindCSS configuration
```

## Features

The plugin offers a rich set of features:

1. **Custom Vehicle Post Type**: A dedicated post type for vehicle listings with custom meta boxes.

2. **Flexible Leasing Options**:
   - Subscription lengths (3, 6, 9, 12 months)
   - Insurance options (Basic, Comprehensive, Premium)
   - Mileage options (500, 1000, 1500, 2000 miles)

3. **Interactive Frontend Form**:
   - User-friendly interface with responsive design
   - Real-time price updates based on selections
   - Visual indicators for selected options
   - "Recommended" tags for guiding users toward optimal options

4. **WhatsApp Integration**:
   - Direct submission to WhatsApp with pre-formatted message
   - Configurable WhatsApp number per vehicle
   - Comprehensive message with all selected options and pricing

5. **Admin Customization**:
   - Add, remove, or edit subscription options
   - Customize pricing, descriptions, and default selections
   - Mark options as "recommended" to highlight them for users
   - Set base pricing and price adjustments for each option

## Admin Interface

### Vehicle Post Type

The plugin creates a custom post type called "Vehicle" with dedicated meta boxes:

1. **Leasing Options**: Configure subscription lengths, insurance options, and mileage options.
   - For each option type, administrators can:
     - Set the base price and price adjustments
     - Mark options as "selected by default"
     - Mark options as "recommended"
     - Add descriptions

2. **Base Pricing**: Set the starting monthly price for the vehicle.

3. **Other Settings**:
   - Number of people watching the vehicle (for social proof)
   - WhatsApp contact number for inquiries

### Option Management

Each option type (subscription, insurance, mileage) follows a consistent pattern:
- Add and remove options
- Reorder options with drag-and-drop
- Configure pricing and descriptions
- Set default selections and recommendations

## Frontend Interface

The frontend interface presents a clean, user-friendly form for selecting leasing options:

1. **Subscription Length Selection**:
   - Grid of clickable options
   - Each option shows duration and monthly price
   - Recommended options are highlighted with a tag

2. **Insurance Selection**:
   - Clear presentation of insurance options
   - Price differences shown in relation to base price
   - Recommended option highlighted

3. **Mileage Selection**:
   - Monthly mileage options with descriptions
   - Price adjustments clearly indicated
   - Recommended option highlighted

4. **Pricing Information**:
   - Real-time total monthly price calculation
   - Number of people watching the vehicle
   - Clear call-to-action button

5. **Visual Indicators**:
   - Selected options have a green check mark
   - Hover effects for better user interaction
   - Information tooltips for clarification

## WhatsApp Integration

The form integrates with WhatsApp for lead generation:

1. **Configuration**: Each vehicle can have a dedicated WhatsApp number set in the admin panel.

2. **Submission Process**:
   - When a user clicks "Subscribe now" or "Proceed"
   - The form collects all selected options
   - A formatted message is created with vehicle and option details
   - The WhatsApp API (wa.me) opens in a new browser tab
   - The pre-formatted message is ready to send

3. **Message Format**:
   ```
   Hello! I'm interested in leasing a [Vehicle Name].

   Selected options:
   • Subscription: [Selected Duration]
   • Insurance: [Selected Insurance]
   • Monthly mileage: [Selected Mileage]
   • Total monthly price: AED [Total Price]

   Please contact me to finalize this subscription. Thank you!
   ```

## Customization

The plugin is built with customization in mind:

1. **Templates**: All frontend templates can be overridden by copying them to your theme directory.

2. **Styling**: Built with TailwindCSS, with custom classes prefixed with `alc-` to avoid conflicts.

3. **JavaScript**: Modular JavaScript structure makes it easy to extend or modify functionality.

4. **Options**: All leasing options are fully customizable from the admin interface.

## Mobile Responsiveness

The leasing form is designed to be responsive and work well on mobile devices. However, there are some considerations to ensure optimal display across all screen sizes:

### Important: Viewport Meta Tag

For proper mobile responsiveness, your WordPress theme **must** include the proper viewport meta tag in the `<head>` section:

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

Without this meta tag, responsive designs will not work correctly on mobile devices. Most modern WordPress themes include this by default, but if you're using a custom theme or experiencing mobile display issues, make sure to check for this tag in your theme's `header.php` file.

### Current Implementation

The form uses TailwindCSS grid layout with the following class pattern:

```js
// Set appropriate grid columns based on number of options
const optionCount = options.length;
if (optionCount === 1) {
    container.className = 'alc-grid alc-grid-cols-1 alc-gap-4';
} else if (optionCount === 2) {
    container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
} else {
    container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
}
```

### Mobile Layout Issues

On smaller screens, the two-column grid can cause elements to become too narrow, resulting in:
- Text overflow or wrapping issues
- Cramped UI elements
- Poor tap target sizes for mobile users

### Improving Mobile Responsiveness

To ensure better display on mobile devices, the grid system should adapt based on screen size. Here's an improved implementation:

1. **Update the renderSubscriptionOptions function:**

```javascript
function renderSubscriptionOptions(options) {
    const container = document.getElementById('subscription-options');
    
    // Clear container
    container.innerHTML = '';
    
    // Responsive grid based on screen size
    container.className = 'alc-grid alc-gap-4 alc-grid-cols-1 sm:alc-grid-cols-2';
    
    // Rest of the function remains the same...
}
```

2. **Apply similar changes to renderInsuranceOptions and renderMileageOptions functions.**

3. **Add responsive padding and margins:**

```css
/* In src/input.css */
.alc-card {
    @apply alc-p-4 sm:alc-p-6;
}

.alc-option-card {
    @apply alc-p-3 sm:alc-p-4;
}
```

4. **Adjust font sizes for smaller screens:**

```css
/* In src/input.css */
.alc-text-lg {
    @apply alc-text-base sm:alc-text-lg;
}

.alc-text-2xl {
    @apply alc-text-xl sm:alc-text-2xl;
}
```

These changes use TailwindCSS responsive prefixes (`sm:`, `md:`, etc.) to apply different styles based on screen size, ensuring that the form displays correctly on mobile devices.

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create vehicle listings using the new 'Vehicles' menu item
4. Configure subscription options for each vehicle
5. Place the shortcode `[leasing_form id="vehicle_id"]` in any page or post

## Usage

### Creating a Vehicle

1. Navigate to Vehicles > Add New in the WordPress admin
2. Enter the vehicle title and description
3. Set a featured image for the vehicle
4. Configure leasing options in the meta boxes
5. Publish the vehicle

### Using the Shortcode

Use the shortcode to display the leasing form anywhere:

```
[leasing_form id="123"]
```

Or simply use `[leasing_form]` when inside a vehicle post.

### Customizing Default Options

The plugin comes with sensible defaults, but you can customize:

1. Subscription lengths and pricing
2. Insurance options and coverage details
3. Mileage options and allowances
4. Default selections and recommendations

## Development

The plugin uses modern development tools:

- **TailwindCSS**: For utility-first styling
- **NPM**: For package management
- **PostCSS**: For CSS processing

To modify the styles:

1. Run `npm install` to install dependencies
2. Edit the `src/input.css` file
3. Run `npm run build` to compile the CSS 

## Troubleshooting

### Mobile Display Issues

If the form doesn't display correctly on mobile devices:

1. **Check Viewport Meta Tag**: Ensure your theme has the proper viewport meta tag:
   ```html
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   ```

2. **Update Grid Classes**: Modify the JavaScript functions as described in the [Mobile Responsiveness](#mobile-responsiveness) section.

3. **Test Across Devices**: Use browser developer tools to test various device sizes.

4. **Consider Single Column Layout**: For very small screens, you may want to force a single column layout regardless of the number of options.

### Form Not Initializing

If the form doesn't initialize correctly:

1. **Check Console Errors**: Look for JavaScript errors in the browser console.

2. **Verify Script Loading**: Ensure that the leasing form script is loaded and the `initLeasingForm` function is available.

3. **Validate Data**: Make sure the `leasingFormData` variable contains valid data. 