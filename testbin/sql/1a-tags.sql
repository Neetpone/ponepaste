CREATE TABLE `tags`
(
    `post_id` int(11)     NOT NULL,
    `tag`     varchar(32) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

ALTER TABLE `tags`
    ADD PRIMARY KEY (`post_id`, `tag`);
COMMIT;