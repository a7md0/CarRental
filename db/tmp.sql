SELECT C.*, CM.*
FROM `dbproj_car` AS C

LEFT OUTER JOIN `dbproj_car_reservation` AS CR
	ON C.`car_id` = CR.`car_id`
    AND CR.`pickup_date` BETWEEN '2021-05-01' AND '2021-05-30'
    AND CR.`return_date` BETWEEN '2021-05-01' AND '2021-05-30'
	-- AND CR.`pickup_date` <= '2021-05-15' AND CR.`return_date` > '2021-05-22'

INNER JOIN `dbproj_car_model` AS CM
	ON C.`car_model_id` = CM.`car_model_id`

WHERE CR.`car_id` IS NULL;
