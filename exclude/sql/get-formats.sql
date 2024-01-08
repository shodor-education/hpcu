SELECT DISTINCT CONCAT(
  '- "',
  SDRTextValue.`entry`,
  '"'
)
FROM SDRTextValue
JOIN SDRFieldValue ON SDRFieldValue.`valueId` = SDRTextValue.`valueId`
JOIN SDRField ON SDRField.`id` = SDRFieldValue.`fieldId`
WHERE SDRField.`name` = 'Format'
ORDER BY SDRTextValue.`entry`
