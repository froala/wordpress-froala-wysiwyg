#!/bin/bash
#
#  indentify the environment & server    
#
#
#  don't deploy for new PR
#
if [ ${TRAVIS_PULL_REQUEST} != "false" ];  then echo "Not deploying on a pull request !!!" && exit 0; fi

 
PACKAGE_VERSION=`jq '.version' version.json | tr -d '"'`
export IMAGE_NAME=`echo "froala-${BUILD_REPO_NAME}_${TRAVIS_BRANCH}:${PACKAGE_VERSION}" | tr '[:upper:]' '[:lower:]'`
DEPLOYMENT_IS_RUNNING=`echo "${BUILD_REPO_NAME}_${TRAVIS_BRANCH}" | tr '[:upper:]' '[:lower:]'`

export BASE_DOMAIN="froala-infra.com"
export SDK_ENVIRONMENT=""
export DEPLOYMENT_SERVER=""
#docker-compose service
SERVICE_NAME=""
# container name --- will be used to identify the oldest deployment for this env
CONTAINAER_NAME=""
DB_CONTAINER_NAME=""
# container-index --- will be used to identify the oldest deployment for this env
# CONTAINER_NAME will be CONTAINER_NAME-INDEX
CT_INDEX=0

OLDEST_CONTAINER=""
OLDEST_DATABASE_CONTAINER=""
#
# make sure we have ssh key from pipeline start
#
echo "${SSH_KEY}"  | base64 --decode > /tmp/sshkey.pem
chmod 400 /tmp/sshkey.pem

####

export MAX_DEPLOYMENTS_NR=0
#
# find the max deployments alloowed per environment; it is defined in version.json file
#
function get_max_deployments_per_env(){

local ENVIRONMENT=$1
echo "getting max deployments for environment ${ENVIRONMENT}"
MAX_DEPLOYMENTS_NR=`jq --arg sdkenvironment ${ENVIRONMENT}  '.[$sdkenvironment]' version.json | tr -d '"'`
echo "detected max deployments: ${MAX_DEPLOYMENTS_NR}"
#return $MAX_DEPLOYMENTS_NR
}


function generate_container_name(){
local LW_REPO_NAME=$1
local LW_SHORT_TRAVIS_BRANCH=$2
local SDK_ENVIRONMENT=$3
local DEPLOYMENT_SERVER=$4
# get the index

echo "searching for ${LW_REPO_NAME} depl..."
# dont't fall into "About a minute ago "
sleep 1

RUNNING_DEPL=`ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker ps | grep -i ${LW_REPO_NAME}"`

echo "running depl var: ${RUNNING_DEPL}"
echo "looking for ${LW_REPO_NAME} deployments"

echo "getting indexes for oldest and latest deployed container"
# build ssh cmd to get a list of running containers for this env
DEPL='ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem '
DEPL="${DEPL}  ${SSH_USER}@${DEPLOYMENT_SERVER} "
REL=' " sudo docker ps | grep -i ' 
DEPL="${DEPL} ${REL} "
DEPL="${DEPL} ${LW_REPO_NAME} "
REL='"'
DEPL="${DEPL} ${REL} "

echo "show docker containers ssh cmd:  $DEPL"
echo   ${DEPL}  | bash > file.txt
echo "running conatiners: "
cat file.txt
# get those indexes  ; $NF always prints the last column
CT_LOWER_INDEX=`cat file.txt | awk -F'-' '{print $NF }' | sort -nk1 | head -1`
CT_HIGHER_INDEX=`cat file.txt | awk -F'-' '{print $NF }' | sort -nk1 | tail -1`

echo "lowest index : ${CT_LOWER_INDEX} ; and highest index : ${CT_HIGHER_INDEX}"

if [ -z "${RUNNING_DEPL}" ]; then
	echo "first deployment"
	CT_INDEX=1
else
	echo "multiple deployments"
	CT_INDEX=${CT_HIGHER_INDEX} && CT_INDEX=$((CT_INDEX+1))
	OLDEST_CONTAINER="${LW_REPO_NAME}-${CT_LOWER_INDEX}"
	OLDEST_DATABASE_CONTAINER="${DB_SERVICE_NAME}-${CT_LOWER_INDEX}"
	echo "new index: ${CT_INDEX}  & oldest horse out there: ${OLDEST_CONTAINER}"
fi

}

