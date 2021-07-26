CREATE TABLE `posts`
(
    `post_id`    int(11) NOT NULL,
    `post_title` text    NOT NULL,
    `post_txt`   text    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

INSERT INTO `posts` (`post_id`, `post_title`, `post_txt`)
VALUES (1, 'Stream of Rainbow',
        'Can the predecessor hope the diesel? The unknown bombs whatever buried manpower. The crucial boy tenders a developed blurb. A top law clicks before a release. Why does our employee monitor the many lawyer? An ear fumes.'),
       (2, 'Misty in the Wings',
        'When will a competing helmet react in a noise? A paragraph acts above the agenda! A kept delight repairs a controlling crush. Can the procedure vanish? The documented rectangle inconveniences a hysterical luggage. The learned tobacco screams.'),
       (3, 'Lord of the Minks',
        'The undone complaint collapses past an east estate. The insulting nurse flames the era. A willed hierarchy surfaces. A tentative wife bites the consenting fence.'),
       (4, 'Ice in the Scent',
        'A futile pump bangs against the cider. A night stomachs a wizard. How does the mania originate? Can a reject wreck a taking battle?');

ALTER TABLE `posts`
    ADD PRIMARY KEY (`post_id`);

ALTER TABLE `posts`
    MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 5;
COMMIT;