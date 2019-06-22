#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "All modules: yarn install"
echo "Thanks to greycat@freenode#bash"
for d in GDO/*/; do (cd "$d" || exit; [[ -f package.json ]] || exit; echo $d; yarn install); done
