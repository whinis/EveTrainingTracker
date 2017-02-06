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
                        Loading Character....
                        <div id="loading{{$selectedCharacter}}" style="display: none"> </div>
                        <script type="application/javascript">
                            $(document).ready(function() {
                                var char = {ldelim}character:"{{$selectedCharacter}}",displayQueue:"true"{rdelim};
                                $.post("#",char,
                                    function( data ) {
                                        $( "#loading{{$selectedCharacter}}" ).parent().parent().html( data );
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="row">Add More Characters To This Account</div>
                <div class="row">{{$loginButton}}</div>
                {foreach $characters as $character}
                    {if $selectedCharacter != $character}
                        <div class="row">
                            <div class="panel panel-primary">
                                Loading Character....
                                <div id="loading{{$character}}" style="display: none"> </div>
                                <script type="application/javascript">
                                    $(document).ready(function() {
                                        var char = {ldelim}character:"{{$character}}"{rdelim};
                                        $.post("#",char,
                                            function( data ) {
                                                $( "#loading{{$character}}" ).parent().parent().html( data );
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    {/if}
    <div class="row">

        Please contact Equto ingame or Whinis@whinis.com with any questions<br><br>
		On github at <a href="https://github.com/whinis/EveTrainingTracker">https://github.com/whinis/EveTrainingTracker</a>
		<br><br>
        EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. CCP hf. has granted permission to Eve JackKnife to use EVE Online and all associated logos and designs for promotional and information purposes on its website but does not endorse, and is not in any way affiliated with, Eve JackKnife. CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage arising from the use of this website.
    </div>
</div>
</body>
</html>