<!DOCTYPE html>
<html>
<head>
<title>Gnomes rust stats</title>
<!-- The style sheet required to make shit look good -->
  <link rel="stylesheet" href="style/style.css">
  
  <?php
  //We need our functions!
	include 'functions.php';
  ?>
  
 <script>
function copytext() {
  var copyText = document.getElementById("sharelink");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  var copiedtext = document.getElementById("copiedtext");
  copiedtext.innerHTML = "Copied!";
  copiedtext.style.opacity = 1;
  
  var fadeEffect = setInterval(function () {
        if (!copiedtext.style.opacity) {
            copiedtext.style.opacity = 1;
        }
        if (copiedtext.style.opacity > 0) {
            copiedtext.style.opacity -= 0.1;
        } else {
            clearInterval(fadeEffect);
        }
    }, 100);
}
</script>
<body>
<div id="note"> Due to lack of permissions, this data cannot be maintained. </div>
<?php 
$error = "";
if(isset($_POST['steamid']) || isset($_GET['id']))
	{
		if(isset($_POST['steamid']))
		{
			if(empty($_POST['steamid']))
			{
				$error = "Please enter a steam id!";
			}else{
				$steamid = $_POST['steamid'];
				$steamid = sanitize_input($steamid);
				if(filter_var($steamid, FILTER_VALIDATE_URL) === TRUE)
				{
					$steamid = Get_ID_From_Steam($steamid);
				}else if(!is_numeric($steamid) || strlen($steamid) < 17)
					{
						$error = "Invalid steam id";
					}
			}
		}else if(isset($_GET['id']))
		{
			if(empty($_GET['id']))
			{
				$error = "Please enter a steam id!";
			}else{
				$steamid = $_GET['id'];
				$steamid = sanitize_input($steamid);
				if(filter_var($steamid, FILTER_VALIDATE_URL) === TRUE)
				{
					$steamid = Get_ID_From_Steam($steamid);
				}else if(!is_numeric($steamid) || strlen($steamid) < 17)
					{
						$error = "Invalid steam id";
					}
			}
		}
	}else{
		$error = "Nothing Sumbitted";
	}
?>

