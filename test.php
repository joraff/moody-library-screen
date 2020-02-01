<?php
$db = mysql_connect('macgyver.baylor.edu', 'logins-r', 'megascreen');

	$totals = array();
	
	# Jeremy has gaps in his computer name ranges.. so we need to fetch the active computer names per area first
	
	#### Study Commons ####
	
	$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-MLGL-%" AND retireddate is NULL');
	while($row = mysql_fetch_array($result))
	  $wins[] = $row[0];
	for($i=1; $i<=35; $i++)
	  $macs[] = sprintf("MoodyMac%02d", $i);
	  
	$totals[] = build_array("Study Commons (Garden Level)", $wins, $macs);
	
	$wins = array();
	$macs = array();
	
	# Now, the checkouts are from a webpage produced by the checkout system (bearcat)
	#  On this website, an "AVAILABLE" string means it is okay to check out.

	$contents = file_get_contents('http://bearcat.baylor.edu/search%7ES7?/.b2914836/.b2914836/1,1,1,B/holdings%7E2914836&FF=&1,0,');
	$checkout_mac = preg_match_all('/AVAILABLE/', $contents, $matches);
	$checkout_due_mac = preg_match_all('/DUE/', $contents, $matches);
	
	$contents = file_get_contents('http://bearcat.baylor.edu/search%7ES7?/.b2369860/.b2369860/1,1,1,B/holdings%7E2369860&FF=&1,0,');
	$checkout_win = preg_match_all('/AVAILABLE/', $contents, $matches);
	$checkout_due_win = preg_match_all('/DUE/', $contents, $matches);
	
	$contents = file_get_contents('http://bearcat.baylor.edu/search%7ES7?/.b3182368/.b3182368/1,1,1,B/holdings%7E3182368&FF=&1,0,');
	$checkout_win += preg_match_all('/AVAILABLE/', $contents, $matches);
	$checkout_due_win += preg_match_all('/DUE/', $contents, $matches);
	
	$totals[] = array(  "title"=>"Laptops<br>(Garden Level)",
	                    "win_open"=>$checkout_win,
	                    "win_total"=>$checkout_win + $checkout_due_win,
	                    "mac_open"=>$checkout_mac,
	                    "mac_total"=>$checkout_mac + $checkout_due_mac);

#### Moody 1st ####

$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-ML2-%" AND retireddate is NULL');
while($row = mysql_fetch_array($result))
  $wins[] = $row[0];

 

$totals[] = build_array("Moody 1st", $wins);  

