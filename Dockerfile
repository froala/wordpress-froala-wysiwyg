FROM wordpress:latest

LABEL maintainer="froala_git_travis_bot@idera.com"

ARG PackageName
ARG PackageVersion
ARG NexusUser
ARG NexusPassword
RUN apt-get update -y
RUN apt-get install -y --no-install-recommends wget unzip

WORKDIR /var/www/html/wp-content/plugins/froala
#RUN mkdir -p /var/www/html/wp-content/plugins/froala
#RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/froala

COPY . .

RUN wget --no-check-certificate --user ${NexusUser}  --password ${NexusPassword} https://nexus.tools.froala-infra.com/repository/Froala-npm/${PackageName}/-/${PackageName}-${PackageVersion}.tgz \
    && tar -zxvf ${PackageName}-${PackageVersion}.tgz \
    && /bin/cp -rf  package/css/* /var/www/html/wp-content/plugins/froala/public/css/ \
    && /bin/cp -rf  package/js/* /var/www/html/wp-content/plugins/froala/public/js/ \
    && rm -rf /var/www/html/wp-content/plugins/froala/admin/css/* \
    && rm -rf /var/www/html/wp-content/plugins/froala/admin/js/* \
    && /bin/cp -rf  package/css/* /var/www/html/wp-content/plugins/froala/admin/css/ \
    && /bin/cp -rf  package/js/* /var/www/html/wp-content/plugins/froala/admin/js/ \
    && chown -R www-data:www-data /var/www/html/wp-content/plugins \
    && /bin/cp -r package / 
#    && rm -rf package/ ${PackageName}-${PackageVersion}.tgz 
 
 RUN wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && php wp-cli.phar --info \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && cd /var/www/html/ \
    && echo "wp-cli installed..."


EXPOSE 80
