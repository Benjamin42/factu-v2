ALTER TABLE `User` ADD `roles` JSON NOT NULL AFTER `salt`;

UPDATE User AS u1
    JOIN (
    SELECT u.id, r.role FROM User u
    JOIN user_role ur on ur.user_id = u.id
    JOIN role r on r.id = ur.role_id
    ) as u2 on u1.id = u2.id
    set u1.roles = JSON_SET('["role"]', '$[0]', u2.role);

