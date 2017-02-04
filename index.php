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
                $db->insert("characters", ['charID' => $userInfo['CharacterID'], 'refreshToken' => $_SESSION['CCP']['access']['refresh_token'], 'accountID' => $db->lastid]);
                $_SESSION['accountID'] = $db->lastid;
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
    if(!isset($_SESSION['characters']))
        $_SESSION['characters'] =[];
    $v['userButton'] = "<a href=\"?logout\">Logout</a>";
    $characterTokens = $db->selectWhere("characters",['accountID'=>$_SESSION['accountID']],['refreshToken','charID']);
    $v['username'] = $db->selectWhere("accounts",['id'=>$_SESSION['accountID']],['mainCharacter'])->results[0]['mainCharacter'];
    $skills = $db->query("SELECT * FROM  invtypes WHERE invtypes.groupID IN (SELECT groupID from invgroups WHERE categoryID = 16)"); // get all the skills
    $skillsCache =[];
    foreach($skills->results as $skills){
        $skillsCache[$skills['typeID']] = $skills['typeName'];
    }
    if($characterTokens->rows > 0){
        foreach($characterTokens->results as $char){
            if(!isset($_SESSION['characters'][$char['charID']])||($_SESSION['characters'][$char['charID']]['tokenExpires']-60) < time()||$oAuth->getUserInfo($_SESSION['characters'][$char['charID']]['accessToken'],false)==null){
                $_SESSION['characters'][$char['charID']]['accessToken'] = $oAuth->getToken($char['refreshToken'],true,false);
                $_SESSION['characters'][$char['charID']]['tokenExpires'] = strtotime('+30 minutes');
            }
            $CURL->setHeader("Authorization","Bearer ".$_SESSION['characters'][$char['charID']]['accessToken']);
            $characters[$char['charID']]['skillQue'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL","https://esi.tech.ccp.is/latest/")."characters/".$char['charID']."/skillqueue/"),true);
            $characters[$char['charID']]['skillQueCount'] = count($characters[$char['charID']]['skillQue']);
            foreach($characters[$char['charID']]['skillQue'] as $key => $skill){
                $characters[$char['charID']]['skillQue'][$key]['name'] = $skillsCache[$skill['skill_id']];
            }
            if(!isset($characters[$char['charID']]['skills'])) {
                $CURL->setHeader("Authorization", "Bearer " . $_SESSION['characters'][$char['charID']]['accessToken']);
                $characters[$char['charID']]['skills'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "characters/" . $char['charID'] . "/skills/"), true);
                $characters[$char['charID']]['skills']['total_sp'] = number_format($characters[$char['charID']]['skills']['total_sp']);
            }
            if(!isset($characters[$char['charID']]['info'])) {
                $characters[$char['charID']]['info'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "characters/" . $char['charID'] . "/"), true);
                $characters[$char['charID']]['info']['corp'] = json_decode($CURL->get(config::getOrDefault("CCP.esiURL", "https://esi.tech.ccp.is/latest/") . "corporations/" . $characters[$char['charID']]['info']['corporation_id'] . "/"), true);
                $characters[$char['charID']]['info']['birthday'] = date("Y m d", strtotime($characters[$char['charID']]['info']['birthday']));
                $characters[$char['charID']]['ID'] = $char['charID'];
            }
            $characters[$char['charID']]['accessToken'] = $_SESSION['characters'][$char['charID']]['accessToken'];
            $characters[$char['charID']]['tokenExpires'] = $_SESSION['characters'][$char['charID']]['tokenExpires'];
            $_SESSION['characters'][$char['charID']] = $characters[$char['charID']];

        }

        //Cleanup
        $remove = [];
        foreach($_SESSION['characters'] as $key=>$char){
            if(!isset($char['info'])||!isset($char['skills'])||isset($char['skills']['error'])){
                $remove[] = $key;
            }

        }
        foreach ($remove as $key){
            unset($_SESSION['characters'][$key]);
        }


        $v['characters'] = $_SESSION['characters'];
        if(isset($_GET['character']))
            $v['selectedCharacter'] = $characters[$_GET['character']];
        else
            $v['selectedCharacter'] = reset($characters);
    }
}else{
    $v['userButton'] = $loginButton;
}
$v['loginButton'] = $loginButton;
$s->assign_merge($v,true);
$s->display("index.tpl");