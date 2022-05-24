#!/bin/bash
# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

dir=$(cd `dirname $0` && pwd)

cat > "$dir/../app/config/config.local.neon" <<EOF
#
# WARNING: it is CRITICAL that this file & directory are NOT accessible directly via a web browser!
# https://nette.org/security-warning
#

services:
    nette.mailer:
        class: Nette\Mail\IMailer
        factory: Nextras\MailPanel\FileMailer(%tempDir%/mail-panel-mails)

tracy:
    bar:
        - Nextras\MailPanel\MailPanel(%tempDir%/mail-panel-latte)
EOF
