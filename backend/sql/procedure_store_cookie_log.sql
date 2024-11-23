select '_________________________________________' as 'separatore';
DROP PROCEDURE IF EXISTS EventsLogCookieManage;

DELIMITER $$

CREATE PROCEDURE EventsLogCookieManage()
    LANGUAGE SQL
    MODIFIES SQL DATA
SQL SECURITY INVOKER
BEGIN

select 'crea log_cookie_storage' as azione;
CREATE TABLE IF NOT EXISTS `log_cookie_storage` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(255) DEFAULT NULL COMMENT 'Code',
    `name` varchar(255) DEFAULT NULL COMMENT 'Name',
    `value` text COMMENT 'Value',
    `expire_date_cookie` datetime DEFAULT NULL COMMENT 'Expire date cookie',
    `expire_date_server` datetime DEFAULT NULL COMMENT 'Expire date server',
    `created_at` datetime DEFAULT NULL COMMENT 'Created at',
    `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
    `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
    `created_by` int(11) DEFAULT NULL COMMENT 'Created by',
    `updated_by` int(11) DEFAULT NULL COMMENT 'Updated at',
    `deleted_by` int(11) DEFAULT NULL COMMENT 'Deleted at',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

select 'move old log_cookie into log_cookie_storage' as azione;
INSERT INTO log_cookie_storage SELECT * FROM log_cookie WHERE DATE(log_cookie.created_at) < DATE((NOW() - INTERVAL 183 DAY));
DELETE FROM log_cookie WHERE ID IN (SELECT ID FROM log_cookie_storage);

select 'delete old records (one year older)' as azione;
DELETE FROM log_cookie_storage WHERE DATE(log_cookie_storage.created_at) < DATE((NOW() - INTERVAL 365 DAY));

END $$

DELIMITER ;

CALL EventsLogCookieManage;