echo " Container port: ${CONTAINER_SERVICE_PORTNO}"

export BRANCH_NAME=`echo "${TRAVIS_BRANCH}" | tr '[:upper:]' '[:lower:]'`

case "${BRANCH_NAME}" in
        dev*) SDK_ENVIRONMENT="dev" && DEPLOYMENT_SERVER=${FROALA_SRV_DEV}  ;;
	ao-dev*) SDK_ENVIRONMENT="dev" && DEPLOYMENT_SERVER=${FROALA_SRV_DEV} ;;
        qa*) SDK_ENVIRONMENT="qa" && DEPLOYMENT_SERVER=${FROALA_SRV_QA}  ;;
	qe*) SDK_ENVIRONMENT="qe" && DEPLOYMENT_SERVER=${FROALA_SRV_QE} ;;
	rc*) SDK_ENVIRONMENT="stg" && DEPLOYMENT_SERVER=${FROALA_SRV_STAGING}  ;;
	release-master*) SDK_ENVIRONMENT="stg" && DEPLOYMENT_SERVER=${FROALA_SRV_STAGING}  ;;
	ft*) echo "Building only on feature branch ${TRAVIS_BRANCH}... will not deploy..."  && exit 0;;
	bf*) echo "Building only on bugfix branch ${TRAVIS_BRANCH}... will not deploy..."  && exit 0;;
        *) echo "Not a deployment branch" && exit -1;;
esac

get_max_deployments_per_env $SDK_ENVIRONMENT 

echo "deploying on environment :${SDK_ENVIRONMENT}, on server ${DEPLOYMENT_SERVER}, max deployments: ${MAX_DEPLOYMENTS_NR}"

export BASE_DOMAIN="froala-infra.com"
# Issues with CN for certificates ; lenght must be max 64
SHORT_REPO_NAME="${BUILD_REPO_NAME:0:17}"
BRANCH_LENGHT=`echo ${TRAVIS_BRANCH} |awk '{print length}'`
if [ ${BRANCH_LENGHT} -lt 18 ]; then 
	SHORT_TRAVIS_BRANCH=${TRAVIS_BRANCH}
else
    SHORT_TRAVIS_BRANCH="${TRAVIS_BRANCH:0:8}${TRAVIS_BRANCH: -8}"
fi
LW_SHORT_TRAVIS_BRANCH="$(echo "${SHORT_TRAVIS_BRANCH}" | sed -e 's/-//g' -e 's/\.//g' -e 's/_//g' | tr '[:upper:]' '[:lower:]')"

# Get the maximum allowed deployment for given environment
function max_allowed_deployment(){
    echo "getting max deployments for environment ${SDK_ENVIRONMENT}"
    MAX_DEPLOYMENTS_NR=$(jq --arg sdkenvironment "${SDK_ENVIRONMENT}"  '.[$sdkenvironment]' version.json | tr -d '"')
    echo "Max allowed deployments: ${MAX_DEPLOYMENTS_NR}"
}
max_allowed_deployment

# Get the total numbers of deployed container for given environment
function existing_deployments(){
    echo "Checking the existing number of running container(s)"
    EXISTING_DEPLOYMENTS_NR=$(ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem "${SSH_USER}"@"${DEPLOYMENT_SERVER}" "sudo docker ps | grep -i ${LW_REPO_NAME}-${AO_IDENTIFIER}" | wc -l)
    echo "Number of existing deployment: ${EXISTING_DEPLOYMENTS_NR}"
}
existing_deployments

