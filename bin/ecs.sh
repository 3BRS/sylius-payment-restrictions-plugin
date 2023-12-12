#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# project root
cd "$(dirname "$DIR")"

set -x

ls -al easy-coding-standard.yml

vendor/bin/ecs --config=easy-coding-standard.yml check src tests "$@"
