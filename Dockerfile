FROM wordpress:latest

LABEL maintainer="Ganga@celestialsys.com"

ARG PackageName
ARG PackageVersion
ARG NexusUser
ARG NexusPassword

RUN apt-get update -y
RUN apt-get install -y --no-install-recommends wget unzip
WORKDIR /var/www/html/wp-content/plugins/froala
#RUN mkdir -p /var/www/html/wp-content/plugins/froala

COPY . .

RUN wget --no-check-certificate --user ${NexusUser}  --password ${NexusPassword} https://nexus.tools.froala-infra.com/repository/Froala-npm/${PackageName}/-/${PackageName}-${PackageVersion}.tgz \
    && tar -zxvf ${PackageName}-${PackageVersion}.tgz \
    && /bin/cp -rf  package/css/* public/css/ \
    && /bin/cp -rf  package/js/* public/js/ \
    && rm -rf admin/css/* \
    && rm -rf admin/js/* \
    && /bin/cp -rf  package/css/* admin/css/ \
    && /bin/cp -rf  package/js/* admin/js/ \
    && chown -R www-data:www-data /var/www/html/wp-content/plugins \
    && /bin/cp -r package / 
#    && rm -rf package/ ${PackageName}-${PackageVersion}.tgz 
 
 RUN wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && cp -p wp-cli.phar /usr/local/bin/wp \
    && cd /var/www/html/ \
    && echo "wp-cli installed..."


EXPOSE 80
