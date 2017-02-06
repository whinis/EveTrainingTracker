{include file='character.tpl'}
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Skill Queue</h3>
    </div>
    <div class="panel-body">
        {if !$queError}
            {if $character.skillQueCount > 0}
                {foreach $character.skillQue as $skill}
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
                No Skills in Queue
            {/if}
        {else}
            Error Loading Queue
        {/if}
    </div>
</div>