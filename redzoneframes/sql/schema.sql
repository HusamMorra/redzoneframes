CREATE DATABASE IF NOT EXISTS morrah_red_zone_frames;
USE morrah_red_zone_frames;

-- this has the regular customers and admin accounts and the role column is how the login system knows where to send someone after they log in because I wanted to seperate a customer and admin logging in
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- passwords are hashed with password_hash(), never stored as plain text
    phone VARCHAR(20),
    street_address VARCHAR(150), -- split the address into separate fields instead of one big text box, easier to work with and looks more like a real checkout form
    city VARCHAR(100),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    role ENUM('customer','admin') DEFAULT 'customer',
    is_active TINYINT(1) DEFAULT 1, -- admin can use this to disable an account
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- this is the actual product catalog and one row = one player frame
-- category is what splits the shop page into active roster vs retro legends
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(100) NOT NULL,
    team VARCHAR(100) NOT NULL,
    jersey_number INT NOT NULL,
    position VARCHAR(50),
    category ENUM('active','legend') DEFAULT 'active',
    base_price DECIMAL(6,2) NOT NULL,
    description TEXT,
    image_filename VARCHAR(150),
    is_active TINYINT(1) DEFAULT 1, -- lets admin hide a player without actually deleting the row if it goes out of stock or something
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- every player has two options frame color + size so option_type is what tells the two apart in this same table
CREATE TABLE player_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    option_type VARCHAR(30) NOT NULL,
    option_value VARCHAR(50) NOT NULL,
    price_modifier DECIMAL(6,2) DEFAULT 0.00, -- how much extra this option costs on top of base_price
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
);

-- one row = one order placed
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    total_amount DECIMAL(8,2) NOT NULL,
    street_address VARCHAR(150), -- same split as the users table, keeps shipping address consistent across the site
    city VARCHAR(100),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- one row = one item inside an order
-- player_id can be null cause design your own orders arent tied to a real catalog player
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    player_id INT NULL,
    frame_color VARCHAR(50),
    frame_size VARCHAR(50),
    custom_player_name VARCHAR(100), -- only gets filled in for design-your-own orders, this is used pretty much to display your order on your account
    custom_number VARCHAR(10),
    add_signature TINYINT(1) DEFAULT 0,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id)
);

