SET SESSION group_concat_max_len = 1000000;
SELECT CONCAT(
  'FILENAME::',
  fields.`cserdId`,
  '\n---\n',
  GROUP_CONCAT(
    CONCAT(
      LOWER(REPLACE(fields.`name`, '_', '-')),
      ':',
      fields.`values_`
    )
    SEPARATOR '\n'
  ),
  '\n---'
)
FROM (
  SELECT SDRResourceProject.`projectId`,
    SDRVersion.`cserdId`,
    SDRField.`name`,
    CONCAT(
      IF(COUNT(*) > 1, '\n  - ', ' '),
      GROUP_CONCAT(
        CONCAT(
          '"',
          REPLACE(
            COALESCE(SDRTextValue.`entry`, SDRDateValue.`entry`),
            '"',
            '\\"'
          ),
          '"'
        )
        ORDER BY COALESCE(SDRTextValue.`entry`, SDRDateValue.`entry`)
        SEPARATOR '\n  - '
      )
    ) AS values_
  FROM SDRResourceProject
  JOIN SDRVersion ON SDRVersion.`cserdId` = SDRResourceProject.`cserdId`
  JOIN SDRVersionFieldValue ON SDRVersionFieldValue.`versionId` = SDRVersion.`id`
  JOIN SDRField ON SDRField.`id` = SDRVersionFieldValue.`fieldId`
  LEFT JOIN SDRTextValue ON SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
  LEFT JOIN SDRDateValue ON SDRDateValue.`valueId` = SDRVersionFieldValue.`valueId`
  WHERE SDRResourceProject.`projectId` = 6
  AND SDRVersion.`state` = 'live'
  GROUP BY SDRVersion.`cserdId`, SDRField.`id`
) AS fields
GROUP BY fields.`cserdId`
