# Front-End Documentation

This document explains the front-end structure of my website, what pages exist, how they're organized, and the design decisions behind the look and feel. Anytime you see a file name, you can place it after http://morrah.myweb.cs.uwindsor.ca/redzoneframes/ to see the live site.

## Design

**Colors:** the site has 3 themes (Classic Theme, Night Theme, Gold Theme), each built using CSS custom properties (variables), so the entire site re-skins when you switch it in the admin panel. The base colors are navy blue and red, matching the NFL branding. The other two were just different colors I thought go together and matcha football theme.

**Fonts:** Anton (bold, condensed) for headings, Inter (clean, readable) for body text, both loaded from Google Fonts.

**Layout:** most pages share a .container and .section wrapper for consistent spacing. The homepage alternates light backgrounds between sections so it isn't one long flat stretch of the same color. 

**Responsive design:** the main navigation collapses into a hamburger menu. Product grids and forms adjust their column counts at several breakpoints (900px, 800px, 600px) to stay usable on mobile devices. This took a lot of testing with different devices but these were the final numbers that worked for me.

**CSS techniques:**
- Hover flip product cards, built with pure CSS 3D transforms, no JavaScript needed. Got the general idea from this: https://www.html-code-generator.com/css/card-hover-flip-animation
- A CSS star rating widget, using radio buttons and the ~ sibling selector. I got the general technique from: https://iamkate.com/code/star-rating-widget/, but I used plain Unicode star characters instead of an SVG image for simplicity
- CSS transitions applied during theme switching so color changes fade smoothly instead of changing instantly

## Public Pages

### shopping flow
- index.php: homepage with a hero section, category teasers, a preview grid of players, customer testimonials, and a "Request a Player" call to action
- shop.php: full catalog with Active Roster and Retro Legends filter tabs
- product.php: individual product page with a live price calculator (frame color, size, quantity) and a customer reviews section
- design-your-own.php: fully custom frame builder (name, number, team, frame color, size, optional engraved nameplate), also with a live price calculator
- cart.php: session based shopping cart with quantity updates and allows removal
- checkout.php: shipping address form with tax, shipping, and total breakdown, places the order
- order-confirmation.php: confirmation page shown after an order is placed

### Account system
- register.php and login.php: account creation and login. If someone tries to check out while logged out, they're redirected back to checkout automatically after logging in
- my-account.php: account details and full order history, with a breakdown of what was purchased, and review links are here
- edit-profile.php: update name, phone number, and mailing address (not email though)
- review-submit.php: leave a rating and comment, restricted to players the customer has actually ordered, also no reviews on custom orders

### Static pages
- about.php, contact.php, size-guide.php, shipping-returns.php, privacy-policy.php: plain pages with no database interaction

### Help wiki
- wiki/index.php: hub page linking to all 5 help topics
- wiki/how-to-order.php, how-to-design.php, sizing-help.php, account-help.php, shipping-faq.php: step by step guides. The first two include video walkthroughs
- Several pages link directly to their most relevant wiki topic: design-your-own links to how-to-design, checkout links to both how-to-order and shipping-faq, register, login, and my-account link to account-help, and product links to sizing-help

### Other
- request-player.php: lets visitors suggest a player not currently in the catalog

## Admin Pages

The full admin interface lives under /admin/, but its behind its own separate login. Check docs/admin-guide.md for more info on this.

## Image Credits

The 20 player frame graphics used throughout the catalog are original artwork generated using ChatGPT, not real photographs or licensed jersey imagery. I got approval to do this from Dr. Kobti.
