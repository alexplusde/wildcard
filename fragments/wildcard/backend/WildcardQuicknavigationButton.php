
<div class="btn-group dropdown">
        <form action="/" method="get" id="wildcard_search">
            <input type="hidden" name="rex-api-call" value="wildcard_search">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="wildcardSearchQuicknavigationButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="rex-icon fa-language"> </i> <span class="caret"></span>
                </button>
            <div style="max-width: calc(100vw - 200px); width: 700px" class="quicknavi quicknavi-items list-group dropdown-menu dropdown-menu-right" aria-labelledby="wildcardSearch">
            <div style="padding: 10px;">
    
    
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" placeholder="<?= \rex_i18n::msg('wildcard_quicknavigation_search_placeholder') ?>">
                        <span class="input-group-btn">
                    <button class="btn btn-primary" id="wildcardSearchButton"><span class="rex-icon fa-search"></span> <?= \rex_i18n::msg('wildcard_quicknavigation_search_button') ?></button>
                        </span>
                    </div>
        </form>
    
        <div id="wildcardSearchResults" style="padding-top: 10px;">
        </div>
        </div>

    
        </div>
    </div>
