# BroadSword

A PMMP plugin that implements a charging sword that can deal great damages

[![](https://poggit.pmmp.io/shield.api/BroadSword)](https://poggit.pmmp.io/p/BroadSword)
[![](https://poggit.pmmp.io/shield.dl.total/BroadSword)](https://poggit.pmmp.io/p/BroadSword)

# Usage

To get started, install the [Customies](https://poggit.pmmp.io/p/Customies/) plugin to your `plugins` folder

Then, start your server and give yourself a BroadSword using the command `/give <name> broad_sword:broad_sword`

You may want to install a custom craft plugin such as [CustomCraft](https://poggit.pmmp.io/p/CustomCraft/) to allow you players to craft this item

# Configuration

Inside of the `plugin_data/BroadSword/config.yml` file, you may change the following:

-   **texture** - The texture of the item
-   **item-name** - The name of the item
-   **durability** - The durability of the item
-   **charging-ticks** - How much ticks does this item takes to charge
-   **attack-points** - How much attack damage does item do
-   **attack-when-loading** - Can a player attack another one while the Broad Sword is not fully loaded ?
-   **reset-when-attacking-while-loading** - Reset the load timer if the item is used while loading
-   **popup** - A popup that is send to the player
    -   **active** - Do you want to send a loading bar to the player
    -   **type** - What kind of popup will be sent
    -   **progress** - A character that is repeated <length> times which makes a loading bar
    -   **length** - The length of the progress bar
    -   **color**:
        -   **progress** - The color of the progress (filled) part of the charging bar
        -   **remaining** - The color of the remaining part of the bar
        -   **filled** - The color of the bar when it is fully charged
