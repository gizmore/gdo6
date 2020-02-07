#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "git commit all repos"
find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git add -A . && (git diff-index --quiet HEAD || git commit -am '$1') && (test \"$(git status --porcelain)\" || git push)" \;
