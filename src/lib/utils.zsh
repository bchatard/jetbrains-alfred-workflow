

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
        appPath=`getAppPath $1`
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
