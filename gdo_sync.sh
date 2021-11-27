#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

if [ "$#" -eq  "2" ]
    then echo "1. git push all repos" && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git push" \;
    else echo "2. git commit & push all repos" && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git add -A . && git commit -am $1 && git push" \;
fi
