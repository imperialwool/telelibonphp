<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
class tgBot{
    private $token = '';
    public function __construct($token){
        $this->token = $token;
    }

    public function request($method, $params = []){
        $url = 'https://api.telegram.org/bot' . $this->token .  '/' . $method;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $out = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $out;
    }
    //KEYBOARD SHIT
    public function keyboardMakeup ($type, $buttonsArray, $additional_params = null)
    {
      $answer = [$type => $buttonsArray];
      if ($additional_params != null)
        $answer = array_merge($answer, $additional_params);
      return $answer;
    }

    public function inlineButton($text, $callback_query, $additional_params = null)
    {
      $answer = ['text' => $text, "callback_data" => $callback_query];
      if ($additional_params != null)
        $answer = array_merge($answer, $additional_params);
      return $answer;
    }

    public function callbackAnswer ($callback_query_id, $text = null, $additional_params = null)
    {
      $params = ['callback_query_id' => $callback_query_id];
      if ($text != null)
        $params['text'] = $text;
      if ($additional_params != null)
        $params = array_merge ($params, $additional_params);
      $a = $this->request('answerCallbackQuery', $params);
      return $a;
    }
    public function endCB ($id) //fast alias to remove loading circle on inline button
    {
      return $this->callbackAnswer($id);
    }
    public function hideKeyboard () //fast alias to hide keyboard
    {
      return ['remove_keyboard' => true];
    }
    //ended, now other
    public function reply($chat,$text, $keyboard = null, $additional_params = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "text" => $text];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($additional_params != null)
          $params = array_merge($params,$additional_params);
        return $this->request('sendMessage', $params);
    }
    public function kick($chat,$userid){
        return $this->request('kickChatMember', ["chat_id" => $chat, 'user_id' => $userid]);
    }
    public function createPoll($chat,$question,$answers = []){
        $a = $this->request('sendPoll', ["chat_id" => $chat, 'question' => $question, "options" => $answers]);
        return $a;
    }
    public function pictureReply($chat,$text,$url_of_picture, $keyboard = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "photo" => $url_of_picture];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $a = $this->request('sendPhoto', $params);
        return $a;
    }
    public function videoReply($chat,$text,$url_of_video, $keyboard = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "video" => $url_of_video];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $a = $this->request('sendVideo', $params); return $a;
    }
    public function gifReply($chat,$text,$url_of_gif, $keyboard = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "animation" => $url_of_gif];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //request
        $a = $this->request('sendAnimation', $params);
        return $a;
    }
    public function audioReply($chat,$text,$url_of_audio, $keyboard = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "audio" => $url_of_audio];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $a = $this->request('sendAudio', $params);
        return $a;
    }
    public function voiceReply($chat,$text,$url_of_voice, $keyboard = null){
        $params = ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "voice" => $url_of_voice];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $a = $this->request('sendVoice', $params);
        return $a;
    }
    public function videoNoteReply($chat,$url_of_vidnote, $keyboard = null){ //URL UNSUPPORTED
        $params = ["chat_id" => $chat, "video" => $url_of_vidnote];
        if ($keyboard != null)
          $params['reply_markup'] = json_encode($keyboard, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $a = $this->request('sendVideoNote', $params);
        return $a;
    }
    public function setChatTitle($chat,$title){
        $a = $this->request('setChatTitle', ["chat_id" => $chat, "title" => $title]);
        return $a;
    }
    public function chatInviteLink($chat){
        $a = $this->request('exportChatInviteLink', ["chat_id" => $chat]);
        return $a['result'];
    }
    public function pinMessage($chat,$message_id){
        $a = $this->request('pinChatMessage', ["chat_id" => $chat, "message_id" => $message_id]);
        return $a;
    }
    public function unpinMessage($chat){
        $a = $this->request('unpinChatMessage', ["chat_id" => $chat]);
        return $a;
    }
    public function tempban($chat,$userid,$time){
        $a = $this->request('kickChatMember', ["chat_id" => $chat, "user_id" => $userid, "until_date" => $time]);
        return $a;
    }
    //))
    public function toUnix($mainstr){
    	if (mb_stripos($mainstr, 'm')) {
    		$end = mb_stripos($mainstr, 'm');
	    	$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 60;
    		return $time;
	    	}
    	elseif (mb_stripos($mainstr, 's')) {
        $end = mb_stripos($mainstr, 's');
        $timeTEMP = mb_substr($mainstr,0,$end);
        return $timeTEMP; //in theory this shit works
    	}
    	elseif (mb_stripos($mainstr, 'h')) {
    		$end = mb_stripos($mainstr, 'h');
    		$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 3600;
    		return $time;
    		}
    	elseif (mb_stripos($mainstr, 'd')) {
    		$end = mb_stripos($mainstr, 'd');
    		$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 86400;
    		return $time;
    		}
    	elseif (mb_stripos($mainstr, 'w')) {
    		$end = mb_stripos($mainstr, 'w');
    		$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 604800;
    		return $time;
    		}
    	elseif (mb_stripos($mainstr, 'M')) {
	    	$end = mb_stripos($mainstr, 'M');
	    	$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 2629743;
    		return $time;
    		}
	    elseif (mb_stripos($mainstr, 'y')) {
    		$end = mb_stripos($mainstr, 'y');
    		$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 31556926;
    		return $time;
    		}
    	else {
    	    $array = ["error_code" => "NO_TIME", "description" => "TIME ISN'T POINTED OR INCORRECT"];
    	    return $array;
    	}
    }
}
