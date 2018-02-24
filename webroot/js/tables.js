const IdToResult = {1: '_wins', 2: '_loses', 3: '_draws'};
const IdToResultOpp = {1: '_loses', 2: '_wins', 3: '_draws'};

let excludedGuilds = { attack: {}, defense: {} };

let table;

function initMatchesTable(data) {
    $('.table-metadata-name').text(data.name);
    $('.table-metadata-mode').text(`${data.mode} (${data.date})`);
    return $('#grid_matches').DataTable({
        "bDestroy": true,
        "lengthMenu": [[10, 20, -1], [10, 20, "All"]],
        "data": data.items,
        "order": [[0, "asc"]],
        'bAutoWidth': false,
        "deferRender": true,
        "dom": 'rt<"bottom"ilfp<"clearfix">>',
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search"
        },
        "columns": [
            { "data": "wizard_name" },
            { "data": "win_count" },
            { "data": "draw_count" },
            { "data": "lose_count" },
            { "data": "absent" },
            { "data": "win_rate" },
            { "data": "round1_wins" },
            { "data": "round1_draws" },
            { "data": "round1_loses" },
            { "data": "round1_winrate" },
            { "data": "round2_wins" },
            { "data": "round2_draws" },
            { "data": "round2_loses" },
            { "data": "round2_winrate" }
        ],
        "columnDefs": [
            {
                "render": function (data, type) {
                    if (type === 'display') {
                        return Math.round(data);
                    } else {
                        return data;
                    }
                },
                "targets": [5, 9, 13]
            }
        ]
    });
}

function initMatchesTableWeek(data) {
    $('.table-metadata-name').text(data.name);
    $('.table-metadata-mode').text(data.date);
    return $('#grid_matches').DataTable({
        "bDestroy": true,
        "lengthMenu": [[10, 20, -1], [10, 20, "All"]],
        "data": data.items,
        "order": [[0, "asc"]],
        'bAutoWidth': false,
        "deferRender": true,
        "dom": 'rt<"bottom"ilfp<"clearfix">>',
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search"
        },
        "columns": [
            { "data": "wizard_name" },
            { "data": "attack.win_count" },
            { "data": "attack.draw_count" },
            { "data": "attack.lose_count" },
            { "data": "attack.absent" },
            { "data": "attack.win_rate" },
            { "data": "defense.round1_wins" },
            { "data": "defense.round1_draws" },
            { "data": "defense.round1_loses" },
            { "data": "defense.round1_winrate" },
            { "data": "defense.round2_wins" },
            { "data": "defense.round2_draws" },
            { "data": "defense.round2_loses" },
            { "data": "defense.round2_winrate" },
            { "data": "defense.win_rate" }
        ],
        "columnDefs": [
            {
                "render": function (data, type) {
                    if (type === 'display') {
                        return Math.round(data);
                    } else {
                        return data;
                    }
                },
                "targets": [5, 9, 13, 14]
            }
        ]
    });
}

function prepareMatchTableDataSingle() {
    let data = JSON.parse(JSON.stringify(rawData));
    let temp = {};
    let tableData = {items: []};
    let guild_id = Number(localStorage.getItem('guild')) || 0;
    let opponent = (data['matches'][0]['guild_id'] == guild_id) ? data['matches'][0]['guild_opp'] : data['matches'][0]['guild'];
    let attendanceMax = 6;

    tableData.mode = getMatchModeByGuild(data['matches'][0], guild_id);
    tableData.name = opponent.name;
    tableData.date = moment(data['matches'][0]['last_fight']).format('D.M.Y');

    data['matches'].forEach(match => {
        match.fights.forEach(fight => {
            let wizard_prefix = (fight['guild_id'] == guild_id) ? '' : 'opp_';
            if(!temp[fight[wizard_prefix + 'wizard_id']])
                temp[fight[wizard_prefix + 'wizard_id']] = {wizard_id: fight[wizard_prefix + 'wizard_id'], wizard_name: fight[wizard_prefix + 'wizard_name'], round_count: 0, win_count: 0, lose_count: 0, draw_count: 0, win_rate: 0, round1_wins: 0, round1_loses: 0, round1_draws: 0, round1_winrate: 0, round2_wins: 0, round2_loses: 0, round2_draws: 0, round2_winrate: 0};
            if(fight['guild_id'] == guild_id) {
                temp[fight[wizard_prefix + 'wizard_id']]['round1' + IdToResult[fight['round1']]]++;
                temp[fight[wizard_prefix + 'wizard_id']]['round2' + IdToResult[fight['round2']]]++;
            } else {
                temp[fight[wizard_prefix + 'wizard_id']]['round1' + IdToResultOpp[fight['round1']]]++;
                temp[fight[wizard_prefix + 'wizard_id']]['round2' + IdToResultOpp[fight['round2']]]++;
            }
            temp[fight[wizard_prefix + 'wizard_id']]['round_count'] += 2;
        });
    });

    Object.keys(temp).forEach(wizard => {
        let absent = (attendanceMax - temp[wizard]['round_count']);
        let relevantRounds = (temp[wizard]['round_count'] + absent);

        temp[wizard]['win_count'] = (temp[wizard]['round1_wins'] +  temp[wizard]['round2_wins']);
        temp[wizard]['lose_count'] = (temp[wizard]['round1_loses'] +  temp[wizard]['round2_loses']);
        temp[wizard]['draw_count'] = (temp[wizard]['round1_draws'] +  temp[wizard]['round2_draws']);
        let relevantWinrateRounds = (temp[wizard]['win_count'] + (temp[wizard]['draw_count'] * 0.5));
        temp[wizard]['absent'] = absent;
        temp[wizard]['win_rate'] = (relevantWinrateRounds / relevantRounds * 100) || 0;
        temp[wizard]['round1_winrate'] = (temp[wizard]['round1_wins'] / (temp[wizard]['round_count'] / 2) * 100) || 0;
        temp[wizard]['round2_winrate'] = (temp[wizard]['round2_wins'] / (temp[wizard]['round_count'] / 2) * 100) || 0;
        tableData.items.push(temp[wizard]);
    });
    table = initMatchesTable(tableData);
}

