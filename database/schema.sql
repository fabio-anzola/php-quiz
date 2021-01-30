DROP DATABASE IF EXISTS quiz;

CREATE DATABASE quiz CHARACTER SET utf8 COLLATE utf8_general_ci;

USE quiz;

CREATE TABLE Subject (
	pk_subject_id int AUTO_INCREMENT PRIMARY KEY,
	subject varchar(255)
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

INSERT INTO `Subject`(`pk_subject_id`, `subject`) VALUES
(1, 'Math'),
(2, 'German'),
(3, 'English'),
(4, 'Physics'),
(5, 'Biology'),
(6, 'Science'),
(7, 'Physical Education'),
(8, 'Music'),
(9, 'Journalism'),
(10, 'Economics');

INSERT INTO `Question`(`pk_question_id`, `question`, `fk_pk_subject_id`) VALUES
(1, 'How much is 1 + 1?', 1),
(2, 'How much is (1 / 3) * 2?', 1),
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




