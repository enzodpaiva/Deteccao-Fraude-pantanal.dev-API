#THIS IS A DOCKERFILE USED TO CREATE NGIX CONTAINER PLEASE DO NOT CHANGE IT UNLESS YOU KNOW WHAT ARE YOU DOING
FROM nginx:latest
#path to public folder inside the container
ENV NGINX_ROOT /var/www/html/public
ENV NGINX_FPM_HOST localhost

#FPM port
ENV NGINX_FPM_PORT 9000

#copy NGINX start script
COPY nginx-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
COPY fpm-template.conf /etc/nginx/fpm.tmpl

#delete default nginx configs and replace with the nginx.config file
RUN rm -f /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/nginx.conf

COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]