function prepareMatchTableDataWeek(initTable) {
    let data = JSON.parse(JSON.stringify(rawData));
    let temp = {};
    let tableData = {items: []};
    let guild_id = Number(data['guildId']) || 0;
    let own = (data['matches'][0]['guild_id'] == guild_id) ? data['matches'][0]['guild'] : data['matches'][0]['guild_opp'];
    tableData.name = own.name;
    let date = data['matches'][0]['last_fight'];
    tableData.date = `${moment(date).startOf('isoweek').format('D.M.')} - ${moment(date).endOf('isoweek').format('D.M.Y')}`;

    let attendanceMax = 0;
    let excludeData = { attack: {}, defense: {} };

    data['matches'].forEach(match => {
        let mode = getMatchModeByGuild(match, guild_id);
        let enemy = getMatchEnemy(match, guild_id);
        if (mode === 'attack') {
            if (!excludeData.attack[enemy.guild_id]) {
                excludeData.attack[enemy.guild_id] = {guild_id: enemy.guild_id, name: enemy.name};
            }
        } else {
            if (!excludeData.defense[enemy.guild_id]) {
                excludeData.defense[enemy.guild_id] = {guild_id: enemy.guild_id, name: enemy.name};
            }
        }
        if(!excludedGuilds[mode][enemy.guild_id]) {
            if (mode === 'attack')
                attendanceMax += 6;

            match.fights.forEach(fight => {
                let wizard_prefix = (fight['guild_id'] == guild_id) ? '' : 'opp_';
                if (!temp[fight[wizard_prefix + 'wizard_id']])
                    temp[fight[wizard_prefix + 'wizard_id']] = {
                        wizard_id: fight[wizard_prefix + 'wizard_id'],
                        wizard_name: fight[wizard_prefix + 'wizard_name'],
                        attack: {
                            round_count: 0,
                            win_count: 0,
                            lose_count: 0,
                            draw_count: 0,
                            win_rate: 0,
                            round1_wins: 0,
                            round1_loses: 0,
                            round1_draws: 0,
                            round1_winrate: 0,
                            round2_wins: 0,
                            round2_loses: 0,
                            round2_draws: 0,
                            round2_winrate: 0
                        },
                        defense: {
                            round_count: 0,
                            win_count: 0,
                            lose_count: 0,
                            draw_count: 0,
                            win_rate: 0,
                            round1_wins: 0,
                            round1_loses: 0,
                            round1_draws: 0,
                            round1_winrate: 0,
                            round2_wins: 0,
                            round2_loses: 0,
                            round2_draws: 0,
                            round2_winrate: 0
                        }
                    };
                if (fight['guild_id'] == guild_id) {
                    temp[fight[wizard_prefix + 'wizard_id']][mode]['round1' + IdToResult[fight['round1']]]++;
                    temp[fight[wizard_prefix + 'wizard_id']][mode]['round2' + IdToResult[fight['round2']]]++;
                } else {
                    temp[fight[wizard_prefix + 'wizard_id']][mode]['round1' + IdToResultOpp[fight['round1']]]++;
                    temp[fight[wizard_prefix + 'wizard_id']][mode]['round2' + IdToResultOpp[fight['round2']]]++;
                }
                temp[fight[wizard_prefix + 'wizard_id']][mode]['round_count'] += 2;
            });
        }
    });

    Object.keys(temp).forEach(wizard => {
        let modes = ['attack', 'defense'];
        modes.forEach(mode_item => {
            temp[wizard][mode_item]['absent'] = (attendanceMax - temp[wizard][mode_item]['round_count']);
            let relevantRounds = (temp[wizard][mode_item]['round_count'] + temp[wizard][mode_item]['absent']);

            temp[wizard][mode_item]['win_count'] = (temp[wizard][mode_item]['round1_wins'] +  temp[wizard][mode_item]['round2_wins']);
            temp[wizard][mode_item]['lose_count'] = (temp[wizard][mode_item]['round1_loses'] +  temp[wizard][mode_item]['round2_loses']);
            temp[wizard][mode_item]['draw_count'] = (temp[wizard][mode_item]['round1_draws'] +  temp[wizard][mode_item]['round2_draws']);
            if(mode_item === 'attack') {
                let relevantWinrateRounds = (temp[wizard][mode_item]['win_count'] + (temp[wizard][mode_item]['draw_count'] * 0.5));
                temp[wizard][mode_item]['win_rate'] = relevantWinrateRounds / relevantRounds * 100 || 0;
            } else {
                temp[wizard][mode_item]['win_rate'] = ((temp[wizard][mode_item]['win_count'] + (temp[wizard][mode_item]['draw_count'] * 0.5)) / temp[wizard][mode_item]['round_count'] * 100) || 0;
            }

            temp[wizard][mode_item]['round1_winrate'] = ((temp[wizard][mode_item]['round1_wins'] + (temp[wizard][mode_item]['round1_draws'] * 0.5)) / (temp[wizard][mode_item]['round_count'] / 2) * 100) || 0;
            temp[wizard][mode_item]['round2_winrate'] = ((temp[wizard][mode_item]['round2_wins'] + (temp[wizard][mode_item]['round2_draws'] * 0.5)) / (temp[wizard][mode_item]['round_count'] / 2) * 100) || 0;
        });
        tableData.items.push(temp[wizard]);
    });

    if(initTable) {
        table = initMatchesTableWeek(tableData);
        setExcludeData(excludeData);
    } else {
        table.clear();
        table.rows.add(tableData.items).draw();
    }
}

