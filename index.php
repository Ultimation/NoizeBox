<?php
/*  PHP System Status
 *  ------------------------------------------
 *  Authors: wutno (#/g/tv - Rizon), ed (https://github.com/9001)
 *  Last update: 2/15/2015 3:11AM -5GMT (clean up ultimations mess with using spaces instead of tabs and add "epic memes" for secondary domains as well as corrected something that's been bothering me...)
 *
 *
 *  GNU License Agreement
 *  ---------------------
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 *  http://www.gnu.org/licenses/gpl-2.0.txt
 */

#Since we all enjoy FOSS
if(isset($_GET['dat']) && $_GET['dat'] == "sauce"){
	$lines = implode(range(1, count(file(__FILE__))), '<br />');
	$content = highlight_file(__FILE__, TRUE);
	die('<html><head><title>Page Source For: '.__FILE__.'</title><style type="text/css">body {margin: 0px;margin-left: 5px;}.num {border-right: 1px solid;color: gray;float: left;font-family: monospace;font-size: 13px;margin-right: 6pt;padding-right: 6pt;text-align: right;}code {white-space: nowrap;}td {vertical-align: top;}</style></head><body><table><tr><td class="num"  style="border-left:thin; border-color:#000;">'.$lines.'</td><td class="content">'.$content.'</td></tr></table></body></html>');
}

function kb2bytes($kb){
	return round($kb * 1024, 2);
}
function format_bytes($bytes){
	if ($bytes < 1024){ return $bytes; }
	else if ($bytes < 1048576){ return round($bytes / 1024, 2).'KB'; }
	else if ($bytes < 1073741824){ return round($bytes / 1048576, 2).'MB'; }
	else if ($bytes < 1099511627776){ return round($bytes / 1073741824, 2).'GB'; }
	else{ return round($bytes / 1099511627776, 2).'TB'; }
}
function numbers_only($string){
	return preg_replace('/[^0-9]/', '', $string);
}
function calculate_percentage($used, $total){
	return @round(100 - $used / $total * 100, 2);
}

$uptime = exec('uptime');
preg_match('/ (.+) up (.+) user(.+): (.+)/', $uptime, $update_out);
$users_out = (substr($update_out[2], strrpos($update_out[2], ' ')+1));
$uptime_out = substr($update_out[2], 0, strrpos($update_out[2], ' ')-2);
$load_out = str_replace(', ',', <small>',$update_out[4]).str_repeat('</small>',2);

#hdd info
$raiddrive = explode(' ', preg_replace('/\s\s+/', ' ', exec('df /dev/md0')));
$raiddrive_out = format_bytes(kb2bytes($raiddrive[2])).'<small> / '.format_bytes(kb2bytes($raiddrive[1])).' <small>('.calculate_percentage(kb2bytes($raiddrive[2]), kb2bytes($raiddrive[1])).'% free)</small></small>';

#bandwidth info
#if we have this enabled creates a scroll bar for 1080
/*$vnstat = explode(';', shell_exec('vnstat --oneline'));
$vnstat[8] = '&#8595; '.$vnstat[8];
$vnstat[9] = ' <small>&#8593; '.$vnstat[9];
$vnstat[10] = ' <small>&#8597; '.$vnstat[10];
$vnstat[11] = ' @ ~'.$vnstat[11];*/

#cpu temperature
$sensors = shell_exec("sensors -u coretemp-isa-0000 | grep input");
$sensors = explode("\n", $sensors);
$temperature = 0;
foreach (array_slice($sensors, 1, 4) as $temps){
	$temperature += substr($temps, 15, 4);
}
$sensors = $temperature / 4 ."<small>&deg;<small>C</small></small>";

#ram and swap
$memory = array( 'Total RAM'  => 'MemTotal',
				 'Free RAM'   => 'MemFree',
				 'Cached RAM' => 'Cached',
				 'Total Swap' => 'SwapTotal',
				 'Free Swap'  => 'SwapFree' );
