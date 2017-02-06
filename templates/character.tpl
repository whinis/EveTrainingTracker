<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">{{$character.info.name}}</h3>
    </div>
    <div class="panel-body">
        <div class="col-md-6 ">
            <img src="https://image.eveonline.com/Character/{{$character.ID}}_128.jpg"/>
        </div>
        <div class="col-md-6 text-left">
            {if !$charError}
                <div class="row">
                    Birthday: {{$character.info.birthday}}
                </div>
                <div class="row">
                    Corp: {{$character.info.corp.corporation_name}}
                </div>
                <div class="row">
                    Sec Status: {{$character.info.security_status}}
                </div>
            {else}
                <div class="row">
                    Birthday: CCP ERROR
                </div>
                <div class="row">
                    Corp: CCP ERROR
                </div>
                <div class="row">
                    Sec Status: CCP ERROR
                </div>
            {/if}
            <div class="row">
                {if !$skillError || $character.skills.total_sp > 0}
                    Current SP: {{$character.skills.total_sp}}
                {else}
                    Current SP: CCP ERROR
                {/if}
            </div>
            {if !$queError}
                {if $character.skillQueCount > 0}
                    <div class="alert alert-success" role="alert">Currently Training </div>
                {else}
                    <div class="alert alert-danger" role="alert">Not Currently Training </div>
                {/if}
            {else}
                <div class="alert alert-danger" role="alert">Error Loading Skill Queue </div>
            {/if}
        </div>
    </div>
</div>