<?php
		//Settings
		
		//Cooldown. 250000 = 0.25 seconds. 500000 = 0.50 seconds. 1000000 = 1 second. You get the point.
		//This is so you don't accidentally spam battlemtetics and get blocked.
		define("cooldown", 250000);
			//Settings for search results. True for on, false for off. You got this.
			/*The more you enable the longer it will take to load. You know how it be!
				Math: (0.25 * (number of true) ) * (number of additional pages)
				Exampe: (0.25 * 5) * 3 = 3.75 seconds.
			
			*/
			define("death_pvp", TRUE); //Someone likes you?
			define("death_fall", FALSE); //Fell out a tree huh?
			define("death_blunt", FALSE); //This is actually satchels. BM is weird as heck.
			
			//Disable the entire category for animals.Remember to enable one of the sub categories as well!
			define("death_animal", FALSE); //Not even the animals want you to have a good time.
				define("death_wolf", FALSE); //Not the wolfs.
				define("death_bear", FALSE); //Or the bears
				define("death_boar", FALSE); //Or the boars!
				
			//Disable the entire category for "entity" Remember to enable one of the sub categories as well!
			define("death_entity", FALSE); //Nothing in this world is safe..
				define("death_spikesfloor", FALSE); //The ground isn't.
				define("death_beartrap", FALSE); //Definitely not.
				define("death_landmine", FALSE); //Pop?
				define("death_woodbarricade", FALSE); //When the ground fights back.
				define("death_externalstonegate", FALSE); //They are rich I see?
				define("death_externalstonewall", FALSE); //Fancy walls won't keep me out!
				define("death_externalwoodwall", FALSE); //Burn them to the ground!
				define("death_externalwoodgate", FALSE); //Oh there was a front door.
				define("death_barricademetal", FALSE); //The ground is fighting back with stronger things!
				define("death_barricadewoodwire", FALSE); //Spikey ground.
				define("death_flameturret", FALSE); //BURN BABY BURN!!
				define("death_shotguntrap", FALSE); //Splat.
				define("death_autoturret", FALSE); //Don't think you can hide from me!
				define("death_campfire", FALSE); //It's for cooking marshmellows not your legs..
				define("death_bradley", FALSE); //Yes. This game has tanks.
				define("death_samsites", FALSE); //Hhahaha thought you were safe in the air huh?
				define("death_minicopter", FALSE); //Even your vehicle hates you.
				define("death_outpost", FALSE);
			define("death_drowned", FALSE); //Why are you like this?
			define("death_suicide", FALSE); //Words can't express my disappointment in you.
			define("death_bleeding", FALSE); //The blood is suppose to stay inside of you not outside.
		
		//Our server connection info (Test environment)
		define("_SEVRNAME", "localhost");
		define("_DBNAME", "lfg");
		define("_USERNAME", "username");
		define("_PASSWORD", "password");
		
		
		//Our BM token!
		define("_TOKEN_BM", "Battlemetrics token");
		
		//Our steam token!
		define("_TOKEN_STEAM", "Steam token");
?>

