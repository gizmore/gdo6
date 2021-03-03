#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "Calling post install hooks on all modules."

find GDO -iname "gdo_post_install.sh" -exec sh -c "{}" \;
