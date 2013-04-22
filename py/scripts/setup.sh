#!/usr/bin/env bash
# stan 2012-07-25

./clean_pyc.sh

python sdist --formats=gztar
python install

read -s -n 1
