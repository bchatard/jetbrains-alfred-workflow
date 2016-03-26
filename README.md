JetBrains: Open project
========================

Alfred2 workflow to easily open your projects with your favorite JetBrains product.

## Requirements
This workflow need one of JetBrains products, and its command line launchers to works:

Example with PhpStorm:

1. Open PhpStorm
2. Go to _Tools_ and _Create Command-line Launcher_
3. In the popup windows, just click on OK

### Customisation
If you change command line tools name, you need to update workflow settings:

1. Go to Workflows and select "JetBrains - Open project"
2. Double click on a box _Run script_ for a JetBrains product (example: WebStorm)
  * Change value in _Keyword_ field
  * Change bin value _Script_ field: `which YOUR_BIN_NAME`
  * Save

## Installation
1. Download workflow from `package` folder
2. Double click on downloaded file (JetBrains - Open project.alfredworkflow)


## How to use
* Open Alfred with your usual hotkey
* Type keyword (example `pstorm`) followed by your project name
![phpstorm-alfred-workflow](https://lh3.googleusercontent.com/Zk8MiGBiZh0hrJ_0YsaINoIdnbeARwi4bcDthcHg_JE=w1335-h420-no)

## Supported versions
I test with this products/versions:

* PhpStorm: v10 / 2016.x ;
* WebStorm: v11 / 2016.x ;
* PyCharm CE: v5 / 2016.x ;
* RubyMine: v8 ;
* IntelliJ Idea CE: v15 / 2016.x ;
* CLion: v1.2 / 2016.x ;
* AppCode: v3.3 ;

NB: about PhpStorm, if you need compatibility with older version please use my old workflow: [PhpStorm Alfred Workflow](https://github.com/bchatard/phpstorm-alfred-workflow)

## Credits
[Bash Workflow Handler](https://github.com/markokaestner/bash-workflow-handler)
