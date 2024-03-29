
=====================
Introduction to hph
=====================

hph boasts a clean HTML5 structure with extensible CSS classes and IDs for
unlimited theming possibilities as well as a top-down load order for improved
SEO. It is fully responsive out-of-the-box and provides an adaptive, elegant,
SASS-based grid system (Bourbon Neat).

hph's goal is to provide themers the building blocks needed to get their
designs up and running quickly and simply.

hph is perfect if you want a simple, smart, and flexible theme starter.

Less code spam, more ham.


============
Installation
============

hph utilizes a Sass framework for adaptive grids and layouts and general structure of the
site. It's recommended to use Sass for building out your theme. The following
packages are included via 'npm install'
  - Bourbon (http://bourbon.io/)
  - Bourbon Neat (http://neat.bourbon.io/)

hph is meant to be YOUR theme. Follow one of the two methods below and you can
rename 'hph' to another name like 'mytheme'. You're also welcome to keep
'hph'.

1. DRUSH: The provided drush install script will duplicate the hph theme
   folder and rename all appropriate files and content to the new theme name you
   provide.

  - Download and enable the hph theme: $ drush en hph
  - Set the theme as default $ drush config-set system.theme default hph
    or copy the includes/hph.drush.inc into your .drush folder.
  - Run the provided drush install script: $ drush hph-install
  - The script will first ask you to enter your theme name (eg. My Theme).
    Second, it will ask you to enter a machine name (eg. my_theme).
  - After complete, you can enable your new theme: $drush en my_theme
  - At this time, you can uninstall hph: $drush pmu hph
  - Commence work on your theme!

2. MANUAL: To manually change the name of the theme follow these steps BEFORE
   enabling the theme:

  - Rename the theme folder to 'mytheme'
  - Rename hph.info.yml to mytheme.info.yml
  - edit hph.info.yml and change the name, description, project (can be
    deleted), and change all other instances of 'hph' to 'mytheme'
  - Rename hph.libraries.yml to mytheme.libraries.yml
  - Rename hph.theme to mytheme.theme
  - in hph.theme, change each instance of 'hph' to 'mytheme'
  - Rename config/schema/hph.schema.yml to mytheme.schema.yml
  - Rename each file in config/install from block.block.hph_tools.yml (for
    example) to block.block.mytheme_tools.yml
  - Every file in config/install, change each instance of 'hph' to
    'mytheme'
  - In js/source/scripts.js, change each instance of 'hph' to 'mytheme'
  - In theme-settings.php, change each instance of 'hph' to 'mytheme'
  - In templates/html.html.twig, change each instance of 'hph' to 'mytheme'
  - In templates/menu-local-tasks.html.twig, change each instance of 'hph' to
    'mytheme'
  - In templates/status-messages.html.twig, change each instance of 'hph' to
    'mytheme'

  When renaming, remember the following:
  - Do not simply replace every instance of 'hph' in every file in the theme.
    Most of hph's dependencies use the word 'hph' somewhere and renaming
    these instances will cause hph to break in unpredictable ways.
  - If you don't rename all these files, you may get a vague and unhelpful error
    message when attempting to enable your theme: "The website encountered an
    unexpected error. Please try again later." Turn on a higher level of error
    logging in your server's php.ini to help determine what you've missed.
  - If you don't bother renaming hph in the above locations, be advised that
    you will run into conflicts with other versions of hph on your site. If
    your site uses more than one theme based on hph, make sure at least one of
    the themes has been renamed properly!


============================
How to compile Sass in hph
============================

To use Sass and automatically compile it within your theme, please refer to "How
to Use Gulp with hph" in the documentation below.

Install node-sass:

  npm install node-sass -g

If you don't like Gulp, or would just prefer to use Sass' internal watch
functionality, simply cd into your theme directory and run:

  node-sass sass -o css --output-style expanded --source-map true --watch

Or simply compile the latest:

  node-sass sass -o css --output-style expanded --source-map true


=======================
What are the files for?
=======================

- hph.info.yml
  Provide informations about the theme, like regions and libraries.
- block.html.twig
  Template to edit the blocks.
- comment.html.twig
  Template to edit the comments.
- node.html.twig
  Template to edit the nodes (in content).
- page.html.twig
  Template to edit the page.
- hph.theme
  Used to modify Drupal's default behavior before outputting HTML through the
  templates.
- theme-settings.php
  Provides additional settings in the theme settings page.


============
In /sass
============

- layout/layout.scss
  Defines the layout of the theme (compiles to css/layout/layout.css)
- theme/print.scss
  Defines the way the theme looks when printed (compiles to css/theme/print.css)
- components/tabs.scss
  Styles for the admin tabs (compiles to css/components/tabs.css)


============
In /js
============

- modernizr.js
  Modernizr detects HTML and CSS features and applies classes to
  the <html> object you can then reference in your stylesheets. Use the URL at
  the top of the modernizr.js file to customize the features you wish to detect.
- selectivizr-min.js
  This script will only be loaded for Internet Explorer 8
  through the ie8 theme library. It will provide a JS fallback for CSS :nth-
  child, an important part of the Bourbon Neat grid system, as it is not
  supported in Internet Explorer 8.
- build/scripts.js & source/scripts.js
  When using Gulp, save files to the
  source folder and a minified version will automatically be saved to the build
  folder. See comments in hph.libraries.yml file to enable the starter
  scripts.js file.


===================
Changing the Layout
===================

The layout used in hph is fairly similar to the Holy Grail method. It has been
tested on all major browsers including IE (5 to >10), Opera, Firefox, Safari,
and Chrome. The purpose of this method is to have a minimal markup for an ideal
display. For accessibility and search engine optimization, the best order to
display a page is the following:

1. Header
2. Content
3. Sidebars
4. Footer

This is how the page template is buit in hph, and it works in fluid and fixed
layout. Refer to the notes in layout.sass to see how to modify the layout.


===========================
How to Use Gulp with hph
===========================

Gulp (https://gulpjs.com/) requires Node.JS to be installed on your machine.
There are various package managers that can handle this for you.

https://nodejs.org/download/

Once Node.JS is installed, go to the root folder of hph and install your Gulp
packages:

  npm install

This will install the neccessary node_modules to run Gulp, including Gulp itself.
Once installed, cd to the root folder of hph and run Gulp via the command
line:

  gulp

This will initialize gulp, compile Sass (as .scss files) to CSS, transpile and 
uglify your JS, and start watching changes to your files. Voilà!


==============
Updating hph
==============

Once you start using hph, you will massively change it until you reach the
point where it has nothing to do with hph anymore. Unlike Zen, hph is not
intended to be use as a base theme for a sub-theme (even though it is possible
to do so). Because of this, it is not necessary to update your theme when a new
version of hph comes out. Always see hph as a STARTER, and as soon as you
start using it, it is not hph anymore, but your own theme.

If you didn't rename your theme, but you don't want to be notified when hph
has a new version by the update module, simply delete "project = "hph" in
hph.info.yml.


================
Bugs & Questions
================

Thanks for using hph, and remember to use the issue queue in drupal.org for
any questions or bug reports:

http://drupal.org/project/issues/hph


====================
Current maintainers:
====================
* Steve Krueger (SteveK)              - https://www.drupal.org/u/stevek
* Leah Marsh (leahtard)               - https://www.drupal.org/u/leahtard
* Joël Pittet (joelpittet)            - https://www.drupal.org/u/joelpittet
* Catherine Winters (CatherineOmega)  - https://www.drupal.org/u/catherineomega
* Johannes Schmidt (johannez)         - https://www.drupal.org/u/johannez
* Chuck Kosman (cwkosman)             - https://www.drupal.org/u/cwkosman
