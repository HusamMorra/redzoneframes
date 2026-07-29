# Installation Guide

Instructions for setting up Red Zone Frames on a different server.

## Requirements

- A web server capable of running PHP with a MySQL database
- A hosting control panel with phpMyAdmin access. I built this project and tested on DirectAdmin, specifically myweb.cs.uwindsor.ca, but any standard PHP and MySQL host should work

## Step 1: Get the files

Download or clone this repository, then upload the entire folder structure to your server's web root, preserving the structure exactly:

redzoneframes/
- admin/
  - includes/ (auth-check.php, admin-nav.php)
  - wiki/ (admin-guide.php, database-guide.php)
  - dashboard.php, manage-players.php, manage-orders.php, manage-users.php, manage-requests.php, template-switcher.php, player-form.php, login.php, logout.php
- css/
  - styles.css
- images/
  - players/ (20 player frame images)
  - examplenameplate.png
- includes/
  - db.example.php (copy this to db.php and fill in real credentials)
  - header.php, footer.php
- js/
  - main.js, designer.js
- sql/
  - schema.sql
- wiki/
  - index.php, how-to-order.php, how-to-design.php, sizing-help.php, account-help.php, shipping-faq.php
- index.php, shop.php, product.php, design-your-own.php, cart.php, checkout.php, order-confirmation.php, login.php, register.php, logout.php, my-account.php, edit-profile.php, about.php, contact.php, size-guide.php, shipping-returns.php, privacy-policy.php, request-player.php

## Step 2: Create the database

1. In your hosting control panel create a new MySQL database. In DirectAdmin, you can go to account manager, then MySQL Management, then create new database
2. Note the exact database name, username, and host provided.
3. Open phpMyAdmin, select the new database, click the Import tab
4. Pick sql/schema.sql from this repository and click Go
5. Confirm the database now has 10 tables, with the players table containing 20 rows

## Step 3: Configure the database connection

1. Copy includes/db.example.php to a new file named includes/db.php
2. Open includes/db.php and replace the placeholder values with your real credentials:
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');

I left my db.php out from this repository since it holds my real credentials.

## Step 4: Create the first admin account

Create the admin account manually

1. Create a temporary file in the project root:

$plainPassword = "Yourpassword123!";
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo "Password: " . $plainPassword . "<br>";
echo "Hash: " . $hash;

2. Open that file in a browser and copy the printed hash
3. In phpMyAdmin, run:
INSERT INTO users (first_name, last_name, email, password_hash, role)
VALUES ('Admin', 'User', 'your-admin-email@example.com', 'PASTE_HASH_HERE', 'admin');

4. Delete the temporary file after copying the hash.

## Step 5: Upload player images

Upload the 20 player images to images/players/, matching the filenames referenced in the players table exactly, including capitalization.

## Step 6: Test it

1. Visit the homepage and confirm the catalog loads correctly
2. Register a customer account and place a test order
3. Log into /admin/login.php with the account created in Step 4 and confirm the dashboard loads
