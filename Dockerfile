FROM debian:unstable-slim

RUN apt-get update
RUN apt-get install -y asterisk asterisk-config asterisk-core-sounds-en-gsm asterisk-modules

# Config from external volume
RUN rm -rf /etc/asterisk
RUN mkdir -p /srv/etc/asterisk
RUN ln -s /srv/etc/asterisk /etc/asterisk

CMD	["asterisk", "-fp"]