#!/bin/sh

if [ -n "${AWS_ENV_PATH}" ]; then
  eval $(aws-env)
fi

exec "$@"