function getMatchEnemy(match, guild_id) {
    return (match.guild.guild_id === guild_id) ? match.guild_opp : match.guild;
}

function getMatchModeByGuild(match, guild_id) {
    if((match['guild_id'] == guild_id && match['log_type'] == 1) || (match['opp_guild_id'] == guild_id && match['log_type'] == 2)) {
        return 'attack';
    } else {
        return 'defense';
    }
}

function setExcludeData(excludeData) {
    let modes = ['attack', 'defense'];

    modes.forEach(mode => {
        $('.settings-weekly-exclude-content.offense .col-lg-6').empty();
        let mode_keys = Object.keys(excludeData[mode]);
        let mode_break = Math.ceil(mode_keys.length / 2) || 0;
        Object.keys(excludeData[mode]).forEach((guild_id, i) => {
            let index = (i < mode_break) ? 0 : 1;
            let checked = excludedGuilds[mode][guild_id] ? '' : 'checked="checked"';
            $('.settings-weekly-exclude-content.' + mode + ' .col-lg-6').eq(index).append(
                `<div class="checkbox">
                <label>
                    <input class="settings-weekly-exclude-option" data-type="${mode}" data-guild_id="${guild_id}" type="checkbox" ${checked}> ${excludeData[mode][guild_id].name}
                </label>
            </div>`);
        });
    });
}

$(function() {
    // get excluded guild from storage
    excludedGuilds = JSON.parse(localStorage.getItem('excludedGuilds')) || excludedGuilds;

    exeAjax({
        action: $('#container-data').data('action')
    });

    $('.settings-weekly-exclude-content').on('change', '.settings-weekly-exclude-option', function (e) {
        let type = $(this).data('type');
        let guild_id = $(this).data('guild_id');
        if(!$(this).prop('checked')) {
            // add guild
            excludedGuilds[type][guild_id] = true;
        } else {
            // remove guild
            if(excludedGuilds[type][guild_id])
                delete excludedGuilds[type][guild_id];
        }

        localStorage.setItem('excludedGuilds', JSON.stringify(excludedGuilds));
        prepareMatchTableDataWeek(false);
    });

    $('.settings-weekly-toggle').on('click', function (e) {
        $('.settings-weekly').slideToggle();
    });

});