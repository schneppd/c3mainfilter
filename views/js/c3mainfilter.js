/*
 * the c3mainfilter factory
 * manage and display filters depending on wich step is used
 * @require jquery
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/29
 */

/*
 * setup the frontend interface for the mainfilter
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_setupMainFilter(){
	if(c3_isDataAvailableToSetupMainFilter) {
		console.log('data available');
		c3_createMainFilterCoreHtmlElements();
		c3_getMainFilterStartSelectionJsonData();
	}
	else
		console.log('C3MainFilter Module error: data not provided to setup html !!!');
	
}

/*
 * tells if all required data is available to setup mainFilter
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_isDataAvailableToSetupMainFilter(){
	try {
		var c3MainFilterData = c3_getDataAvailableToSetupMainFilter();
		if (typeof c3MainFilterData === 'undefined')
			return false;
		if(c3MainFilterData.length == 0)
			return false;
	}
	catch (error) {
		return false;
	}
	return true;
}

/*
 * retrives the data needed to setup mainFilter
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_getDataAvailableToSetupMainFilter(){
	return c3MainFilterDataStart;
}

/*
 * creates all c3MainFilter core html elements for setup
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_createMainFilterCoreHtmlElements(){
	var mainFilter = $("<div id='c3MainFilter' class='hide'></div>");
	var form = $('<form action="' + c3_getMainFilterWebserviceUrl() + '" id="c3MainFilterForm"></form>');
	var legend = 'place holder';
	var fieldset = $('<fieldset><legend>' + legend + '</legend><div class="interactionElements"></div></fieldset>');
	
	form.append(fieldset);
	mainFilter.append(form);
	$('#header div.container').append(mainFilter);
}

/*
 * returns the url used to fecht data for the filter
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_getMainFilterWebserviceUrl(){
	var url = baseDir + 'c3mainfilter';
	return url;
}

/*
 * fetch data and creates all c3MainFilter html elements for given step
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_getMainFilterStartSelectionJsonData(){
	var c3MainFilterData = c3_getDataAvailableToSetupMainFilter();
	var queryData = {
		action: 'get_available_choices'
		,ajax: true
		,id_filter_selection_group: c3MainFilterData.id_filter_selection_group
	};
	c3_makeAjaxCallAndProcessResponse(queryData, c3_showFilterChoices);
}

/*
 * fetch data and calls successFunction to process it
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 * @param queryData param for the call
 * @param successFunction function to call if success fetch
 */
function c3_makeAjaxCallAndProcessResponse(queryData, successFunction){
	var formUrl = c3_getMainFilterWebserviceUrl() + '?rand=' + Math.floor((Math.random() * 1000) + 500) + new Date().getTime() + Math.floor((Math.random() * 1000) + 500);
	
	var query = $.ajax({
		type: 'POST'
		,url: formUrl
		,data: queryData
		,dataType: 'json'
		,error: function(xhr, status, error) {
			//show error message
			console.log('C3MainFilter Module error: ' + error);
		}
	}).done(successFunction);
}

/*
 * fetch data and creates all c3MainFilter html elements for given step
 * 
 * @author Schnepp David
 * @since v0.2 2016/10/30
 */
function c3_showFilterChoices(json){
	var id_filter_selection_group = json['id_filter_selection_group'];
	console.log('result: ' + json);
}

$(document).ready(c3_setupMainFilter);