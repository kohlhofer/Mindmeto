var tickerCount = 0;

function removeReminder( id ) {
	
	var remove = confirm("Are you sure you want to remove this reminder? This cannot be undone!");
	if( remove == true ) {
		
		$.getJSON("ajax.php?a=remove&id="+id,
			function(data){
	    	
				if( data == true ) {
					$("#reminder-"+id).fadeOut("true", function() { $(this).remove(); });
				}
	
		    }
		);
		
	}
	
	return false;
	
}

if ( window.addEventListener ) {
	var kkeys = [], konami = "38,38,40,40,37,39,37,39,66,65";
	window.addEventListener("keydown", function(e){
		kkeys.push( e.keyCode );
		if ( kkeys.toString().indexOf( konami ) >= 0 ) {
			$('#user-avatar').attr('src', 'public/img/misc/hypnotoad.gif');
		}
	}, true);
}

jQuery.slowEach = function( array, interval, callback ) { 
	
	if( ! array.length ) return; 
	var i = 0; 
	next(); 
	function next() { 
	    if( callback.call( array[i], i, array[i] ) !== false ) 
	        if( ++i > 0 ) 
	            setTimeout( next, interval ); 
	} 
	
};

function updateTicker() {
	
	if( $("#ticker") ) {

		var url = ( tickerLastId == 0 ) ? "ajax.php?a=ticker" : "ajax.php?a=ticker&id="+tickerLastId;
		$.getJSON(url,
			function(tickerData){
				
				if( tickerData.reminders ) {
					
					if( tickerLastId == 0 ) {
						$.each(tickerData.reminders, tickerCallback );
					} else {
						$.slowEach(tickerData.reminders, 1000, tickerCallback );
					}
					
					tickerLastId = tickerData.latestId;
				}

		});
	
	}
	
}

function tickerCallback( i, item ){
	
	var listEl = $("<li/>");
	var html = '<div class="reminder">'+	
					'<b>@mindmeto</b> '+item.reminder+
				'</div>'+
				'<div class="reminder-meta">'+
					'<a href="http://twitter.com/'+item.username+'"><img src="'+item.avatar+'" class="avatar" alt="'+item.username+'"  /> '+item.username+'</a>'+
				'</div>';
			
	if( tickerCount % 2 != 0 ) listEl.attr('class', 'odd');
	listEl.html(html).prependTo("#ticker").hide().fadeIn("fast");
	tickerCount = ( tickerCount ) ? 0 : 1;
	
	if( $("#ticker li").length > 20 ) $('#ticker li:last-child').fadeOut("fast", function() { $('#ticker li:last-child').remove(); });
	
}