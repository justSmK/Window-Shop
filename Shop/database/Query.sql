SELECT w.id, w.type, p.name AS profile, num_of_cam, glass_unit, f.name AS fittings, colour, price, c.name AS category

FROM `window` w

JOIN profile p ON profile_id = p.id
JOIN fittings f ON fittings_id = f.id
JOIN category c ON category_id = c.id
ORDER BY w.id
;



SELECT w.id, w.type, p.name AS profile, num_of_cam, glass_unit, f.name AS fittings, colour, price, CONCAT('img/', i.name) AS image_path, c.name AS category

FROM `window` w

JOIN profile p ON profile_id = p.id
JOIN fittings f ON fittings_id = f.id
JOIN category c ON category_id = c.id
JOIN window_image wi ON w.id = wi.window_id 
JOIN image i ON wi.image_id = i.id
WHERE category_id = 1
ORDER BY w.id
;

SELECT w.id AS id, w.type AS type, p.name AS profile, CONCAT('img/', i.name) AS image_path 
FROM `window` w
JOIN profile p ON profile_id = p.id
JOIN window_image wi ON w.id = wi.window_id 
JOIN image i ON wi.image_id = i.id
WHERE category_id = 1 AND w.id = ?
ORDER BY w.id
;

SELECT id, type, c.name AS category FROM `window` JOIN category c ON category_id = c.id ORDER BY id;





UPDATE window SET 
`type` = ,
