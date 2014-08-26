# Cloner Hacks #

This repo stores some snippets to extend  [Cloner Plugin](https://premium.wpmudev.org/project/cloner/).


## Why should I extend Cloner? ##

Cloner tries to make an exact copy from a subsite to another using WP Native functions. That means that WP will assign automatically Post, pages or CPTs IDs so these IDs will be different in the destination site from the source site.

Sometimes, plugins or themes save in DB references to Posts IDs. It's impossible for Cloner to know if those IDs are posts or what so we'll need to add manually some code to extend Cloner and remap those Posts IDs. One of the most powerful things of Cloner is that it comes with many, many filters. Let's use them to customize it.

## What kind of extensions am I going to find here? ##

As soon as I discover plugins/themes that need a snippet, I'll put it here. Most famous plugins or themes will be inserted in the Cloner code so you won't need to search here.

## How do I use this code? ##

Navigate through folders to search your theme/plugin snippet. There should be only one file per plugin or theme. Once you find it, click on the file and then, click on **Raw** button and copy hte file content.

Then go to your site through FTP, for instance, create a new file into your wp-content/mu-plugins folder (if it does not exist, create it now). Called your file whatever you like but I'd call it **copier-hooks-the-slug-of-your-plugin.php** so you don't get confused.

Paste the contents to this new file. MU Plugins are loaded always automatically. Next time you clone something, the code will work on its own, you don't need to do anything.
