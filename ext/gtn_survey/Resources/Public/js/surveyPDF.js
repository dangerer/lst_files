requirejs([
    'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.0/jspdf.min.js',
    '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/html2canvas.js'
], function(jsPDF, html2canvas) {
    $('#printButton').on('click', () => {
        var reportPageHeight = $('.gtn-survey-container').innerHeight();
        var reportPageWidth = 595;
        let allCanvHeight = 0;
        $("canvas").each(function(index) {
            allCanvHeight += $(this).height();

        });
        var reportPageHeight = allCanvHeight;

        // create a new canvas object that we will populate with all other canvas objects

        expand();
        startLoadingScreen();
        let remainingPx = 842;
        let curPxVal = 0;
        let canvasArr = [];
        let promises =[];
        let elHeightArr = [];
        var pdf = new jsPDF('p', 'pt');
        $.each($('.question-grp-container'),(i, val) => {
            console.log(val);
            let isExportChecked = $($(val).find('.export-selector')[0]).is(':checked');

            if(isExportChecked) {
                $.each($(val).find('.ui-accordion-content .row, .ui-accordion-header'), (i2, val2) => {

                    console.log(elHeight);
                    if ($(val2).is('h3')) {
                        var elHeight = $(val2).height() + 15;

                    } else {
                        var elHeight = $(val2).height() - 50;
                    }
                    console.log(elHeight);
                    elHeightArr.push(elHeight);
                    promises.push(html2canvas($(val2)[0],
                        {
                            removeContainer: false,
                            scale: 1.0
                        }
                    ));
                    /*.then(canvas => {
                                          canvasArr.push({canvas: canvas, yPos: curPxVal});
                                          curPxVal += elHeight;

                                          collapse();
                                          endLoadingScreen();
                                      }));

                });*/

            });
    }
});

        var pageCnt = 1;
        Promise.all(promises).then(data => {

            data.forEach((canvas,index) => {

                if((curPxVal + elHeightArr[index] > 860 || elHeightArr[index] < 100) && index > 0) {
                    curPxVal = 0;
                    pdf.addPage();
                    console.log("added page");
                }
                console.log("Index: " + index);
                console.log("Current Pixel height: " + curPxVal);
                pdf.addImage(canvas, 'PNG', 5, curPxVal);
                curPxVal += elHeightArr[index];


            });
            pdf.save($('.header-title').text() + '.pdf');
            collapse();
            endLoadingScreen();
        });
      /*  var reportPageHeight = $('.gtn-survey-container').innerHeight();
        var reportPageWidth = 595;
        let allCanvHeight = 0;
        $("canvas").each(function(index) {
            allCanvHeight += $(this).height();

        });
        var reportPageHeight = allCanvHeight;

        // create a new canvas object that we will populate with all other canvas objects

        expand();
        startLoadingScreen();
       /* setTimeout(() => {
            $('.gtn-survey .accordion .question-grp-container').each((index, element) => {


            });
            // var pdf = new jsPDF('p', 'pt', [reportPageWidth, survHTML[0].clientHeight]);
            var pdf = new jsPDF('p', 'pt');

            html2canvas($('.gtn-survey-container')[0], {
                removeContainer: false,
                scale: 1.0
            }).then(canvas => {
                console.log(canvas); // ""
                console.log(typeof canvas); //object
                pdf.addImage(canvas, 'PNG', 15,
                    20);
                pdf.save($('.header-title').text() + '.pdf');

                collapse();
                endLoadingScreen();
            });
        }, 300);*/
        /*ÜsetTimeout(() => {
            let survHTML = $('.gtn-survey-container');
            console.log(survHTML[0].clientHeight);
           // var pdf = new jsPDF('p', 'pt', [reportPageWidth, survHTML[0].clientHeight]);
            var pdf = new jsPDF('p', 'pt');

            html2canvas($('.gtn-survey-container')[0], {
                removeContainer: false,
                scale: 1.0
            }).then(canvas => {
                console.log(canvas); // ""
                console.log(typeof canvas); //object
                pdf.addImage(canvas, 'PNG', 15,
                    20);
                pdf.save($('.header-title').text() + '.pdf');

                collapse();
                endLoadingScreen();
            });
        }, 300);*/

        // download the pdf
    });

    function startLoadingScreen() {
        $('.loader').css('display', '');
        $('.gtn-survey').css('opacity', '0.6');
        $('.gtn-survey').css('pointer-events', 'none');

    }

    function endLoadingScreen() {
        $('.loader').css('display', 'none');
        $('.gtn-survey').css('opacity', '1.0');
        $('.gtn-survey').css('pointer-events', '');
    }

    function expand() {
        $('.gtn-survey-container .top-nav-container').hide();
        $('.gtn-survey-container .ui-accordion-header').each(function() {
            if ($(this).parent().parent().find('input.export-selector:first').prop('checked')) {
                $(this).removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({
                    'aria-selected': 'true',
                    'tabindex': '0'
                });
                $(this).find('.ui-icon').removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({
                    'aria-selected': 'true',
                    'tabindex': '0'
                });
                $(this).parent().find('.ui-accordion-content').addClass('ui-accordion-content-active').attr({
                    'aria-expanded': 'true',
                    'aria-hidden': 'false'
                }).show();
            } else {
                $(this).hide();
            }

        });

        $('.gtn-survey-container input.export-selector').hide();
        //$('.gtn-survey-container .ui-accordion-header').removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({'aria-selected':'true','tabindex':'0'});
        //$('.gtn-survey-container .ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
        //  $('.gtn-survey-container .ui-accordion-content').addClass('ui-accordion-content-active').attr({'aria-expanded':'true','aria-hidden':'false'}).show();

    }

    function collapse() {
        $('.gtn-survey-container .top-nav-container').show();

        $('.gtn-survey-container .ui-accordion-header').removeClass('ui-accordion-header-active ui-state-active ui-corner-top').addClass('ui-corner-all').attr({
            'aria-selected': 'false',
            'tabindex': '-1'
        });
        $('.gtn-survey-container .ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
        $('.gtn-survey-container .ui-accordion-content').removeClass('ui-accordion-content-active').attr({
            'aria-expanded': 'false',
            'aria-hidden': 'true'
        }).hide();

        $('.gtn-survey-container input.export-selector').show();
        $('.gtn-survey-container .ui-accordion-header').css('display', 'block');


    }

    $('.select-all').change(function() {
        var checkboxes = $('.gtn-survey').find(':checkbox');
        checkboxes.prop('checked', $(this).is(':checked'));
    });
    $('#expandAllButton').on('click', () => {
    	debugger
        if ($('.gtn-survey-container .ui-accordion-header').hasClass('ui-accordion-header-active')) {
            $('.gtn-survey-container .ui-accordion-header').removeClass('ui-accordion-header-active ui-state-active ui-corner-top').addClass('ui-corner-all').attr({
                'aria-selected': 'false',
                'tabindex': '-1'
            });
            $('.gtn-survey-container .ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            $('.gtn-survey-container .ui-accordion-content').removeClass('ui-accordion-content-active').attr({
                'aria-expanded': 'false',
                'aria-hidden': 'true'
            }).hide();
        } else {
            $('.gtn-survey-container .ui-accordion-header').removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({
                'aria-selected': 'true',
                'tabindex': '0'
            });
                    $('.ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
/*
            $('.gtn-survey-container .ui-accordion-header .ui-icon').removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({
                'aria-selected': 'true',
                'tabindex': '0'
            });*/
            $('.gtn-survey-container .ui-accordion-content').addClass('ui-accordion-content-active').attr({
                'aria-expanded': 'true',
                'aria-hidden': 'false'
            }).show();
        }
    });
});
