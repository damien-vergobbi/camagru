CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  user_email VARCHAR(255) NOT NULL UNIQUE,
  user_name VARCHAR(128) NOT NULL UNIQUE,
  user_pass VARCHAR(255) NOT NULL,
  user_token VARCHAR(255) NOT NULL UNIQUE,
  user_verified TINYINT(1) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS posts (
  post_id INT AUTO_INCREMENT PRIMARY KEY,
  post_date DATETIME DEFAULT NOW(),
  post_user_id INT NOT NULL,
  post_image VARCHAR(255) NOT NULL,
  FOREIGN KEY (post_user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS comments (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  comment_user_id INT NOT NULL,
  comment_post_id INT NOT NULL,
  comment_text VARCHAR(255) NOT NULL,
  FOREIGN KEY (comment_user_id) REFERENCES users(user_id),
  FOREIGN KEY (comment_post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes (
  like_id INT AUTO_INCREMENT PRIMARY KEY,
  like_user_id INT NOT NULL,
  like_post_id INT NOT NULL,
  FOREIGN KEY (like_user_id) REFERENCES users(user_id),
  FOREIGN KEY (like_post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
  UNIQUE KEY user_post_unique (like_user_id, like_post_id)
);
