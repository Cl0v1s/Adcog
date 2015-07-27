#!/bin/bash

if [ -z "$1" ]; then ENV="prod"; else ENV="$1"; fi

reset

SCRIPT=`readlink -f "$0"`
SCRIPT_PATH=`dirname "${SCRIPT}"`
SCRIPT_CONSOLE="${SCRIPT_PATH}/app/console"
PHP=`which "php"`

cat > "${SCRIPT_PATH}/env.php" <<EOL
<?php
define('ENV', '${ENV}');
define('KEY', `date +%s`);
\$_SERVER['SYMFONY__VERSION'] = KEY;
setlocale(LC_ALL, 'fr_FR.utf8');
mb_internal_encoding('utf-8');
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, 'fr_FR.utf8', 'fr_FR');
EOL

echo "Reloading cache ..."
${PHP} "${SCRIPT_CONSOLE}" cache:clear -e "${ENV}"

echo "Reloading database ..."
${PHP} "${SCRIPT_CONSOLE}" doctrine:schema:update --force -e "${ENV}"
${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "UPDATE adcog_user SET accountExpired = NULL WHERE accountExpired = '0000-00-00 00:00:00'"
${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "UPDATE adcog_user SET accountLocked = NULL WHERE accountLocked = '0000-00-00 00:00:00'"
${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "UPDATE adcog_user SET credentialsExpired = NULL WHERE credentialsExpired = '0000-00-00 00:00:00'"
${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "UPDATE adcog_user SET termsAccepted = NULL WHERE termsAccepted = '0000-00-00 00:00:00'"

${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "DELETE FROM adcog_payment WHERE discr IS NULL OR discr = ''"
${PHP} "${SCRIPT_CONSOLE}" doctrine:query:sql "DELETE FROM adcog_experience WHERE discr IS NULL OR discr = ''"

echo "Reloading assets ..."
${PHP} "${SCRIPT_CONSOLE}" assets:install -e "${ENV}"
${PHP} "${SCRIPT_CONSOLE}" assetic:dump -e "${ENV}"

echo "Reloading cache ..."
${PHP} "${SCRIPT_CONSOLE}" cache:clear -e "${ENV}"
${PHP} "${SCRIPT_CONSOLE}" eb:doctrine:fix -e "${ENV}"
