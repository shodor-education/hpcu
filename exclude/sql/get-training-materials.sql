SET SESSION group_concat_max_len = 1000000;
SELECT CONCAT(
  'FILENAME::',
  trainingMaterials.`id`,
  '\n---\n',
  'submitter: "',
  trainingMaterials.`submitter`,
  '"\n',
  'submitter-institution: ',
  COALESCE(
    CONCAT(
      '"',
      trainingMaterials.`submitterInstitution`,
      '"'
    ),
    'null'
  ),
  '\nsubmission-date: "',
  trainingMaterials.`submitDate`,
  '"\ndescription: "',
  REPLACE(
    trainingMaterials.`description`,
    '"',
    '\\"'
  ),
  '"\nmaterials:\n',
  GROUP_CONCAT(
    '  - path: "',
    COALESCE(
      materialItem.`resourcePath`,
      materialItem.`href`
    ),
    '"\n    description: "',
    REPLACE(
      materialItem.`description`,
      '"',
      '\\"'
    ),
    '"'
    SEPARATOR '\n'
  ),
  '\n---'
)
FROM trainingMaterials
LEFT JOIN materialItem ON materialItem.`tmid` = trainingMaterials.`id`
WHERE trainingMaterials.`isConfirmed` = 1
GROUP BY trainingMaterials.`id`
ORDER BY materialItem.`displayOrder`
