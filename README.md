# Elementor Pro Form Action for TIAA WordPress Plugin

### Description

This plugin extends **Elementor Pro** to integrate with the [TIAA WordPress Plugin](https://tiaa-forum.org/). It offers a custom form action capability that allows administrators to create user invitation workflows, sending new user invites directly to the TIAA Forum via Elementor Pro forms.

This form action was necessary since the WebHook action available in Elementor Pro only allows return values of Success or Failure and we wanted to be able to immediately respond differently to a new member's input based on whether the email had already been registered or if one of a few error conditions occurred.

This plugin is designed to work **in conjunction with** the [TIAA WordPress Plugin](https://tiaa-forum.org/) and assumes it is already installed and configured. For additional functionality provided by the TIAA WordPress Plugin, please refer to its documentation.

---

## Features

- **New Elementor Pro Form Action:** Adds a custom action `TIAA Invite` for Elementor Pro Forms to process invitations to the TIAA Forum.
- **Effortless User Onboarding:** Integrates directly with the TIAA Forum's backend to handle user invitations seamlessly.
- **REST API Integration:** Provides a REST endpoint for managing form submissions and connecting with the TIAA Forum.
- **Zero-Code Configuration:** Simple setup and integration via Elementor Pro's Form editor.
- **Site-wide clickable Loop Grid cards:** All Elementor Loop Item cards across the site are made fully clickable via a lightweight footer script.

---

## Requirements

While these requirements make sense (the plugin won't work without close links to Elementor Pro and the TIAA-WPPlugin), specifying them in the plugin code (e.g. in tiaa-elementor-forms-invite-action.php) and the automatic dependencies enforced by WordPress make it at least problematic and maybe impossible to do upgrades and configuration changes to any of the 3 plugins.

### WordPress
- WordPress Version: **6.0** or higher
- PHP Version: **7.1.0** or higher

### Required Plugins
- [TIAA WordPress Plugin](https://tiaa-forum.org/) (refer to its documentation for setup and configuration)
- [Elementor](https://elementor.com/)
- [Elementor Pro](https://elementor.com/pro)

---

## Installation

1. Ensure **Elementor** and **Elementor Pro** are installed and activated.
2. Install and activate the **TIAA WordPress Plugin** (see the [documentation](https://tiaa-forum.org/)).
3. Download or clone this plugin to your WordPress plugins directory (typically `/wp-content/plugins`).
4. Activate this plugin via `Plugins > Installed Plugins` in your WordPress dashboard.

---

## Usage

### Add the TIAA Invite Form Action
1. Navigate to Elementor and create or edit a form.
2. In the "Actions After Submit" dropdown, add the new action called `TIAA Invite`.
3. Configure the form fields:
    - Map the **email field** to collect the email addresses of users to be invited.
    - Optionally, add custom form fields if needed for your workflow.
4. Publish the form and test submissions.

### How it Works
- When a form is submitted, this plugin triggers an API call to the TIAA WordPress Plugin's invite system.
    - The form requires an Elementor Pro `form-action` of `TIAA-invite` after submission that causes some JavaScript code to be loaded that completely bypasses the Elementor Pro Ajax API handler (e.g. fetch...)
    - In order to circumvent the normal form submission process, we need to stub out a response from the handler (always success) and let this plugin's JS respond according to the 3-4 expected responses from the TIAA-plugin for an invite request to Discourse.
- The TIAA plugin processes the submission by communicating with the TIAA Forum Discourse server to send invitations to the email address on the submitted form.

### Clickable Loop Grid Cards

As of v0.0.6, this plugin makes all Elementor Loop Item cards (`.e-loop-item`) fully clickable site-wide. A transparent anchor overlay covers the entire card, linking to the first `<a>` href found inside — typically the post title link. This eliminates the need for visitors to click precisely on the title or button.

The script runs in `wp_footer` on all front-end pages. It is lightweight and self-contained — it only activates when `.e-loop-item` elements are present, so it is harmless on pages without Loop Grids.

**Pages currently using Loop Grids that benefit from this:**
- Homepage (Hot Topics preview, Forum Categories)
- Hot Topics archive (`/hot-topics/`)
- Related Organizations (`/related-orgs/` or embedded on `/resources/`)

---

## File Structure Overview

| File | Functionality |
|---|---|
| `tiaa-elementor-forms-invite-action.php` | Registers the custom Elementor Pro form action `TIAA Invite` and site-wide clickable card JS. |
| `form-action/tiaa-invite.php` | Executes form actions to connect Elementor forms with the TIAA API. |
| `assets/js/form-handler.js` | Handles REST API requests for form-related functionality. |
| `README.md` | This file. |
| `LICENSE.md` | License boilerplate. |

---

## Changelog

### 0.0.6 — 2026-03-29
- Removed `is_front_page()` restriction from clickable Loop Grid card script.
  Cards are now clickable site-wide on any page containing `.e-loop-item` elements.
  Previously only applied to the homepage.

### 0.0.5
- Previous stable release.
- Clickable cards restricted to front page only.

---

## Development & Contributions

### Prerequisites
- WordPress development environment installed and running.

### Testing
Ensure to test the plugin alongside:
- **TIAA WordPress Plugin**: Invite functionality must interact properly with its API.
- **Elementor Pro**: The `TIAA Invite` action must be available and functional.
- **Loop Grid pages**: Verify clickable card overlay works on homepage, Hot Topics archive, and Related Organizations.

### Contributions
We welcome contributions! Follow these steps:
1. Fork the repository.
2. Create a feature branch for your updates.
3. Submit a pull request with a descriptive explanation of changes.

---

## License

This plugin is licensed under the **GPL v2.0 or later**. See the `LICENSE.md` file for full details.

---

## Support

For issues, feature requests, or feedback, please contact the TIAA Forum Admin Platform Sub-Team via https://tiaa-forum.org/contact.
