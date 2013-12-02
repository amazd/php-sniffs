#!/usr/bin/env bash

_BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ -z `which pear` ]; then
  echo "pear does not seem to be installed"
  exit 1
fi

_PEAR_DIR=`pear config-get php_dir`
_STANDARDS_DIR=${_PEAR_DIR}/PHP/CodeSniffer/Standards

if [ ! -d ${_STANDARDS_DIR} ]; then
  echo "${_STANDARDS_DIR} does not exist - make sure that phpcs is installed"
  exit 1
fi

cp -r ${_BASE_DIR}/Behance ${_STANDARDS_DIR}/.
