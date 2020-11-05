<?php
	define('GUARD', true);

	$split = preg_split('/:/', $_GET['ip'], -1, PREG_SPLIT_NO_EMPTY);
	$ip = $split[0];
	$port = $split[1];

	require '../functions/lgsl_protocol.php';
	$server = lgsl_query_live('halflife', $ip, $port, $port, $port, 'sp');
	$k = 1;

	echo '
	<link rel="stylesheet" href="../../style/css/bootstrap3.css">
	<table class="table table-bordered table-hover" style="font: 12.4px/1.7em \'Open Sans\', arial, sans-serif;">
		<thead class="alert-info" style="color: black;">
			<tr>
				<th>№</th>
				<th>PID</th>
				<th>Ник</th>
				<th>Фраги</th>
				<th>Время</th>
			</tr>
		</thead>
		<tbody>';
		if( sizeof($server['p']) > 0 ) {
			foreach( $server['p'] as $Player ){
				echo '
				<tr>
					<td><b>'.$k++.'</b></td>
					<td><b>'.htmlspecialchars( $Player[ 'pid' ] ).'</b></td>
					<td><b>'.htmlspecialchars( $Player[ 'name' ] ).'</b></td>
					<td><b>'.htmlspecialchars( $Player[ 'score' ] ).'</b></td>
					<td><b>'.htmlspecialchars( $Player[ 'time' ] ).'</b></td>
				</tr>';
			}
			} else {
			echo '<tr class="error"><td colspan="5"><b>На сервере нет игроков!</b></td></tr>';
		}
		
		echo '</tbody>
	</table>';

	//print_r($server);
	
?>