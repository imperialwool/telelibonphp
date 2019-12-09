<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
class tgBot{
    private $token = '';
    public function __construct($token){
        $this->token = $token;
    }
    
    public function request($method, $params = []){ //да-да, request на post-е. в коем-то веке.
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
    
    public function reply($chat,$text){
        $a = $this->request('sendMessage', ["parse_mode" => "markdown", "chat_id" => $chat, "text" => $text]);
        return $a;
    }
    public function kick($chat,$userid){
        $a = $this->request('kickChatMember', ["chat_id" => $chat, 'user_id' => $userid]); return $a;
    }
    public function createPoll($chat,$question,$answers = []){
        $a = $this->request('sendPoll', ["chat_id" => $chat, 'question' => $question, "options" => $answers]); return $a;
    }
    public function pictureReply($chat,$text,$url_of_picture){
        $a = $this->request('sendPhoto', ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "photo" => $url_of_picture]); return $a;
    }
    public function videoReply($chat,$text,$url_of_video){
        $a = $this->request('sendVideo', ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "video" => $url_of_video]); return $a;
    }
    public function gifReply($chat,$text,$url_of_gif){
        $a = $this->request('sendAnimation', ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "animation" => $url_of_gif]); return $a;
    }
    public function audioReply($chat,$text,$url_of_audio){
        $a = $this->request('sendAudio', ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "audio" => $url_of_audio]);return $a;
    }
    public function voiceReply($chat,$text,$url_of_voice){
        $a = $this->request('sendVoice', ["parse_mode" => "markdown", "chat_id" => $chat, "caption" => $text, "voice" => $url_of_voice]);return $a;
    }
    public function videoNoteReply($chat,$url_of_vidnote){
        $a = $this->request('sendVideoNote', ["chat_id" => $chat, "video" => $url_of_vidnote]);return $a;
    }
    public function setChatTitle($chat,$title){
        $a = $this->request('setChatTitle', ["chat_id" => $chat, "title" => $title]);return $a;
    }
    public function chatInviteLink($chat){
        $a = $this->request('exportChatInviteLink', ["chat_id" => $chat]);return $a['result'];
    }
    public function pinMessage($chat,$message_id){
        $a = $this->request('pinChatMessage', ["chat_id" => $chat, "message_id" => $message_id]);return $a;
    }
    public function unpinMessage($chat){
        $a = $this->request('unpinChatMessage', ["chat_id" => $chat]);return $a;
    }
    public function tempban($chat,$userid,$time){
        $a = $this->request('kickChatMember', ["chat_id" => $chat, "user_id" => $userid, "until_date" => $this->toUnix($time)]);return $a;
    }
    //))
    public function toUnix($mainstr){
    	if (mb_stripos($mainstr, 'm')) {
    		$end = mb_stripos($mainstr, 'm');
	    	$timeTEMP = mb_substr($mainstr,0,$end); $time = $timeTEMP * 60;
    		return $time;
	    	}
    	elseif (mb_stripos($mainstr, 's')) {
    		$array = ["error_code" => "FALSE_TIME", "description" => "SECONDS ARE UNSUPPORTED"];
    	    return $array;
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
    	elseif (mb_stripos($mainstr, 'mn')) {
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