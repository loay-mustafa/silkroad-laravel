## Silkroad Laravel

![https://github.com/Devsome/silkroad-laravel/actions?query=workflow%3ACI](https://github.com/Devsome/silkroad-laravel/workflows/CI/badge.svg)

A free and open-source project for the MMORPG Silkroad Online.
Feel free to create isses or contribute to that project. If you have any questions, just ask!

#### Discord
We now have a Discord [server](https://discord.gg/MNjY4By) for questions, feedback or chit-chat.

If you want to help me, you can buy a [Coffee](https://www.buymeacoffee.com/Mi0v2sB) for me.
<hr>

### Downloading

1. First you need to clone the repo on your computer.
    ```
    git clone git@github.com:Devsome/silkroad-laravel.git
    ```
2. If you want to use vagrant.
    ```
    vagarnt up
    ``` 
3. Go with `vagrant ssh` into your VM and run the following commands from the `/var/www`
    1. Composer
        ```
        composer install
        ```
    2. Editing the `SRO_VT_LOG`.`_AddLogChar` mssql Procedure. After all the `set` variables you need to paste this stuff.
        ```sql
        BEGIN
            SET NOCOUNT ON;
               INSERT INTO loginhistory ([CharID], [status])
               VALUES (@CharID, @EventId)

               IF EXISTS (SELECT 1 FROM onlineofflinelog WHERE CharID = @CharID)
                    UPDATE onlineofflinelog
                    SET    status = @EventID
                    WHERE  CharID = @CharID
               ELSE
                   INSERT INTO onlineofflinelog ([CharID],  [status])
                   VALUES  (@CharID, @EventID)
        END
        IF (@EventID = 6)
        BEGIN
            UPDATE [SRO_VT_SHARD].[dbo]._Char 
                set ItemPoints = (
                SELECT
                ISNULL((sum(ISNULL(Binding.nOptValue, 0)) + sum(ISNULL(OptLevel, 0)) + sum(ISNULL(Common.ReqLevel1, 0))), 0) as ItemPoints
                FROM [SRO_VT_SHARD].[dbo].[_Inventory] as inventory
                join [SRO_VT_SHARD].[dbo]._Items as Items on Items.ID64  = inventory.ItemID
                join [SRO_VT_SHARD].[dbo]._RefObjCommon as Common on Items.RefItemId  = Common.ID
                left join [SRO_VT_SHARD].[dbo]._BindingOptionWithItem as Binding on Binding.nItemDBID = Items.ID64
                where
                    inventory.slot < 13 and
                    inventory.slot != 8 and
                    inventory.slot != 7 and
                    inventory.CharID = _Char.CharID
            ) WHERE _Char.CharID = @CharID
        
            Declare @GuildID int;
            SELECT @GuildID = GuildID FROM [SRO_VT_SHARD].[dbo]._Char WHERE _Char.CharID = @CharID
        
            IF (@GuildID > 0)
            BEGIN
                UPDATE [SRO_VT_SHARD].[dbo]._Guild 
                  set ItemPoints = (
                  SELECT
                    SUM(Char.ItemPoints) as ItemPoints
                    FROM [SRO_VT_SHARD].[dbo]._Char as Char
                    where
                        Char.GuildID = _Guild.ID
                ) WHERE _Guild.ID = @GuildID
            END
        END
        ```
        That inserts a record into the `SRO_VT_LOG`.`onlineofflinelog` & `SRO_VT_LOG`.`loginhistory`. If you think, that you are missing that table. Do the next step and the Table exist.
        <br><br>Also the `EventId = 6` part is for the ranking of your players.
    4. Open your Silkroad Database query and run this commands
        ```sql
        ALTER TABLE dbo._Char ADD ItemPoints int NOT NULL DEFAULT 0;
        ALTER TABLE dbo._Guild ADD ItemPoints int NOT NULL DEFAULT 0;
        ``` 
        That stuff allows the Database to fill the **Itempoints** to the ranking.
    3. Laravel stuff for generating the storage path, unique key and seeding the tables.
        ```bash
        php artisan storage:link && php artisan key:generate && php artisan migrate --seed
        ```
4. (Optional) You can generate a ssh key for your local maschine. Just read the [ssl-script README.md](/ssl-script/README.md)
5. Add to your hosts file the following:
    ```
    50.51.52.53 silkroad-laravel
    ```
6. If you want to connect to the local mysql Database
    1. SSH Host: `50.51.52.53`
    2. Username: `vagrant`
    3. Password: `vagrant`
    4. MySQL Host: `127.0.0.1`
    5: Username: `root`
    6. Password: `root`
    
    Also in the Vagrantfile.

7. Edit the `.env.example` to `.env` and change your credentials.

8. Setting up your Cronjobs
    ```bash
    crontab -e
    ```
    Then paste this and put it on the last line.
    ```bash
    * * * * * php /var/www/artisan schedule:run
    ```
9. If you want to add your existing Silkroad Accounts to the **web** Database, run this command:
    ```bash
    php artisan import:silkroad-accounts
    ```
    You need to be in your root web directory.
    
### Chaning Javascript / CSS styling

1. Install npm
    ```bash
    npm install
    ``` 
2. Check the `webpack.mix.js` file for the compiling files and destination
3. If you want to run in production.
    ```bash
    // Run all Mix tasks and minify output...
    npm run production
    ```
    
    Want to debug your compiling stuff?
    ```bash
    npm run dev
    ```
    
    You can also watch all your changes in dev mode.
    ```bash
    npm run watch
    ```

If you want some help, check the [Laravel Compiling Assets (Mix)](https://laravel.com/docs/6.x/mix)


Hopefully you are good to go with that. 
<hr>     

License
===
  
    The MIT License (MIT)
    
    Copyright (c) 2019 - 2020 Devsome
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.
