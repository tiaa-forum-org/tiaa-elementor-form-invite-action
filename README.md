# TIAA Elementor

Elementor Pro extensions for tiaa-forum.org.

This plugin supersedes the plugin formerly named `tiaa-elementor-forms-invite-action`.
**That plugin must be deactivated and removed before activating this one.**

---

## Features

### Discourse invite form action
Adds a custom Elementor Pro form action called `TIAA Invite` that triggers the Discourse invite
flow via the TIAA WordPress Plugin API. This was necessary because Elementor Pro's built-in
Webhook action can only return success or failure, whereas the TIAA invite API returns several
distinct outcomes (new invite sent, already registered, error states) that each require agit a
different response to the visitor.

When a form with the `tiaa` submit action is rendered, a lightweight JavaScript handler
(`assets/js/form-handler.js`) is enqueued. It intercepts the form submission, calls the TIAA
REST endpoint, and updates the page according to the API response — bypassing Elementor Pro's
standard Ajax handler entirely.

### Site-wide clickable Loop Grid cards
All Elementor Loop Item cards (`.e-loop-item`) across the site are made fully clickable via a
lightweight footer script (`loop-grid/clickable-cards.php`). A transparent anchor overlay covers
the entire card and links to the first `<a>` href found inside it — typically the post title link.
The script is harmless on pages with no Loop Grid widgets.

Pages currently benefiting from clickable cards:
- Homepage — Hot Topics preview and Forum Categories grids
- Hot Topics archive (`/hot-topics/`)
- Related Organizations (`/related-orgs/` or embedded on `/resources/`)
- Any future page using an Elementor Loop Grid widget

---

## Requirements

### WordPress
- WordPress: **6.0** or higher
- PHP: **7.4** or higher

### Required plugins
- [TIAA WordPress Plugin](https://tiaa-forum.org/) — provides the invite REST API
- [Elementor](https://elementor.com/)
- [Elementor Pro](https://elementor.com/pro)

> **Note on dependency enforcement:** Declaring hard plugin dependencies in code creates
> problems for upgrading any of the three plugins independently. Requirements are therefore
> documented here rather than enforced programmatically.

---

## Installation

1. Ensure **Elementor**, **Elementor Pro**, and the **TIAA WordPress Plugin** are installed and
   active.
2. Deactivate and delete the old `tiaa-elementor-forms-invite-action` plugin if present.
3. Clone or copy this plugin to `wp-content/plugins/tiaa-elementor/`.
4. Activate via **Plugins › Installed Plugins** in the WordPress dashboard.

---

## Usage

### Adding the TIAA Invite form action

1. Open Elementor and create or edit a form widget.
2. In the **Actions After Submit** setting, add the `TIAA Invite` action.
3. Map the **email** field to the visitor's email input.
4. Publish and test a submission.

The form handler script is enqueued automatically — only on pages where a form with the `tiaa`
action is rendered.

### Clickable Loop Grid cards

No configuration required. The overlay script runs automatically site-wide. Any page that
contains `.e-loop-item` elements gains fully clickable cards.

---

## File Structure

```
tiaa-elementor/
├── tiaa-elementor.php              # Plugin entry point — registers all features
├── loop-grid/
│   └── clickable-cards.php         # Site-wide clickable card overlay script
├── form-action/
│   └── tiaa-invite-action.php      # Elementor Pro form action class
├── assets/js/
│   └── form-handler.js             # Invite form REST handler (client-side)
├── README.md                       # This file
└── LICENSE.md
```

---

## Changelog

### 0.0.8 — 2026-04-18
- **Renamed** plugin from `tiaa-elementor-forms-invite-action` to `tiaa-elementor`.
  Plugin slug, text domain, and directory name all updated.
- **Extracted** clickable Loop Grid card script from the main plugin file into
  `loop-grid/clickable-cards.php`. Logic is unchanged; this is a structural refactor
  to keep Elementor extensions organised by feature as the plugin grows.
- Removed duplicate clickable card `add_action` from `tiaa-wpplugin` (was already
  superseded by the site-wide version in v0.0.6).
- Renamed global form-action registration function from
  `register_tiaa_custom_form_action` to `tiaa_elementor_register_invite_form_action`
  to avoid collision risk with other plugins.

### 0.0.7
- Previous stable release under the old plugin name.

### 0.0.6 — 2026-03-29
- Removed `is_front_page()` restriction from clickable Loop Grid card script.
  Cards are now clickable site-wide on any page containing `.e-loop-item` elements.

### 0.0.5
- Clickable cards restricted to the front page only.

---

## Development & Contributions

### Testing checklist
- **TIAA WordPress Plugin API** — invite form must send and receive responses correctly.
- **Elementor Pro** — `TIAA Invite` action must appear in the Actions After Submit dropdown.
- **Loop Grid pages** — verify clickable card overlay on homepage, Hot Topics archive, and
  Related Organizations; confirm no JavaScript errors on pages without Loop Grids.

### Contributions
1. Fork the repository.
2. Create a feature branch.
3. Submit a pull request with a clear description of changes.

---

## License

GPLv2 or later. See `LICENSE.md` for full terms.

---

## Support

Contact the TIAA Forum Admin Platform Sub-Team via https://tiaa-forum.org/contact.
