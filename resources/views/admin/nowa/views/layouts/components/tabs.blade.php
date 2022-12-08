<div class="panel panel-primary tabs-style-2">
    <div class=" tab-menu-heading">
        <div class="tabs-menu1">
            <!-- Tabs -->
            <ul class="nav panel-tabs main-nav-line">
                @foreach(config('translatable.locales') as $locale)
                    <?php
                    $active = '';
                    if($loop->first) $active = 'active';
                    ?>

                    <li><a href="#cat-{{$locale}}-{{$apartment->id}}" class="nav-link {{$active}}" data-bs-toggle="tab">{{$locale}}</a></li>
                @endforeach

            </ul>
        </div>
    </div>
    <div class="panel-body tabs-menu-body main-content-body-right border">
        <div class="tab-content">

            @foreach(config('translatable.locales') as $locale)

                <?php
                $active = '';
                if($loop->first) $active = 'active';
                ?>
                <div class="tab-pane {{$active}}" id="cat-{{$locale}}-{{$apartment->id}}">
                    {{$apartment->translate($locale)->floor ?? ''}}
                </div>
            @endforeach

        </div>
    </div>
</div>
