<?php

class WeatherMapDataSource_observium extends WeatherMapDataSource {

	function Init(&$map)
	{
		if($map->context == 'observium')
		{
			return(TRUE);
		}
		else
		{
			wm_debug('ReadData editor: Database library not found.\n');
		}

		return(TRUE);
	}

	function Recognise($targetstring)
	{
		if(preg_match("/^observium:(\d+)$/",$targetstring,$matches))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function ReadData($targetstring, &$map, &$item)
	{

		$data[IN] = NULL;
		$data[OUT] = NULL;
		$data_time = 0;

		if(preg_match("/^observium:(\d+)$/",$targetstring,$matches))
		{
			$device_id = intval($matches[1]);

			$SQL = "SELECT devices.status AS 'status',devices.disabled AS 'disabled',devices.ignore AS 'ignore' FROM devices WHERE device_id=$device_id";

			$state = 0; // 0 by default
			$statename = 'unknown'; // unknown by default

			$con = mysqli_connect('localhost','root','observium','observium');
			if ($query = mysqli_query($con,$SQL))
			{
				while ($result = mysqli_fetch_assoc($query))
				{
					if($result['status'] == 0) { $state = 2; $statename = 'down'; }
					if($result['status'] == 1) { $state = 3; $statename = 'up'; }
					if($result['disabled'] == 1) { $state = 1; $statename = 'disabled'; }
					if($result['ignore'] == 1) { $state = 1; $statename = 'ignored'; }
				}
			}
			mysqli_close($con);
			$data[IN] = $state;
			$data[OUT] = $state;
			$item->add_note("state",$statename);
		}

		wm_debug ("observium ReadData: Returning (".($data[IN]===NULL?'NULL':$data[IN]).",".($data[OUT]===NULL?'NULL':$data[OUT]).",$data_time)\n");

		return( array($data[IN], $data[OUT], $data_time) );
	}
}


// vim:ts=4:sw=4:
?>
