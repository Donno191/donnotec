function BUILDGRID_server(id,ColModel_varible,data_var = '',sortname_varible,sortorder_varible,extra = ''){
	$(id).jqGrid({
		url: data_var,
		datatype : 'json',
		mtype: "GET",
		colModel: ColModel_varible,
		sortname: sortname_varible,
		sortorder: sortorder_varible,
		height:'auto',
        autoencode:false,
		gridComplete: function(data) {
			GridResize(id);
            if ($.isFunction(window.jqGridComplete)) {
                jqGridComplete(id);
            }
		}
	});
    if (extra != ''){
        $(id).setGridParam({extra}).trigger("reloadGrid");
    }
	/*RESIZE GRID EVENT*/
	$( window ).resize(function() {
		waitForFinalEvent(function(){
			GridResize(id);
		}, 100, id);
	});
	/*Wait for final event before resizing*/
	var waitForFinalEvent = (function () {
		var timers = {};
		return function (callback, ms, uniqueId) {
			if (!uniqueId) {
				uniqueId = id;
			}
			if (timers[uniqueId]) {
				clearTimeout (timers[uniqueId]);
			}
			timers[uniqueId] = setTimeout(callback, ms);
		};
	})();
	/*RESIZE GRID FUNCTION*/
	function GridResize(id){
			$(id).setGridWidth($(id).closest(".ui-jqgrid").parent().width()-5);
	}
}
