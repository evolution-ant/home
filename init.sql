DROP TABLE IF EXISTS `jokes`;
CREATE TABLE `jokes` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `content` text(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `remark` text(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `codes`;
CREATE TABLE `codes` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `remark` text(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE codes add title varchar(255) COLLATE utf8mb4_unicode_ci default NULL;

DROP TABLE IF EXISTS `todos`;
CREATE TABLE `todos` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `remark` text(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE codes add title varchar(255) COLLATE utf8mb4_unicode_ci default NULL;


DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE types MODIFY name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型';


DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE tags MODIFY name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签';


DROP TABLE IF EXISTS `joke_tag`;
CREATE TABLE `joke_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `joke_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `code_tag`;
CREATE TABLE `code_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `code_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tag_todo`;
CREATE TABLE `tag_todo` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `todo_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
