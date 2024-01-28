CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  username VARCHAR(128) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  verification_token VARCHAR(255) NOT NULL UNIQUE,
  is_verified TINYINT(1) DEFAULT 0,
  email_notification TINYINT(1) DEFAULT 1
);

-- CREATE TABLE IF NOT EXISTS posts (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   created_at DATETIME DEFAULT NOW(),
--   user_id INT NOT NULL,
--   image VARCHAR(255) NOT NULL,
--   FOREIGN KEY (user_id) REFERENCES users(id)
-- );

-- CREATE TABLE IF NOT EXISTS comments (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   user_id INT NOT NULL,
--   post_id INT NOT NULL,
--   comment VARCHAR(255) NOT NULL,
--   FOREIGN KEY (user_id) REFERENCES users(id),
--   FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
-- );

-- CREATE TABLE IF NOT EXISTS likes (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   user_id INT NOT NULL,
--   post_id INT NOT NULL,
--   FOREIGN KEY (user_id) REFERENCES users(id),
--   FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
--   UNIQUE KEY user_post_unique (user_id, post_id)
-- );