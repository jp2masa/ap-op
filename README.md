# AP OP
Project for the Riot Games API Challenge 2.0.

##Node.js
We used 2 Node.js scripts:

* The first one was to get matches data (Patch, Match Type, Champion ID, Items and Win/Loss), which separated the data into 10 files, one for each region. The data was stored in csv.
* The second one was to register the number of ocurrences of each item, in each patch(By Item) and in each champion for each patch(By Champion). The data was separated into 2 folders and 20 files, 2 for each region(By Champion and By Item).

##PHP
We did a website in PHP to show the stats for the stored data.

The website has 2 options of viewing the data:

* The first, "By Champion" shows the stats for the use of changed items, for the selected champion, in the selected region(or globally, option "ALL").
* The second, "By Item", shows the stats for the use of changed items by region(or globally, option "ALL").

AP OP isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
