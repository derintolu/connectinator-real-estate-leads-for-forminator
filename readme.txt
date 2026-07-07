=== Lead Sync for Follow Up Boss & Forminator ===
Contributors: derintolu
Tags: forminator, follow up boss, crm, real estate, leads
Requires at least: 6.2
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Follow Up Boss as a native Forminator integration. Connect with your API key and send form entries to Follow Up Boss as leads.

== Description ==

Follow Up Boss appears right inside Forminator's own Integrations screen — alongside Mailchimp, HubSpot and the rest. Connect it once with your Follow Up Boss API key, then add it to any form and map your fields. On submission the contact is created or updated in Follow Up Boss (via the Events API) with lead routing, dedup and source tracking handled by Follow Up Boss.

Features:

* Native Forminator integration — connect under Forminator → Integrations, configure per form under the form's "Connect apps" tab.
* Field mapping — map First Name, Last Name, Email and Phone to your form fields; every submitted field is included in the event message.
* Configurable source, system, event type, and tags.
* Works with programmatically created entries: other plugins can push an entry with `do_action( 'forminator_fub/push', $form_id, $entry_id )` (auto-detects fields), which is required for entries added via `Forminator_API::add_form_entry()` since those fire no Forminator hooks.
* Developer filter `forminator_fub/event` to customize the payload.
* API key can be encrypted at rest (Forminator handles it) and never needs to touch code.

== External services ==

This plugin sends form submission data (name, email, phone, and the submitted field values) to Follow Up Boss (https://www.followupboss.com/) via its API (https://api.followupboss.com) when you connect the integration and enable it on a form. Data is transmitted only for forms you configure. See the Follow Up Boss Privacy Policy: https://www.followupboss.com/privacy-policy/

== Installation ==

1. Install and activate Forminator and this plugin.
2. Go to **Forminator → Integrations**, find **Follow Up Boss**, click **Connect**, and paste your API key (Follow Up Boss → Admin → API).
3. Edit a form, open **Integrations / Connect apps**, add Follow Up Boss, and map your fields.

== Frequently Asked Questions ==

= Where do I get an API key? =
In Follow Up Boss, go to Admin → API and create a key.

= Does it work with entries created by code? =
Yes. Any plugin can trigger a push with `do_action( 'forminator_fub/push', $form_id, $entry_id )`. This is required for entries added via `Forminator_API::add_form_entry()`, which do not fire Forminator's submission hooks.

== Screenshots ==

1. Follow Up Boss appears as a native connected app in Forminator → Integrations.
2. The connect screen — paste your Follow Up Boss API key and set the default lead source, system name, event type, and tags.

== Changelog ==

= 1.0.0 =
* Initial release.
