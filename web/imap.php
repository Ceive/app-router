<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.app-router
 */


include '../vendor/autoload.php';

use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;
use SSilence\ImapClient\IncomingMessage;

$mailbox = 'imap.gmail.com';
$username = 'partners@call4life.net';
$password = 'C$Ladmin';
$encryption = Imap::ENCRYPT_SSL;

// Open connection
try{
    $imap = new Imap($mailbox, $username, $password, $encryption);
    // You can also check out example-connect.php for more connection options



}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL;
    die(); // Oh no :( we failed
}

// Get all folders as array of strings
$folders = $imap->getFolders();
foreach($folders as $folder) {
    echo $folder;
}

// Select the folder Inbox
$imap->selectFolder('INBOX');

// Count the messages in current folder
$overallMessages = $imap->countMessages();
$unreadMessages = $imap->countUnreadMessages();

// Fetch all the messages in the current folder
//$emails = $imap->getMessages();
//var_dump($emails);


$messages = $imap->getMessages();
/** @var IncomingMessage $message */
foreach($messages as $message){

    foreach( $message->attachments as $attachment){

    }

}
// Create a new folder named "archive"
//$imap->addFolder('archive');

// Move the first email to our new folder
//$imap->moveMessage($emails[0]['uid'], 'archive');

// Delete the second message
//$imap->deleteMessage($emails[1]['uid']);