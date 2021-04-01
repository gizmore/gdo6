#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

SLEEP=0
if [ $# -gt 0 ]; then
  SLEEP=$1
fi;

echo "Resetting all repositories with git reset --hard."

find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && 
pwd && LANG=en_GB LC_ALL=en_GB git reset --hard" \;
