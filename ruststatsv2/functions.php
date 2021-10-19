<?php

include 'config.php';

//Sanitizes the data to prevent any code injection
	function sanitize_input($data) 
	{
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
	

//Connects to the database.
function connect_to_db()
{
	$charset = "utf8mb4";
	$servername = _SEVERNAME;
	$dbname = dbname;
	
	$dsn = "mysql:host="._SEVERNAME.";dbname="._DBNAME.";charset=$charset";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			 $pdo = new PDO($dsn, _USERNAME, _PASSWORD , $options);
		} catch (\PDOException $e) {
			 throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		
	return $pdo;
		
}

//Breaks the Steam URL down to the part we want. -> https://steamcommunity.com/id/gnomeofslayer/ becomes -> gnomeofslayer
	function BreakURL($myURL)
	{
		$explodedURL = explode('/', $myURL);
		$myURL = $explodedURL[4];
		return $myURL;
	}
	
//Searches steam for the players steam ID based on their URL
	function Get_ID_From_Steam($URL)
	{
		
		$URL = BreakURL($URL);
		
		$MyResponse = "";
		
		$APIURL = "https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?format=json&key=" . _TOKEN_STEAM . "&vanityurl=" . $URL . "&url_type=1";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			  CURLOPT_RETURNTRANSFER => 1,
			  CURLOPT_URL => $APIURL,
			  CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ". _TOKEN_STEAM,
			  )),
			);

			$resp = curl_exec($curl);
			curl_close($curl);

			$response = json_decode($resp,true);
			if(empty($response['response']['steamid']))
			{
				$steamid = $URL;
			}else{
				$steamid = $response['response']['steamid'];
			}
			return $steamid;
	}
//Checks if it's even a URL
function is_url($url)
{
	$isurl = false;
	if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) 
	{
		$isurl = false;
	}else{
		$isurl = true;
	}
	return $isurl;				
}

//Returns an array for players matching our steam ID!
	function Get_Info_Steam($steamID)
	{
		
		$APIURL = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?format=json&key=" . _TOKEN_STEAM. "&steamids=" . $steamID;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			  CURLOPT_RETURNTRANSFER => 1,
			  CURLOPT_URL => $APIURL,
			  CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ". _TOKEN_STEAM,
			  )),
			);

			$resp = curl_exec($curl);
			curl_close($curl);

			$response = json_decode($resp,true);
			return $response;
	}

//Search battlemetrics for this users details
	function search_bm($steamID)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_RETURNTRANSFER => 1,
		  CURLOPT_URL => "https://api.battlemetrics.com/players?filter[search]=".$steamID."&include=identifier",
		  CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ". _TOKEN_BM,
		  )),
		);

		$resp = curl_exec($curl);
		curl_close($curl);
	
		$nothing = "";
		
		$response = json_decode($resp,true);
		if(!empty($response['data'][0]['id']))
		{
			return $response['data'][0]['id'];
			
		}else{
			return $nothing;
		}
	}
	
	//Gets all PVP related deaths/kills
	function get_death_pvp($id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => "https://api.battlemetrics.com/activity?filter[types][whitelist]=rustLog:playerDeath:PVP&filter[players]=$id&include=server&page[size]=1000&access_token="._TOKEN_BM,
          ),
        );

        $resp = curl_exec($curl);
        curl_close($curl);
		
        $response = json_decode($resp,true);
       return $response;
    }
	
	//Gets all non PVP related deaths
	function get_death_additional($id)
	{
		$curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => "https://api.battlemetrics.com/activity?filter[types][whitelist]=rustLog:playerDeath:bleeding,rustLog:playerDeath:suicide,rustLog:playerDeath:drowned,rustLog:playerDeath:entity,rustLog:playerDeath:animal,rustLog:playerDeath:fall&filter[players]=$id&include=server&page[size]=1000&access_token="._TOKEN_BM,
          ),
        );

        $resp = curl_exec($curl);
        curl_close($curl);
		
        $response = json_decode($resp,true);
       return $response;
	}
	
	//Gets any additional pages for PVP
	function get_death_pvp_key($id,$key)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => "https://api.battlemetrics.com/activity?filter[types][whitelist]=rustLog:playerDeath:PVP&filter[players]=$id&include=server&page[size]=1000&access_token="._TOKEN_BM."&page[key]=$key&page[rel]=next",
          ),
        );

        $resp = curl_exec($curl);
        curl_close($curl);
		
        $response = json_decode($resp,true);
       return $response;
    }
	
	//Gets any additional pages for Additional deaths
	function get_death_additional_key($id,$key)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => "https://api.battlemetrics.com/activity?filter[types][whitelist]=rustLog:playerDeath:bleeding,rustLog:playerDeath:suicide,rustLog:playerDeath:drowned,rustLog:playerDeath:entity,rustLog:playerDeath:animal,rustLog:playerDeath:fall&filter[players]=$id&include=server&page[size]=1000&access_token="._TOKEN_BM."&page[key]=$key&page[rel]=next",
          ),
        );

        $resp = curl_exec($curl);
        curl_close($curl);
		
        $response = json_decode($resp,true);
       return $response;
    }
	
	//Gets the users playedtime on the server
	function getplayedtime($id)
	{
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => "https://api.battlemetrics.com/players/$id?include=server&access_token="._TOKEN_BM,
          ),
        );

        $resp = curl_exec($curl);
        curl_close($curl);
		
        $response = json_decode($resp,true);
       return $response;
	}
 ?>