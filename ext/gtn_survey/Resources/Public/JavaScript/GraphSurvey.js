console.log("GraphSurvey.js loaded from asset");


function initCharts() {

    //real action
    let surveyResults = JSON.parse($('#chartHolder').attr('data-survey'));
    console.log(surveyResults);
    var chartNr = 0;
    for (let group in surveyResults) {
        for (let quest in surveyResults[group])
            if (surveyResults[group][quest].hasOwnProperty('sections')) {
                for (let section in surveyResults[group][quest]['sections']) {
                    let labels = [];
                    let data = [];
                    for (let value in surveyResults[group][quest]['sections'][section]) {
                        labels.push(value);
                        data.push(surveyResults[group][quest]['sections'][section][value]);
                    }
                    let curCanv = $('.canvas-chart')[chartNr];
                    curCanv.height = 500;
                    new Chart(curCanv.getContext('2d'), getBarConf(labels, data, quest));
                    chartNr++;

                }
            } else {
                let labels = [];
                let data = [];
                for (let value in surveyResults[group][quest]['counts']) {
                    labels.push(value);
                    data.push(surveyResults[group][quest]['counts'][value]);
                }
                let curCanv = $('.canvas-chart')[chartNr];
                curCanv.height = 500;

                new Chart(curCanv.getContext('2d'), getBarConf(labels, data));
                chartNr++;
            }

    }
}


//$("#accordion").accordion();

function getBarConf(labels, data, questionText) {
    return {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Anzahl:",
                    backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
                    data: data
                }
            ]
        },
        options: {
            legend: {display: false},
            title: {
                display: true,
                text: questionText
            }
        }
    };
}

initCharts();