JetBrains: Open project
========================

__Alfred3__ workflow to easily open your projects with your favorite JetBrains product.

⚠ Currently in beta, feedbacks welcome - [stable](https://github.com/bchatard/jetbrains-alfred-workflow/tree/v1.0)


## Requirements
You need Alfred __3.5+__

This workflow need one of JetBrains products, and its command line launchers to works:


### Create command line launcher
Example with PhpStorm:

1. Open PhpStorm
2. Go to _Tools_ and _Create Command-line Launcher_
![jetbrains-alfred-workflow-create-cli](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/requirement/jetbrains-alfred-workflow-create-cli.png)
3. In the popup windows, just click on OK
![jetbrains-alfred-workflow-create-cli-2](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/requirement/jetbrains-alfred-workflow-create-cli-2.png)


## Installation
1. Download workflow from `package` folder, or [here](https://github.com/bchatard/jetbrains-alfred-workflow/blob/master/package/JetBrains%20-%20Open%20project.alfredworkflow?raw=true)
2. Double click on downloaded file (JetBrains - Open project.alfredworkflow)

Alfred2 user, please use [v1.0](https://github.com/bchatard/jetbrains-alfred-workflow/tree/v1.0)


## How to use
* Open Alfred with your usual hotkey
* Type keyword (example `pstorm`) followed by your project name
![jetbrains-projects-secret-light](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/jetbrains-projects-secret-light.png)


### Default Keywords
 * AppCode: `appcode` ;
 * CLion: `clion` ;
 * IntelliJ Idea: `idea` ;
 * PhpStorm: `pstorm` (before 2016.3) or `phpstorm` (2016.3+) ;
 * PyCharm: `charm` ;
 * RubyMine: `mine` ;
 * WebStorm: `wstorm` (before 2016.3) or `webstorm` (2016.3+) ;
 * Android Studio: `studio` ;
 * Gogland: `gogland` ;
 * Rider: `rider` ;
 * DataGrip: `datagrip` (since v2.0.0-beta7) ;


### Supported versions
I test with this products/versions:

* AppCode: v3.3 ;
* CLion: v1.2 / 2016.x / 2017.x  ;
* IntelliJ Idea CE: v15 / 2016.x / 2017.x  ;
* PhpStorm: v10 / 2016.x / 2017.x  ;
* PyCharm CE: v5 / 2016.x / 2017.x  ;
* RubyMine: v8 / 2016.x / 2017.x  ;
* WebStorm: v11 / 2016.x / 2017.x  ;
* Android Studio: v2.x ;
* Gogland: 2017.x ; 
* Rider: 2017.x ;
* DataGrip: 2017.3+

NB: about PhpStorm, if you need compatibility with older version please use my old workflow: [PhpStorm Alfred Workflow](https://github.com/bchatard/phpstorm-alfred-workflow)

NB2: PhpStorm & WebStorm change default bin name in 2016.3 [#9](https://github.com/bchatard/jetbrains-alfred-workflow/issues/9).


## JetBrains Actions

With keyword `jb` you can access to some actions for this workflow.

### Clean Projects Cache
Projects list is cached for better performance (one cache per products). You can change cacle lifetime (see [environment variable](#workflow-environment-variables)). This command clean cache for all products.


## Customisation
If you change command line tools name/path or if you want to change keyword, you need to update workflow settings:

1. Go to Workflows and select "JetBrains - Open project"
![jetbrains-workflow-script-filter](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/customisation/jetbrains-workflow-script-filter.png)
2. Double click on a box _Run script_ for a JetBrains product (example: PhpStorm)
  * Change value in _Keyword_ field
  * Change bin value _Script_ field: `echo (new Project('/YOUR/BIN/NAME/PATH'))->search("{query}");`
  * Save
![jetbrains-workflow-script-edit](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/customisation/jetbrains-workflow-script-edit.png)


## Known issue
After an application upgrade you need to update the _Command-line launcher_
![jetbrains-alfred-caskroom](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/jetbrains-alfred-update.png)


## FAQ

### How to update the Command-line launcher after the update of my software?
> Follow "[Create command line launcher](#create-command-line-launcher)" instructions again.

### How to update the workflow
> Download the latest package and open it

## Debug

1. Go to Workflows and select "JetBrains - Open project"
2. Click on _Configure workflow and variables_
![jetbrains-alfred-workflow-debug](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/debug/jetbrains-alfred-workflow-debug.png)
3. Change environment variable `jb_debug` to `1`
4. Open Alfred with your usual hotkey, and type keyword (example `pstorm`) followed by debug
5. You will see something like this, press ⌘ + C to copy path to log file
![jetbrains-alfred-workflow-log](https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/doc/img/debug/jetbrains-alfred-workflow-log.png)


## Workflow Environment Variables
* `jb_debug`: enable [debug mode](#debug)
* `jb_cache_lifetime`: cache lifetime in seconds for projects list (default: 3600 seconds)


## Changelog
[Changelog](CHANGELOG.md)

## License

[MIT](LICENSE) © [bchartard](https://github.com/bchatard)
