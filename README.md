# FreshRSS - Teem video extension

This FreshRSS extension allows you to directly watch the videos from the [Teem](https://jointheteem.com) #JOINTHETEEM feed.

To use it, upload the ```xExtension-Teem``` directory to the FreshRSS `./extensions` directory on your server and enable it on the extension panel in FreshRSS.

## The feed URL

It handles videos for the RSS feed URL: https://jointheteem.com/feed/

## Roadmap

- Currently does not support Vimeo videos
- Display warning in extension panel, if YouTube is not installed 

## Requirements

This extension uses the [YouTube Extension](https://github.com/kevinpapst/freshrss-youtube) and needs at least v0.5

The YouTube extension must be installed and activated, otherwise this one will not work. 

## Installation

The first step is to put the extension into your FreshRSS extension directory:
```
cd /var/www/FreshRSS/extensions/
wget https://github.com/kevinpapst/freshrss-teem/archive/master.zip
unzip master.zip
mv freshrss-youtube-master/xExtension-Teem .
rm -rf freshrss-teem-master/
```

Then switch to your browser https://localhost/FreshRSS/p/i/?c=extension and activate it.

## About FreshRSS
[FreshRSS](https://freshrss.org/) is a great self-hosted RSS Reader written in PHP, which is can also be found here at [GitHub](https://github.com/FreshRSS/FreshRSS).

More extensions can be found at [FreshRSS/Extensions](https://github.com/FreshRSS/Extensions).

## Changelog

0.1: 
* Initial version supporting YouTube videos
