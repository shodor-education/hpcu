#!/bin/bash

while read line; do
  dir=$(echo ${line} | cut -d'/' -f1);
  mkdir -p ${dir};
  curl http://hpcuniversity.org/media/TrainingMaterials/${line} -k --output ${line};
done <${1};
