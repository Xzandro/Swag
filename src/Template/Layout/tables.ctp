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
    <?= $this->Html->css('jquery.dataTables.min.css') ?>
    <?= $this->Html->css('style.css') ?>

    <?= $this->Html->script('jquery-3.1.1.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('jquery.dataTables.min.js') ?>
    <?= $this->Html->script('moment.js') ?>
    <?= $this->Html->script('tables.js') ?>
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
                        <select id="settings-region" class="form-control settings" data-setting="region" disabled="disabled">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="settings-guild">Select your guild</label>
                        <select id="settings-guild" class="form-control settings" data-setting="guild" disabled="disabled">
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="container">
    <?= $this->fetch('content') ?>
</div>
<footer>
</footer>
</body>
</html>
