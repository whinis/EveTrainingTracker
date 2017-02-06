<?php
function Redirect($url, $permanent = false)
{
    if (headers_sent() === false) //if headers havn't been sent then we can use header
    {
        header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }else{//header send, output some javascript
        echo ' <script type="application/javascript">';
        echo '      window.onload = function() {';
        echo '          window.location.assign('.$url.');';
        echo '      };';
        echo '</script>';
    }

    exit();
}

include "include.php";
session_start();
if(isset($_GET['code'])){
    unset($_SESSION['CCP']);
    $token=$oAuth->getToken($_GET['code']);
    if(!$token){
        $_SESSION['error'][]="CCP Returned Nothing";
    }else {
        $userInfo=$oAuth->getUserInfo($token,false);

        if(!isset( $_SESSION['accountID'])) {
            $accounts = $db->selectWhere("characters", ['charID' => $userInfo['CharacterID']], ['accountID']);
            if ($accounts->rows == 0) {
                $result = $db->insert("accounts", ['mainCharacter' => $userInfo['CharacterName']]);
                $accountID=$db->lastid;
                $db->insert("characters", ['charID' => $userInfo['CharacterID'], 'refreshToken' => $_SESSION['CCP']['access']['refresh_token'], 'accountID' => $accountID]);
                $_SESSION['accountID'] = $accountID;
            } else {
                $_SESSION['accountID'] = $accounts->results[0]['accountID'];
            }
            $_SESSION['LOGGED_IN'] = true;
        }else{
            $db->insert("characters", ['charID' => $userInfo['CharacterID'], 'refreshToken' => $_SESSION['CCP']['access']['refresh_token'], 'accountID' =>  $_SESSION['accountID']]);
        }
    }
    Redirect(config::get("SSO.hostURL"));
}

if(isset($_GET['logout'])){
    session_destroy();
    Redirect(config::get("SSO.hostURL"));
}

