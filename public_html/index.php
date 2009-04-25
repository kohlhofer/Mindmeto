<?php

	define(MINDMETO_ROOT, '');
	
	require_once(MINDMETO_ROOT.'inc/db.php');

	$headerCode = <<<JS
		<script src="jquery-1.3.2.js" type="text/javascript"></script>
		<script>
			// When the page is ready
			$(document).ready(function(){
				$("#commands").hide();
				$("#commandsLink").click(function () {
					$("#commands").slideToggle(300);
				});
			});
		</script>
JS;
	
	include(MINDMETO_ROOT.'tmp/welcome.php');

?>