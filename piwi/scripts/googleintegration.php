<?
if($_POST['uemail'] != "") {
	if($_POST['action_type'] == "subscribe") {
		 mail("YOUR_GROUP_NAME@googlegroups.com", "subscribe", "subscribe","\r\nfrom:".$_POST['uemail']);
		 $valid = "Your request has been sent. If any trouble occurs, contact me please.";
  		 $redirectTo = "newsletter";
	} else if ($_POST['action_type'] == "unsubscribe") {
		 mail("YOUR_GROUP_NAME-unsubscribe@googlegroups.com", "unsubscribe", "unsubscribe","\r\nfrom:".$_POST['uemail']);
 		 $valid = "Your request has been sent. If any trouble occurs, contact me please.";
 		 $redirectTo = "newsletter";
	}
}
 		 $redirectTo = "newsletter";
?>