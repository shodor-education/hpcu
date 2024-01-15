SELECT materialItem.`resourcePath`
FROM trainingMaterials
LEFT JOIN materialItem ON materialItem.`tmid` = trainingMaterials.`id`
WHERE trainingMaterials.`isConfirmed` = 1
AND materialItem.`resourcePath` IS NOT NULL
ORDER BY materialItem.`displayOrder`
