FROM httpd:2.4

# Copy apache vhost file to proxy php requests to php-fpm container
COPY httpd.conf /usr/local/apache2/conf/httpd.conf
COPY vhost.conf /usr/local/apache2/conf/vhost.conf
