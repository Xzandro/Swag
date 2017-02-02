<?php

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('style.css') ?>

    <?= $this->Html->script('jquery-3.1.1.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('moment.js') ?>
    <?= $this->Html->script('main.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-82063910-2', 'auto');
        ga('send', 'pageview');

    </script>

</head>
<body>
    <?= $this->Flash->render() ?>
    <header>
        <div class="container">
        <?php echo $this->Html->link(
            $this->Html->image("swag_icon.png", ["alt" => "Swag"]),
            "/",
            ['escape' => false, 'class' => 'header-icon']
        ); ?>
            <h1>SW Analyze GuildWars</h1>
            <div class="panel-wrapper">
                <div class="panel panel-default panel-settings">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="settings-region">Select a region</label>
                            <select id="settings-region" class="form-control settings" data-setting="region">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="settings-guild">Select your guild</label>
                            <select id="settings-guild" class="form-control settings" data-setting="guild">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="settings-week">Pick a GW week!</label>
                            <select id="settings-week" class="form-control settings" data-setting="week">
                            </select>
                        </div>
                    </div>
                </div>
                <div id="weekly-stats-wrapper">
                    <a href="" id="weekly-stats">To the weekly stats</a>
                </div>
                <div class="onoffswitch">
                    <input type="checkbox" name="match-mode" class="onoffswitch-checkbox settings" id="settings-mode" data-setting="mode" checked="checked">
                    <label class="onoffswitch-label" for="settings-mode">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
            <div id="swag-instructions" data-toggle="modal" data-target="#modal-instruction"></div>
        </div>
    </header>
    <div class="container">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    </footer>
    <div class="modal fade" id="modal-instruction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h3>What is this?</h3>
                    <p>This is a guild war tool to track and see your guild‘s offense & defense fights for each week. See who are the most solid performers on GWO and who‘s the boss on GWD.</p>
                    <h3>How do I use this?</h3>
                    <p>It‘s all based on SWProxy. Have SWProxy running and <strong>tap on the general „Attack“ and „Defense“ tab</strong>. All data will be uploaded to this tool automatically! Some data of those tabs might get lost if you don‘t log regularly (best is doing it every day). If you want to log every Saturday for example, you to <strong>tap on the Offense and Defense Logs of each member</strong>.</p>
                    <p><strong>Mandatory:</strong></p>
                    Download the latest SWPRoxy from lstern <a href="https://github.com/lstern/SWProxy-plugins/releases" target="_blank">here</a>.<br />
                    Download this additional plugin <a href="https://github.com/Xzandro/SwagLogger/releases" target="_blank">here</a> and put the two files in the plugin folder.<br /><br />
                    <p>Thanks, that‘s it!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
