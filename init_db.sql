CREATE DATABASE testDB;
CREATE USER 'phpUser'@'%' IDENTIFIED WITH mysql_native_password BY 'adminGeniat%2023';
GRANT ALL ON testDB.* TO 'phpUser'@'%';
USE testDB;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `role` varchar(60) DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `token_exp` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `users` (`name`, `lastname` ,`email`, `password`, `role`) VALUES
('Administrador', '', 'admin', '$2y$10$vGD9GFuRJz/F39qTgjrFPOTHYVzS9IOgFh6uWtDVFxCuhqgxBtN42', 'alto');

