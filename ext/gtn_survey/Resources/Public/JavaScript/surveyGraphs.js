/*requirejs( ['Graph', 'jquery-ui'*/
function initCharts() {

    //real action
    let surveyResults = JSON.parse($('#chartHolder').attr('data-survey'));

    console.log(surveyResults);
    var chartNr = 0;
    var labels = [];
    var data = [];
    var curCanv = null;
    for (let group in surveyResults) {
        for (let quest in surveyResults[group]) {
            let groupType = surveyResults[group][quest].groupType;

            switch (groupType) {
                case 2:
                case 4:
                    for (let section in surveyResults[group][quest]['sections']) {

                        data = [];
                        for (let value in surveyResults[group][quest]['sections'][section]) {
                            data.push(surveyResults[group][quest]['sections'][section][value]);
                        }
                        curCanv = $('.canvas-chart')[chartNr];
                        curCanv.height = 400;
                        curCanv.width = 700;

                        labels = groupType == 2 ? ['🙁', ' ', '😐', ' ', "🙂"] : ['🙁', ' ', ' ', "🙂"];
                        let bgColors = groupType == 2 ? ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"] : ["#3e95cd", "#8e5ea2", "#e8c3b9", "#c45850"]
                        new Chart(curCanv.getContext('2d'), getBarConf(labels, bgColors, data, quest, true));
                        chartNr++;
                    }
                    break;
                case 3:
                case 5:
                    data = [];
                    for (let value in surveyResults[group][quest]['counts']) {
                        data.push(surveyResults[group][quest]['counts'][value]);
                    }
                    curCanv = $('.canvas-chart')[chartNr];
                    curCanv.height = 400;
                    curCanv.width = 700;

                    labels = groupType == 3 ? ['🙁', ' ', '😐', ' ', "🙂"] : ['🙁', ' ', ' ', "🙂"];
                    let bgColors = groupType == 3 ? ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"] : ["#3e95cd", "#8e5ea2", "#e8c3b9", "#c45850"]
                    new Chart(curCanv.getContext('2d'), getBarConf(labels, bgColors, data, quest, true));
                    chartNr++;
                    break;
                default:
                    if (surveyResults[group][quest].hasOwnProperty('answers')) {
                        let tableHtml = '<table class="table">\
												  <thead>\
													<tr>\
													  <th scope="col">#</th>\
													  <th scope="col">Antwort</th>\
													</tr>\
												  </thead>\
												  <tbody>';
                        let answrCnt = 1;
                        for (let answrId in surveyResults[group][quest]['answers'])
                            tableHtml += '<tr>\
												  <th scope="row">' + answrCnt++ + '</th>\
												  <td>' + surveyResults[group][quest]['answers'][answrId] + '</td>\
											</tr>';
                        tableHtml += '</tbody>\
</table>';
                        $($('.canvas-chart')[chartNr]).replaceWith(tableHtml);
                    } else {
                        labels = [];
                        data = [];
                        for (let value in surveyResults[group][quest]['counts']) {
                            labels.push(splitString(value));
                            data.push(surveyResults[group][quest]['counts'][value]);
                        }
                        curCanv = $('.canvas-chart')[chartNr];
                        curCanv.height = 400;

                        new Chart(curCanv.getContext('2d'), getBarConf(labels, ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"], data, quest));
                        chartNr++;
                    }
                    break;
            }
        }
    }
}

function splitString(str) {
    let arr = str.split(' ');
    if (arr.length > 1)
        for (let i = 1; i < arr.length; i++) {
            if ((arr[i - 1].length + arr[i].length) < 16) {
                arr[i - 1] = arr[i - 1] + " " + arr[i];
                arr.splice(i, 1);
            }
        }
    return arr;

}


function getBarConf(labels, bgColors, data, questionText, isSmileyChart = false) {
    let fontSizePx = isSmileyChart ? 19 : 16;
    return {
        type: 'horizontalBar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Anzahl:",
                    backgroundColor: bgColors,
                    data: data
                }
            ]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
            legend: {display: false},
            title: {
                display: true,
                text: questionText,
                fontSize: 17,
                fontColor: '#00000f'
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontFamily: 'FontAwesome',
                        fontSize: 14,
                        autoSkip: true,
                        mirror: true,
                        maxBarThickness: 5,
                        fontSize: 16, //make the font slightly larger
                        maxRotation: 0,
                        minRotation: 0,
                        stepSize: 10
                    }
                }],
                yAxes: [{
                    ticks: {
                        fontFamily: '"Font Awesome 5 Free"',
                        fontSize: fontSizePx,
                    }
                }]
            }
        }
    };
}


$('.accordion').accordion({
    header: 'h3:not(.ignore)',
    active: false,
    collapsible: true,
    heightStyle: "content"
});
initCharts();





