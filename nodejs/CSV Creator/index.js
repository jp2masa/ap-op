var fs = require('fs');
var request = require('request');
var stringify = require('csv-stringify');

var api_key = ""; //API Key
var n = 0;
var region = ""; //Region to create CSV fileCreatedDate
var rg = region.toLowerCase();

region = region.toUpperCase();

fs.readFile("AP_ITEM_DATASET/5.11/NORMAL_5X5/" + region + ".json", "utf8",
	function (err, data) {
		var normal_11 = JSON.parse(data);
		fs.readFile("AP_ITEM_DATASET/5.11/RANKED_SOLO/" + region + ".json", "utf8",
			function (err, data) {
				var ranked_11 = JSON.parse(data);
				fs.readFile("AP_ITEM_DATASET/5.14/NORMAL_5X5/" + region + ".json", "utf8",
					function (err, data) {
						var normal_14 = JSON.parse(data);
						fs.readFile("AP_ITEM_DATASET/5.14/RANKED_SOLO/" + region + ".json", "utf8",
							function (err, data) {
								var ranked_14 = JSON.parse(data);

								var matches = [];
								
								matches["5.11"] = {};
								matches["5.14"] = {};
								
								matches["5.11"]["NORMAL_5X5"] = normal_11;
								matches["5.11"]["RANKED_SOLO"] = ranked_11;
								matches["5.14"]["NORMAL_5X5"] = normal_14;
								matches["5.14"]["RANKED_SOLO"] = ranked_14;
								
								for(var version in matches)
								{
									for(var type in matches[version])
									{
								        for(var match in matches[version][type])
								        {
									       	getMatch(version, type, matches[version][type][match]);
								        }
									}
								}
							}
						);
					}
				);
			}
		);
	}
);

function getMatch(version, type, matchid) {
    request("https://" + rg + ".api.pvp.net/api/lol/" + rg + "/v2.2/match/" + matchid + "?api_key=" + api_key, function(error, response, body) {
        if(!error && response.statusCode == 200)
        {
            $match_data = JSON.parse(body);
            for(var participant in $match_data["participants"])
            {
                var participant_data = $match_data["participants"][participant];

                if(participant_data['stats']['winner'])
                    var win = 1;
                else
                    var win = 0;

                var row = [[version, type, participant_data['championId'], participant_data['stats']['item0'], participant_data['stats']['item1'], participant_data['stats']['item2'], participant_data['stats']['item3'], participant_data['stats']['item4'], participant_data['stats']['item5'], win]];

                console.log(row);
				
				stringify(row, function(err, output) {
                    fs.appendFileSync('data/' + region + '.csv', output);
					
					n++;
					console.log(n);
                });
            }
        } else {
			console.log(version, type, matchid);
			fs.appendFileSync('data/' + rg + '_log.json', [version, type, matchid] + "\n");
            getMatch(version, type, matchid);
        }
    })
}
