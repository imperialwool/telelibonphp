<?
include 'tgLib.php';

const TOKEN = 'my token is here!';
$bot = new tgBot(TOKEN);
$data = json_decode(file_get_contents('php://input'),true);
if (!$data) die;
$text = $data['message']['text'];
$chat = $data['message']['chat']['id'];
$id = $data['message']['from']['id'];
$first_name = $data['message']['from']['first_name'];
if ($data['message']['reply_to_message']['from']['id']) {$reply_author = $data['message']['reply_to_message']['from']['id'];}
if ($data['message']['reply_to_message']['message_id']) {$reply_message_id = $data['message']['reply_to_message']['message_id'];}

if ($text){
    if (strtolower($text) == "/start") $bot->reply($chat, "*Hello.*\nI'm Telegram bot!.");
    if (strtolower($text) == "/invitelink") $bot->reply($chat,"*Chat's link.*\n{$bot->chatInviteLink($chat)}");
    if (strtolower($text) == "/kick") {
    	if ($reply_author){
    		$bot->kick($chat,$reply_author);
    		$bot->reply($chat,"[User](tg://user?id={$reply_author}) kicked, [{$first_name}](tg://user?id={$id}).");
    	} else {
    		$bot->reply($chat, "Reply on message and write this command.");
    	}
    } 
    if(strtolower($text) == "/picture") $bot->pictureReply($chat,"*It's a picture*, [{$first_name}](tg://user?id={$id})!", "http://pm1.narvii.com/7360/5109a4d976422ebdcdbfa23563d6b37a10a27966r1-736-1104v2_hq.jpg");
    if(strtolower($text) == "/video") $bot->videoReply($chat,"*It's a video*, [{$first_name}](tg://user?id={$id}).", "link to video or id");
    if(strtolower($text) == "/gif") $bot->gifReply($chat,"*It's a GIF*, [{$first_name}](tg://user?id={$id}).", "https://media.giphy.com/media/ifAxq0ON2i5fcjeOdR/giphy.gif");
    if(strtolower($text) == "/audio") $bot->audioReply($chat,"*It's a music*, [{$first_name}](tg://user?id={$id}).", "link to mp3 or id");
    if(strtolower($text) == "/voice") $bot->voiceReply($chat,"*It's a voice*, [{$first_name}](tg://user?id={$id}).", "link to opus ogg or id"); //ONLY .ogg IN OPUS CODEC!!!!
    if(strtolower($text) == "/videonote") $bot->videoNoteReply($chat, "link to videonote or id");
    if(strtolower(mb_substr($text,0,9)) == "/newtitle") {
        $bot->setChatTitle($chat,mb_substr($text,10));
        $bot->reply($chat,"*Name of chat changed*, [{$first_name}](tg://user?id={$id}).");
    }
    if(strtolower($text) == "/pin") {
        if (!$reply_message_id) die($bot->reply($chat,"Reply on message with this command."));
        $bot->pinMessage($chat,$reply_message_id);
        $bot->reply($chat,"Pinned, [{$first_name}](tg://user?id={$id}).");
    }
    if(strtolower($text) == "/unpin") {
        $bot->unpinMessage($chat);
        $bot->reply($chat,"Unpinned, [{$first_name}](tg://user?id={$id}).");
        }
    if(strtolower(mb_substr($text,0,8)) == "/tempban") {
        if ($reply_author){
            $unixtime = $bot->toUnix(mb_substr($text,mb_stripos($text," ")));
            $bot->reply($chat,$unixtime);
    		$tempban = $bot->tempban($chat,$reply_author,$unixtime);
    		$bot->reply($chat,"[User](tg://user?id={$reply_author}) banned for a {$unixtime} seconds, [{$first_name}](tg://user?id={$id})."); //if time<30 or >1 year - ban for forever
    	} else {
    		$bot->reply($chat, "Reply on message with this command.");
    	}
    }
    
} 
exit('ok'); //tg can be shocked if willn't see 'ok'))))