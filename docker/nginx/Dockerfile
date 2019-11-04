FROM nginx:alpine

RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

CMD ["nginx"]

EXPOSE 80 443
