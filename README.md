
# MauticCustomDeduplicateBundle

Plugin allow custom deduplicate contacts. Plugin extend current Mautic deduplicate options https://github.com/mautic/mautic/pull/5344
and prevent multiple deduplicate run.

## Support my projects

- https://mtcextendee.com/

## Installation

### Command line
- `composer require mtcextendee/mautic-custom-deduplicate-bundle`
- `php app/console mautic:plugins:reload`
- Go to /s/plugins and setup Custom Deduplicate integration

### Manual 
- Download last version https://github.com/mtcextendee/mautic-custom-deduplicate-bundle/releases
- Unzip files to plugins/MauticCustomDeduplicateBundle
- Go to /s/plugins/reload
- Setup Custom Deduplicate integration

## Setup Custom Deduplicate

- Merge If these fields exists - merge If all of these fields are duplicate
- Skip If these fields are not empty - skip If one of these fields are not empty

<img src="https://user-images.githubusercontent.com/462477/57339919-e58cee00-7133-11e9-9488-797ece50a81a.png" width="400px">

## Usage

- Require this pull request https://github.com/mautic/mautic/pull/7502
- From console: `php app/console mautic:contacts:deduplicate:custom`
- From Mautic 

<img src="https://user-images.githubusercontent.com/462477/57340051-7b287d80-7134-11e9-9caf-9f91b0482793.png" width="250px">

If you run deduplicate process From Mautic,  you're notified by notification center.

<img src="https://user-images.githubusercontent.com/462477/57340196-0efa4980-7135-11e9-838e-53b450d09ced.png" width="250px">

## Credits

Icons made by <a href="https://www.flaticon.com/authors/metropolicons" title="Metropolicons">Metropolicons</a> from <a href="https://www.flaticon.com/"             title="Flaticon">www.flaticon.com</a>
