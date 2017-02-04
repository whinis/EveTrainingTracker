<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eve Skill Tracker</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Eve Skill Tracker</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                {if isset($username)}
                    <li><a href="#" disabled="true">{{$username}}</a></li>
                {/if}
                <li>{{$userButton}}</li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
    {if isset($username)}
        <div class="row">
            <div class="col-md-4 text-center"></div> <!-- spacer -->
            <div class="col-md-4 text-center">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{$selectedCharacter.info.name}}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6 ">
                                <img src="https://image.eveonline.com/Character/{{$selectedCharacter.ID}}_128.jpg"/>
                            </div>
                            <div class="col-md-6 text-left">
                                <div class="row">
                                    Birthday: {{$selectedCharacter.info.birthday}}
                                </div>
                                <div class="row">
                                    Corp: {{$selectedCharacter.info.corp.corporation_name}}
                                </div>
                                <div class="row">
                                    Sec Status: {{$selectedCharacter.info.security_status}}
                                </div>
                                <div class="row">
                                    Current SP: {{$selectedCharacter.skills.total_sp}}
                                </div>
                                {if $selectedCharacter.skillQueCount > 0}
                                    <div class="alert alert-success" role="alert">Currently Training </div>
                                {else}
                                    <div class="alert alert-danger" role="alert">Not Currently Training </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Skill Queue</h3>
                        </div>
                        <div class="panel-body">
                            {if $selectedCharacter.skillQueCount > 0}
                            {foreach $selectedCharacter.skillQue as $skill}
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    {{$skill.name}}
                                </div>
                                <div class="col-md-6 text-left">
                                    {{$skill.finished_level}}
                                </div>
                            </div>
                            {/foreach}
                            {else}
                                No Skills in Que
                            {/if}
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4 text-center">
                <div class="row">Add More Characters To This Account</div>
                <div class="row">{{$loginButton}}</div>
                {foreach $characters as $character}
                    {if $selectedCharacter.ID != $character.ID}
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><a href = "?character={{$character.ID}}">{{$character.info.name}}</a></h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6 ">
                                    <img src="https://image.eveonline.com/Character/{{$character.ID}}_128.jpg"/>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="row">
                                        Birthday: {{$character.info.birthday}}
                                    </div>
                                    <div class="row">
                                        Corp: {{$character.info.corp.corporation_name}}
                                    </div>
                                    <div class="row">
                                        Sec Status: {{$character.info.security_status}}
                                    </div>
                                    <div class="row">
                                        Current SP: {{$character.skills.total_sp}}
                                    </div>
                                    <div class="row">
                                    {if $character.skillQueCount > 0}
                                        <div class="alert alert-success" role="alert">Currently Training </div>
                                    {else}
                                        <div class="alert alert-danger" role="alert">Not Currently Training </div>
                                    {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    {/if}
    <div class="row">

        Please contact Equto ingame or Whinis@whinis.com with any questions<br><br>
        EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. CCP hf. has granted permission to Eve JackKnife to use EVE Online and all associated logos and designs for promotional and information purposes on its website but does not endorse, and is not in any way affiliated with, Eve JackKnife. CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage arising from the use of this website.
    </div>
</div>
</body>
</html>