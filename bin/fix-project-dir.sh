#!/bin/bash

SCRIPT_DIR=`dirname "$0"`
KERNEL_FILE=$SCRIPT_DIR/../src/backend/Kernel.php
SERVICES_FILE=$SCRIPT_DIR/../config/services.yaml
ANNOTATIONS_FILE=$SCRIPT_DIR/../config/routes/annotations.yaml
DOCTRINE_FILE=$SCRIPT_DIR/../config/packages/doctrine.yaml
DOCTRINE_MIGRATIONS_FILE=$SCRIPT_DIR/../config/packages/doctrine_migrations.yaml
TMP_FILE=$SCRIPT_DIR/temp

sed 's/return \\dirname(__DIR__);/return parent::getProjectDir();/g' $KERNEL_FILE > $TMP_FILE
mv $TMP_FILE $KERNEL_FILE

BACKEND_PRESENT=`cat $SERVICES_FILE | grep "src/backend" | wc -l`
if [ $BACKEND_PRESENT -eq "0" ]; then
  sed 's/..\/src\//..\/src\/backend\//g' $SERVICES_FILE > $TMP_FILE
  mv $TMP_FILE $SERVICES_FILE
fi
BACKEND_PRESENT=`cat $ANNOTATIONS_FILE | grep "src/backend" | wc -l`
if [ $BACKEND_PRESENT -eq "0" ]; then
  sed 's/..\/src\//..\/src\/backend\//g' $ANNOTATIONS_FILE > $TMP_FILE
  mv $TMP_FILE $ANNOTATIONS_FILE
fi
BACKEND_PRESENT=`cat $DOCTRINE_FILE | grep "src/backend" | wc -l`
if [ $BACKEND_PRESENT -eq "0" ]; then
  sed 's/\/src\//\/src\/backend\//g' $DOCTRINE_FILE > $TMP_FILE
  mv $TMP_FILE $DOCTRINE_FILE
fi
BACKEND_PRESENT=`cat $DOCTRINE_MIGRATIONS_FILE | grep "src/backend" | wc -l`
if [ $BACKEND_PRESENT -eq "0" ]; then
  sed 's/\/src\//\/src\/backend\//g' $DOCTRINE_MIGRATIONS_FILE > $TMP_FILE
  mv $TMP_FILE $DOCTRINE_MIGRATIONS_FILE
fi

