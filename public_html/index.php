<?php

	define(MINDMETO_ROOT, '');
	
	require_once(MINDMETO_ROOT.'inc/session.php');

	$headerCode = <<<JS
		<script>
			$(document).ready(function() {

				updateTicker();

			});
		</script>
JS;
	
	include(MINDMETO_ROOT.'tmp/welcome.php');

?>