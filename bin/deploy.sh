#!/bin/bash
# Deploy config to VPS

cd "$(dirname "$0")"/..

php bin/build-config.php && \
rsync -zr --chown=asterisk:asterisk -e ssh build/  laurentpichler.com:/srv/phone-server && \
bin/transcode-sounds-to-opus && \
ssh laurentpichler.com "cp /srv/phone-server/usr/share/asterisk/sounds/en_US_f_Allison/*.opus /usr/share/asterisk/sounds/en_US_f_Allison"  && \
ssh laurentpichler.com "sudo /bin/systemctl restart asterisk"
