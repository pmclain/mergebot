FROM nginx:1.9

LABEL MAINTAINER="Patrick McLain <pat@pmclain.com>"

COPY etc/vhost.conf /etc/nginx/conf.d/default.conf
COPY etc/certs/ /etc/nginx/ssl/
COPY bin/* /usr/local/bin/

EXPOSE 443

ENV FPM_HOST app
ENV FPM_PORT 9000
ENV DEBUG false

RUN ["sed", "-i", "s/user  nginx;/user root;/", "/etc/nginx/nginx.conf"]

RUN ["chmod", "+x", "/usr/local/bin/docker-environment"]

ENTRYPOINT ["/usr/local/bin/docker-environment"]
CMD ["nginx", "-g", "daemon off;"]
