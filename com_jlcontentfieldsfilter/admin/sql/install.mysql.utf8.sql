CREATE TABLE IF NOT EXISTS `#__jlcontentfieldsfilter_data` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `catid` int(10) UNSIGNED NOT NULL,
        `filter_hash` varchar(32) NOT NULL,
        `filter` varchar(1000) NOT NULL,
        `meta_title` varchar(255) NOT NULL,
        `meta_desc` TEXT NOT NULL,
        `meta_keywords` TEXT NOT NULL,
        `state` int NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE KEY `filter_hash` (`filter_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;