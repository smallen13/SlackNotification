How to use this:

Simple:

	$slack = new SlackNotification('<web hook url>');
	$slack->send();

With options:

	$options = new SlackNotificationOptions();
	$options->setChannel('#mychannel');
	$options->setText("New message");
	$options->setUsername("Username");
	$options->setIconEmoji(":poop:");

	$slack = new SlackNotification('<web hook url>',$options,$debug);
	$slack->send();

with an attachment and fields:

	$attachments = array();

	$tempAttach = array();
	$tempAttach['title'] = "Message:";
	$tempAttach['color'] = "#009ddc";
	$tempAttach['fallback'] = "Message:\n";
	$tempAttach['text'] = "";
	$tempAttach['fallback'].= "From: " . $name . "\nEmail: " . $email . "\nMessage: " . $message . "\n";

	$tempAttach['fields'] = array();

	$tempField = array();
	$tempField['title'] = "From";
	$tempField['value'] = $name;
	$tempField['short'] = "true";
	array_push($tempAttach['fields'],$tempField);

	$tempField = array();
	$tempField['title'] = "Email";
	$tempField['value'] = $email;
	$tempField['short'] = "true";
	array_push($tempAttach['fields'],$tempField);

	$tempField = array();
	$tempField['title'] = "Message";
	$tempField['value'] = $message;
	$tempField['short'] = "false";
	array_push($tempAttach['fields'],$tempField);

	array_push($attachments,$tempAttach);

	$options = new SlackNotificationOptions();
	$options->setChannel('#mychannel');
	$options->setText("New message");
	$options->setUsername("Username");
	$options->setIconEmoji(":poop:");
	$options->setAttachments($attachments);

	$slack = new SlackNotification('<web hook url>',$options,$debug);
	$slack->send();
