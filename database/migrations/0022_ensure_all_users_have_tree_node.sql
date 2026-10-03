-- Ensure all users have a corresponding row in the tree table
INSERT INTO `tree` (`userid`, `left_id`, `right_id`, `status`, `join_side`, `leftsp`, `rightsp`, `leftpv`, `rightpv`, `leftcount`, `rightcount`, `lefttotal`, `righttotal`)
SELECT u.`userid`, '', '', 1, COALESCE(NULLIF(u.`join_side`, ''), 'left'), 0, 0, 0, 0, 0, 0, 0, 0
FROM `user` u
LEFT JOIN `tree` t ON t.`userid` = u.`userid`
WHERE t.`userid` IS NULL;
