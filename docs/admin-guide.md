# Admin Guide

This is like the admin help/wiki page (admin/wiki/admin-guide.php) but I added it here too so its part of the documentation. 

## Logging In

The admin panel uses its own separate login, isolated from customer accounts:

/admin/login.php

Admin accounts are distinguished by the role column in the users table. The customer facing login.php explicitly blocks admin accounts from logging in there, redirecting them to the admin login instead.

## Dashboard

admin/dashboard.php is the landing page after logging in. It shows:
- Total orders, pending orders, pending player requests, and registered customer counts
- A bar chart of total sales that is broken down per player that is offered and then one combined custom builds bar representing all Design Your Own orders together
- A total revenue figure pulled from the full amount customers were actually charged, including tax and shipping.

## Manage Players

admin/manage-players.php allows you to create, edit and delete players.

**Adding a new player:**
1. Upload the player's image to images/players/ through the file manager first. The form takes the file name not a picture drag and drop, I did this because it does the job but is simpler to implement
2. Fill in the name, team, jersey number, position, category, price, and description
3. Type the exact image filename (make sure its identical as it is case sensitive)
4. Then when you save the new player automatically receives the same 6 standard options every other player has (3 frame colors, 3 sizes) and is available in the shop.

**Editing or removing:** Edit changes any field on an existing player and delete removes them (the player will only be deleted if no one ordered a frame of them to avoid orphaning historical order data). If they have data but you want them removed then just uncheck "Visible on the shop page" instead, that hides the player from the public site.

## Manage Orders

admin/manage-orders.php lists every order with the customer's info, a list of what was purchased, the shipping address, and a status dropdown that saves immediately on change.

## Manage Users

admin/manage-users.php lists every registered customer, their order count, and an enable/disable toggle where disabling sets is_active to 0, which the customer login checks and blocks. An admin can't disable their own account through this page.

## Player Requests

admin/manage-requests.php reviews player suggestions submitted through the public request a player form, with a status dropdown. Approving a request does not automatically add the player, you have to manually add them, the approve is more for tracking.

## Site Theme

admin/template-switcher.php switches the site wide color theme (Classic, Night, Gold) by updating a single row in the site_settings table which includes/header.php reads on every page load.

## Updating Videos

Video content is embedded from YouTube as Unlisted videos rather than hosted directly, since myweb.cs.uwindsor.ca didn't load .mp4 files correctly when hosted directly on that server. To update a video, upload the replacement to YouTube as Unlisted, copy its video ID from the URL, and swap it into the relevant page's iframe src tag (a slightly more step-by-step format can be found at the bottom of admin/wiki/admin-guide.php)

## Database Guide

Check admin/wiki/database-guide.php for a breakdown of the tables and how they connect.
