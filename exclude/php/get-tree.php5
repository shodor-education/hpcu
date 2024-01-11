<?php
header('Content-Type: text/plain');
include_once('helpers.php5');

$fieldName = 'HPCU_Subject_2';

function echoTree($parentId = null, $level) {
  global $fieldName, $sdrDbConn;
  $indent = '';
  for ($i = 0; $i < $level; $i++) {
    $indent .= '  ';
  }
  $parent = is_null($parentId) ? 'IS NULL' : "= $parentId";
  $query = <<<END
    SELECT SDRTextValue.`valueId`, SDRTextValue.`entry`
    FROM SDRFieldValue
    JOIN SDRField ON SDRField.`id` = SDRFieldValue.`fieldId`
    JOIN SDRTextValue ON SDRTextValue.`valueId` = SDRFieldValue.`valueId`
    WHERE SDRField.`name` = "$fieldName"
    AND SDRFieldValue.`parentValueId` $parent
    ORDER BY SDRTextValue.`entry`
END;
  $results = $sdrDbConn->query($query);
  if ($level != 0) {
    echo ':';
  }
  if ($results->num_rows == 0) {
    echo ' null';
  }
  echo "\n";
  while ($result = $results->fetch_assoc()) {
    echo "$indent\"$result[entry]\"";
    echoTree($result['valueId'], $level + 1);
  }
}

echoTree(null, 0);
?>
