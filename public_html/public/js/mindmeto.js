var tickerLastId = 0;

function updateTicker() {
	
	if( $("#ticker") ) {
	
		var url = ( tickerLastId == 0 ) ? "ajax.php?a=ticker" : "ajax.php?a=ticker&id="+tickerLastId;
		$.getJSON(url,
			function(tickerData){
				
				$.each(tickerData.reminders, function(i,item){
					var listEl = $("<li/>");
					var html = '<div class="reminder">'+	
									item.reminder+
								'</div>'+
								'<div class="reminder-meta">'+
									'<a href="http://twitter.com/'+item.username+'"><img src="'+item.avatar+'" class="avatar" alt="'+item.username+'"  /> '+item.username+'</a>'+
								'</div>';
					if( i % 2 != 0 ) listEl.attr('class', 'odd');
					listEl.html(html).appendTo("#ticker").hide().fadeIn("fast");
				});
				
				tickerLastId = tickerData.latestId;

		});
	
	}
	
}