# Get the old container name, no of deployments, and generate the new index and container name
function generate_container_name(){

    DEPL=$(ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem "${SSH_USER}"@"${DEPLOYMENT_SERVER}" sudo docker ps | grep -i "${LW_REPO_NAME}"-"${AO_IDENTIFIER}")
    echo "Containers running for ${AO_IDENTIFIER}:  ${DEPL}"
    echo "${DEPL}" > file.txt

    echo "Getting indexes of oldest and latest deployed containers for ${AO_IDENTIFIER}"
    CT_LOWER_INDEX=$(awk -F'-' '{print $NF }' < file.txt | sort -nk1 | head -1)
    CT_HIGHER_INDEX=$(awk -F'-' '{print $NF }' < file.txt | sort -nk1 | tail -1)
    echo "Lowest index : ${CT_LOWER_INDEX} ; and Highest index : ${CT_HIGHER_INDEX}"	

    if [ -z "${DEPL}" ]; then
        echo "First deployment. Setting the container name."
        CT_INDEX=1
        CONTAINER_NAME="${LW_REPO_NAME}-${AO_IDENTIFIER}-${CT_INDEX}"
        SERVICE_NAME="${LW_REPO_NAME}-${LW_SHORT_TRAVIS_BRANCH}" 
    else
        echo "Multiple deployments detected. Setting the container name (old and new)"
        CT_INDEX=${CT_HIGHER_INDEX} && CT_INDEX=$((CT_INDEX+1))
        OLDEST_CONTAINER="${LW_REPO_NAME}-${AO_IDENTIFIER}-${CT_LOWER_INDEX}"
        CONTAINER_NAME="${LW_REPO_NAME}-${AO_IDENTIFIER}-${CT_INDEX}"
        SERVICE_NAME="${LW_REPO_NAME}-${LW_SHORT_TRAVIS_BRANCH}-${CT_INDEX}"
        echo "New index: ${CT_INDEX}"
    fi
}
generate_container_name

# Print useful details.
echo -e "\n"
echo "----------------------------------------------------------------------"
echo "  Selected environment:                   ${SDK_ENVIRONMENT}.         "
echo "  Deployment server:                      ${DEPLOYMENT_SERVER}.       "
echo "  Max allowed deployments:                ${MAX_DEPLOYMENTS_NR}.      "
echo "  Number of existing deployment:          ${EXISTING_DEPLOYMENTS_NR}  "
echo "  Oldest container name:                  ${OLDEST_CONTAINER}         "
echo "  Container name for this deployment:     ${CONTAINER_NAME}           "
echo "----------------------------------------------------------------------"
echo -e "\n"

# Set the deployment URL
DEPLOYMENT_URL="${CONTAINER_NAME}.${SDK_ENVIRONMENT}.${BASE_DOMAIN}"

