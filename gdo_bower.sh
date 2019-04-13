#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "All modules: bower update"
echo "Thanks to greycat@freenode#bash"
for d in GDO/*/; do (cd "$d" || exit; [[ -f bower.json ]] || exit; echo $d; bower update); done
