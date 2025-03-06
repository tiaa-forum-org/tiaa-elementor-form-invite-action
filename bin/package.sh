#!/usr/bin/env bash

cd ../../
if [ -d "../../tiaa-backup/" ]; then
  TIAA_BACKUP="../../tiaa-backup"
else
  echo "no target directory"
  exit 1
fi

current_date=$(date "+%y%m%d%H%M")

zip -r /tmp/elementor-forms-tiaa-invite-action.zip elementor-forms-tiaa-invite-action


cp /tmp/elementor-forms-tiaa-invite-action.zip "${TIAA_BACKUP}/${current_date}-elementor-forms-tiaa-invite-action.zip"

cd ${TIAA_BACKUP}
TIAA_BACKUP_DIR=`pwd`
echo "saved in ${TIAA_BACKUP_DIR}/${current_date}-elementor-forms-tiaa-invite-action.zip"