# Modify the compose file and run the docker-compose.
function deploy(){

    # Copy the docker-compose template to docker-compose.yml
    cp docker-compose.yml.template docker-compose.yml

    # Replace the sample values
    sed -i "s/ImageName/${NEXUS_CR_TOOLS_URL}\/${IMAGE_NAME}/g" docker-compose.yml
    sed -i "s/UrlName/${DEPLOYMENT_URL}/g" docker-compose.yml
    sed -i "s/ServiceName/${SERVICE_NAME}/g" docker-compose.yml
    sed -i "s/PortNum/${CONTAINER_SERVICE_PORTNO}/g" docker-compose.yml
    sed -i "s/ContainerName/${CONTAINER_NAME}/g" docker-compose.yml

    sed -i "s/DbWordSName/${DB_SERVICE_NAME}/g" docker-compose.yml
    sed -i "s/MysqlRootPassword/${MYSQL_ROOT_PASSWORD}/g" docker-compose.yml
    sed -i "s/MysqlDatabase/${MYSQL_DATABASE}/g" docker-compose.yml
    sed -i "s/MysqlUser/${MYSQL_USER}/g" docker-compose.yml
    sed -i "s/MysqlPassword/${MYSQL_PASSWORD}/g" docker-compose.yml
    sed -i "s/MysqlTcpPort/${MYSQL_TCP_PORT}/g" docker-compose.yml
    sed -i "s/WordPressDbHost/${DB_CONTAINER_NAME}:${MYSQL_TCP_PORT}/g" docker-compose.yml
    sed -i "s/MysqlContainerName/${DB_CONTAINER_NAME}/g" docker-compose.yml

    echo -e "\n"
    echo "Below is the content of docker-compose.yml"
    echo "-------------------------------------------------"
    cat docker-compose.yml
    echo "-------------------------------------------------
    echo -e "\n"

    # Run docker-compose down on deployment_server
    
    # Remove the old docker-compose from deployment_server
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem "${SSH_USER}"@"${DEPLOYMENT_SERVER}" "if [ -d /services/${SERVICE_NAME} ];  then rm -rf /services/${SERVICE_NAME}; fi && mkdir /services/${SERVICE_NAME}"
    
    # Copy the latest docker-compose file to deployment_server
    scp  -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem docker-compose.yml "${SSH_USER}"@"${DEPLOYMENT_SERVER}":/services/"${SERVICE_NAME}"/docker-compose.yml

    # Run docker-compose pull on deployment_server
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem "${SSH_USER}"@"${DEPLOYMENT_SERVER}" "cd /services/${SERVICE_NAME}/ && sudo docker-compose pull"
    sleep 10
    
    # Run docker-compose up on deployment_server
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem "${SSH_USER}"@"${DEPLOYMENT_SERVER}" "cd /services/${SERVICE_NAME}/ && sudo docker-compose up -d --force-recreate"
    sleep 180

   # sleep 30
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME}	wp core update --allow-root "
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp plugin deactivate froala --allow-root && sleep 5" 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} rm -rf /var/www/html/wp-content/plugins/froala/admin/css && sleep 5" 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} rm -rf /var/www/html/wp-content/plugins/froala/admin/js && sleep 5" 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} cp -pr /package/js /var/www/html/wp-content/plugins/froala/admin/ && sleep 5 " 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} cp -pr /package/css /var/www/html/wp-content/plugins/froala/admin/ && sleep 5 "
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker stop ${CONTAINER_NAME} && sleep 5" 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker start ${CONTAINER_NAME} && sleep 5" 
    ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp plugin activate froala --allow-root && sleep 5" 

    echo "\n If no error above (cp related errors) then froala plugin is consuming the unpublished core library \n" 

    RET_CODE=$(curl -k -s -o /tmp/notimportant.txt -w "%{http_code}" https://"${DEPLOYMENT_URL}")
    echo "validation code: $RET_CODE for  https://${DEPLOYMENT_URL}"
    if [ "${RET_CODE}" -ne 200 ]; then 
        echo "Deployment validation failed!!! Please check pipeline logs." 
        exit 1 
    else 
        echo -e "\n\tService available at URL: https://${DEPLOYMENT_URL}\n"
    fi
}

# If existing deployment less than max deployment then just deploy don't remove old container.
if [ "${EXISTING_DEPLOYMENTS_NR}" -lt "${MAX_DEPLOYMENTS_NR}" ]; then
    deploy
fi

echo " short branch name : ${SHORT_TRAVIS_BRANCH}"
SHORT_TRAVIS_BRANCH=`echo ${SHORT_TRAVIS_BRANCH} | sed -r 's/-//g'`
SHORT_TRAVIS_BRANCH=`echo ${SHORT_TRAVIS_BRANCH} | sed -r 's/\.//g'`
SHORT_TRAVIS_BRANCH=`echo ${SHORT_TRAVIS_BRANCH} | sed -r 's/_//g'`
echo " short branch name : ${SHORT_TRAVIS_BRANCH}"
DEPLOYMENT_URL="${SHORT_REPO_NAME}-${SHORT_TRAVIS_BRANCH}.${SDK_ENVIRONMENT}.${BASE_DOMAIN}"
echo " deployment URL: https://${DEPLOYMENT_URL}"

#DEPLOYMENT_URL="${BUILD_REPO_NAME}-${TRAVIS_BRANCH}.${SDK_ENVIRONMENT}.${BASE_DOMAIN}"
#echo " deployment URL: https://${DEPLOYMENT_URL}"

