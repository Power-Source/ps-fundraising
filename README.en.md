# PS Fundraising

[Deutsch](README.md) | **English**

[![Version](https://img.shields.io/badge/Version-1.0.2-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.2.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

### Your fundraiser. Your website. Your community.

With **PS Fundraising**, you can run fundraisers and crowdfunding campaigns directly on your WordPress or ClassicPress website. Create campaigns with funding goals and rewards, show progress, and manage contributions from your admin dashboard.

## What’s included

- **Fundraising and crowdfunding:** Create campaigns with funding goals, time limits, and reward tiers.
- **Contribution management:** Keep campaigns and contributions organized in one place.
- **Multiple payment options:** Use PayPal, Stripe, and manual payments, depending on your configuration.
- **Bank transfer details:** Configure the account holder, IBAN, BIC, bank name, and a payment reference prefix.
- **Pending transfers:** Manual payments start with a **Pending** status and can be marked complete after the funds arrive.
- **SEPA QR code:** For EUR transfers, an EPC QR code can be generated locally on your website.
- **Flexible embedding:** Add campaigns with shortcodes, widgets, or theme templates.
- **BuddyPress integration:** Bring fundraising into your community.
- **Responsive campaign layout:** Campaign stats sit side by side on larger screens and adapt to smaller screens.
- **Customizable appearance:** Adjust colors, surfaces, borders, and corner radius with CSS variables or custom CSS.

> Payment providers may charge their own transaction fees. PS Fundraising does not charge a platform fee.

## Quick start

1. Install and activate PS Fundraising.
2. In your admin dashboard, open **Fundraising → Settings**.
3. Enable and configure the payment gateways you want to use.
4. For bank transfers, enter your bank details and payment reference prefix in the **Manual Transfer** settings.
5. Create a campaign under **Fundraising** and add it to a page or post.

### Setting up bank transfers

In the manual gateway settings, you can enter the account holder and IBAN; BIC and bank name are optional. A unique reference for each contribution is automatically added to the payment reference. When the account holder, a valid IBAN, and a EUR amount are available, the confirmation page also displays an EPC/SEPA QR code.

After the transfer arrives, mark the contribution as **Complete** in the admin area. Until then, it remains **Pending** and is not counted as completed income.

## Customize the design

All campaigns use the same modern, responsive layout. Under **Fundraising → Settings → Presentation → Custom CSS**, customize the accent color, surfaces, text, borders, and corner radius:

```css
.wdf-modern {
    --wdf-modern-primary: #176b5b;
    --wdf-modern-primary-hover: #105347;
    --wdf-modern-surface: #ffffff;
    --wdf-modern-muted-surface: #f3f7f6;
    --wdf-modern-text: #172622;
    --wdf-modern-muted: #5b6d67;
    --wdf-modern-border: #dce7e3;
    --wdf-modern-radius: 16px;
}
```

The previous color styles are no longer offered as separate campaign layouts. The global CSS field remains available for your own adjustments.

## Shortcodes

Display a campaign by its ID:

```text
[fundraiser_panel id="123"]
```

Display a campaign’s contributions:

```text
[pledges_panel id="123"]
```

Add a simple donation button:

```text
[donate_button]
```

Available widgets can be added from **Appearance → Widgets**. The output and appearance may vary depending on your theme and settings.

## Requirements

- PHP **8.0 or later**
- WordPress **4.9 or later** (tested up to WordPress 7.2.4)
- ClassicPress **2.7 or later**
- PHP **mbstring** extension for local EPC QR code generation
- A configured PayPal or Stripe account for online payments

## Development and tests

The plugin is modular: payment gateways are in `lib/gateways/`, and widgets are in `lib/widgets/`. You can run the payment-flow test harness with:

```bash
php tests/payment-gateway-flows.php
```

## Contributing

Bug reports, ideas, and contributions are welcome:

- [Repository and source code](https://github.com/Power-Source/ps-fundraising)
- [Issues and feature requests](https://github.com/Power-Source/ps-fundraising/issues)

PS Fundraising is free software licensed under **GPL-2.0-or-later**.
