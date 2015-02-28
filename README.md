# SimpleHungerGames
A simple PocketMine-MP plugin that add the HungerGames minigame to your server.

This plugin is *NOT* made for multi-world server.
The server will be all dedicated to the HG match.
You will probably install this plugin with: SimpleAuth, and a kit plugin such as AdvancedKits. Other plugins are not required.
Only 1 arena is running on the server. Maybe in the future, i can make more, if I have time :)

How it works:
- Install the plugin on your server, and edit the config file as you like.
- - max_players is the max. number of the players in the arena
- - min_players is the minumum number of player required to start a match
- - in spawn_locs you set all position where players spawn, you should provide as many positions as the maxplayers field
- - chat-format when set to true, display chat messages with this format: "[k:1] [d:3] playerName: random message".
- - chest_items are the items you want chests be filled of. You set items with the format: numeric id, meta/damage, quantity
- - world is the hg world arena
- When you are done with editing the configs, start your server.
- There is a timer in the plugin that does a countdown and calculate the total amount of minutes per match, depending on how you set the config
- Players can PvP only if the match is started
- When deathmatch starts, all chest are refilled with items you set in the config
- If a player dies, he is kicked from the game
- When game finishes, server shutdowns (you need a loop to make it restarts)
- A player can join the server if the match is in waiting time, he can't during the match
- Players can't break or place blocks (only open chests, doors, ...)
- ... 
