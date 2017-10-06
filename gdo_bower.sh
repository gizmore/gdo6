#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "All modules: bower update"
#find GDO -maxdepth 1 -type d -exec sh -c "cd $CORE && cd {} && rm -rf bower_components" \;
find GDO -maxdepth 1 -type d -exec sh -c "cd $CORE && cd {} && bower update" \;
