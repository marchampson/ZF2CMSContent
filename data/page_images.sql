CREATE TABLE `page_images` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `page_id` int(11) NOT NULL,
 `alt_text` varchar(100) COLLATE utf8_bin DEFAULT NULL,
 `position` smallint(6) DEFAULT NULL,
 `image` varchar(100) COLLATE utf8_bin DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
