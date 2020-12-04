FROM rabbitmq:3.6.11-alpine

RUN apk add --update \
    tzdata

ENV TZ=Europe/Moscow

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN rabbitmq-plugins enable --offline rabbitmq_management

EXPOSE 15671 15672
