#!/bin/zsh
ORIG_IFS=${IFS}
IFS='#########################'

. ./lib/utils.zsh
. ./lib/workflow.zsh


BIN=$1
QUERY=$2

##
 # 0: disabled / 1: enabled
 # if enabled log information in file debug_YMD.log (in current directory)
DEBUG=0


XPATH_RECENT_PROJECT_DIRECTORIES="//component[@name='RecentDirectoryProjectsManager']/option[@name='recentPaths']/list/option/@value"
XPATH_RECENT_PROJECTS="//component[@name='RecentProjectsManager']/option[@name='recentPaths']/list/option/@value"

XPATH_PROJECT_NAME="(//component[@name='ProjectView']/panes/pane[@id='ProjectPane']/subPane/PATH/PATH_ELEMENT/option/@value)[1]"

##
 # Retrieve project name from project configuration
 #  search project name in this file because project name can be different than folder name
 #   ex: folder: my-project ; project name: My Private Project
 #
 # @param $1 string (path to project)
 # @return string
extractProjectName()
{
    nameFile="$1/.idea/.name"
    workspaceFile="$1/.idea/workspace.xml"
    if [[ -r ${nameFile} ]]; then
        addDebug "extractProjectName via .name"
        projectName=$(cat ${nameFile})
        echo ${projectName}
    elif [[ -r ${workspaceFile} ]]; then
        addDebug "extractProjectName via workspace.xml"
        projectName=$(xmlDecode $(xmllint --xpath ${XPATH_PROJECT_NAME} ${workspaceFile} 2>/dev/null | sed -e 's/ value="//g' -e 's/"//g'))
        echo ${projectName}
    fi
}

##
 # Retrieve project from PhpStorm configuration
 #  return a string with paths separate by a ;
 #
 # @return string
getProjectsPath()
{
    escapedHome=$(echo ${HOME} | sed -e 's/[/]/\\\//g')

    configPath=$(grep -F -m 1 'CONFIG_PATH =' ${BIN})
    configPath="${configPath#*\'}"
    configPath="${configPath%\'*}"
    addDebug "Config Path: ${configPath}"

    recentProjectDirectories="${configPath}/options/recentProjectDirectories.xml"
    recentProjects="${configPath}/options/recentProjects.xml"

    projectsPath=''

    if [[ -r ${recentProjectDirectories} ]]; then
        addDebug "Work with: ${recentProjectDirectories}"
        projectsPath=$(xmllint --xpath ${XPATH_RECENT_PROJECT_DIRECTORIES} ${recentProjectDirectories} 2>/dev/null)
    elif [[ -r ${recentProjects} ]]; then # Intellij Idea
        addDebug "Work with: ${recentProjects}"
        projectsPath=$(xmllint --xpath ${XPATH_RECENT_PROJECTS} ${recentProjects} 2>/dev/null)
    fi

    if [[ -n ${projectsPath} ]]; then
        addDebug "Raw Projects Path:\n${projectsPath}"
        projectsPath=$(xmlDecode $(echo ${projectsPath} | sed -e 's/ value="//g' -e 's/"/\\n/g' -e "s/[$]USER_HOME[$]/${escapedHome}/g"))
        addDebug "Projects Path:\n${projectsPath}"
    fi

    echo ${projectsPath}
}

##
 # Entry point
 #
 # @return string (XML for Alfred)
findProject()
{
    # Enable debug mode if needed
    enableDebug ${DEBUG}

    # Add test before run search
    appPath=$(getAppPath ${BIN})
    if [[ -z ${appPath} ]]; then
        addItem 'error' '' "Can't find command line launcher for '${BIN}'" "Create/Update command line launcher in Tools > Create Command-line Launcher" 'AlertCautionIcon.icns' 'yes' ''
    else
        addDebug "AppPath: ${appPath}"
        # Check if some projects exists
        projectsPath=$(getProjectsPath)
        if [[ -z ${projectsPath} ]]; then
            addItem 'none' '' "Can't find projects" "check configuration or contact developer" $(getAppIcon ${BIN}) 'yes' ''
        else
            # Search
            nbProject=0
            queryLowerCase=$(toLowerCase ${QUERY})

            for projectPath in "${(@f)projectsPath}"; do
                addDebug "\tProcess ${projectPath}"
                # Limit result? Can improve performance but we lose information
#                if [[ ${nbProject} -eq 9 ]]; then
#                    break;
#                fi

                projectName=$(extractProjectName ${projectPath})
                addDebug "\t Project Name ${projectName}"
                if [[ -n "${projectName}" ]]; then

                    if [[ -z "${QUERY}" ]]; then # list projects if no query
                        addItem ${projectName} "${BIN}||${projectPath}" ${projectName} ${projectPath} $(getAppIcon ${BIN}) 'yes' ${projectName}
                        ((nbProject++))
                    else
                        # search project by name or by directory name (last directory in project path)
                        projectNameLowerCase=$(toLowerCase ${projectName})
                        projectPathLowerCase=$(toLowerCase ${projectPath##*/})

                        addDebug "\t  Search '${queryLowerCase}' in '${projectNameLowerCase}' OR '${projectPathLowerCase}'"

                        if [[ ${projectNameLowerCase} = *${queryLowerCase}* ]] || [[ ${projectPathLowerCase} = *${queryLowerCase}* ]]; then
                            addDebug "\t   founded"
                            addItem ${projectName} "${BIN}||${projectPath}||${projectName}" ${projectName} ${projectPath} $(getAppIcon ${BIN}) 'yes' ${projectName}
                            ((nbProject++))
                        fi
                    fi
                fi

                addDebug ""
            done

            # if there is no project display information
            if [[ ${nbProject} -eq 0 ]]; then
                addItem 'none' '' "No project match '${QUERY}'" "No project match '${QUERY}'" $(getAppIcon ${BIN}) 'yes' ${QUERY}
            fi
        fi
    fi

    # Render XML
    getItemsXml
}

findProject

IFS=${ORIG_IFS}
