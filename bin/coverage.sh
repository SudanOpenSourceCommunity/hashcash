#!/usr/bin/env bash

SCRIPT_BASEDIR=$(dirname "$0")


set -e
cd "${SCRIPT_BASEDIR}/.."

mkdir -p tmp/test_data
vendor/bin/phpunit --coverage-html tmp/coverage
rm -r tmp/test_data
