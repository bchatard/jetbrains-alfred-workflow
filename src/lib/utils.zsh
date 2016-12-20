
############################
##  String manipulations  ##
############################

##
 # Perform removeAccents & toLowerCase
 #
 # @param $1 string
 # @return string
cleanString()
{
    cleanStr=$(removeAccents $1)
    cleanStr=$(toLowerCase ${cleanStr})

    echo ${cleanStr}
}

##
 # Credits: http://www.alfredforum.com/topic/2015-encoding-issue/?p=26379
 #
 # @param $1
 # @return string
removeAccents()
{
#    echo $1 | iconv -f utf8 -t ascii//TRANSLIT
    echo $1 | iconv -f UTF8-MAC -t ascii//TRANSLIT
}

##
 # @param $1 string
 # @return string
toLowerCase()
{
    echo "${(L)${1}}"
}

#############################
##  Manage App & App Icon  ##
#############################

APP_PATH=''
APP_ICON=''

##
 # Retrieve App Icon
 #
 # @param $1 string (bin path - usually ${BIN})
 # @return string
getAppIcon()
{
    if [[ -z "${APP_ICON}" ]]; then
        appPath=$(getAppPath $1)
        if [[ -n "${appPath}" ]]; then
            APP_ICON="fileicon:${appPath}"
        fi
    fi

    echo ${APP_ICON}
}

##
 # Retrieve App path
 #
 # @param $1 string (bin path - usually ${BIN})
 # @return string
getAppPath()
{
    if [[ -z "${APP_PATH}" ]]; then
        runPath="$(grep -F -m 1 'RUN_PATH =' $1)"
        runPath="${runPath#*\'}"
        runPath="${runPath%\'*}"
        if [[ -d "${runPath}" ]]; then
            APP_PATH="${runPath}"
        fi
    fi

    echo ${APP_PATH}
}

###########
## Debug ##
###########

DEBUG_ENABLED=0
DEBUG_FILENAME=''

enableDebug()
{
    if [[ $1 -eq 1 ]]; then
        DEBUG_ENABLED=1
        DEBUG_FILENAME="debug_$(date +'%Y%m%d').log"
        if [[ ! -w ${DEBUG_FILENAME} ]]; then
            echo "" > ${DEBUG_FILENAME}
        fi
    fi

}

addDebug()
{
    if [[ ${DEBUG_ENABLED} -eq 1 ]]; then
        echo $1 >> ${DEBUG_FILENAME}
    fi
}
