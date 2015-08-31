# AP OP
Project for the Riot Games API Challenge 2.0.

This project is open source and was created for the Riot Games API Challenge 2.0 in about 15 days of hard work.

##Node.js
We used 2 Node.js scripts:

* The first one was to get matches data (Patch, Match Type, Champion ID, Items and Win/Loss), which separated the data into 10 files, one for each region. The data was stored in csv.
* The second one was to register the number of ocurrences of each item, in each patch(By Item) and in each champion for each patch(By Champion). The data was separated into 2 folders and 20 files, 2 for each region(By Champion and By Item).

##PHP
We did a website in PHP to show the stats for the stored data.

The website has 2 options of viewing the data:

* The first, "By Champion" shows the stats for the use of changed items, for the selected champion, in the selected region(or globally, option "ALL").
* The second, "By Item", shows the stats for the use of changed items by region(or globally, option "ALL").

We used [Bootstrap v3.3.5](http://getbootstrap.com/), [jQuery 2.1.4](http://jquery.com/) and [Tooltipster 3.0(jQuery plugin)](http://iamceege.github.io/tooltipster/).

AP OP isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
