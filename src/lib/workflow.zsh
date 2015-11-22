
# based on https://github.com/markokaestner/bash-workflow-handler
#  simplified for my need

ITEMS=()

##
 # Add item
 #  doc: https://www.alfredapp.com/help/workflows/inputs/script-filter/#xml
 #
 # @param $1 string (UID)
 # @param $2 string (arg)
 # @param $3 string (title)
 # @param $4 string (subtitle)
 # @param $5 string (icon)
 # @param $6 string (valid)
 # @param $7 string (autocomplete)
addItem()
{
    ITEM="<item uid=\"$(xmlEncode "$1")\" valid=\"$6\" autocomplete=\"$7\">"
    ITEM="${ITEM}<title>$(xmlEncode "$3")</title>"
    ITEM="${ITEM}<subtitle>$(xmlEncode "$4")</subtitle>"
    ITEM="${ITEM}<arg>$(xmlEncode "$2")</arg>"
    if [[ ! "$5" == '' ]]; then
        if [[ $5 =~ fileicon:* ]]; then
            icon=`echo $5 | sed -e 's/fileicon://g'`
            ITEM="${ITEM}<icon type=\"fileicon\">$(xmlEncode "${icon}")</icon>"
        elif [[ $5 == *icns ]]; then
            icon="/System/Library/CoreServices/CoreTypes.bundle/Contents/Resources/$5"
            ITEM="${ITEM}<icon>$(xmlEncode "${icon}")</icon>"
        else
            ITEM="${ITEM}<icon>$(xmlEncode "$5")</icon>"
        fi
    fi
    ITEM="${ITEM}</item>"

    ITEMS+=("$ITEM")
}

xmlEncode()
{
    echo "$1" | sed -e 's/&/\&amp;/g' -e 's/>/\&gt;/g' -e 's/</\&lt;/g' -e "s/'/\&apos;/g" -e 's/"/\&quot;/g'
}

getItemsXml()
{
    echo "<?xml version=\"1.0\"?><items>"
    for R in ${ITEMS[*]}; do
        echo "$R" | tr "\n" " "
    done
    echo "</items>"
}
