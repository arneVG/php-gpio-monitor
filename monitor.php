<?php
	if (isset($_POST['action'])) {
		$gpio = shell_exec('gpio readall');
		$newline_split = explode("\n", $gpio);
		
		// device
		$device = $newline_split[0];
		$device = str_replace("-", "", $device);
		$device = str_replace("+", "", $device);

		// gpio
		$gpio_split = array_slice($newline_split, 3, -4);
		foreach ($gpio_split as &$row) {
			$row = str_replace(" ", "", $row);
			$row = explode("|", $row);
		};
		unset($row);
		
		echo json_encode(array("device" => $device, "gpio" => $gpio_split));
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<style>
			body {
				font-family: "Courier New", Courier, monospace;
			}
			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
				padding: 2px;
				text-align: center;
			}
			caption, th {
				background-color: Gainsboro;
			}
			caption {
				border: 1px solid black;
				border-bottom: 0px;
				padding: 5px;
				font-weight: bold;
			}
		</style>
	</head>
	<body>
		<div>
			<table id="table">
				<caption id="caption">No data, is WiringPi installed and PHP enabled?</caption>
				<tr>
					<th>Name</th>
					<th>Mode</th>
					<th>V</th>
					<th>Pin</th>
					<th>Pin</th>
					<th>V</th>
					<th>Mode</th>
					<th>Name</th>
				<tr>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script>
			function constructTable() {
				for (i = 0; i < 20; i++) {
					$("table#table > tbody").append("<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>");
				}
			}

			function fillTable(gpio) {
				gpio.forEach(function(item, index) {
					$("table#table > tbody").children().eq(index + 2).children('td').eq(0).text(item[3]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(1).text(item[4]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(2).text(item[5]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(3).text(item[6]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(4).text(item[8]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(5).text(item[9]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(6).text(item[10]);
					$("table#table > tbody").children().eq(index + 2).children('td').eq(7).text(item[11]);
				})
			}

			function getDeviceName() {
				$.ajax({
					data: {'action': "refresh"},
					type: 'post',
					dataType: "json",
					success: function(result) {
						$('caption#caption').html(result.device);
					}
				});
			}

			const timer = ms => new Promise(res => setTimeout(res, ms))

			async function monitor() {
				while (true) {
					$.ajax({
						data: {'action': "refresh"},
						type: 'post',
						dataType: "json",
						success: function(result) {
							fillTable(result.gpio);
						}
					});
					await timer(500);
				}
			}
		
			$('document').ready(function() {
				getDeviceName();
				constructTable();
				monitor();
			});
		</script>
	</body>
</html>
