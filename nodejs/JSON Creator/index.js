var fs = require('fs');
var parse = require('csv-parse');

region = ""; //Region to create JSON file.

data_csv = fs.readFileSync("data/matches/" + region + ".csv");

parse(data_csv, function(err, output){
	if(err)
		console.log(err);
	else
		data = output;
	
	statsc = {};
	statsi = {};
	
	statsc["5.11"] = {};
	statsc["5.14"] = {};
	statsi["5.11"] = {};
	statsi["5.14"] = {};
	
	for(participant in data)
	{
		row = data[participant];
		
		if(typeof statsc[row[0]][row[2]] == 'undefined' && !(statsc[row[0]][row[2]] instanceof Array))
			statsc[row[0]][row[2]] = {};
		
		if(typeof statsc[row[0]][row[2]][row[3]] == 'undefined')
			statsc[row[0]][row[2]][row[3]] = 0;

		if(typeof statsc[row[0]][row[2]][row[4]] == 'undefined')
			statsc[row[0]][row[2]][row[4]] = 0;

		if(typeof statsc[row[0]][row[2]][row[5]] == 'undefined')
			statsc[row[0]][row[2]][row[5]] = 0;

		if(typeof statsc[row[0]][row[2]][row[6]] == 'undefined')
			statsc[row[0]][row[2]][row[6]] = 0;

		if(typeof statsc[row[0]][row[2]][row[7]] == 'undefined')
			statsc[row[0]][row[2]][row[7]] = 0;

		if(typeof statsc[row[0]][row[2]][row[8]] == 'undefined')
			statsc[row[0]][row[2]][row[8]] = 0;


		statsc[row[0]][row[2]][row[3]] += 1;
		statsc[row[0]][row[2]][row[4]] += 1;
		statsc[row[0]][row[2]][row[5]] += 1;
		statsc[row[0]][row[2]][row[6]] += 1;
		statsc[row[0]][row[2]][row[7]] += 1;
		statsc[row[0]][row[2]][row[8]] += 1;
		
		if(typeof statsi[row[0]][row[3]] == 'undefined')
			statsi[row[0]][row[3]] = 0;

		if(typeof statsi[row[0]][row[4]] == 'undefined')
			statsi[row[0]][row[4]] = 0;

		if(typeof statsi[row[0]][row[5]] == 'undefined')
			statsi[row[0]][row[5]] = 0;

		if(typeof statsi[row[0]][row[6]] == 'undefined')
			statsi[row[0]][row[6]] = 0;

		if(typeof statsi[row[0]][row[7]] == 'undefined')
			statsi[row[0]][row[7]] = 0;

		if(typeof statsi[row[0]][row[8]] == 'undefined')
			statsi[row[0]][row[8]] = 0;
		
		statsi[row[0]][row[3]] += 1;
		statsi[row[0]][row[4]] += 1;
		statsi[row[0]][row[5]] += 1;
		statsi[row[0]][row[6]] += 1;
		statsi[row[0]][row[7]] += 1;
		statsi[row[0]][row[8]] += 1;
	}
	
	fs.writeFileSync("data/json/by-champion-stats/" + region + ".json", JSON.stringify(statsc, null, 4));
	fs.writeFileSync("data/json/by-item-stats/" + region + ".json", JSON.stringify(statsi, null, 4));
});