foreach ($memory as $key => $value){
	$memory[$key] = kb2bytes(numbers_only(exec('grep -E "^'.$value.'" /proc/meminfo')));
}
$memory['Used Swap'] = $memory['Total Swap'] - $memory['Free Swap'];
$memory['Used RAM'] = $memory['Total RAM'] - $memory['Free RAM'] - $memory['Cached RAM'];
$memory['RAM Percent Free'] = calculate_percentage($memory['Used RAM'],$memory['Total RAM']);
$memory['Swap Percent Free'] = calculate_percentage($memory['Used Swap'],$memory['Total Swap']);
$memory_out = format_bytes($memory['Used RAM']).'<small> / '.format_bytes($memory['Total RAM']).' <small> *'.format_bytes($memory['Cached RAM']).' Cached ('.$memory['RAM Percent Free'].'% free)</small></small>';
$swap_out = format_bytes($memory['Used Swap']).'<small> / '.format_bytes($memory['Total Swap']).' <small>('.$memory['Swap Percent Free'].'% free)</small></small>';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>NoizeBox stats</title>
		<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noodp" /> <!-- I fucking hate robots... -->
		<meta name="description" content="Status page for NoizeBox." />
		<meta http-equiv="refresh" content="10">
		<meta charset="UTF-8" />
		<style type="text/css">
			span { color: #FFB;display: block;font-size: 1.3em;margin-bottom: .5em;padding: 0 .5em; }
			html { background: #000 url('images/bg.png'); background-size:cover; background-attachment:fixed; color: #FFF;font-family: sans-serif; font-size: 1.8em;padding: 1em 2em; }
			div { float: right;text-align: right; }
			a { color: #FF6;display: block;font-size: 1.7em;text-decoration: none; }
			small { color: #FF9; }
			small>small { color: #FF8; }
		</style>
	</head>
	<body>
		<div id="links">
		bros
			<a href="/~ultimation/">ultimation</a>
			<a href="/~dawnkiller/">dawnkiller</a>
			<a href="/~jackperazzi/">jack</a>
			<a href="/~wutno/">wutno</a>
			<a href="/~psychonaught/">psychonaught</a>

		links
			<a href="ts3server://noize.link?port=9987&password=hunter2&addbookmark=noize.link">teamspeak</a>
			<a href="http://noize.link:8080">minecraft</a>
			<a href="/plex/index.html">plex<small><small><small> listings</small></small></small></a>
			<a href="http://192.168.1.10:32400/manage/index.html">plex<small><small><small> (local only)</small></small></small></a>
			<a href="index.php?dat=sauce">source</a>
    </div>
		local time<span><?=date("Y-F-j H:i:s", time());?></span> <!-- Server time is actally PHP time since PHP wants to bitch -->
		uptime<span><?=$uptime_out;?></span> <!-- Server uptime -->
		core temp<span><?=$sensors;?></span> <!-- CPU Temperature -->
		users logged in<span><?=$users_out;?></span> <!-- Users logged in -->
		load<span><?=$load_out;?></span> <!-- CPU load averages -->
		memory<span><?=$memory_out;?></span> <!-- RAM usage -->
		swap<span><?=$swap_out;?></span> <!-- SWAP usage -->
		disk<span><?=$raiddrive_out;?></span> <!-- RAID information -->
		<?=($_SERVER['SERVER_NAME'] == "noize.link" ? '' : "<small><small>POWERED BY</small> epic memes</small>");?>
		<!-- ,------.              ,--.        ,--.                                           ,--.            ,--.   -->
		<!-- |  .---',--.,--. ,---.|  |,-.     |  | ,---.  ,--,--.,--,--,  ,---.  ,---.  ,---.|  |,-. ,---. ,-'  '-. -->
		<!-- |  `--, |  ||  || .--'|     /     |  || .-. :' ,-.  ||      \| .-. || .-. || .--'|     /| .-. :'-.  .-' -->
		<!-- |  |`   '  ''  '\ `--.|  \  \     |  |\   --.\ '-'  ||  ||  || '-' '' '-' '\ `--.|  \  \\   --.  |  |   -->
		<!-- `--'     `----'  `---'`--'`--'    `--' `----' `--`--'`--''--'|  |-'  `---'  `---'`--'`--'`----'  `--'   -->
		<!--                                                              `--'                                       -->
	</body>
</html>
