<?php
class ApiCest
{    
    public function tryApiDefaultRoute(ApiTester $I)
    {
        $I->sendGet('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'before' => 'float:>0',
            'after' => 'float:>0',
            'preloadDuration' => 'string',
            'taskDuration' => 'string',
        ]);
    }

    public function tryDefaultIndex(ApiTester $I)
    {
        $I->sendGet('/default/index');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'context' => [
                'sapi' => 'string',
                'swoole' => 'boolean',
            ],
            'before' => 'float:>0',
            'after' => 'float:>0',
            'preloadDuration' => 'string',
            'taskDuration' => 'string',
        ]);
    }

//    public function tryDefaultView(ApiTester $I)
//    {
//        $I->sendGet('/default/view');
//        $I->seeResponseCodeIs(200);
//        $I->seeResponseIsJson();
//
//        $I->seeResponseContainsJson([
//            'work' => 'done'
//        ]);
//    }

    public function tryDefaultOpenApi(ApiTester $I)
    {
        $I->sendGet('/default/open-api');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Enso',
            ],
        ]);
    }

    public function tryDefaultTelegram(ApiTester $I)
    {
        $I->sendGet('/default/telegram');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'apiResponse' => [
                'ok' => true,
            ],
        ]);
    }
    
    public function tryBadRoute(ApiTester $I)
    {
        $I->sendGet('/some/bad/link');
        $I->seeResponseCodeIsServerError();
        $I->seeResponseCodeIs(500);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'class' => 'string',
            'file' => 'string',
            'line' => 'integer:>0',
            'message' => 'string',
        ]);
    }
    
    public function tryStaticFile(ApiTester $I)
    {
        $faviconMd5 = md5(file_get_contents("public/favicon.ico"));
        $I->sendGet('/favicon.ico');
        $I->seeResponseCodeIs(200);

        $I->seeBinaryResponseEquals($faviconMd5);
    }

}