-- reviews left by customers after ordering
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- i was initially gonna have a contact form but i didnt know if the page would count as static anymore so i didnt add it but its in the database in case i wanna add that feature later but its not rlly used right now
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- stores player suggestions submitted through the request a player page and it shows up on the admin page so they can reviews these and marks them approved or declined
CREATE TABLE player_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requested_name VARCHAR(100) NOT NULL,
    requested_team VARCHAR(100),
    requester_email VARCHAR(100) NOT NULL,
    reason TEXT,
    status ENUM('pending','approved','declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- holds a logged in users cart when they log out so it can be restored next time they log back in, or else logging out would just wipe your cart
CREATE TABLE saved_carts (
    user_id INT PRIMARY KEY,
    cart_data TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- just a config table basically, right now it only holds which theme is active and admin changes this from the template switcher page
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value VARCHAR(100) NOT NULL
);

INSERT INTO site_settings (setting_key, setting_value) VALUES ('active_theme', 'classic-theme');

-- for the active roster and retro legends, this includes the ones included when i first built the site but as you add players in admin panel theyll be in added in the database but these are the base 20
-- active roster
INSERT INTO players (player_name, team, jersey_number, position, category, base_price, description, image_filename) VALUES
('Josh Allen', 'Buffalo Bills', 17, 'QB', 'active', 69.99, 'The dual-threat QB leading Buffalo, known for bulldozing through defenders as easily as he throws deep.', 'Allen.png'),
('Joe Burrow', 'Cincinnati Bengals', 9, 'QB', 'active', 69.99, 'Cool under pressure and deadly accurate, Burrow has turned the Bengals into real contendors.', 'Burrow.png'),
('Myles Garrett', 'Los Angeles Rams', 95, 'DE', 'active', 64.99, 'Elite edge rusher and reigning sack king, now terrorizing quarterbacks for the Rams.', 'Garrett.png'),
('Jahmyr Gibbs', 'Detroit Lions', 26, 'RB', 'active', 49.99, 'Explosive, big-play running back who has become the engine of Detroit''s offense.', 'Gibbs.png'),
('Jared Goff', 'Detroit Lions', 16, 'QB', 'active', 64.99, 'Steady, high-volume passer who has turned Detroit into an NFC powerhouse.', 'Goff.png'),
('Justin Herbert', 'Los Angeles Chargers', 10, 'QB', 'active', 59.99, 'One of the strongest arms in the league, quietly one of the NFL''s most efficient quarterbacks.', 'Herbert.png'),
('Lamar Jackson', 'Baltimore Ravens', 8, 'QB', 'active', 69.99, 'Former unanimous MVP who redefined what a quarterback can do with his legs.', 'Jackson.png'),
('Justin Jefferson', 'Minnesota Vikings', 18, 'WR', 'active', 64.99, 'Widely regarded as the best route runner in football and a nightmare matchup for any defense.', 'Jefferson.png'),
('Patrick Mahomes', 'Kansas City Chiefs', 15, 'QB', 'active', 69.99, 'Two-time MVP and the engine behind the Chiefs dynasty.', 'Mahomes.png'),
('Drake Maye', 'New England Patriots', 10, 'QB', 'active', 64.99, 'The Patriots'' new franchise quarterback, building chemistry with a retooled receiving corps.', 'Maye.png'),
('Micah Parsons', 'Green Bay Packers', 11, 'LB', 'active', 59.99, 'All-Pro pass rusher who became the second highest-paid non-QB in NFL history after a blockbuster trade to Green Bay.', 'Parsons.png'),
('Brock Purdy', 'San Francisco 49ers', 13, 'QB', 'active', 49.99, 'The last pick of his draft class turned franchise quarterback for the 49ers.', 'Purdy.png'),
('Aaron Rodgers', 'Pittsburgh Steelers', 8, 'QB', 'active', 59.99, 'Four-time MVP playing one of the final seasons of his Hall of Fame career in Pittsburgh.', 'Rodgers.png'),
('Jaxon Smith-Njigba', 'Seattle Seahawks', 11, 'WR', 'active', 49.99, 'Breakout receiver who has become Seattle''s go-to target.', 'Smith-Njigba.png'),
('Matthew Stafford', 'Los Angeles Rams', 9, 'QB', 'active', 69.99, 'Veteran gunslinger who brought a championship to Los Angeles.', 'Stafford.png'),
('Saquon Barkley', 'Philadelphia Eagles', 26, 'RB', 'active', 49.99, 'Explosive, record-breaking running back who powered the Eagles to a Super Bowl title.', 'Barkley.png');

-- retro legends
INSERT INTO players (player_name, team, jersey_number, position, category, base_price, description, image_filename) VALUES
('Tom Brady', 'New England Patriots', 12, 'QB', 'legend', 74.99, 'Seven-time Super Bowl champion and widely considered the greatest quarterback of all time.', 'Brady.png'),
('Peyton Manning', 'Indianapolis Colts', 18, 'QB', 'legend', 74.99, 'Five-time MVP known for his elite command of the offense at the line of scrimmage.', 'Manning.png'),
('Randy Moss', 'Minnesota Vikings', 84, 'WR', 'legend', 74.99, 'One of the most dominant deep-ball receivers the league has ever seen.', 'Moss.png'),
('Ray Lewis', 'Baltimore Ravens', 52, 'LB', 'legend', 74.99, 'Hall of Fame middle linebacker and one of the most feared defenders to ever play the position.', 'Lewis.png');

-- giving every player 3 frame color options and 3 size options
-- this insert into select trick i learnt applies the same 6 options to every player in one shot so i dont have to write out 100+ separate insert lines manually
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'frame_color', 'Black', 0.00 FROM players;
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'frame_color', 'Walnut Brown', 5.00 FROM players;
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'frame_color', 'Team Color Accent', 8.00 FROM players;
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'size', '8x10 in', 0.00 FROM players;
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'size', '11x14 in', 15.00 FROM players;
INSERT INTO player_options (player_id, option_type, option_value, price_modifier)
SELECT id, 'size', '16x20 in', 30.00 FROM players;