#############
# creating docker-compose file...
#
cp docker-compose.yml.template docker-compose.yml
#manipulate to strictly verify depl on envs
LW_REPO_NAME=`echo "${BUILD_REPO_NAME}" | tr '[:upper:]' '[:lower:]'`
LW_REPO_NAME=`echo ${LW_REPO_NAME} | sed -r 's/_//g'`  #delete all _ from service name
LW_REPO_NAME=`echo ${LW_REPO_NAME} | sed -r 's/-//g'`  #delete all - from service name
LW_REPO_NAME=`echo ${LW_REPO_NAME} | sed -r 's/\.//g'`  #delete all . from service name
LW_SHORT_TRAVIS_BRANCH=`echo "${SHORT_TRAVIS_BRANCH}" | tr '[:upper:]' '[:lower:]'`
#SERVICE_NAME=`echo "${BUILD_REPO_NAME}-${SHORT_TRAVIS_BRANCH}" | tr '[:upper:]' '[:lower:]'`

SERVICE_NAME="${LW_REPO_NAME}-${LW_SHORT_TRAVIS_BRANCH}" 

generate_container_name ${LW_REPO_NAME} ${LW_SHORT_TRAVIS_BRANCH} ${DEPLOYMENT_SERVER} ${DEPLOYMENT_SERVER} 

CONTAINER_NAME="${LW_REPO_NAME}-${CT_INDEX}"
DB_SERVICE_NAME="${DB_SERVICE_NAME}-${CT_INDEX}"
DB_CONTAINER_NAME="${DB_SERVICE_NAME}"
echo "service name : ${SERVICE_NAME} & container name : ${CONTAINER_NAME}  and database container name: ${DB_CONTAINER_NAME} "

sed -i "s/ImageName/${NEXUS_CR_TOOLS_URL}\/${IMAGE_NAME}/g" docker-compose.yml
sed -i "s/UrlName/${DEPLOYMENT_URL}/g" docker-compose.yml
sed -i "s/ServiceName/${SERVICE_NAME}/g" docker-compose.yml
sed -i "s/PortNum/${CONTAINER_SERVICE_PORTNO}/g" docker-compose.yml
sed -i "s/ContainerName/${CONTAINER_NAME}/g" docker-compose.yml

sed -i "s/DbWordSName/${DB_SERVICE_NAME}/g" docker-compose.yml
sed -i "s/MysqlRootPassword/${MYSQL_ROOT_PASSWORD}/g" docker-compose.yml
sed -i "s/MysqlDatabase/${MYSQL_DATABASE}/g" docker-compose.yml
sed -i "s/MysqlUser/${MYSQL_USER}/g" docker-compose.yml
sed -i "s/MysqlPassword/${MYSQL_PASSWORD}/g" docker-compose.yml
sed -i "s/MysqlTcpPort/${MYSQL_TCP_PORT}/g" docker-compose.yml
sed -i "s/WordPressDbHost/${DB_CONTAINER_NAME}:${MYSQL_TCP_PORT}/g" docker-compose.yml
sed -i "s/MysqlContainerName/${DB_CONTAINER_NAME}/g" docker-compose.yml

cat docker-compose.yml

######
#  Redeploy or don't deploy if max deployments reached
#
#echo "${SSH_KEY}"  | base64 --decode > /tmp/sshkey.pem
#chmod 400 /tmp/sshkey.pem
#SHORT_SERVICE_NAME="${SERVICE_NAME:0:15}"
LW_REPO_NAME_LENGTH=`echo ${LW_REPO_NAME} |awk '{print length}'`
SHORT_SERVICE_NAME="${SERVICE_NAME:0:$LW_REPO_NAME_LENGTH}"
echo "short service name: ${SHORT_SERVICE_NAME}"



function deploy_service(){
#####
#deploy the container
#
# make sure docker-compose dir exists and is clean
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} "if [ -d /services/${SERVICE_NAME} ];  then sudo docker-compose -f /services/${SERVICE_NAME}/docker-compose.yml down; fi"
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} "if [ -d /services/${SERVICE_NAME} ];  then rm -rf /services/${SERVICE_NAME}; fi && mkdir /services/${SERVICE_NAME}"
scp  -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem docker-compose.yml ${SSH_USER}@${DEPLOYMENT_SERVER}:/services/${SERVICE_NAME}/docker-compose.yml
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " cd /services/${SERVICE_NAME}/ && sudo docker-compose pull"
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " cd /services/${SERVICE_NAME}/ && sudo docker-compose up -d"
sleep 10 && ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker ps -a | grep -i ${SERVICE_NAME}" 

