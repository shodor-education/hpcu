<?php
header('Content-Type: text/plain');
include_once('helpers.php5');

function echoHpcuSubjects($parentId = null, $level) {
  global $sdrDbConn;
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
    WHERE SDRField.`name` = 'HPCU_Subject_2'
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
    echoHpcuSubjects($result['valueId'], $level + 1);
  }
}

echoHpcuSubjects(null, 0);
?>
