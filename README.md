
# MauticCustomDeduplicateBundle

Plugin allow custom deduplicate contacts. Plugin extend current Mautic deduplicate options https://github.com/mautic/mautic/pull/5344
and prevent multiple deduplicate run.

## Support my projects

- https://mtcextendee.com/

## Installation

### Command line
- composer require mtcextendee/mautic-custom-deduplicate-bundle
- php app/console mautic:plugins:reload
- Go to /s/plugins and setup  Custom Deduplicate integration

### Manual 
- Download last version https://github.com/mtcextendee/mautic-custom-deduplicate-bundle/releases
- Unzip files to plugins/MauticCustomDeduplicateBundle
- Go to /s/plugins/reload
- Setup  Custom Deduplicate integration

## Setup Custom Deduplicate

- Merge If these fields exists - merge If all of these fields are duplicate
- Skip If these fields are not empty - skip If one of these fields are not empty

![image](https://user-images.githubusercontent.com/462477/57339919-e58cee00-7133-11e9-9488-797ece50a81a.png)

## Usage

- Require this pull request https://github.com/mautic/mautic/pull/7502
- By command: `php app/console mautic:contacts:deduplicate:custom`
- BY UI 

![image](https://user-images.githubusercontent.com/462477/57340051-7b287d80-7134-11e9-9caf-9f91b0482793.png)


<div>Icons made by <a href="https://www.flaticon.com/authors/metropolicons" title="Metropolicons">Metropolicons</a> from <a href="https://www.flaticon.com/"             title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"             title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
