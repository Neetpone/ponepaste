drop database if exists ponepaste;
create database ponepaste;
use ponepaste;

DELIMITER $$

CREATE PROCEDURE ExecuteScript()
BEGIN
    DECLARE exit handler for sqlexception
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    create table admin
    (
        id   int auto_increment
            primary key,
        user varchar(250) null,
        pass varchar(250) null
    );

    create table admin_history
    (
        id        int auto_increment
            primary key,
        last_date varchar(255) null,
        ip        varchar(255) null
    );

    create table ads
    (
        id       int auto_increment
            primary key,
        text_ads longtext null,
        ads_1    longtext null,
        ads_2    longtext null
    );

    create table ban_user
    (
        id        int auto_increment
            primary key,
        ip        varchar(16) not null,
        last_date varchar(15) not null
    );

    create table page_view
    (
        id     int auto_increment
            primary key,
        date   varchar(255) null,
        tpage  varchar(255) null,
        tvisit varchar(255) null
    );

    create table pages
    (
        id           int auto_increment
            primary key,
        last_date    varchar(255) null,
        page_name    varchar(255) null,
        page_title   longtext     null,
        page_content longtext     null
    );

    create table sitemap_options
    (
        id         int auto_increment
            primary key,
        priority   varchar(255) null,
        changefreq varchar(255) null
    );

    create table tags
    (
        id   int auto_increment
            primary key,
        name varchar(255) not null,
        slug varchar(255) not null,
        constraint tags_name_uindex
            unique (name),
        constraint tags_slug_uindex
            unique (slug)
    );

    create table user_reports
    (
        id         int auto_increment
            primary key,
        m_report   longtext   not null,
        p_report   int        not null,
        rep_reason tinyint(1) not null,
        t_report   int        not null
    );

    create table users
    (
        id                  int auto_increment
            primary key,
        oauth_uid           longtext                                       null,
        username            varchar(255)                                   null,
        email_id            varchar(255)                                   null,
        platform            longtext                                       null,
        password            varchar(255)                                   null,
        verified            tinyint(1)                   default 0         not null,
        picture             longtext                                       null,
        date                longtext                                       null,
        ip                  longtext                                       null,
        badge               tinyint(1) unsigned zerofill default 0         not null,
        banned              tinyint(1)                   default 0         not null,
        recovery_code_hash  varchar(255)                                   not null,
        admin               tinyint(1)                   default 0         not null,
        admin_password_hash varchar(64)                                    null,
        created_at          datetime                     default now()     not null,
        updated_at          datetime                                       not null,
        constraint users_username_uindex
            unique (username)
    );

    INSERT INTO users (id, username, created_at, updated_at, recovery_code_hash) VALUES (1, 'anonfilly', NOW(), NOW(), "");

    create table admin_logs
    (
        id      int auto_increment
            primary key,
        user_id int                                   not null,
        action  int                                   not null,
        time    timestamp default current_timestamp() not null on update current_timestamp(),
        ip      varchar(16)                           not null,
        constraint admin_logs_users_id_fk
            foreign key (user_id) references users (id)
                on update cascade on delete cascade
    );

    create table pastes
    (
        id         int auto_increment
            primary key,
        title      longtext                             null,
        content    longtext                             null,
        visible    longtext                             null,
        code       longtext                             null,
        expiry     longtext                             null,
        password   longtext                             null,
        encrypt    longtext                             null,
        ip         longtext                             null,
        views      int                                  null,
        s_date     longtext                             null,
        tagsys     longtext                             null,
        user_id    int                                  null,
        created_at datetime default current_timestamp() null,
        updated_at datetime default current_timestamp() null,
        constraint users_id_fkey
            foreign key (user_id) references users (id)
    );

    create table paste_taggings
    (
        id       int auto_increment
            primary key,
        paste_id int null,
        tag_id   int null,
        constraint paste_taggings_uindex
            unique (paste_id, tag_id),
        constraint paste_taggings_ibfk_2
            foreign key (tag_id) references tags (id),
        constraint paste_taggings_ibfk_3
            foreign key (paste_id) references pastes (id)
                on delete cascade
    );

    create index tag_id
        on paste_taggings (tag_id);

    create table user_favourites
    (
        id       int auto_increment
            primary key,
        paste_id int                                  null,
        f_time   datetime default current_timestamp() not null,
        user_id  int                                  not null,
        constraint paste_id_fk
            foreign key (paste_id) references pastes (id),
        constraint user_id_fk
            foreign key (user_id) references users (id)
    );

    create table user_sessions
    (
        id         int auto_increment
            primary key,
        user_id    int                                  not null,
        token      varchar(255)                         not null,
        expire_at  datetime                             not null,
        created_at datetime default current_timestamp() not null,
        updated_at datetime default current_timestamp() not null,
        constraint user_sessions_token_uindex
            unique (token),
        constraint user_sessions_users_id_fk
            foreign key (user_id) references users (id)
    );

    -- Commit the transaction if no errors occurred
    COMMIT;
END $$

DELIMITER ;

CALL ExecuteScript();

DROP PROCEDURE IF EXISTS ExecuteScript;
