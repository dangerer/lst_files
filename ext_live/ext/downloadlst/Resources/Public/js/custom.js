$(document).ready(function(){

	var tree = new treefilter($("#my-tree"), {
		offsetLeft : 10,
		searcher : $("input#my-search"),
		multiselect : false
	});

    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }
    var urlParams = getUrlVars();
    if('tx_kesearch_pi1%5Bsword%5D' in urlParams) {
        let kesearchString = decodeURIComponent(urlParams['tx_kesearch_pi1%5Bsword%5D']);
        $('.tx-downloadlst #my-search').val(kesearchString);
        setTimeout(() => {
            $("input#my-search").trigger('keyup');

        }, 500);
    }
});

