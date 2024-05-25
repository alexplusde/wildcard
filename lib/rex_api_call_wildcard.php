<?php

class rex_api_wildcard_search extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        $search = rex_request('q', 'string', '');
        $result = Alexplusde\Wildcard\Wildcard::query()->whereRaw(
            "`package` LIKE '%".$search."%' OR `wildcard` LIKE '%".$search."%' OR `text_de_DE` LIKE '%".$search."%'"
            )->limit(10)->orderBy('package')->orderBy('wildcard')->find(['search' => $search]);
        $wildcards = [];
        foreach ($result as $item) {
            $wildcards[$item->wildcard] = ['de_DE' => $item->text_de_DE];
        }
        /* Header setzen */
        header('Content-Type: application/json');
        echo json_encode($wildcards);
        exit;
    }
}