$wins = array();
$macs = array();
	
	#### Moody 2nd ####
	
	$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-ML2-%" AND retireddate is NULL');
	while($row = mysql_fetch_array($result))
	  $wins[] = $row[0];
	  
	for($i=1; $i<=3; $i++)
	 $macs[] = sprintf("ML-IC-Mac-%02d", $i);
	  
	$totals[] = build_array("Moody 2nd", $wins, $macs);  
	  
	$wins = array();
	$macs = array();  
	
	#### Moody 3rd ####
	  
	$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-ML3-%" AND retireddate is NULL');
	while($row = mysql_fetch_array($result))
	  $wins[] = $row[0];
	  
	for($i=1; $i<=12; $i++)
	  $macs[] = sprintf("METAMac-%02d", $i);
	  $macs[] = "METAMac-Instr";
	  
	$totals[] = build_array("Moody 3rd", $wins, $macs);
	
	$wins = array();
	$macs = array();
	#### Jones 1st ####
	  
	$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-JL1-%" AND retireddate is NULL');
	while($row = mysql_fetch_array($result))
	  $wins[] = $row[0];  
	  
	for($i=1; $i<=12; $i++)
	  $macs[] = sprintf("JJL-1st-mac-%02d", $i);  
	  
	$totals[] = build_array("Jones 1st", $wins, $macs);  
	  
	$wins = array();
	$macs = array();  
	#### Jones 2nd ####  
	  
	$result = mysql_query('SELECT computername FROM userlog.computer WHERE computername LIKE "LWS-JL2-%" AND retireddate is NULL');
	while($row = mysql_fetch_array($result))
	  $wins[] = $row[0];  
	  
	for($i=1; $i<=5; $i++)
	  $macs[] = sprintf("JJL-2nd-mac-%02d", $i);  
	
	$totals[] = build_array("Jones 2nd", $wins, $macs); 
	
	
	
	function build_array($title, $windows=array(), $macs=array()) {
	  $temp = array();
	  $temp["title"] = $title;
	  
		if(count($windows)) {
	  	// Build Windows numbers
	
	  	$str = implode("','", $windows);
	  	$result = mysql_query("SELECT max(id) FROM userlog.logins WHERE computername in ('$str') group by computername");
	  	while($row = mysql_fetch_array($result))
	  	  $ids[] = $row[0];
	  	
	  	$str = implode(",", $ids);
	  	$result = mysql_query("SELECT id FROM userlog.logins WHERE id IN ($str) AND logoutdate != '0000-00-00 00:00:00'");
	  	while($row = mysql_fetch_assoc($result))
	  	  $comps[] = $row['i'];
	  	
	  	$temp["win_open"] = count($comps);
	  	$temp["win_total"] = count($ids);
		} else {
			$temp["win_open"] = 0;
	  	$temp["win_total"] = 0;
		}
	  $ids = array();
	  $comps = array();
	  
	  //Build Mac numbers
		if(count($macs)) {
			
	  	$str = implode("','", $macs);
	  	$result = mysql_query("SELECT max(id) FROM userlog.logins WHERE computername in ('$str') group by computername");
	  	while($row = mysql_fetch_array($result))
	  	  $ids[] = $row[0];
	  	
	  	$str = implode(",", $ids);
	  	$result = mysql_query("SELECT id FROM userlog.logins WHERE id IN ($str) AND logoutdate IS NOT NULL");
	  	while($result && $row = mysql_fetch_assoc($result))
	  	  $comps[] = $row['id'];
				
			$temp["mac_open"] = count($comps);
		  $temp["mac_total"] = count($ids);
		} else {
			$temp["mac_open"] = 0;
		  $temp["mac_total"] = 0;
		}
	  
	  
	  
	  return $temp;
	}
	
	

	                    
	                    
	                    
	####################################################################################
	##             
	## Now we have our totals array! Time to start generating content....
	##
	####################################################################################
	

	# Draw Mac Column
	
	?>
	<div id="columnContainer">
	<div id="macs" class="graphColumn">
	<h2><img src="Mac.png" height="120"/></h2>
	<?php
		
		print "<ul>";
		foreach($totals as $location) {
			print "<li class=\"row\">";
			
			$open = $location["mac_open"];
			$used = $location["mac_total"] - $location["mac_open"];
			$total = $location["mac_total"] ? $location["mac_total"] : 380;
			
			$px_slice = floor(380/$total);
			$open_px = $open * $px_slice;
			$used_px = $used * $px_slice;
			
			# Make sure, because of the floor(), that our graph is 380px. If not, add it to the open section.
			if( ($open_px+$used_px) < 380 )
				$open_px += 380 - ($open_px+$used_px);
				
			if($open == 0) {
				print "<div class=\"inuse fourRound\" style=\"width: 380px\">$used</div>";
				print "<div class=\"available hidden\" style=\"width: 0px\">$open</div>";
			} elseif($open == $total) {
				print "<div class=\"inuse hidden\" style=\"width: 0px\">$used</div>";
				print "<div class=\"available fourRound\" style=\"width: 380px\">$open</div>";
			} else {
				print "<div class=\"inuse \" style=\"width: ".$used_px."px\">$used</div>";
				print "<div class=\"available \" style=\"width: ".$open_px."px\">$open</div>";
			}
			print "</li>";
		}
		print "</ul></div>";
	# Draw Location Titles
	
	print "<div class=\"locationColumn\">";
		print "<ul>";
		foreach($totals as $location) {
			$title = $location["title"];
			print "<li class=\"row\"><div class=\"location\">$title</div></li>";
		}
		print "</ul>";
	print "</div>";
	
	
	# Draw Windows Column
	
	?>
	<div id="columnContainer">
	<div id="pcs" class="graphColumn">
	<h2><img src="Win.png" height="120"/></h2>
	<?php
	
		print "<ul>";
		foreach($totals as $location) {
			print "<li class=\"row\">";
	
			$open = $location["win_open"];
			$used = $location["win_total"] - $location["win_open"];
			$total = $location["win_total"];
	
			if($total == 0)
				$px_slice = 0;
			else
				$px_slice = floor(380/$total);
			$open_px = $open * $px_slice;
			$used_px = $used * $px_slice;
	
			# Make sure, because of the floor(), that our graph is 380px. If not, add it to the open section.
			if( ($open_px+$used_px) < 380 )
				$open_px += 380 - ($open_px+$used_px);
	
			if($open == 0) {
				print "<div class=\"inuse fourRound\" style=\"width: 380px\">$used</div>";
				print "<div class=\"available hidden\" style=\"width: 0px\">$open</div>";
			} elseif($open == $total) {
				print "<div class=\"inuse hidden\" style=\"width: 0px\">$used</div>";
				print "<div class=\"available fourRound\" style=\"width: 380px\">$open</div>";
			} else {
				print "<div class=\"inuse \" style=\"width: ".$used_px."px\">$used</div>";
				print "<div class=\"available \" style=\"width: ".$open_px."px\">$open</div>";
			}
			print "</li>";
		}
		print "</ul></div>";
	?>
	
	<div id="legend">
		<div class="inuse" style="width: 160px">In-Use</div>
		<div class="available" style="width: 160px">Open</div>
	</div>
		
	<div id="timestamp">
		<span>Last Updated: <?php print date("g:i:s A")?></span>
	</div>

