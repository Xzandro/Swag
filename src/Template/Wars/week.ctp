<?php
$this->assign('title', 'Swag - ' . $guildname .' (Weekly Stats)');
?>
<div id="container-data" class="container" data-action="getmatchweek">
    <div class="tables-wrapper">
        <div class="table-metadata">
            <p class="table-metadata-name"></p>
            <p class="table-metadata-mode"></p>
        </div>
        <table id="grid_matches" class="table table-bordered stripe table-weekly">
            <thead>
            <tr>
                <th rowspan="2" style="text-align: center;">Member</th>
                <th colspan="5">Offense</th>
                <th colspan="9">Defense</th>
            </tr>
            <tr>
                <th>W</th>
                <th>D</th>
                <th>L</th>
                <th>Absent</th>
                <th>Winrate <span class="information-tooltips" data-toggle="tooltip" data-placement="right" title="Draws count as a 0.5 Win. So generous. Unused swords are treated as a Loss."></span></th>
                <th>T1 W</th>
                <th>T1 D</th>
                <th>T1 L</th>
                <th>T1 %</th>
                <th>T2 W</th>
                <th>T2 D</th>
                <th>T2 L</th>
                <th>T2 %</th>
                <th>Winrate</th>
            </tr>
            </thead>
            <tbody></tbody>

        </table>
        <div class="settings-weekly-toggle"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></div>
    </div>
    <div class="settings-weekly">
        <div class="settings-weekly-exclude">
            <div class="settings-weekly-header">Exclude guild fights</div>
            <div class="settings-weekly-body">
                <div class="row">
                    <div class="col-lg-6 settings-weekly-exclude-content attack">
                        <p>Offense</p>
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6"></div>
                    </div>
                    <div class="col-lg-6 settings-weekly-exclude-content defense">
                        <p>Defense</p>
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>