echo "Docker-compose is in : /services/${SERVICE_NAME} "

#############
#
# workaround for froala plugin to consume unpublished core library : deactivate plugin & replace files 
#
sleep 30
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp core update --allow-root "
##ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp core install --allow-root --url=https://${DEPLOYMENT_URL} --admin_user=admin --admin_email=gangadhar.k@celestialsys.com --title=test "
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp plugin deactivate froala --allow-root && sleep 5" 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} rm -rf /var/www/html/wp-content/plugins/froala/admin/css && sleep 5" 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} rm -rf /var/www/html/wp-content/plugins/froala/admin/js && sleep 5" 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} cp -pr /package/js /var/www/html/wp-content/plugins/froala/admin/ && sleep 5 " 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} cp -pr /package/css /var/www/html/wp-content/plugins/froala/admin/ && sleep 5 && chown -R www-data:www-data  /var/www/html/wp-content/plugins/froala/ "
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker stop ${CONTAINER_NAME} && sleep 5" 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker start ${CONTAINER_NAME} && sleep 5" 
ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker exec ${CONTAINER_NAME} wp plugin activate froala --allow-root && sleep 5" 

echo "\n If no error above (cp related errors) then froala plugin is consuming the unpublished core library \n" 

#############

#############
#
# validate deployment
#
sleep 10
RET_CODE=`curl -k -s -o /tmp/notimportant.txt -w "%{http_code}" https://${DEPLOYMENT_URL}`
echo "validation code: $RET_CODE for  https://${DEPLOYMENT_URL}"
if [ $RET_CODE -ne 200 ]; then 
	echo "Deployment validation failed!!! Please check pipeline logs." 
	exit -1 
else 
	echo " Service available at URL: https://${DEPLOYMENT_URL}/wp-admin/"
	exit 0
fi


}  

#using SHORT_SERVICE_NAME to redeploy the service
REDEPLOYMENT=`ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker ps -a | grep -i "${DEPLOYMENT_IS_RUNNING}" | wc -l" `
echo "${DEPLOYMENT_IS_RUNNING}"
echo "checking if this PRD exists & do redeploy: ${REDEPLOYMENT}"
if [ ${REDEPLOYMENT} -eq 1 ]; then 
#	REDEPLOYMENT=true
	echo "Redeploying service: ${SERVICE_NAME} ..."
	deploy_service  #call deploy_service function
#else REDEPLOYMENT=false
fi

# verfify existing deployments nr for this particular env
# use only repo name for pattern
# probably a problem when pattern conflicts - eg cake3  & cake3php : pattern for cake3 might generate false conditional results - 5 cake3php deployments & 1 cake3 means no new cake3 service deployment even max nr it's not reached

EXISTING_DEPLOYMENTS=`ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker ps  | grep -i "${LW_REPO_NAME}" | wc -l" `

if [ ${EXISTING_DEPLOYMENTS} -ge ${MAX_DEPLOYMENTS_NR} ]; then
	echo "Maximum deployments reached  on ${SDK_ENVIRONMENT} environment for ${BUILD_REPO_NAME}  ; existing deployments: ${EXISTING_DEPLOYMENTS} ; max depl: ${MAX_DEPLOYMENTS_NR} "
	echo "Stopping container  ${OLDEST_CONTAINER} ..."
	RCMD='ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem  '
	RCMD="${RCMD} ${SSH_USER}@${DEPLOYMENT_SERVER} "
	REM='" sudo docker stop '
	RCMD="${RCMD} $REM ${OLDEST_DATABASE_CONTAINER} ${OLDEST_CONTAINER} "'"'
#	echo "ssh -o "StrictHostKeyChecking no" -i  /tmp/sshkey.pem ${SSH_USER}@${DEPLOYMENT_SERVER} " sudo docker stop ${OLDEST_CONTAINER}"
        echo $RCMD | bash
	sleep 12
#	echo "Please cleanup environment manually before redeploy"
	
	deploy_service  #call deploy_service function
#	exit -1
else 
	echo "Deploying service ..."
	deploy_service  #call deploy_service function
fi
#