<?php
	$pagename = "AP Item Usage Stats";
	
	$ap_items = array(1026, 1058, 3003, 3027, 3040, 3089, 3115, 3116, 3135, 3136, 3151, 3152, 3157, 3174, 3285);
	
	$data_items_json = file_get_contents("data/items/item.json");
	$data_items = json_decode($data_items_json, true);

	if(isset($_GET["sort"]))
		$sort = $_GET["sort"];
	
	if(isset($_GET["champion"]))
		$champion = $_GET["champion"];
	
	if(isset($_POST["region"]))
		$region = $_POST["region"];
	else
		$region = "ALL";
?>

<div class="content text-background">
	<div class="btn-group" role="group" aria-label="Stats Sort">
		<a class="btn btn-default<?php if($sort != "by-champion" && $sort != "by-item") echo " active"; ?>" href="/item-stats">AP Item Usage Stats</a>
		<a class="btn btn-default<?php if($sort == "by-champion") echo " active"; ?>" href="/item-stats/by-champion">By Champion</a>
		<a class="btn btn-default<?php if($sort == "by-item") echo " active"; ?>" href="/item-stats/by-item">By Item</a>
	</div>
<?php
	if($sort != "by-champion" && $sort != "by-item" && isset($sort))
	{
?>
	<div class="alert alert-warning" role="alert">
		<p><b>Warning! Couldn't find type of sort "<?php echo $sort ?>"!</b></p>
		<p>Displaying AP Item Usage Page.</p>
	</div>
<?php
	} else if(!isset($sort) || ($sort != "by-champion" && $sort != "by-item")) {
?>
	<div class="item-stats-home">
		<h1>Welcome to AP Item Usage Stats!</h1>
		<p>In this feature, you have two different ways to see the difference between the AP Item Usage, before and after the <a href="http://na.leagueoflegends.com/en/news/game-updates/patch/patch-513-notes#patch-ability-power-itemization">AP Item Changes</a>: By Champion or By Item.</p>
		<p>Here is the top 5 of the most used changed items, before and after the changes:</p>
		<div>
<?php
	$items_ap = array();
	
	$items_ap["5.11"] = array();
	$items_ap["5.14"] = array();
	
	foreach(glob("data/by-item-stats/*.json") as $filename)
	{
		$items_data_json = file_get_contents($filename);
		$items_data = json_decode($items_data_json, true);
	
		foreach($items_data as $patch => $patch_array)
		{
			foreach($patch_array as $item => $item_count)
			{
				if(in_array($item, $ap_items))
				{
					$items_ap[$patch][$item] += $item_count;
					$items_count[$patch] += $item_count;
				}
			}
		}
	}

	arsort($items_ap["5.11"]);
	arsort($items_ap["5.14"]);
		
	foreach($items_ap as $patch => $patch_array)
	{
?>
			<table class="table">
				<tbody>
					<tr>
						<td class="title"><h2><?php echo $patch; ?></h2></td>
					<tr>
<?php
		$n = 1;
		
		foreach($patch_array as $item => $item_count)
		{
			if($n <= 5)
			{
?>
					<tr>
						<td><h3><?php echo $n; ?></h3></td>
						<td><img src="/img/items/<?php echo $item; ?>.png"></img></td>
						<td><h3><?php echo $data_items["data"][$item]["name"]; ?></h3></td>
						<td><h3><?php echo (round(($item_count / $items_count[$patch] * 100), 2)); ?>%</h3></td>
					</tr>
<?php
			}
				$n++;
		}
?>
				</tbody>
			</table>
<?php
		}
?>
		</div>
	</div>
<?php
	} else if($sort == "by-champion") {
		$data_champions_json = file_get_contents("data/champions/champion.json");
		$data_champions = json_decode($data_champions_json, true);
		
		if(isset($champion))
		{
			if(isset($data_champions["data"][$champion]))
			{
?>
		<div class="region-selection">
			<form action="/item-stats/<?php echo "$sort/$champion"; ?>" method="POST" role="form">
				<div class="btn-group">
					<button class="btn btn-default btn-sm<?php if($region == "ALL" || !file_exists("data/by-champion-stats/$region.json")) echo " active"; ?>" name="region" type="submit" value="ALL">ALL</button>

<?php
				foreach(glob("data/by-champion-stats/*.json") as $filename)
				{
					$file_region = basename($filename, ".json");
					$by_champion_json[$region] = file_get_contents($filename);
?>
					<button class="btn btn-default btn-sm<?php if($region == $file_region) echo " active"; ?>" name="region" type="submit" value="<?php echo $file_region; ?>"><?php echo $file_region; ?></button>
<?php
				}
?>
				</div>
			</form>
		</div>
		<div class="help-text">
			<p>Now you can compare the usage of the AP Items.</p>
		</div>
<?php
				if(file_exists("data/by-champion-stats/$region.json"))
				{
					$items_data_json = file_get_contents("data/by-champion-stats/$region.json");
					$items_data = json_decode($items_data_json, true);
					
					$ap_items_id_name = array();
					
					foreach($ap_items as $ap_item)
					{
						$ap_items_id_name[$data_items["data"][$ap_item]["name"]] = $ap_item;
					}
					
					$items_ap = array();
					$items_ap_ids = array();
					
					$items_ap["5.11"] = array();
					$items_ap["5.14"] = array();
					$items_ap_ids["5.11"] = array();
					$items_ap_ids["5.14"] = array();
					
					foreach($items_data as $patch => $patch_array)
					{
						foreach($patch_array[$data_champions["data"][$champion]["key"]] as $item => $item_count)
						{
							if(in_array($item, $ap_items))
							{
								$items_ap[$patch][$data_items["data"][$item]["name"]] = $item_count;
								$items_ap_ids[$patch][$data_items["data"][$item]["name"]] = $item;
								$items_count[$patch] += $item_count;
							}
						}
					}
													
					$patch_difference = array();
					
					$patch_difference["5.11"] = array();
					$patch_difference["5.14"] = array();
?>
		<div class="item-stats">
			<div>
<?php				
					foreach($items_ap["5.11"] as $item_name => $item_count)
					{
						if(!isset($items_ap["5.14"][$item_name]))
						{
							$items_ap["5.14"][$item_name] = 0;
							$items_ap_ids["5.14"][$item_name] = $ap_items_id_name[$item_name];
						}
					}
				
					foreach($items_ap["5.14"] as $item_name => $item_count)
					{
						if(!isset($items_ap["5.11"][$item_name]))
						{
							$items_ap["5.11"][$item_name] = 0;
							$items_ap_ids["5.11"][$item_name] = $ap_items_id_name[$item_name];
						}
					}
					
					if(isset($items_count["5.11"]) && isset($items_count["5.14"]))
					{
						foreach($items_ap as $patch => $patch_array)
						{
							ksort($items_ap[$patch]);
?>
				<table class="table">
					<tbody>
						<tr>
							<td class="title">
								<h2><?php echo $patch; ?></h2>
							</td>
						</tr>
<?php
						foreach($items_ap[$patch] as $item_name => $item_count)
						{
?>
						<tr class="item-row">
							<td>
								<img class="<?php echo $items_ap_ids[$patch][$item_name]; ?>" src="/img/items/<?php echo $items_ap_ids[$patch][$item_name]; ?>.png"></img>
								<script>
									$(document).ready(function() {
										$('.<?php echo $items_ap_ids[$patch][$item_name]; ?>').tooltipster({
											content: $("<div class=\"item-tooltip\"><h4 class=\"tooltip-title\"><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["name"]; ?></h4><span><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["description"]; ?></span></div>")
										});
									});
								</script>
							</td>
							<td>
								<h3><?php echo $item_name; ?></h3>
							</td>
							<td>
								<h3><?php if($items_count[$patch] == 0) $items_count[$patch] = 1; $patch_difference[$patch][$item_name] = round(($item_count / $items_count[$patch] * 100), 2); echo $patch_difference[$patch][$item_name]; ?>%</h3>
							</td>
						</tr>
<?php
						}
?>
					</tbody>
				</table>
<?php
					}
?>
				<table class="table">
					<tbody>
						<tr>
							<td class="title"></td>
						</tr>
<?php
				foreach($items_ap["5.11"] as $item_name => $item_count)
				{
					if($patch_difference["5.11"][$item_name] < $patch_difference["5.14"][$item_name])
						$img = "up";
					else if($patch_difference["5.11"][$item_name] > $patch_difference["5.14"][$item_name])
						$img = "down";
					else if($patch_difference["5.11"][$item_name] == $patch_difference["5.14"][$item_name])
						$img = "same";
?>
						<tr class="difference-row">
							<td>
								<img class="difference-image" src="/img/item_stats/<?php echo $img; ?>.png"></img>
							</td>
						</tr>
<?php 
				}
?>
					</tbody>
				</table>
				<script>
					$(window).load(function() {
						$(".difference-row").height($(".item-row").height());
					});
					
					$(window).on('resize', function(){
						$(".difference-row").height($(".item-row").height());
					});
				</script>
			</div>
		</div>
<?php
					} else {
?>
		<div class="alert alert-info">
			<b>Info: There are no item stats for this champion in this region. Try another region.</b>
		</div>
<?php
					}
				} else {
					$items_ap = array();
					$items_ap_ids = array();
					
					$items_ap["5.11"] = array();
					$items_ap["5.14"] = array();
					$items_ap_ids["5.11"] = array();
					$items_ap_ids["5.14"] = array();
					
					foreach(glob("data/by-champion-stats/*.json") as $filename)
					{
						$items_data_json = file_get_contents($filename);
						$items_data = json_decode($items_data_json, true);
						
						foreach($items_data as $patch => $patch_array)
						{
							foreach($patch_array[$data_champions["data"][$champion]["key"]] as $item => $item_count)
							{
								if(in_array($item, $ap_items))
								{
									$items_ap[$patch][$data_items["data"][$item]["name"]] += $item_count;
									$items_ap_ids[$patch][$data_items["data"][$item]["name"]] = $item;
									$items_count[$patch] += $item_count;
								}
							}
						}
					}
										
					$ap_items_id_name = array();
					
					foreach($ap_items as $ap_item)
					{
						$ap_items_id_name[$data_items["data"][$ap_item]["name"]] = $ap_item;
					}
		
					$patch_difference = array();
					
					$patch_difference["5.11"] = array();
					$patch_difference["5.14"] = array();
?>
		<div class="item-stats">
			<div>
<?php				
					foreach($items_ap["5.11"] as $item_name => $item_count)
					{
						if(!isset($items_ap["5.14"][$item_name]))
						{
							$items_ap["5.14"][$item_name] = 0;
							$items_ap_ids["5.14"][$item_name] = $ap_items_id_name[$item_name];
						}
					}
				
					foreach($items_ap["5.14"] as $item_name => $item_count)
					{
						if(!isset($items_ap["5.11"][$item_name]))
						{
							$items_ap["5.11"][$item_name] = 0;
							$items_ap_ids["5.11"][$item_name] = $ap_items_id_name[$item_name];
						}
					}
					
					if(isset($items_count["5.11"]) && isset($items_count["5.14"]))
					{
						foreach($items_ap as $patch => $patch_array)
						{
							ksort($items_ap[$patch]);
?>
				<table class="table">
					<tbody>
						<tr>
							<td class="title">
								<h2><?php echo $patch; ?></h2>
							</td>
						</tr>
<?php
						foreach($items_ap[$patch] as $item_name => $item_count)
						{
?>
						<tr class="item-row">
							<td>
								<img class="<?php echo $items_ap_ids[$patch][$item_name]; ?>" src="/img/items/<?php echo $items_ap_ids[$patch][$item_name]; ?>.png"></img>
								<script>
									$(document).ready(function() {
										$('.<?php echo $items_ap_ids[$patch][$item_name]; ?>').tooltipster({
											content: $("<div class=\"item-tooltip\"><h4 class=\"tooltip-title\"><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["name"]; ?></h4><span><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["description"]; ?></span></div>")
										});
									});
								</script>
							</td>
							<td>
								<h3><?php echo $item_name; ?></h3>
							</td>
							<td>
								<h3><?php if($items_count[$patch] == 0) $items_count[$patch] = 1; $patch_difference[$patch][$item_name] = round(($item_count / $items_count[$patch] * 100), 2); echo $patch_difference[$patch][$item_name]; ?>%</h3>
							</td>
						</tr>
<?php
						}
?>
					</tbody>
				</table>
<?php
					}
?>
				<table class="table">
					<tbody>
						<tr>
							<td class="title"></td>
						</tr>
<?php
				foreach($items_ap["5.11"] as $item_name => $item_count)
				{
					if($patch_difference["5.11"][$item_name] < $patch_difference["5.14"][$item_name])
						$img = "up";
					else if($patch_difference["5.11"][$item_name] > $patch_difference["5.14"][$item_name])
						$img = "down";
					else if($patch_difference["5.11"][$item_name] == $patch_difference["5.14"][$item_name])
						$img = "same";
?>
						<tr class="difference-row">
							<td>
								<img class="difference-image" src="/img/item_stats/<?php echo $img; ?>.png"></img>
							</td>
						</tr>
<?php 
				}
?>
					</tbody>
				</table>
				<script>
					$(window).load(function() {
						$(".difference-row").height($(".item-row").height());
					});
					
					$(window).on('resize', function(){
						$(".difference-row").height($(".item-row").height());
					});
				</script>
			</div>
		</div>
<?php
					} else {
?>
		<div class="alert alert-info">
			<b>Info: There are no item stats for this champion in this region. Try another region.</b>
		</div>
<?php
					}
				}
			} else {
?>
		<div class="alert alert-warning" role="alert">
			<p><b>Warning! Couldn't find champion "<?php echo $champion ?>"!</b></p>
			<p>Displaying Champion Selection Page.</p>
		</div>
<?php
				$champion = "";
			}
		}

		if(!isset($champion) || empty($champion))
		{
			$champions_id_name = array();
			
			foreach($data_champions["data"] as $champion_name => $champion_array)
			{
				$champions_id_name[$champion_array["key"]] = $champion_name;
			}
?>
	<div class="by-champion">
		<div class="help-text">
			<p>Here you can see the difference of the AP Item Usage By Champion on each region. You can also compare the global item usage for the selected champion.</p>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Select a champion</h3>
			</div>
			<div class="champion-selection panel-body">
				<p>(This list only has champions which were built with the changed AP items, either on Patch 5.11 or on Patch 5.14, which are actually all.)</p>
<?php			
			$champions_by_name = array();
			
			foreach(glob("data/by-champion-stats/*.json") as $filename)
			{
				$champions_data_json = file_get_contents($filename);
				$champions_data = json_decode($champions_data_json, true);
					foreach($champions_data["5.11"] as $champion_id => $items)
					{
						foreach($items as $item => $item_count)
						{
							if(in_array($item, $ap_items))
								$champions_by_name[$champions_id_name[$champion_id]] = true;
						}
					}
				}
		
			foreach($champions_data["5.14"] as $champion_id => $items)
			{
				foreach($items as $item => $item_count)
				{
					if(in_array($item, $ap_items))
						$champions_by_name[$champions_id_name[$champion_id]] = true;
				}
			}
			
			ksort($champions_by_name);

			foreach($champions_by_name as $champion_key => $has_changed_items)
			{
				if($has_changed_items)
				{
?>
				<script>
					$(document).ready(function() {
						$('#<?php echo $champion_key; ?>').tooltipster({
							content: $("<h4 class=\"tooltip-title\"><?php echo $data_champions["data"][$champion_key]["name"]; ?></h4> <span><?php echo $data_champions["data"][$champion_key]["title"]; ?></span>")
						});
					});
				</script>
				<a class="btn btn-link" href="/item-stats/by-champion/<?php echo $champion_key; ?>" id="<?php echo $champion_key ?>" name="champion" type="submit" value="<?php echo $champions_id_name[$champion_id]; ?>">
					<img src="/img/champions/<?php echo $data_champions["data"][$champion_key]['image']['full']; ?>"></img>
				</a>
<?php
				}
			}
?>	
			</div>
		</div>
	</div>
<?php
		}
	} else if($sort == "by-item") {
?>
	<div class="region-selection">
		<form action="/item-stats/<?php echo $sort; ?>" method="POST" role="form">
			<div class="btn-group">
				<button class="btn btn-default btn-sm<?php if($region == "ALL" || !file_exists("data/by-champion-stats/$region.json")) echo " active"; ?>" name="region" type="submit" value="ALL">ALL</button>
<?php
		foreach(glob("data/by-item-stats/*.json") as $filename)
		{
			$file_region = basename($filename, ".json");
			$by_item_json[$region] = file_get_contents($filename);
?>
				<button class="btn btn-default btn-sm<?php if($region == $file_region) echo " active"; ?>" name="region" type="submit" value="<?php echo $file_region; ?>"><?php echo $file_region; ?></button>
<?php
		}
?>
			</div>
		</form>
	</div>
	<div class="help-text">
		<p>Here you can see the difference between patches of the AP Item Usage on each region.</p>
	</div>
	<div class="item-stats">
		<div>	
<?php
		if(file_exists("data/by-item-stats/$region.json"))
		{
			$items_data_json = file_get_contents("data/by-item-stats/$region.json");
			$items_data = json_decode($items_data_json, true);
			
			$items_ap = array();
			$items_ap_ids = array();
			
			$items_ap["5.11"] = array();
			$items_ap["5.14"] = array();
			$items_ap_ids["5.11"] = array();
			$items_ap_ids["5.14"] = array();
			
			foreach($items_data as $patch => $patch_array)
			{
				foreach($patch_array as $item => $item_count)
				{
					if(in_array($item, $ap_items))
					{
						$items_ap[$patch][$data_items["data"][$item]["name"]] = $item_count;
						$items_ap_ids[$patch][$data_items["data"][$item]["name"]] = $item;
						$items_count[$patch] += $item_count;
					}
				}
			}
			
			$patch_difference = array();
			
			$patch_difference["5.11"] = array();
			$patch_difference["5.14"] = array();
			
			foreach($items_ap as $patch => $patch_array)
			{
				ksort($items_ap[$patch]);
?>
			<table class="table">
				<tbody>
					<tr>
						<td class="title">
							<h2><?php echo $patch; ?></h2>
						</td>
					</tr>
<?php
				foreach($items_ap[$patch] as $item_name => $item_count)
				{
?>
					<tr class="item-row">
						<td>
							<img class="<?php echo $items_ap_ids[$patch][$item_name]; ?>" src="/img/items/<?php echo $items_ap_ids[$patch][$item_name]; ?>.png"></img>
							<script>
								$(document).ready(function() {
									$('.<?php echo $items_ap_ids[$patch][$item_name]; ?>').tooltipster({
										content: $("<div class=\"item-tooltip\"><h4 class=\"tooltip-title\"><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["name"]; ?></h4><span><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["description"]; ?></span></div>")
									});
								});
							</script>
						</td>
						<td>
							<h3><?php echo $item_name; ?></h3>
						</td>
						<td>
							<h3><?php if($items_count[$patch] == 0) $items_count[$patch] = 1; $patch_difference[$patch][$item_name] = round(($item_count / $items_count[$patch] * 100), 2); echo $patch_difference[$patch][$item_name]; ?>%</h3>
						</td>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
<?php
			}
?>
			<table class="table">
				<tbody>
					<tr>
						<td class="title"></td>
					</tr>
<?php
			foreach($items_ap["5.11"] as $item_name => $item_count)
			{
				if($patch_difference["5.11"][$item_name] < $patch_difference["5.14"][$item_name])
					$img = "up";
				else if($patch_difference["5.11"][$item_name] > $patch_difference["5.14"][$item_name])
					$img = "down";
				else if($patch_difference["5.11"][$item_name] == $patch_difference["5.14"][$item_name])
					$img = "same";
?>
					<tr class="difference-row">
						<td>
							<img class="difference-image" src="/img/item_stats/<?php echo $img; ?>.png"></img>
						</td>
					</tr>
<?php 
			}
?>
				</tbody>
			</table>
			<script>
				$(window).load(function() {
					$(".difference-row").height($(".item-row").height());
				});
				
				$(window).on('resize', function(){
					$(".difference-row").height($(".item-row").height());
				});
			</script>
<?php	
		} else {
		
			$items_ap = array();
			$items_ap_ids = array();
			
			$items_ap["5.11"] = array();
			$items_ap["5.14"] = array();
			$items_ap_ids["5.11"] = array();
			$items_ap_ids["5.14"] = array();
		
			foreach(glob("data/by-item-stats/*.json") as $filename)
			{
				$items_data_json = file_get_contents($filename);
				$items_data = json_decode($items_data_json, true);
			
				foreach($items_data as $patch => $patch_array)
				{
					foreach($patch_array as $item => $item_count)
					{
						if(in_array($item, $ap_items))
						{
							$items_ap[$patch][$data_items["data"][$item]["name"]] += $item_count;
							$items_ap_ids[$patch][$data_items["data"][$item]["name"]] = $item;
							$items_count[$patch] += $item_count;
						}
					}
				}
			}
		
			$patch_difference = array();
		
			$patch_difference["5.11"] = array();
			$patch_difference["5.14"] = array();
			
			foreach($items_ap as $patch => $patch_array)
			{
				ksort($items_ap[$patch]);
?>
			<table class="table">
				<tbody>
					<tr>
						<td class="title">
						<h2><?php echo $patch; ?></h2>
					</td>
				</tr>
<?php
				foreach($items_ap[$patch] as $item_name => $item_count)
				{
?>
					<tr class="item-row">
						<td>
							<img class="<?php echo $items_ap_ids[$patch][$item_name]; ?>" src="/img/items/<?php echo $items_ap_ids[$patch][$item_name]; ?>.png"></img>
							<script>
								$(document).ready(function() {
									$('.<?php echo $items_ap_ids[$patch][$item_name]; ?>').tooltipster({
										content: $("<div class=\"item-tooltip\"><h4 class=\"tooltip-title\"><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["name"]; ?></h4><span><?php echo $data_items["data"][$items_ap_ids[$patch][$item_name]]["description"]; ?></span></div>")
									});
								});
							</script>
						</td>
						<td>
							<h3><?php echo $item_name; ?></h3>
						</td>
						<td>
							<h3><?php if($items_count[$patch] == 0) $items_count[$patch] = 1; $patch_difference[$patch][$item_name] = round(($item_count / $items_count[$patch] * 100), 2); echo $patch_difference[$patch][$item_name]; ?>%</h3>
						</td>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
<?php
			}
?>
			<table class="table">
				<tbody>
					<tr>
						<td class="title"></td>
					</tr>
<?php
			foreach($items_ap["5.11"] as $item_name => $item_count)
			{
				if($patch_difference["5.11"][$item_name] < $patch_difference["5.14"][$item_name])
					$img = "up";
				else if($patch_difference["5.11"][$item_name] > $patch_difference["5.14"][$item_name])
					$img = "down";
				else if($patch_difference["5.11"][$item_name] == $patch_difference["5.14"][$item_name])
					$img = "same";
?>
					<tr class="difference-row">
						<td>
							<img class="difference-image" src="/img/item_stats/<?php echo $img; ?>.png"></img>
						</td>
					</tr>
<?php 
			}
?>
				</tbody>
			</table>
			<script>
				$(window).load(function() {
					$(".difference-row").height($(".item-row").height());
				});
				
				$(window).on('resize', function(){
					$(".difference-row").height($(".item-row").height());
				});
			</script>
<?php	
		}
	}
?>
		</div>
	</div>
</div>