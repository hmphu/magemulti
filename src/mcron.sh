#!/bin/sh
INSTALLDIR=`pwd -P`'/'
PHP_BIN=`which php`

if [ -z "$1" ]; then
    CRONSCRIPT=mcron.sh
    CURRENTDIR=`pwd -P`
    CODES=$(php -r "
        require_once \"$CURRENTDIR/app/Mage.php\";
        require_once \"$CURRENTDIR/app/MageMulti.php\";
        \$codes = array();
        foreach(glob(\"$INSTALLDIR\".MageMulti::CLIENT_DIR.\"/*\") as \$file) {
            \$bits = explode('/', \$file);
            \$codes[] = \$bits[count(\$bits) - 1];
        }
        echo implode(\"\n\", \$codes);
    ")
    for CODE in $CODES; do
        $INSTALLDIR$CRONSCRIPT $CODE &
    done
    exit 0
fi

CLIENT_CODE=" $1"

if [ ! "$2" = "" ]; then
    CRONSCRIPT=$2
else
    CRONSCRIPT=mcron.php
fi

MODE=""
if [ ! "$3" = "" ]; then
    MODE=" $3"
fi

if ! ps auxwww | grep "$INSTALLDIR$CRONSCRIPT$CLIENT_CODE$MODE" | grep -v grep 1>/dev/null 2>/dev/null; then
    $PHP_BIN $INSTALLDIR$CRONSCRIPT$CLIENT_CODE$MODE &
fi
