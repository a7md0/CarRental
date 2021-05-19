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

-------------------------------------

SELECT C.*, CM.*
FROM `dbproj_car` AS C

INNER JOIN `dbproj_car_model` AS CM
	ON C.`car_model_id` = CM.`car_model_id`

WHERE NOT EXISTS (SELECT DISTINCT UCR.`car_id` FROM `dbproj_user_car_reservation` AS UCR
														WHERE UCR.`car_id` = C.`car_id`
														AND UCR.`return_date` >= '2021-05-10' AND UCR.`pickup_date` <= '2021-05-22'
									);
									
-------------------------------------
					
SELECT C.*, CM.*
FROM `dbproj_car` AS C

LEFT OUTER JOIN `dbproj_user_car_reservation` AS CR
	ON C.`car_id` = CR.`car_id`
    AND CR.`return_date` >= '2021-05-10' AND CR.`pickup_date` <= '2021-05-22'

INNER JOIN `dbproj_car_model` AS CM
	ON C.`car_model_id` = CM.`car_model_id`
	
WHERE CR.`car_id` IS NULL;

----------------------------------------