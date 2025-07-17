# Table of Contents
1. [COE Lobby Screen](#coe-lobby-screen)
2. [Motivation](#motivation)
3. [Installation and Usage](#installation-and-usage)
4. [Framework/Tech Used](#frameworktech-used)
5. [Reinstallation](#reinstallation)

## COE Lobby Screen
Project utilizes the screens in the Packard Lab lobby to make an interactive page to help show prospective Lehigh students what the college of engineering has to offer. 

## Motivation
The folks over at the college of engineering wanted put the 2x2 grid of screens in the lobby screen to good use. To that end, they made it a capstone project that our team will work on over the Spring/Fall 2023 semesters.

## Installation and Usage
To use the code, after cloning the repository, open the index.html file in a browser to view the main landing page. From there, clicking on various other pages and videos will go to that content. Since the project is still in development, some content will be changed and added with most updates. When used on the lobby screens, a keypad will be used to click buttons, move the mouse, full screen videos, and more.

## Framework/Tech Used
The code is written in HTML, CSS, and JavaScript. But later on, other language may be added to enhance functionality.We incorporated the [Youtube's iFrame API](https://developers.google.com/youtube/iframe_api_reference) to control video playback and ensure all videos are fullscreened on start. We also used the [Google Maps API](https://developers.google.com/maps) to display in interactive map of Lehigh with pins to mark points of interest.

## Reinstallation
Through our development, we've noted a few tools are required to achieve our vision for this project. To get the project completely working on a new computer, download the [Unhook extension for google chrome](https://chrome.google.com/webstore/detail/unhook-remove-youtube-rec/khncfooichmfjbepaaaebmommgaepoid), which will allow a user to hide the end screen cards/feed on youtube after the videos are done playing. Ensure that, after installed, you UNCHECK the option to disable autoplay. Next, for autoplay to work with sound, go to google chromes root folder to chrome.exe. Right click and go to properties and add to the end of the target the following Command:
chrome.exe –autoplay-policy=no-user-gesture-required

