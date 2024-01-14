<?php
header('Content-Type: text/plain');
include_once('helpers.php5');

function getValues($fieldName, $cserdId) {
  global $sdrDbConn;
  $query = <<<END
    SELECT COALESCE(SDRTextValue.`entry`, SDRDateValue.`entry`) AS entry
    FROM SDRResourceProject
    JOIN SDRVersion ON SDRVersion.`cserdId` = SDRResourceProject.`cserdId`
    JOIN SDRVersionFieldValue ON SDRVersionFieldValue.`versionId` = SDRVersion.`id`
    JOIN SDRField ON SDRField.`id` = SDRVersionFieldValue.`fieldId`
    LEFT JOIN SDRTextValue ON SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
    LEFT JOIN SDRDateValue ON SDRDateValue.`valueId` = SDRVersionFieldValue.`valueId`
    WHERE SDRResourceProject.`projectId` = 6
    AND SDRVersion.`state` = 'live'
    AND SDRField.`name` = "$fieldName"
    AND SDRVersion.`cserdId` = $cserdId
    ORDER BY COALESCE(SDRTextValue.`entry`, SDRDateValue.`entry`)
END;
  return $sdrDbConn->query($query);
}

function echoValue($value) {
  echo '"' . str_replace('"', '\\"', $value) . '"';
}

function echoField(
  $fieldDisplay,
  $fieldName,
  $cserdId,
  $replacements = array()
) {
  echo "$fieldDisplay: ";
  $values = getValues($fieldName, $cserdId);
  if ($values->num_rows == 0) {
    echo "null";
  } else {
    $result = $values->fetch_assoc();
    $value = $result['entry'];
    foreach ($replacements as $replacement) {
      list($searches, $replace) = $replacement;
      foreach ($searches as $search) {
        $value = str_replace($search, $replace, $value);
      }
    }
    echoValue($value);
  }
  echo "\n";
}

function echoMultiField($fieldDisplay, $fieldName, $cserdId) {
  echo "$fieldDisplay:";
  $values = getValues($fieldName, $cserdId);
  if ($values->num_rows == 0) {
    echo " null";
  }
  echo "\n";
  while ($value = $values->fetch_assoc()) {
    echo '  - ';
    echoValue($value['entry']);
    echo "\n";
  }
}

$query = <<<END
  SELECT SDRVersion.`cserdId`
  FROM SDRResourceProject
  JOIN SDRVersion ON SDRVersion.`cserdId` = SDRResourceProject.`cserdId`
  WHERE SDRResourceProject.`projectId` = 6
  AND SDRVersion.`state` = 'live'
  ORDER BY SDRVersion.`cserdId`
END;
$results = $sdrDbConn->query($query);
while ($result = $results->fetch_assoc()) {
  echo "FILENAME::$result[cserdId]\n";
  echo "---\n";
  echoField('title', 'Title', $result['cserdId']);
  echoField('start-date', 'Start_Date', $result['cserdId']);
  echoField('end-date', 'End_Date', $result['cserdId']);
  $replacements = array(
    array(
      array(
        'http://www.shodor.org/media/content//hpcu/website/resources/'
      ),
      'https://shodor-education.github.io/hpcu/media/'
    ),
    array (
      array(
        'http://hpcuniversity.org/',
        'http://www.hpcuniversity.org/',
        'http://hpcuniv.org/'
      ),
      'https://shodor-education.github.io/hpcu/',
    )
  );
  echoField('resource-url', 'Url', $result['cserdId'], $replacements);
  echoField('description', 'Description', $result['cserdId']);
  echoMultiField('creator', 'Creator', $result['cserdId']);
  echoMultiField('contributor', 'Contributor', $result['cserdId']);
  echoMultiField('publisher', 'Publisher', $result['cserdId']);
  echoMultiField('type', 'Type', $result['cserdId']);
  echoMultiField('subject', 'Subject', $result['cserdId']);
  echoMultiField('format', 'Format', $result['cserdId']);
  echoMultiField('hpcu-subject', 'HPCU_Subject', $result['cserdId']);
  echoMultiField('hpcu-subject-2', 'HPCU_Subject_2', $result['cserdId']);
  echoMultiField('hpcu-keywords', 'HPCU_Keywords', $result['cserdId']);
  echoMultiField('location', 'Location', $result['cserdId']);
  echoField('duration', 'Duration', $result['cserdId']);
  echoField('rights', 'Rights', $result['cserdId']);
  echoField('relation', 'Relation', $result['cserdId']);
  echoMultiField('language', 'Language', $result['cserdId']);
  echoMultiField('audience', 'Audience', $result['cserdId']);
  echoMultiField('education-level', 'Education_Level', $result['cserdId']);
  echoMultiField('keyword', 'Keyword', $result['cserdId']);
  echoMultiField('sector', 'Sector', $result['cserdId']);
  echoMultiField('difficulty', 'Difficulty', $result['cserdId']);
  echoField('address', 'Address', $result['cserdId']);
  echo 'reviews:';
  $query = <<<END
    SELECT review.`type`,
      review.`modified`,
      review.`cache`,
      user.`firstName`,
      user.`lastName`,
      (response.`response` + 1) AS 'numStars'
    FROM review
    JOIN user ON user.`userId` = review.`authorId`
    JOIN response ON response.`reviewId` = review.`reviewId`
    JOIN question ON question.`questionId` = response.`questionId`
    WHERE review.`cserdId` = $result[cserdId]
    AND review.`state` = 'published'
    AND review.`active` = 1
    AND question.`ref` = 'usability'
END;
  $reviews = $cserdDbConn->query($query);
  if ($reviews->num_rows == 0) {
    echo ' null';
  }
  echo "\n";
  while ($review = $reviews->fetch_assoc()) {
    echo '  - type: ';
    echoValue($review['type']);
    echo "\n";
    echo '    author: ';
    echoValue("$review[firstName] $review[lastName]");
    echo "\n";
    echo '    modified: ';
    echoValue($review['modified']);
    echo "\n";
    echo "    contents:\n";
    $pieces = explode('</div><table', $review['cache']);
    echo '      text: ';
    echoValue(str_replace('<div class="reviewText">', '', $pieces[0]));
    echo "\n";
    $pieces = explode('</td><td>', $pieces[1]);
    $pieces = explode('</td>', $pieces[1]);
    echo '      usability: ';
    echoValue($pieces[0]);
    echo "\n";
    echo "    num-stars: $review[numStars]\n";
  }
  echo 'redirect_from: "/resources/' . $result['cserdId'] . "/\"\n---\n";
}
