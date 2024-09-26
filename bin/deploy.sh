# Deploy config to VPS
ROOTDIR=`pwd`"/..";

php bin/build-config.php && \
rsync -zr --chown=asterisk:asterisk -e ssh build/  laurentpichler.com:/srv/phone-server && \
ssh laurentpichler.com "sudo /bin/systemctl restart asterisk"