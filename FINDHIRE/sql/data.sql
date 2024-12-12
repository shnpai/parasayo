CREATE TABLE user_accounts (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255),
	first_name VARCHAR(255),
	last_name VARCHAR(255),
	password TEXT,
	is_admin TINYINT(1) NOT NULL DEFAULT 0,
	is_suspended TINYINT(1) NOT NULL DEFAULT 0,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    posted_by INT NOT NULL,
    date_posted DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES user_accounts(user_id)
);

CREATE TABLE job_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    position_applied VARCHAR(255) NOT NULL,
    resume_path TEXT NOT NULL,
    date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	status VARCHAR(255) DEFAULT 'pending',
    FOREIGN KEY (job_id) REFERENCES job_posts(id),
    FOREIGN KEY (applicant_id) REFERENCES user_accounts(user_id)
);



CREATE TABLE inquiries ( -- A typical user can only create inquiries
	inquiry_id INT AUTO_INCREMENT PRIMARY KEY,
	description TEXT,
	user_id INT,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE replies ( -- An admin can only reply to inquiries
	reply_id INT AUTO_INCREMENT PRIMARY KEY,
	description TEXT,
	inquiry_id INT,
	user_id INT,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'read') DEFAULT 'sent',
    FOREIGN KEY (sender_id) REFERENCES user_accounts(user_id),
    FOREIGN KEY (receiver_id) REFERENCES user_accounts(user_id)
);


CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    posted_by INT NOT NULL,
    date_posted DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES user_accounts(user_id)
);


# superadmin account

# Username: superadmin
# Password: $2y$10$Y4b1UT2wJyp8XNykJjX7