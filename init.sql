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

CREATE TABLE `books` (
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

CREATE TABLE `todos` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remark` text(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `item` text(2550) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `importance` int(11) DEFAULT 0,
    `status` tinyint(1) DEFAULT 1,
    `deadline_at` timestamp DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `child_todos` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `todo_id` int(11) DEFAULT 0,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` tinyint(1) DEFAULT 0,
    `deadline_at` timestamp NOT NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `types` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tags` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `joke_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `joke_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `code_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `code_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tag_todo` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `todo_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `book_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `book_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `collections` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `remark` text(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `collection_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `collection_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `words` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `translations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phonetic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `explains` varchar(2550) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tag_word` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `word_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sentences` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int(11) DEFAULT 0,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `translations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `explains` varchar(2550) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reading_times` tinyint(1) DEFAULT 0,
    `like` tinyint(1) DEFAULT 0,
    `importance` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sentence_tag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) DEFAULT 0,
    `sentence_id` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mindmaps` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `js_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `md_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wisesayings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `en_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
