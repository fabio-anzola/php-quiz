DROP DATABASE IF EXISTS quiz;

CREATE DATABASE quiz CHARACTER SET utf8 COLLATE utf8_general_ci;

USE quiz;

CREATE TABLE Subject (
	pk_subject_id int AUTO_INCREMENT PRIMARY KEY,
	subject varchar(255),
	fk_pk_subject_id int,
	FOREIGN KEY (fk_pk_subject_id) REFERENCES Subject(pk_subject_id) ON DELETE CASCADE
);

CREATE TABLE Question (
	pk_question_id int AUTO_INCREMENT PRIMARY KEY,
	question varchar(255),
	fk_pk_subject_id int,
	FOREIGN KEY (fk_pk_subject_id) REFERENCES Subject(pk_subject_id) ON DELETE CASCADE
);

CREATE TABLE Answer (
	pk_answer_id int AUTO_INCREMENT PRIMARY KEY,
	answer varchar(255),
	correct boolean,
	fk_pk_question_id int,
	FOREIGN KEY (fk_pk_question_id) REFERENCES Question(pk_question_id) ON DELETE CASCADE
);

CREATE TABLE User (
	pk_user_id int AUTO_INCREMENT PRIMARY KEY,
	firstname varchar(255),
	lastname varchar(255),
	username varchar(255),
	passphrase varchar(255) 
);


INSERT INTO `Subject`(`pk_subject_id`, `subject`, `fk_pk_subject_id`) VALUES
(1, 'Math', NULL),
(2, 'German', NULL),
(3, 'English', NULL),
(4, 'Physics', NULL),
(5, 'Biology', NULL),
(6, 'Science', NULL),
(7, 'Physical Education', NULL),
(8, 'Music', NULL),
(9, 'Journalism', NULL),
(10, 'Economics', NULL),
(11, 'Statistic', 1);

INSERT INTO `Question`(`pk_question_id`, `question`, `fk_pk_subject_id`) VALUES
(1, 'How much is 1 + 1?', 1),
(2, 'How much is (1 / 3) * 2?', 11),
(3, 'How much is 1 / 0?', 1),
(4, 'How much is 2 ^ 6?', 1);

INSERT INTO `Answer`(`pk_answer_id`, `answer`, `correct`, `fk_pk_question_id`) VALUES
(1, '2', true, 1),
(2, '3', false, 1),
(3, '4', false, 1),
(4, '5', false, 1),
(5, '0.666...', true, 2),
(6, '1', false, 2),
(7, '3', false, 2),
(8, '2/3', true, 2),
(9, '62', false, 3),
(10, 'NaN', true, 3),
(11, '3', false, 3),
(12, '0', false, 3),
(13, '64', true, 4),
(14, '0', false, 4),
(15, '100', false, 4),
(16, '256', false, 4);

INSERT INTO `User`(`pk_user_id`, `firstname`, `lastname`, `username`, `passphrase`) VALUES
(1, 'Fabio', 'Anzola', 'fabioanzola', MD5('junioradmin'));