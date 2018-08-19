CREATE TABLE users( 
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		email VARCHAR(50),
		password VARCHAR(50)
		);
INSERT INTO users (email, password) VALUE ('arthur@guide.com', SHA1('arthur@guide.comBathrobe'));
INSERT INTO users (email, password) VALUE ('ford@guide.com', SHA1('ford@guide.comciao'));
