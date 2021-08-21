<?php
include 'tgLib.php';

const TOKEN = '995442052:AAHRIMxoAPt_pAc2XD_1rjU3LM8Tv3bVsKw'; //EXAMPLE OF TOKEN, IT DOES NOT WORKS
$bot = new tgBot(TOKEN);
$data = json_decode(file_get_contents('php://input'),true);
if (!$data) die('ok');

if (!$data['callback_query']) {
  $text = $data['message']['text'];
  $textArr = explode (' ', $text); //for finding commands and arguments f.e.
  $chat = $data['message']['chat']['id'];
  $id = $data['message']['from']['id'];
  $first_name = $data['message']['from']['first_name'];
  if ($data['message']['reply_to_message']['from']['id']) {$reply_author = $data['message']['reply_to_message']['from']['id'];}
  if ($data['message']['reply_to_message']['message_id']) {$reply_message_id = $data['message']['reply_to_message']['message_id'];}
}
if ($text != null && isset($text))
  switch ($textArr[0]) //better than if-else conditions
  {
    case '/start':
      $bot->reply($chat, "*Hello!*\nI'm Telegram bot.");
      break;
    case '/invitelink':
      $link = $bot->chatInviteLink($chat);
      if (!$link)
        $bot->reply($chat, "*Chat's link is unavailable here.*");
      else
        $bot->reply($chat,"*Chat's link.*\n{$link}");
      break;
    case '/kick':
      if ($reply_author){
        $bot->kick($chat,$reply_author);
        $bot->reply($chat,"[User](tg://user?id={$reply_author}) kicked, [{$first_name}](tg://user?id={$id}).");
      } else
          $bot->reply($chat, "Reply on message and write this command.");
      break;
    case '/picture':
      $bot->pictureReply($chat,"*It's a picture*, [{$first_name}](tg://user?id={$id})!", "http://pm1.narvii.com/7360/5109a4d976422ebdcdbfa23563d6b37a10a27966r1-736-1104v2_hq.jpg");
      break;
    case '/video':
      $bot->videoReply($chat,"*It's a video*, [{$first_name}](tg://user?id={$id}).", "link to video or id");
      break;
    case '/gif':
      $bot->gifReply($chat,"*It's a GIF*, [{$first_name}](tg://user?id={$id}).", "https://media.giphy.com/media/ifAxq0ON2i5fcjeOdR/giphy.gif");
      break;
    case '/audio':
      $bot->audioReply($chat,"*It's a music*, [{$first_name}](tg://user?id={$id}).", "link to mp3 or id");
      break;
    case '/voice':
      $bot->voiceReply($chat,"*It's a voice*, [{$first_name}](tg://user?id={$id}).", "link to opus ogg or id"); //ONLY .ogg IN OPUS CODEC!!!!
      break;
    case '/videonote':
      $bot->videoNoteReply($chat, "link to videonote or id");
      break;
    case '/newtitle':
      $bot->setChatTitle($chat,mb_substr($text,10));
      $bot->reply($chat,"*Name of chat changed*, [{$first_name}](tg://user?id={$id}).");
      break;
    case '/pin':
      if (!$reply_message_id)
      {
        $bot->reply($chat,"Reply on message with this command.");
        exit;
      }
      $bot->pinMessage($chat,$reply_message_id);
      $bot->reply($chat,"Pinned, [{$first_name}](tg://user?id={$id}).");
      break;
    case '/unpin':
      $bot->unpinMessage($chat);
      $bot->reply($chat,"Unpinned, [{$first_name}](tg://user?id={$id}).");
      break;
    case '/tempban':
      if ($reply_author)
      {
        $unixtime = $bot->toUnix($textArr[1]); //ban period
        $bantime = time() + $unixtime; //date when person will be unbanned
        $tempban = $bot->tempban($chat,$reply_author,$unixtime);
        $bot->reply($chat,"[User](tg://user?id={$reply_author}) banned for a {$unixtime} seconds, [{$first_name}](tg://user?id={$id})."); //if $unixtime < 30 or $unixtime > 1 year - ban for forever
      } else
        $bot->reply($chat, "Reply on message with this command.");
      break;
    case '/time': //time converter lmao))
      if (!$textArr[1])
      {
        $bot->reply($chat, "Can't see your timestamp.");
        exit;
      }
        $answer = $bot->toUnix($textArr[1]);
      if ($answer['error_code'])
        $bot->reply($chat, "{$answer['error_code']}: {$answer['description']}.");
      else
        $bot->reply($chat, "In UNIX it will be `{$answer}`.");
      break;
    case '/openkeyboard':
      $rawButtons =
      [
        [
          "Button1",
          "Button2",
          "Button3"
        ],
        [
          'Hide this'
        ]
      ];
      $buttons = $bot->keyboardMakeup('keyboard', $rawButtons, ['resize_keyboard' => true, "one_time_keyboard" => false]); //array is not required, i just show examples of additional params
      $bot->reply($chat, "**Click the buttons!**", $buttons);
      break;
    //answers on keyboard's buttons (IT cAN cONFUSE YOU AND YOUR cODE!! BEWARE!! ANSWERS FROM KEYBOARD'S BUTTONS ARE THE SAME LIKE FROM USUAL TEXT!!!)
    case 'Button1':
    case 'Button2':
    case 'Button3':
      $bot->reply($chat, "You pressed the button!");
      break;
    case 'Hide':
      if ($textArr[1] == 'this' || $textArr[1] == 'keyboard')
        $bot->reply($chat, "Keyboard is hidden now.", $bot->hideKeyboard());
      break;
    //
    case '/inlinebuttons':
      $rawButtons =
      [
        [
          $bot->inlineButton("First Button", "Button1"),
          $bot->inlineButton("Second Button", "Button2")
        ],
        [
          $bot->inlineButton("Third Button", "Button3")
        ],
        [
          $bot->inlineButton("Test 1", "cbTest1"),
          $bot->inlineButton("Test 2", "cbTest2"),
          $bot->inlineButton("Test 3", "cbTest3")
        ]
      ];
      $buttons = $bot->keyboardMakeup ('inline_keyboard', $rawButtons);
      $bot->reply($chat,"**Click the buttons!**", $buttons);
      break;
    default:
      exit('ok');
      break;
  }
if ($data['callback_query'])
{
  $button_callback = $data['callback_query']['data'];
  $chat = $data['callback_query']['message']['chat']['id'];
  $id = $data['callback_query']['from']['id'];
  $first_name = $data['callback_query']['from']['first_name'];
  $message_id = $data['callback_query']['message']['message_id']; //maybe for someone it will be useful
  $cbqID = $data['callback_query']['id'];
  switch ($button_callback) {
    case 'Button1':
      $bot->reply($chat, "You touched first inline button!");
      $bot->endCB($cbqID); //it's shorted 'callbackAnswer' function just for disable loading circle
      break;
    case 'Button2':
      $bot->reply($chat, "You touched second inline button!");
      $bot->endCB($cbqID);
      break;
    case 'Button3':
      $bot->reply($chat, "You touched third inline button!");
      $bot->endCB($cbqID);
      break;
    case 'cbTest1':
      $bot->callbackAnswer($cbqID, "This is test!");
      break;
    case 'cbTest2':
      $bot->callbackAnswer($cbqID, "This is test!", ['show_alert' => true]);
      break;
    case 'cbTest3':
      $bot->callbackAnswer($cbqID, "a.", ['url' => 'https://github.com/toxichead/telelibonphp']);
      break;
    default:
      exit('ok');
      break;
  }
}

exit('ok');
