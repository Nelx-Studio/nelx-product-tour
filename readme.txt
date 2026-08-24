=== Nelx Product Tour ===
Contributors: nelxstudio
Tags: elementor, onboarding, product tour, guided tour, dashboard
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.17
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create modern guided product tours directly inside Elementor templates and pages.

== Description ==

Nelx Product Tour adds a "Nelx Product Tour" setup section to Elementor widgets and layout elements. It appears under Content for widgets and under Layout for sections, columns, Containers, and Grid Containers so Elementor keeps its native tab order. Turn any element into a tour step, write a title and description, choose a target mode, and let logged-in users walk through the page.

The plugin also registers two visible Elementor widgets under the "Nelx Product Tour" category: "Product Tour Launcher" for an in-page launcher and "Floating Tour Replay Button" for an independently styled fixed replay button.

Tours run on frontend Elementor-rendered pages only. In the Elementor editor, enabled steps are outlined, and the optional Tour Card Preview switch shows a lightweight live card for styling without starting a real tour.

Features:

* Elementor panel controls for widgets, sections, columns, and containers, including Grid containers.
* CSS selector targeting for custom widget IDs and classes.
* Ordered steps with top-to-bottom fallback sorting.
* Next, back, skip, progress dots, overlay spotlight, auto-start, and replay button.
* Native Elementor Style tab controls for tour card styling, typography, borders, box shadows, button hover states and transforms, and target highlights. The independent Floating Tour Replay Button includes its original gradient default, typography, borders, box shadows, hover styling, hover lift, close-button styling, close-button padding, and X/Y positioning controls.
* Desktop-safe tour card layout that prevents theme/global CSS from collapsing the popover content into a narrow column. Back/Next navigation defaults inside the card on desktop, with an optional outside-card position; mobile always keeps navigation inside.
* Dismissible Floating Tour Replay Button widget with a per-page dismissal state; use the Product Tour Launcher widget to replay after dismissal. The floating widget is fixed at the viewport level so it does not participate in the surrounding Elementor header or container layout.
* Completion remembered in both user meta and browser localStorage.
* Logged-in users only.
* Bundled Driver.js 1.8.0 assets with no runtime CDN dependency.
* Translation-ready and standalone.

== Installation ==

1. Upload the `nelx-product-tour` folder to `/wp-content/plugins/`.
2. Activate Nelx Product Tour.
3. Edit an Elementor page or template.
4. Open a widget, section, column, or container.
5. Open Content for a widget, or Layout for a section, column, Container, or Grid Container, then select Nelx Product Tour.
6. Enable Tour Step and fill in the step content.
7. Optional: add the Product Tour Launcher widget or Floating Tour Replay Button widget from the Nelx Product Tour category.

== Frequently Asked Questions ==

= Does this show tours in wp-admin? =

No. Tours display on frontend Elementor-rendered pages. The Elementor editor includes preview support while editing.

= Can guests see tours? =

No. Tours are only shown to logged-in WordPress users.

= How are steps ordered? =

Use Step Order for exact control. When steps share the same order, the runtime falls back to visual top-to-bottom, then left-to-right order.

= Can I target a specific inner widget element? =

Yes. Set Target Mode to Custom CSS Selector and enter a class or ID such as `.dashboard-card` or `#task-summary`.

= What is the Elementor widget called? =

The visible widget is called "Product Tour Launcher". It starts or replays a tour by Tour ID.

== Changelog ==

= 1.0.17 =
* Made desktop Inside Card navigation use the exact same native Driver.js flex-footer blueprint as mobile.
* Kept the step indicator and Back/Next/Done controls as normal-flow footer siblings, with no desktop coordinates or breakpoint logic.
* Kept Outside Card navigation as a separate card-relative layout below the card.

= 1.0.15 =
* Preserved Elementor's native tab order by placing Product Tour controls in Layout for layout elements and Content for widgets.
* Replaced dynamic tour navigation positioning with card-relative CSS: inside uses the native footer; outside is always below the card.
* Removed navigation scroll/resize listeners and animation-frame coordinate calculations.

= 1.0.14 =
* Rebuilt the Elementor control injection from the 1.0.10 baseline.
* Moved Nelx Product Tour setup from the Advanced tab to the Content tab for widgets, sections, columns, containers, and Grid containers.
* Added eight independent conditional Style sections: Tour Card, Title & Description, Step Indicator, Tour Buttons Position, Tour Skip Button, Tour Back Button, Tour Next/Done Button, and Target Highlight.
* Kept Show Tour Card Preview inside the Tour Card section.
* Registered Content and Style sections once per Elementor element using the documented before-section injection hook and an object-level duplicate guard.
* Scoped native typography, border, and box-shadow group-control selectors to an intentionally absent child so generated Elementor CSS cannot affect the host element or header.

= 1.0.6 =
* Made desktop Inside Card navigation use the same footer structure and explicit static positioning as mobile on the real frontend tour card.
* Made Outside Card navigation use viewport-fixed coordinates below the card and clamp horizontally inside the viewport so right-edge cards cannot push Back/Next off-screen.

= 1.0.5 =
* Fixed frontend Back/Next navigation so Inside Card mode is consistently applied to the real Driver.js tour card, not only the editor preview.
* Improved Outside Card navigation so Back/Next buttons remain below the tour card and automatically realign within the viewport when the card is close to the right edge. Mobile continues to keep navigation inside the card.
* Prevented Product Tour typography, border, box-shadow, and color controls from generating Elementor styling on the parent widget/container. These values are now treated as runtime tour styling and applied to the actual Driver.js card, buttons, highlight, and floating replay UI.

= 1.0.4 =
* Fixed a PHP fatal error in the floating replay button border-radius control registration.
* Restored tour controls on widget-level elements such as Button, Icon, and Image while retaining Container/Grid support.
* Removed redundant container-specific control hook to avoid duplicate registration paths.

= 1.0.3 =
* Restored the floating replay button's original gradient background and hover lift as defaults.
* Added hover background, text, border, shadow, and transform/lift controls for tour buttons and the floating replay button.
* Added configurable desktop Back/Next navigation placement: inside the card by default or outside the card; mobile always keeps navigation inside.
* Expanded Product Tour Launcher widget styling with typography, border, box shadow, hover controls, and hover-lift transform controls.
* Added an editor-only Tour Card Preview switch with lightweight live synchronization of the selected element's tour card settings.

= 1.0.2 =
* Fixed Elementor editor fatal error caused by reading element settings while the controls stack was still initializing.

= 1.0.1 =
* Added tour step support to Elementor containers and Grid containers.
* Moved tour styling controls to Elementor's Style tab and replaced generic shadow input controls with native Elementor typography, border, box-shadow, slider, and dimensions controls.
* Added styling controls for the tour card, title, description, step indicator, Skip/Back/Next buttons, target highlight, and floating replay button.
* Fixed desktop tour card content layout so title, description, and footer remain properly stacked.
* Added a close button to the floating replay button with page-specific dismissal persistence.

= 1.0.0 =
* Initial release.
