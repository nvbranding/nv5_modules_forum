SELECT DISTINCT entry.content_id,
                entry.user_group_id,
                entry.user_id
FROM xf_permission_entry_content AS entry
INNER JOIN xf_permission AS permission ON (permission.permission_group_id = entry.permission_group_id
                                           AND permission.permission_id = entry.permission_id)
LEFT JOIN xf_user AS USER ON (USER.user_id = entry.user_id
                              AND entry.user_id > 0)
LEFT JOIN xf_user_group AS user_group ON (user_group.user_group_id = entry.user_group_id
                                          AND entry.user_group_id > 0)
WHERE entry.content_type = 'node'
  AND ( USER.user_id IS NOT NULL
       OR user_group.user_group_id IS NOT NULL
       OR (entry.user_id = 0
           AND entry.user_group_id = 0) )
		   
/*////////*/
SELECT user.*
			FROM xf_permission_entry_content AS permission_entry_content
			INNER JOIN xf_user AS user ON
				(user.user_id = permission_entry_content.user_id)
			INNER JOIN xf_permission AS permission ON
				(permission.permission_group_id = permission_entry_content.permission_group_id
				AND permission.permission_id = permission_entry_content.permission_id)
			WHERE permission_entry_content.content_type = 'node'
				AND permission_entry_content.content_id = '15'
				AND permission_entry_content.user_group_id = 0
				AND permission_entry_content.user_id > 0
			GROUP BY permission_entry_content.user_id
			ORDER BY user.username


SELECT permission.*,
				entry_content.permission_value, entry_content.permission_value_int,
				COALESCE(entry_content.permission_value, 'unset') AS value,
				COALESCE(entry_content.permission_value_int, 0) AS value_int
			FROM xf_permission AS permission
			LEFT JOIN xf_permission_entry_content AS entry_content ON
				(entry_content.permission_id = permission.permission_id
				AND entry_content.permission_group_id = permission.permission_group_id
				AND entry_content.content_type = 'node'
				AND entry_content.content_id = '15'
				AND entry_content.user_group_id = '0'
				AND entry_content.user_id = '0')
			WHERE permission.permission_group_id IN ('general')
			ORDER BY permission.display_order
			
			
SELECT * 
FROM xf_permission_entry_content 
WHERE content_type = 'node' AND content_id = '17' AND user_group_id = '3' AND user_id = '0' 
ORDER BY `permission_entry_id` DESC