<div id="stats_search_container">
	<form method="post" action="index.php">
		<p id="formtext">Enter a steam URL or Steam64 ID</p>
		<input type="text" id="steamid" name="steamid"></input>
		<button value="submit" type="submit">Submit!</button>
	</form>
	
	
	<?php if(!empty($error)): ?>
		<?php if($error !== "Nothing Sumbitted"): ?>
		<div id="error"><?php echo $error; ?> </div>
	<?php endif;endif ?>
	<br>
	</div>
	
	
	<?php 
		if(empty($error)) 
			{
				//Get the users BM id
				$bmid = search_bm($steamid);
				
				//Gets the initial data.
				$deathsPVP_initial = get_death_pvp($bmid);				
				$deathsAdditional_initial = get_death_additional($bmid);
				
				$deathsByServer = [];
				
				$spiked = 0;
				$nature = 0;
				$floor = 0;
				$traps = 0;
				$npc = 0;
				$stupid = 0;
				$vehicle = 0;
				$stupidityScore = 0;
				$totalKills = 0;
				$totalDeaths = 0;
				$suicide = 0;
				$killRatio = 0;
				
				if(!empty($deathsPVP_initial['links']['next']))
				{
					$nextlink = $deathsPVP_initial['links']['next'];
					$nextlink = explode("&", $nextlink);
					$nextlink = explode("=", $nextlink[4]);
					$nextlink = $nextlink[1];							
				}else if(empty($deathsPVP_initial['links']['next']))
				{
					$nextlink = '';
				}
				while(!empty($nextlink))
				{
					$mydata = get_death_pvp_key($bmid, $nextlink);
					if(!empty($mydata['links']['next']))
					{
						$nextlink = $mydata['links']['next'];
						$nextlink = explode("&", $nextlink);
						$nextlink = explode("=", $nextlink[4]);
						$nextlink = $nextlink[1];
					}else if(empty($mydata['links']['next']))
					{
						$nextlink = '';
					}
					foreach($mydata['data'] as $d)
					{
						array_push($deathsPVP_initial['data'], $d);
					}
					
						usleep(cooldown); //See config for settings.
				}
				
				if(!empty($deathsAdditional_initial['links']['next']))
				{
					$nextlink = $deathsAdditional_initial['links']['next'];
					$nextlink = explode("&", $nextlink);
					$nextlink = explode("=", $nextlink[4]);
					$nextlink = $nextlink[1];							
				}else if(empty($deathsAdditional_initial['links']['next']))
				{
					$nextlink = '';
				}
				while(!empty($nextlink))
				{
					$mydata = get_death_additional_key($bmid, $nextlink);
					if(!empty($mydata['links']['next']))
					{
						$nextlink = $mydata['links']['next'];
						$nextlink = explode("&", $nextlink);
						$nextlink = explode("=", $nextlink[4]);
						$nextlink = $nextlink[1];
					}else if(empty($mydata['links']['next']))
					{
						$nextlink = '';
					}
					foreach($mydata['data'] as $d)
					{
						array_push($deathsAdditional_initial['data'], $d);
					}
					
						usleep(cooldown); //See config for settings.
				}
				
				foreach($deathsPVP_initial['data'] as $d)
				{
					$serverID = $d['relationships']['servers']['data'][0]['id'];
					$killerID = $d['attributes']['data']['killerSteamID'];
					$victimID = $d['attributes']['data']['steamID'];
					
					$serverName = "Unknown";
					
					$kills = 0;
					$deaths = 0;
					
					foreach($deathsPVP_initial['included'] as $i)
					{
						if($i['id'] == $serverID)
						{
							$serverName = $i['attributes']['name'];
							break;
						}
					}
					
					if($killerID == $steamid)
					{
						$kills = 1;
						$totalKills++;
					}else if($victimID == $steamid)
					{
						$deaths = 1;
						$totalDeaths++;
					}
					
					$add_new = "Yes";
					$myarray = array("server" => array("Server_id" => $serverID, "Server_name" => $serverName, "Played_time" => 0, "Kills" => $kills, "Deaths" => $deaths),);
					if(empty($deathsByServer))
					{
						array_push($deathsByServer, $myarray);
					}else if(!empty($deathsByServer))
					{
						foreach($deathsByServer as &$qqq)
						{
							if($qqq['server']['Server_id'] == $serverID)
							{
								$add_new = "No";
								$qqq['server']['Kills'] = $qqq['server']['Kills'] + $kills;
								$qqq['server']['Deaths'] = $qqq['server']['Deaths'] + $deaths;
							}
						}
						if($add_new === "Yes")
						{
							array_push($deathsByServer, $myarray);
						}
					}
					
				}
				foreach($deathsAdditional_initial['data'] as $d)
					{
						$messageType = $d['attributes']['messageType'];
						if($messageType == "rustLog:playerDeath:entity")
							{
								$type = $d['attributes']['data']['entity'];
								if($type == "beartrap")
								{
									$floor++;
								} if($type == "spikes.floor")
								{
									$floor++;
								} if($type == "landmine")
								{
									$floor++;
								} if($type == "barricade.wood")
								{
									$spiked++;
								} if($type == "gates.external.high.stone")
								{
									$spiked++;
								} if($type == "wall.external.high.stone")
								{
									$spiked++;
								} if($type == "gates.external.high.wood")
								{
									$spiked++;
								} if($type == "wall.external.high.wood")
								{
									$spiked++;
								} if($type == "barricade.metal")
								{
									$spiked++;
								} if($type == "barricade.woodwire")
								{
									$spiked++;
								} if($type == "flameturret.deployed")
								{
									$traps++;
								} if($type == "autoturret_deployed")
								{
									$traps++;
								} if($type == "guntrap.deployed")
								{
									$traps++;
								} if($type == "sentry.scientist.static")
								{
									$npc++;
								} if($type == "campfire")
								{
									$stupid++;
								} if($type == "bradleyapc")
								{
									$npc++;
								} if($type == "sam_static")
								{
									$npc++;
								} if($type == "minicopter.entity")
								{
									$vehicle++;
								}
							}
								
							if($messageType == "rustLog:playerDeath:suicide")
							{
								$suicide++;
							}
							
							if($messageType == "rustLog:playerDeath:blunt")
							{
								$stupid++;
							}
							if($messageType == "rustLog:playerDeath:cold")
							{
								$nature++;
							}
							if($messageType == "rustLog:playerDeath:radiation")
							{
								$nature++;
							}
							
							if($messageType == "rustLog:playerDeath:drowned")
							{
								$nature++;
							}
							
							if($messageType == "rustLog:playerDeath:fall")
							{
								$stupid++;
							}
							
							if($messageType == "rustLog:playerDeath:animal")
							{
								$type = $d['attributes']['data']['animal'];
								if($type == "bear")
								{
									$npc++;
								}
								if($type == "boar")
								{
									$npc++;
								}
								if($type == "wolf")
								{
									$npc++;
								}
							}
					}
				$playedTime = getplayedtime($bmid)['included'];
				
				foreach($playedTime as $p)
				{
					$sid = $p['id'];
					foreach($deathsByServer as &$d)
					{
						if($d['server']['Server_id'] == $sid)
						{
							$d['server']['Played_time'] = $p['meta']['timePlayed'];
							break;
						}
					}
				}
				
				$serverCount = count($deathsByServer);
				
				$stupidityScore = ($spiked + $nature + $floor + $traps + $npc + $vehicle) * $stupid;
				$stupidityScore = round($stupidityScore, 2);
				
				if($totalDeaths == 0)
					{
						$killRatio = $totalKills;
					}else{
						$killRatio = round($totalKills / $totalDeaths,2);
					}
				
				$steamInfo = Get_Info_Steam($steamid);
				$displayPicture = $steamInfo['response']['players'][0]['avatarfull'];
				$playerName = $steamInfo['response']['players'][0]['personaname'];
				echo "<div id=\"statscontainer\">";
				include "stats.html";
				echo "<div id=\"statsservers\">";
				foreach($deathsByServer as $rrrr)
				{
					$serverid = $rrrr['server']['Server_id'];
					$servername = $rrrr['server']['Server_name'];
					$playedTime = $rrrr['server']['Played_time'];
					
					$hours = floor($playedTime / 3600);
					$minutes = floor(($playedTime / 60) % 60);
					
					$kills = $rrrr['server']['Kills'];
					$deaths = $rrrr['server']['Deaths'];
					
					if($deaths == 0)
					{
						$ratio = $kills;
					}else{
						$ratio = round($kills / $deaths, 2);
					}
					include "servers.html";
				}
				echo "</div></div>";
			}
			
	?>
	
</body>
</html>