$loginButton =  $oAuth->generateLoginButton(   ['esi-skills.read_skills.v1', 'esi-skills.read_skillqueue.v1']);
if(isset($_SESSION['LOGGED_IN'])&&$_SESSION['LOGGED_IN']) {
    if(isset($_POST['character'])){
        if(!isset($characters[$charID]['skillError'])){
            $characters[$charID]['skillError'] = false;
        }
        if(!isset($characters[$charID]['queError'])){
            $characters[$charID]['queError'] = false;
        }
        if(!isset($characters[$charID]['charError'])){
            $characters[$charID]['charError'] = false;
        }
        set_time_limit(150);// to infinity for example
        $skills = $db->query("SELECT * FROM  invtypes WHERE invtypes.groupID IN (SELECT groupID from invgroups WHERE categoryID = 16)"); // get all the skills
        $skillsCache =[];
        foreach($skills->results as $skills){
            $skillsCache[$skills['typeID']] = $skills['typeName'];
        }
        $charID = $_POST['character'];
        $char= $db->selectWhere("characters", ['accountID' => $_SESSION['accountID']], ['charID','refreshToken']);
        if(!$char->rows){
            die("Character Not Found");
        }
        $char=$char->results[0];
        if(isset($_SESSION['characters'][$charID]))
            $characters[$charID] = $_SESSION['characters'][$charID];
        if(!isset($_SESSION['characters'][$charID])||
            ($_SESSION['characters'][$charID]['tokenExpires']-60) < time()||
            !isset($_SESSION['characters'][$charID]['accessToken'])||
            $oAuth->getUserInfo($_SESSION['characters'][$charID]['accessToken'],false)==null
        )
        {
            $_SESSION['characters'][$charID]['accessToken'] = $oAuth->getToken($char['refreshToken'],true,false);
            $_SESSION['characters'][$charID]['tokenExpires'] = strtotime('+30 minutes');
        }
        $CURL->setOpt(CURLOPT_CONNECTTIMEOUT,5);
        $CURL->setOpt(CURLOPT_TIMEOUT,30);
        $CURL->setHeader("Authorization","Bearer ".$_SESSION['characters'][$charID]['accessToken']);
        $characters[$charID]['skillQue'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL","https://esi.tech.ccp.is/latest/")."characters/".$charID."/skillqueue/"),true);
        if(isset($characters[$charID]['skillQue']['error'])) {
            $characters[$charID]['skillQue'] = [];
            $characters[$charID]['queError'] = true;
        }
        foreach($characters[$charID]['skillQue'] as $key => $skill){
            $characters[$charID]['skillQue'][$key]['name'] = $skillsCache[$skill['skill_id']];
        }
        $characters[$charID]['skillQueCount'] = count($characters[$charID]['skillQue']);
        if(!isset($characters[$charID]['skills'])) {
            $CURL->setOpt(CURLOPT_CONNECTTIMEOUT,5);
            $CURL->setOpt(CURLOPT_TIMEOUT,30);
            $CURL->setHeader("Authorization", "Bearer " . $_SESSION['characters'][$charID]['accessToken']);
            $characters[$charID]['skills'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "characters/" . $charID . "/skills/"), true);
            if(isset($characters[$charID]['skills']['error'])){
                $characters[$charID]['skillError'] = true;
            }
            $characters[$charID]['skills']['total_sp'] = number_format($characters[$charID]['skills']['total_sp']);
        }
        if(!isset($characters[$charID]['info'])) {
            $CURL->setOpt(CURLOPT_CONNECTTIMEOUT,5);
            $CURL->setOpt(CURLOPT_TIMEOUT,30);
            $characters[$charID]['info'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "characters/" . $charID . "/"), true);
            if(isset($characters[$charID]['info']['error'])){
                $characters[$charID]['charError'] = true;
            }
            $CURL->setOpt(CURLOPT_CONNECTTIMEOUT,5);
            $CURL->setOpt(CURLOPT_TIMEOUT,30);
            $characters[$charID]['info']['corp'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "corporations/" . $characters[$charID]['info']['corporation_id'] . "/"), true);
            $characters[$charID]['info']['birthday'] = date("Y m d", strtotime($characters[$charID]['info']['birthday']));
            $characters[$charID]['ID'] = $charID;
            $characters[$charID]['info']['security_status'] = number_format($characters[$charID]['info']['security_status'],4);
        }
        $v['queError'] = $characters[$charID]['queError'];
        $v['charError'] = $characters[$charID]['charError'];
        $v['skillError'] = $characters[$charID]['skillError'];
        $characters[$charID]['accessToken'] = $_SESSION['characters'][$charID]['accessToken'];
        $characters[$charID]['tokenExpires'] = $_SESSION['characters'][$charID]['tokenExpires'];
        $_SESSION['characters'][$charID] = $characters[$charID];
        $v['character'] = $characters[$charID];
        $s->assign_merge($v,true);
        if(isset($_POST['displayQueue'])&&$_POST['displayQueue']=="true"){
            $s->display("MainCharacter.tpl");
        }else{
            $s->display("character.tpl");
        }
        exit();
    }else {
        if (!isset($_SESSION['characters']))
            $_SESSION['characters'] = [];
        $v['userButton'] = "<a href=\"?logout\">Logout</a>";
        $charactersResult= $db->selectWhere("characters", ['accountID' => $_SESSION['accountID']], ['charID']);
        $account = $db->selectWhere("accounts", ['id' => $_SESSION['accountID']], ['mainCharacter']);
        if($account->rows != 1){
            var_dump("Account ".$_SESSION['accountID']." not Found");
        }else{
            $v['username']=$account->results[0]['mainCharacter'];
        }

        $v['selectedCharacter'] = 0;
        $characters = [];
        if ($charactersResult->rows > 0) {
            foreach( $charactersResult->results as $char){
                if($v['selectedCharacter'] == 0){
                    $v['selectedCharacter'] = $char['charID'];
                }
                if (isset($_GET['character']) &&$char['charID'] == $_GET['character']) {
                    $v['selectedCharacter'] = $char['charID'];
                }
                $characters[] = $char['charID'];
            }
            $v['characters'] = $characters;
        }
        $v['characters'] = $characters;
    }
}else{
    $v['userButton'] = $loginButton;
}
$v['loginButton'] = $loginButton;
$s->assign_merge($v,true);
$s->display("index.tpl");