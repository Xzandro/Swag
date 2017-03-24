let rawData = [];
let match_data = {};

let init = false;

function exeAjax(args) {
    let params = args.params || {};
    $.ajax({
        type : 'GET',
        url : args.url,
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json"
        },
        data : params
    }).done(function(data) {
        if (data) {
            handleData(data, args);
        }
    });
}

function handleData(data, args) {
    switch(args.action) {
        case 'setregions':
            let options_region = '<option value="0">Select region</option>';
            data['regions'].forEach(region => {
                options_region += `<option value="${region.region_id}">${region.name}</option>`;
            });
            $('#settings-region').html(options_region).val(localStorage.getItem('region') || 0).change();
            break;
        case 'setguilds':
            let options_guild = '';
            data['guilds'].forEach(guild => {
                options_guild += `<option value="${guild.guild_id}">${guild.name}</option>`;
            });
            $('#settings-guild').html(options_guild).val(localStorage.getItem('guild')|| 0).change();
            break;
        case 'getmatches':
            prepareMatches(data);
            break;
        case 'initmatches':
            initMatches(data);
            break;
        case 'getmatchsingle':
            rawData = data;
            prepareMatchTableDataSingle();
            break;
        case 'getmatchweek':
            rawData = data;
            prepareMatchTableDataWeek(true);
            break;
    }
}

function prepareMatches(data) {
    match_data = {};
    data['matches'].forEach((match) => {
        let week = moment(match.last_fight).format('W');
        let year = moment(match.last_fight).format('Y');
        let object_key = `${year}-${week}`;
        if(!match_data[object_key])
            match_data[object_key] = {
                start: moment(match.last_fight).startOf('isoweek').format('D.M'),
                end: moment(match.last_fight).endOf('isoweek').format('D.M.Y'),
                year: year,
                week: week,
                items: []
            };
        match_data[object_key].items.push(match);
    });

    let match_keys = Object.keys(match_data);
    let options_week = (match_keys.length === 0) ? '<option value="0">No data.</option>' : '';
    match_keys.forEach(week => {
        options_week += `<option value="${match_data[week]['year']}-${match_data[week]['week']}">Week ${match_data[week]['week']}, ${match_data[week]['start']} - ${match_data[week]['end']}</option>`;
    });
    $('#settings-week').html(options_week).change();
}

function initMatches(data) {
    let structure = '';

    $('#weekly-stats').attr('href', '/wars/week/' + $('#settings-guild').val() + '?week=' + localStorage.getItem('week') + '&year=' + localStorage.getItem('year'));

    data.matches.forEach((match) => {
        let opp_object_string = (match['guild_id'] == localStorage.getItem('guild')) ? 'guild_opp' : 'guild';
        let opponent = match[opp_object_string];

        let ratios = {
            win: Math.round(match.round_wins / match.round_count * 100),
            draw: Math.round(match.round_draws / match.round_count * 100),
            lose: Math.round(match.round_loses / match.round_count * 100)
        };

        let match_end = moment(match.last_fight).format('dddd, D.M.Y');
        let modus = getModus();

        structure += `<div class="col-lg-3">
            <a href="/wars/view/${match.match_id}">
            <div class="panel panel-default ${modus}">
                    <div class="panel-heading">
                        <img src="/img/${modus}.png" class="img-fight" />
                        ${opponent.name}
                    </div>
                    <div class="panel-body">
                        <p>${match_end}</p>
                        <div class="col-lg-6">
                            <p>Wins:</p>
                            <p>Draws:</p>
                            <p>Losses:</p>
                        </div>
                        <div class="col-lg-6">
                            <p>${match.round_wins}/${match.round_count} (${ratios.win}%)</p>
                            <p>${match.round_draws}/${match.round_count} (${ratios.draw}%)</p>
                            <p>${match.round_loses}/${match.round_count} (${ratios.lose}%)</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>`

    });
    $('.spinner').hide();
    $('.container-matches').html(structure);
    init = true;
}

function getModus() {
    let mode = ($('#settings-mode').prop('checked')) ? 'attack' : 'defense';
    return mode;
}

$(function() {
    $('#settings-mode').prop('checked', (localStorage.getItem('mode') == 'true') ? true : false);
    // get regions
    exeAjax({
        url: '/api/regions/',
        action: 'setregions'
    });

    //settings
    $('.settings').on('click change input', function (e) {
        let setting_name = $(this).data('setting');
        let setting_type = $(this).attr('type');

        switch (setting_type) {
            case 'checkbox':
                localStorage.setItem(setting_name, $(this).prop('checked'));
                break;
            case 'radio':
                localStorage.setItem(setting_name, $('input[data-setting=' + setting_name + ']:checked').val());
                break;
            default:
                localStorage.setItem(setting_name, $(this).val());
        }
    });

    $('#settings-guild').select2({
        placeholder: 'Select guild'
    });

    $('#settings-region').on('change', function (e) {
        exeAjax({
            url: '/api/guilds/' + (localStorage.getItem('region') || 0),
            action: 'setguilds'
        });
    });

    $('#settings-guild').on('change', function (e) {
        if(!$('#settings-mode').val())
            return;

        exeAjax({
            url: '/api/matches/' + (localStorage.getItem('guild') || 0),
            params: {type: getModus()},
            action: 'getmatches'
        });
    });

    $('#settings-mode').on('change', function (e) {
        if(!init)
            return;

        $('.spinner').fadeIn();
        $('.container-matches').empty();

        exeAjax({
            url: '/api/matches/' + (localStorage.getItem('guild') || 0),
            params: {type: getModus()},
            action: 'getmatches'
        });
    });

    $('#settings-week').on('change', function (e) {
        let data = $(this).val().split('-');
        localStorage.setItem('year', data[0]);
        localStorage.setItem('week', data[1]);

        $('.spinner').fadeIn();
        $('.container-matches').empty();

        exeAjax({
            url: '/api/matches/' + (localStorage.getItem('guild') || 0),
            params: {week: data[1], year: data[0], fights: 1, stats: 1, type: getModus()},
            action: 'initmatches'
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
});