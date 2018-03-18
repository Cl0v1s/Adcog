#!/bin/bash
# Correction on uppercase path
# author: Nicolas Drufin <nicolas.drufin@ensc.fr>
# TODO: same on tables : adcog_post, adcog_user, adcog_school, adcog_slider
TABLE_LIST="adcog_post adcog_user adcog_school adcog_slider adcog_file"

for TABLE in $TABLE_LIST; do
  mysql -D adcog -e "SELECT id, path from $TABLE" -B -N | while read FILEID FILEPATH; do
    NEWFILEPATH="${FILEPATH/Adcog/adcog}"
    if [ "$FILEPATH" != "$NEWFILEPATH" ] ; then
      if $(mysql -D adcog -e "UPDATE $TABLE SET path = '$NEWFILEPATH' WHERE id = $FILEID") ; then
        echo "ID $FILEID de la table $TABLE modifié"
      fi
    fi
  done
done
